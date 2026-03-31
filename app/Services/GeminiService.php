<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class GeminiService
{
    private string $apiKey;

    // Use v1 (stable) not v1beta
    private string $baseUrl = 'https://generativelanguage.googleapis.com/v1/models';

    private array $models = [
        'gemini-2.5-flash',       // current recommended stable free model
        'gemini-2.0-flash',       // fallback
        'gemini-3-flash-preview', // latest preview
    ];

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key');
    }

    public function generateMoodQuote(int $moodLevel, array $feelings = [], ?string $reflection = null): ?string
    {
        if (!$this->apiKey) {
            Log::warning('Gemini: GEMINI_API_KEY is not set in .env');
            return null;
        }

        if (Cache::has('gemini_rate_limited')) {
            Log::info('Gemini skipped — still in rate-limit cooldown');
            return null;
        }

        $prompt = $this->buildPrompt($moodLevel, $feelings, $reflection);

        foreach ($this->models as $model) {
            $result = $this->callModel($model, $prompt);

            if ($result === 'RATE_LIMITED') continue;
            if ($result === 'NOT_FOUND')    continue;
            if ($result !== null)           return $result;
        }

        return null;
    }

    private function callModel(string $model, string $prompt): string|null
    {
        try {
            $response = Http::timeout(15)
                ->post("{$this->baseUrl}/{$model}:generateContent?key={$this->apiKey}", [
                    'contents' => [
                        ['parts' => [['text' => $prompt]]]
                    ],
                    'generationConfig' => [
                        'temperature'     => 0.85,
                        'maxOutputTokens' => 120,
                        'topP'            => 0.9,
                    ],
                ]);

            $json   = $response->json();
            $status = $response->status();

            if ($status !== 200) {
                Log::warning("Gemini [{$model}] HTTP {$status}", [
                    'error' => $json['error'] ?? $json,
                ]);
            }

            if ($status === 429) {
                $retryAfter = $this->extractRetryAfter($json);
                Cache::put('gemini_rate_limited', $retryAfter, now()->addSeconds(min($retryAfter, 120)));
                return 'RATE_LIMITED';
            }

            if ($status === 404) return 'NOT_FOUND';
            if ($response->failed()) return null;

            return $this->extractQuote($json);

        } catch (\Exception $e) {
            Log::error("Gemini exception [{$model}]", ['error' => $e->getMessage()]);
            return null;
        }
    }

    private function buildPrompt(int $moodLevel, array $feelings, ?string $reflection): string
    {
        $moodDescription = $this->moodDescription($moodLevel);
        $feelingsList    = !empty($feelings) ? implode(', ', $feelings) : null;

        $prompt  = "You are a compassionate wellness companion. Generate ONE short, original, meaningful motivational quote ";
        $prompt .= "for someone who is currently feeling {$moodDescription} (mood level {$moodLevel}/10).";
        if ($feelingsList) $prompt .= " Their current feelings include: {$feelingsList}.";
        if ($reflection)   $prompt .= " They wrote: \"{$reflection}\".";
        $prompt .= "\n\nRules:\n- Return ONLY the quote text, nothing else.\n- No quotation marks, no author name, no explanation.\n- Max 2 sentences. Be warm, personal, and uplifting.\n- If mood is low (1-4), be gentle and compassionate.\n- If mood is medium (5-6), be encouraging and grounding.\n- If mood is high (7-10), be celebratory and energising.";

        return $prompt;
    }

    private function extractQuote(array $json): ?string
    {
        $text = $json['candidates'][0]['content']['parts'][0]['text'] ?? null;
        if (!$text) return null;
        return trim(str_replace(['"', '"', '"'], '', $text));
    }

    private function extractRetryAfter(array $json): int
    {
        $message = $json['error']['message'] ?? '';
        if (preg_match('/retry in ([\d.]+)s/', $message, $matches)) {
            return (int) ceil((float) $matches[1]);
        }
        return 60;
    }

    private function moodDescription(int $level): string
    {
        return match(true) {
            $level <= 2  => 'very low and struggling',
            $level <= 4  => 'low and a bit down',
            $level <= 6  => 'neutral, just getting by',
            $level <= 8  => 'good and positive',
            default      => 'excellent and energised',
        };
    }
}