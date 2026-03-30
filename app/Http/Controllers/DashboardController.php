<?php

namespace App\Http\Controllers;

use App\Models\MoodEntry;
use App\Models\Quote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Today's entry with feelings eager-loaded
        $todayEntry = $user->todayEntry();

        // Last 11 entries for the chart (oldest → newest for left-to-right display)
        $recentEntries = $user->moodEntries()
            ->with('feelings')
            ->orderBy('entry_date', 'desc')
            ->take(11)
            ->get()
            ->reverse()
            ->values();

        // Trend: last 5 vs previous 5 (min 10 entries required)
        $trendData = $this->calculateTrend($user);

        // Quote based on today's mood, or any active quote if no entry yet
        $quote = $todayEntry
            ? Quote::forMoodLevel($todayEntry->mood_level)   
            : Quote::where('is_active', true)->inRandomOrder()->first();

        // Unread notification count for the nav badge
        $unreadCount = $user->notifications()->unread()->count();

        return view('dashboard.index', compact(
            'user',
            'todayEntry',
            'recentEntries',
            'trendData',
            'quote',
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

        // minimum 5 entries required for comparison
        if ($entries->count() < 10) {
            return null;
        }

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