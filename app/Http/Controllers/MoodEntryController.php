<?php

namespace App\Http\Controllers;

use App\Models\MoodEntry;
use App\Models\Feeling;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MoodEntryController extends Controller
{
    /**
     * Inject GeminiService to reuse the working API logic
     */
    public function __construct(private GeminiService $gemini)
    {
    }

    /**
     * Show the mood entry form.
     */
    public function create()
    {
        $user = Auth::user();
        $todayEntry = $user->todayEntry();
        
        // If entry exists today, redirect to edit
        if ($todayEntry) {
            return redirect()->route('mood.edit', $todayEntry->id);
        }
        
        $feelings = Feeling::all();
        
        return view('mood.create', compact('feelings'));
    }
    
    /**
     * Analyze journal text with Gemini AI.
     * Uses the GeminiService for all API communication.
     */
    public function analyzeJournal(Request $request)
    {
        $request->validate([
            'journal_text' => 'required|string|min:10|max:2000',
        ]);
        
        $journalText = $request->journal_text;
        
        try {
            // Call the GeminiService method we added
            $analysis = $this->gemini->analyzeJournal($journalText);
            
            if (!$analysis) {
                throw new \Exception('Failed to analyze journal - no response from AI');
            }
            return response()->json([
                'success' => true,
                'analysis' => $analysis,
            ]);
            
        } catch (\Exception $e) {
            Log::error('Gemini analysis failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to analyze journal entry: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Store the mood entry.
     */
    public function store(Request $request)
    {
        $request->validate([
            'mood_level' => 'required|integer|min:1|max:10',
            'reflection' => 'nullable|string|max:500',
            'sleep_hours' => 'nullable|numeric|min:0|max:24',
            'feelings' => 'nullable|array',
            'feelings.*' => 'exists:feelings,id',
        ]);
        
        $user = Auth::user();
        
        // Check if entry already exists today
        $existingEntry = $user->todayEntry();
        if ($existingEntry) {
            return redirect()
                ->route('mood.edit', $existingEntry->id)
                ->with('error', 'You already have an entry for today. Edit it instead.');
        }
        
        // Create mood entry
        $entry = MoodEntry::create([
            'user_id' => $user->id,
            'entry_date' => now()->toDateString(),
            'mood_level' => $request->mood_level,
            'reflection' => $request->reflection,
            'sleep_hours' => $request->sleep_hours,
        ]);
        
        // Attach feelings
        if ($request->has('feelings')) {
            $entry->feelings()->attach($request->feelings);
        }
        
        return redirect()
            ->route('dashboard')
            ->with('success', 'Your mood has been logged! 🎉');
    }
    
    /**
     * Show edit form for existing entry.
     */
    public function edit($id)
    {
        $entry = MoodEntry::with('feelings')->findOrFail($id);
        
        // Only allow editing own entries
        if ($entry->user_id !== Auth::id()) {
            abort(403);
        }
        
        // Only allow editing today's entry
        if (!$entry->isEditable()) {
            return redirect()
                ->route('dashboard')
                ->with('error', 'You can only edit today\'s entry.');
        }
        
        $feelings = Feeling::all();
        
        return view('mood.edit', compact('entry', 'feelings'));
    }
    
    /**
     * Update existing mood entry.
     */
    public function update(Request $request, $id)
    {
        $entry = MoodEntry::findOrFail($id);
        
        // Only allow editing own entries
        if ($entry->user_id !== Auth::id()) {
            abort(403);
        }
        
        // Only allow editing today's entry
        if (!$entry->isEditable()) {
            return redirect()
                ->route('dashboard')
                ->with('error', 'You can only edit today\'s entry.');
        }
        
        $request->validate([
            'mood_level' => 'required|integer|min:1|max:10',
            'reflection' => 'nullable|string|max:500',
            'sleep_hours' => 'nullable|numeric|min:0|max:24',
            'feelings' => 'nullable|array',
            'feelings.*' => 'exists:feelings,id',
        ]);
        
        $entry->update([
            'mood_level' => $request->mood_level,
            'reflection' => $request->reflection,
            'sleep_hours' => $request->sleep_hours,
        ]);
        
        // Sync feelings
        if ($request->has('feelings')) {
            $entry->feelings()->sync($request->feelings);
        } else {
            $entry->feelings()->detach();
        }
        
        return redirect()
            ->route('dashboard')
            ->with('success', 'Your mood entry has been updated! ✨');
    }
}