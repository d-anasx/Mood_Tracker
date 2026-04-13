<?php
namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    private string $apiKey;
    private string $baseUrl = 'https://generativelanguage.googleapis.com/v1/models';

    private string $model = 'gemini-2.5-flash-lite';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key');
    }

    public function generateMoodQuote(int $moodLevel, array $feelings = [], ?string $reflection = null): ?string
    {
        if (! $this->apiKey) {
            return null;
        }

        if (Cache::has('gemini_rate_limited')) {
            Log::info('Gemini skipped — still in rate-limit cooldown');
            return null;
        }

        $prompt = $this->buildPrompt($moodLevel, $feelings, $reflection);

        $result = $this->callModel($this->model, $prompt);

        return $result === 'RATE_LIMITED' ? null : $result;
    }

    private function callModel(string $model, string $prompt): string | null
    {
        try {
            $response = Http::timeout(30)
                ->post("{$this->baseUrl}/{$model}:generateContent?key={$this->apiKey}", [
                    'contents'         => [
                        ['parts' => [['text' => $prompt]]],
                    ],
                    'generationConfig' => [
                        'temperature'     => 0.85,
                        'maxOutputTokens' => 1024, // enough for 2 full sentences
                        'topP'            => 0.9,
                        'stopSequences'   => [], // no early stopping
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

            if ($status === 404) {
                return 'NOT_FOUND';
            }

            if ($response->failed()) {
                return null;
            }

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
        $feelingsList    = ! empty($feelings) ? implode(', ', $feelings) : null;

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
        if (! $text) {
            return null;
        }

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
        return match (true) {
            $level <= 2 => 'very low and struggling',
            $level <= 4 => 'low and a bit down',
            $level <= 6 => 'neutral, just getting by',
            $level <= 8 => 'good and positive',
            default     => 'excellent and energised',
        };
    }

    /**
     * Analyze journal text and return structured analysis
     */
    /**
     * Analyze journal text and return structured analysis
     */
    /**
     * Analyze journal text and return structured analysis
     */
    public function analyzeJournal(string $journalText): ?array
    {

        if (! $this->apiKey) {
            Log::error('Gemini API key not configured');
            return null;
        }

        $prompt = $this->buildAnalysisPrompt($journalText);

                                 // Call the API using the same working method as generateMoodQuote
        $model   = $this->model; // 'gemini-2.5-flash'
        $baseUrl = 'https://generativelanguage.googleapis.com/v1/models';

        try {
            $response = Http::timeout(30)
                ->post("{$baseUrl}/{$model}:generateContent?key={$this->apiKey}", [
                    'contents'         => [
                        ['parts' => [['text' => $prompt]]],
                    ],
                    'generationConfig' => [
                        'temperature'     => 0.7,
                        'maxOutputTokens' => 1024,
                    ],
                ]);

            if (! $response->successful()) {
                Log::error('Gemini API error', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return null;
            }

            $data       = $response->json();
            $aiResponse = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

            if (! $aiResponse) {
                Log::error('No text in Gemini response', ['data' => $data]);
                return null;
            }

            // Parse the JSON response
            return $this->parseAnalysisResponse($aiResponse);

        } catch (\Exception $e) {
            Log::error('Gemini exception: ' . $e->getMessage());
            return null;
        }
    }

/**
 * Build analysis prompt for journal entry
 */
    private function buildAnalysisPrompt(string $journalText): string
    {
        return "You are an empathetic emotional wellness assistant for MoodTrace, a mood tracking app.

Analyze the following journal entry and provide a JSON response with this exact structure:

{
  \"mood_level\": 7,
  \"emotional_tone\": \"Generally positive with some anxiety about the future\",
  \"detected_emotions\": [\"hopeful\", \"anxious\", \"grateful\"],
  \"advice\": \"Your awareness of both excitement and anxiety is healthy. Try writing down three things you're grateful for today to anchor the positive feelings.\",
  \"suggested_feelings\": [\"Hopeful\", \"Anxious\"]
}

Important rules:
- mood_level: MUST be a number between 1 and 10 only
- emotional_tone: One short sentence
- detected_emotions: Array of 3-5 emotions as strings
- advice: 2-3 warm, supportive sentences
- suggested_feelings: Array of 2-3 feelings from: Happy, Sad, Anxious, Calm, Excited, Tired, Grateful, Stressed, Hopeful, Frustrated, Content, Overwhelmed

SPECIAL HANDLING FOR OUT-OF-CONTEXT ENTRIES:
If the journal entry is:
- Nonsense text (like \"asdfasdf\", \"12345\", keyboard mashing)
- Not about feelings, emotions, or daily experiences
- Too short to analyze meaningfully (less than 15 meaningful words)
- Written in a language other than English (unless it's clearly emotional content)

THEN respond with this default output:
{
  \"mood_level\": 5,
  \"emotional_tone\": \"Neutral - unable to detect clear emotional content\",
  \"detected_emotions\": [\"neutral\", \"calm\"],
  \"advice\": \"Try writing a few sentences about your day, how you're feeling, or what's on your mind. The more you share, the better insights I can provide!\",
  \"suggested_feelings\": [\"Calm\"]
}

Journal Entry:
\"\"\"
{$journalText}
\"\"\"

Respond ONLY with valid JSON. Do not include any markdown formatting, code blocks, or extra text outside the JSON.";
    }

/**
 * Parse analysis response from Gemini
 */
    private function parseAnalysisResponse(string $response): ?array
    {
        // Clean the response
        $cleaned = trim($response);

        // Remove markdown code blocks
        $cleaned = preg_replace('/```json\s*/', '', $cleaned);
        $cleaned = preg_replace('/```\s*/', '', $cleaned);

        // Try to find JSON object
        if (preg_match('/\{[^{}]*"mood_level"[^{}]*\}/', $cleaned, $matches)) {
            $cleaned = $matches[0];
        }

        // Decode JSON
        $parsed = json_decode($cleaned, true);

        if (! $parsed) {
            Log::error('JSON decode failed', ['cleaned' => $cleaned]);

            // Try to fix single quotes
            $fixed  = str_replace("'", '"', $cleaned);
            $parsed = json_decode($fixed, true);

            if (! $parsed) {
                // Return a default analysis
                return [
                    'mood_level'         => 5,
                    'emotional_tone'     => 'Unable to analyze at this moment',
                    'detected_emotions'  => ['reflective'],
                    'advice'             => 'Thank you for sharing. Every journal entry helps you understand yourself better.',
                    'suggested_feelings' => ['Calm', 'Content'],
                ];
            }
        }

        // Ensure all required fields exist
        return [
            'mood_level'         => max(1, min(10, (int) ($parsed['mood_level'] ?? 5))),
            'emotional_tone'     => $parsed['emotional_tone'] ?? 'Reflective moment',
            'detected_emotions'  => $parsed['detected_emotions'] ?? ['reflective'],
            'advice'             => $parsed['advice'] ?? 'Keep journaling to better understand your emotions.',
            'suggested_feelings' => $parsed['suggested_feelings'] ?? ['Calm', 'Content'],
        ];
    }
}
