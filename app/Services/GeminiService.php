<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class GeminiService
{
    private string $apiKey;
    private string $baseUrl = 'https://generativelanguage.googleapis.com/v1/models';

    private string $model = 'gemini-2.5-flash';

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

        
        $result = $this->callModel($this->model, $prompt);

        // dump("Gemini result: ", $result);
        return $result === 'RATE_LIMITED' ? null : $result;
    }

    private function callModel(string $model, string $prompt): string|null
    {
        try {
            $response = Http::timeout(20)
                ->post("{$this->baseUrl}/{$model}:generateContent?key={$this->apiKey}", [
                    'contents' => [
                        ['parts' => [['text' => $prompt]]]
                    ],
                    'generationConfig' => [
                        'temperature'     => 0.85,
                        'maxOutputTokens' => 1000,  // enough for 2 full sentences
                        'topP'            => 0.9,
                        'stopSequences'   => [],    // no early stopping
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

            $quote = $this->extractQuote($json);


            return $quote;

        } catch (\Exception $e) {
            Log::error("Gemini exception [{$model}]", ['error' => $e->getMessage()]);
            return null;
        }
    }

    private function buildPrompt(int $moodLevel, array $feelings, ?string $reflection): string
    {
        $moodDescription = $this->moodDescription($moodLevel);
        $feelingsList    = !empty($feelings) ? implode(', ', $feelings) : null;

        $prompt  = "You are a compassionate wellness companion.\n\n";
        $prompt .= "Generate ONE complete, original, meaningful motivational quote ";
        $prompt .= "for someone feeling {$moodDescription} (mood level {$moodLevel}/10).";

        if ($feelingsList) {
            $prompt .= " Their feelings include: {$feelingsList}.";
        }

        if ($reflection) {
            $prompt .= " They wrote: \"{$reflection}\".";
        }

        $prompt .= "\n\nStrict rules:";
        $prompt .= "\n- Write a COMPLETE quote. Never stop mid-sentence.";
        $prompt .= "\n- Always end with proper punctuation (. or ! or ?).";
        $prompt .= "\n- Maximum 2 full sentences.";
        $prompt .= "\n- Return ONLY the quote text. No quotation marks, no author, no explanation.";
        $prompt .= "\n- Be warm, personal, and uplifting.";
        $prompt .= "\n- Tone guide: low mood (1-4) = gentle & compassionate | medium (5-6) = grounding & encouraging | high (7-10) = celebratory & energising.";

        return $prompt;
    }

    private function extractQuote(array $json): ?string
    {
        $text = $json['candidates'][0]['content']['parts'][0]['text'] ?? null;
        if (!$text) return null;

        // Clean up stray quotes and extra whitespace
        $text = trim(str_replace(['"', '"', '"', "'", "\n", "\r"], ['', '', '', '', ' ', ''], $text));
        $text = preg_replace('/\s+/', ' ', $text); // collapse multiple spaces

        return $text ?: null;
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