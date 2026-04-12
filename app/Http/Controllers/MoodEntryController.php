<?php

namespace App\Http\Controllers;

use App\Models\MoodEntry;
use App\Models\Feeling;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class MoodEntryController extends Controller
{
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
     */
    public function analyzeJournal(Request $request)
    {
        $request->validate([
            'journal_text' => 'required|string|min:10|max:2000',
        ]);
        
        $journalText = $request->journal_text;
        
        try {
            $analysis = $this->callGeminiAPI($journalText);
            
            return response()->json([
                'success' => true,
                'analysis' => $analysis,
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to analyze journal entry. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Call Gemini API for journal analysis.
     */
    private function callGeminiAPI($journalText)
    {
        $apiKey = env('GEMINI_API_KEY');
        
        if (!$apiKey) {
            throw new \Exception('Gemini API key not configured');
        }
        
        $prompt = $this->buildAnalysisPrompt($journalText);
        
        $response = Http::timeout(30)
            ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'topK' => 40,
                    'topP' => 0.95,
                    'maxOutputTokens' => 1024,
                ],
            ]);
        
        if (!$response->successful()) {
            throw new \Exception('Gemini API request failed: ' . $response->body());
        }
        
        $data = $response->json();
        
        if (!isset($data['candidates'][0]['content']['parts'][0]['text'])) {
            throw new \Exception('Invalid response from Gemini API');
        }
        
        $aiResponse = $data['candidates'][0]['content']['parts'][0]['text'];
        
        return $this->parseGeminiResponse($aiResponse);
    }
    
    /**
     * Build the analysis prompt for Gemini.
     */
    private function buildAnalysisPrompt($journalText)
    {
        return <<<PROMPT
You are an empathetic emotional wellness assistant for MoodTrace, a mood tracking app. 

Analyze the following journal entry and provide a JSON response with this exact structure:

{
  "mood_level": <number 1-10>,
  "emotional_tone": "<brief description>",
  "detected_emotions": ["<emotion1>", "<emotion2>", "<emotion3>"],
  "advice": "<personalized supportive advice in 2-3 sentences>",
  "suggested_feelings": ["<feeling1>", "<feeling2>"]
}

Guidelines:
- mood_level: 1 (very low/depressed) to 10 (euphoric/excellent). Be realistic, most entries are 4-7.
- emotional_tone: One sentence summary of overall emotional state
- detected_emotions: 3-5 key emotions present (e.g., "anxious", "hopeful", "frustrated", "grateful")
- advice: Warm, supportive, actionable guidance. Acknowledge their feelings, then suggest one concrete step.
- suggested_feelings: 2-3 feelings that match (choose from: Happy, Sad, Anxious, Calm, Excited, Tired, Grateful, Stressed, Hopeful, Frustrated, Content, Overwhelmed)

Journal Entry:
"""
{$journalText}
"""

Respond ONLY with valid JSON, no markdown formatting or additional text.
PROMPT;
    }
    
    /**
     * Parse Gemini's JSON response.
     */
    private function parseGeminiResponse($aiResponse)
    {
        // Remove markdown code blocks if present
        $cleaned = preg_replace('/```json\s*|\s*```/', '', $aiResponse);
        $cleaned = trim($cleaned);
        
        $parsed = json_decode($cleaned, true);
        
        if (!$parsed) {
            throw new \Exception('Failed to parse AI response as JSON');
        }
        
        // Validate structure
        if (!isset($parsed['mood_level']) || !isset($parsed['advice'])) {
            throw new \Exception('Invalid AI response structure');
        }
        
        // Ensure mood_level is between 1-10
        $parsed['mood_level'] = max(1, min(10, (int)$parsed['mood_level']));
        
        return $parsed;
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