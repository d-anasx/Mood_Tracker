<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $user  = Auth::user();
        $days  = in_array((int) $request->get('days'), [30, 60, 90]) ? (int) $request->get('days') : 30;
        $since = now()->subDays($days)->startOfDay();

        // ── 1. Mood Evolution ──────────────────────────────────
        $moodEvolution = $user->moodEntries()
            ->selectRaw('entry_date, mood_level, sleep_hours')
            ->where('entry_date', '>=', $since)
            ->orderBy('entry_date')
            ->get()
            ->map(fn($e) => [
                'date'        => Carbon::parse($e->entry_date)->format('M j'),
                'mood_level'  => (float) $e->mood_level,
                'sleep_hours' => $e->sleep_hours ? (float) $e->sleep_hours : null,
            ]);

        // ── 2. Top Feelings ────────────────────────────────────
        $topFeelings = DB::table('mood_entry_feelings')
            ->join('mood_entries', 'mood_entries.id', '=', 'mood_entry_feelings.mood_entry_id')
            ->join('feelings',     'feelings.id',     '=', 'mood_entry_feelings.feeling_id')
            ->where('mood_entries.user_id',    $user->id)
            ->where('mood_entries.entry_date', '>=', $since)
            ->selectRaw('feelings.name, feelings.icon, feelings.color, COUNT(*) as count')
            ->groupBy('feelings.id', 'feelings.name', 'feelings.icon', 'feelings.color')
            ->orderByDesc('count')
            ->limit(8)
            ->get();

        // ── 3. Best Day of Week ────────────────────────────────
        $dayOfWeek = $user->moodEntries()
            ->selectRaw('EXTRACT(DOW FROM entry_date) as dow, AVG(mood_level) as avg_mood, COUNT(*) as total') // use DAYOFWEEK for MySQL , i used EXTRACT for postgresql
            ->where('entry_date', '>=', $since)
            ->groupBy('dow')
            ->orderBy('dow')
            ->get()
            ->map(fn($row) => [
                'day'      => ['', 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'][(int) $row->dow - 1], // dow-1 because EXTRACT(DOW) returns 0 for Sunday, 1 for Monday not like MySQL's DAYOFWEEK which returns 1 for Sunday
                'avg_mood' => round((float) $row->avg_mood, 1),
                'total'    => (int) $row->total,
            ]);

        // ── 4. Streak (consecutive days logged) ───────────────
        $streak = $user->getStreak();

        // ── 5. Summary stats ──────────────────────────────────
        $periodEntries = $user->moodEntries()->where('entry_date', '>=', $since);
        $stats = [
            'total_entries'  => $periodEntries->count(),
            'avg_mood'       => round($periodEntries->avg('mood_level') ?? 0, 1),
            'days_possible'  => $days,
            'completion_pct' => $days > 0
                ? min(round(($periodEntries->count() / $days) * 100), 100)
                : 0,
        ];

        return view('analytics.index', compact(
            'days',
            'moodEvolution',
            'topFeelings',
            'dayOfWeek',
            'streak',
            'stats',
        ));
    }

}