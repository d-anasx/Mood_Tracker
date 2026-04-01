<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use App\Services\GeminiService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(private GeminiService $gemini) {}

    public function index()
    {
        $user = Auth::user();

        $todayEntry = $user->todayEntry();

        $recentEntries = $user->moodEntries()
            ->with('feelings')
            ->orderBy('entry_date', 'desc')
            ->take(11)
            ->get()
            ->reverse()
            ->values();

        $trendData = $this->calculateTrend($user);

        // ── Quote logic ─────────────────────────────────────
        // Priority: 1) Gemini AI  2) DB quote  3) hardcoded fallback
        $quote       = null;
        $quoteSource = 'fallback';

        if ($todayEntry) {
            // 1. Try Gemini
            $feelingNames = $todayEntry->feelings->pluck('name')->toArray();
            $aiText = $this->gemini->generateMoodQuote(
                $todayEntry->mood_level,
                $feelingNames,
                $todayEntry->reflection
            );
            if ($aiText) {
                $quote       = (object) ['text' => $aiText, 'author' => 'MoodTrace AI'];
                $quoteSource = 'ai';
            }

            // 2. Try DB quote
            if (!$quote) {
                $dbQuote = Quote::where('is_active', true)->inRandomOrder()->first();
                if ($dbQuote) {
                    $quote       = $dbQuote;
                    $quoteSource = 'db';
                }
            }

        } else {
            // No entry today — show a generic encouraging quote
            $dbQuote = Quote::where('is_active', true)->inRandomOrder()->first();
            $quote       = $dbQuote ?? (object) [
                'text'   => 'How are you feeling today? Take a moment to check in with yourself.',
                'author' => 'MoodTrace',
            ];
            $quoteSource = $dbQuote ? 'db' : 'fallback';
        }

        $unreadCount = $user->notifications()->unread()->count();

        return view('dashboard.index', compact(
            'user',
            'todayEntry',
            'recentEntries',
            'trendData',
            'quote',
            'quoteSource',
            'unreadCount'
        ));
    }

    // -------------------------------------------------------

    private function calculateTrend($user): ?array
    {
        $entries = $user->moodEntries()
            ->orderBy('entry_date', 'desc')
            ->take(10)
            ->get();

        if ($entries->count() < 10) return null;

        $recent   = $entries->take(5);
        $previous = $entries->skip(5)->take(5);

        $recentMoodAvg   = round($recent->avg('mood_level'), 1);
        $previousMoodAvg = round($previous->avg('mood_level'), 1);
        $moodChange      = round($recentMoodAvg - $previousMoodAvg, 1);

        $recentSleepAvg   = round($recent->whereNotNull('sleep_hours')->avg('sleep_hours') ?? 0, 1);
        $previousSleepAvg = round($previous->whereNotNull('sleep_hours')->avg('sleep_hours') ?? 0, 1);
        $sleepChange      = round($recentSleepAvg - $previousSleepAvg, 1);

        return [
            'mood' => [
                'recent'   => $recentMoodAvg,
                'previous' => $previousMoodAvg,
                'change'   => $moodChange,
                'trend'    => $moodChange > 0 ? 'up' : ($moodChange < 0 ? 'down' : 'stable'),
            ],
            'sleep' => [
                'recent'   => $recentSleepAvg,
                'previous' => $previousSleepAvg,
                'change'   => $sleepChange,
                'trend'    => $sleepChange > 0 ? 'up' : ($sleepChange < 0 ? 'down' : 'stable'),
            ],
        ];
    }
}