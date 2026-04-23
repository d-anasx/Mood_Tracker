<?php

namespace App\Http\Controllers;

use App\Services\GeminiService;
use App\Models\Quote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class QuoteController extends Controller
{
    public function __construct(private GeminiService $gemini) {}

    // =========================================================
    // GET /quote/generate
    // Called via AJAX from the dashboard after a mood entry is saved.
    // Falls back to a DB quote if Gemini fails.
    // =========================================================

    public function generate(Request $request): JsonResponse
    {
        $request->validate([
            'mood_level' => ['required', 'integer', 'min:1', 'max:10'],
            'feelings'   => ['nullable', 'array'],
            'feelings.*' => ['string'],
            'reflection' => ['nullable', 'string', 'max:500'],
        ]);

        $moodLevel  = (int) $request->mood_level;
        $feelings   = $request->feelings ?? [];
        $reflection = $request->reflection;

        // Try Gemini first
        $aiQuote = $this->gemini->generateMoodQuote($moodLevel, $feelings, $reflection);
        Log::info("AI Quote generation for mood level $moodLevel with feelings [" . implode(', ', $feelings) . "] and reflection: $reflection. Result: " . ($aiQuote ?? 'null'));
        if ($aiQuote) {
            return response()->json([
                'source' => 'ai',
                'quote'  => $aiQuote,
                'author' => 'MoodTrace AI',
            ]);
        }

        // Fallback: pull a quote from the database
        $dbQuote = Quote::forMoodLevel($moodLevel);

        if ($dbQuote) {
            return response()->json([
                'source' => 'db',
                'quote'  => $dbQuote->text,
                'author' => $dbQuote->author,
            ]);
        }

        // Last resort
        return response()->json([
            'source' => 'fallback',
            'quote'  => 'Every step forward counts, no matter how small.',
            'author' => 'MoodTrace',
        ]);
    }
}