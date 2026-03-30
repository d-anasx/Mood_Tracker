<?php

namespace App\Http\Controllers;

use App\Models\Feeling;
use App\Models\MoodEntry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\Request;

class MoodEntryController extends Controller
{
    // ── Show form to create today's entry ──
    public function create(): View|RedirectResponse
    {
        $user = Auth::user();

        if ($user->todayEntry()) {
            return redirect()
                ->route('mood.edit', $user->todayEntry()->id)
                ->with('info', 'You already logged today. You can edit your entry below.');
        }

        $feelings = Feeling::all();

        return view('mood.create', compact('feelings'));
    }

    // ── Save today's entry ──
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // Double-check: one entry per day
        if ($user->todayEntry()) {
            return redirect()
                ->route('mood.edit', $user->todayEntry()->id)
                ->with('info', 'You already have an entry for today.');
        }

        $entry = MoodEntry::create([
            'user_id'    => $user->id,
            'mood_level' => $request->mood_level,
            'sleep_hours'=> $request->sleep_hours,
            'reflection' => $request->reflection,
            'entry_date' => today(),
        ]);

        // Attach selected feelings (pivot)
        if ($request->filled('feelings')) {
            $entry->feelings()->attach($request->feelings);
        }

        return redirect()
            ->route('dashboard')
            ->with('success', 'Mood logged! Keep it up 🌟');
    }

    // ── Show edit form for today's entry ──
    public function edit(MoodEntry $moodEntry): View|RedirectResponse
    {
        // Only owner can edit, and only today's entry
        if ($moodEntry->user_id !== Auth::id()) {
            abort(403);
        }

        if (! $moodEntry->isEditable()) {
            return redirect()
                ->route('dashboard')
                ->with('error', 'Past entries can only be viewed, not edited.');
        }

        $feelings = Feeling::all();

        return view('mood.edit', compact('moodEntry', 'feelings'));
    }

    // ── Update today's entry ──
    public function update(Request $request, MoodEntry $moodEntry): RedirectResponse
    {
        if ($moodEntry->user_id !== Auth::id()) {
            abort(403);
        }

        if (! $moodEntry->isEditable()) {
            return redirect()
                ->route('dashboard')
                ->with('error', 'This entry can no longer be edited.');
        }

        $moodEntry->update([
            'mood_level'  => $request->mood_level,
            'sleep_hours' => $request->sleep_hours,
            'reflection'  => $request->reflection,
        ]);

        // Sync feelings (replaces old ones)
        $moodEntry->feelings()->sync($request->feelings ?? []);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Entry updated successfully.');
    }
}