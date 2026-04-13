@extends('layouts.app')

@section('title', 'Log Your Mood')

@section('orbs')
  <div class="orb orb-purple"></div>
  <div class="orb orb-teal"></div>
  <div class="orb orb-rose" style="opacity:0.3;"></div>
@endsection

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/mood-entry.css') }}"/>
@endpush

@section('content')

<div class="mood-entry-page">
  
  {{-- Back to dashboard --}}
  <a href="{{ route('dashboard') }}" class="back-link">
    ← Back to Dashboard
  </a>

  <div class="glass-card entry-card">
    
    <div class="entry-header">
      <div class="header-icon">✨</div>
      <h1 class="entry-title">How are you feeling today?</h1>
      <p class="entry-subtitle">{{ now()->format('l, F j, Y') }}</p>
    </div>

    <form action="{{ route('mood.store') }}" method="POST" id="moodEntryForm">
      @csrf

      {{-- ══════════════════════════════════════
           STEP 1: Journal Entry (AI Analysis)
      ══════════════════════════════════════ --}}
      <div class="form-section">
        <div class="section-header">
          <h2 class="section-title">
            <span class="section-icon">📝</span>
            Your Journal
          </h2>
          <p class="section-subtitle">Write freely about your day, thoughts, or feelings</p>
        </div>

        <div class="field">
          <textarea 
            id="journalText" 
            rows="6" 
            placeholder="Today I felt..."
            maxlength="2000"
          ></textarea>
          <div class="char-count">
            <span id="charCount">0</span> / 2000
          </div>
        </div>

        <button type="button" id="analyzeBtn" class="btn-analyze" disabled>
          <span class="btn-icon">🤖</span>
          <span class="btn-text">Analyze with AI</span>
          <span class="btn-loader" style="display:none;">
            <span class="loader-dot"></span>
            <span class="loader-dot"></span>
            <span class="loader-dot"></span>
          </span>
        </button>

        {{-- AI Analysis Result --}}
        <div id="aiAnalysis" class="ai-analysis" style="display:none;">
          <div class="analysis-header">
            <span class="analysis-icon">✨</span>
            <h3 class="analysis-title">AI Insights</h3>
          </div>
          
          <div class="analysis-content">
            <div class="insight-item">
              <div class="insight-label">Emotional Tone</div>
              <div class="insight-value" id="emotionalTone"></div>
            </div>
            
            <div class="insight-item">
              <div class="insight-label">Detected Emotions</div>
              <div class="emotion-tags" id="detectedEmotions"></div>
            </div>
            
            <div class="insight-item">
              <div class="insight-label">Suggested Mood Level</div>
              <div class="suggested-mood" id="suggestedMood"></div>
            </div>
            
            <div class="insight-item advice-box">
              <div class="insight-label">Personalized Advice</div>
              <p class="advice-text" id="aiAdvice"></p>
            </div>
          </div>
        </div>
      </div>

      {{-- ══════════════════════════════════════
           STEP 2: Mood Level
      ══════════════════════════════════════ --}}
      <div class="form-section">
        <div class="section-header">
          <h2 class="section-title">
            <span class="section-icon">📊</span>
            Mood Level
          </h2>
          <p class="section-subtitle">Rate your overall mood from 1 (very low) to 10 (excellent)</p>
        </div>

        <div class="mood-slider-container">
          <input 
            type="range" 
            id="moodSlider" 
            name="mood_level" 
            min="1" 
            max="10" 
            value="7"
            required
            class="mood-slider"
          />
          <div class="mood-value-display">
            <span class="mood-number" id="moodValue">7</span>
            <span class="mood-label" id="moodLabel">Good</span>
          </div>
          <div class="mood-scale-labels">
            <span>1 - Very Low</span>
            <span>10 - Excellent</span>
          </div>
        </div>
      </div>

      {{-- ══════════════════════════════════════
           STEP 3: Feelings
      ══════════════════════════════════════ --}}
      <div class="form-section">
        <div class="section-header">
          <h2 class="section-title">
            <span class="section-icon">💭</span>
            Select Your Feelings
          </h2>
          <p class="section-subtitle">Choose all that apply (optional)</p>
        </div>

        <div class="feelings-grid">
          @foreach($feelings as $feeling)
          <label class="feeling-card" data-feeling-id="{{ $feeling->id }}">
            <input 
              type="checkbox" 
              name="feelings[]" 
              value="{{ $feeling->id }}"
              class="feeling-checkbox"
            />
            <span class="feeling-icon" style="color: {{ $feeling->color }};">{{ $feeling->icon }}</span>
            <span class="feeling-name">{{ $feeling->name }}</span>
            <span class="feeling-check">✓</span>
          </label>
          @endforeach
        </div>
      </div>

      {{-- ══════════════════════════════════════
           STEP 4: Additional Details
      ══════════════════════════════════════ --}}
      <div class="form-section">
        <div class="section-header">
          <h2 class="section-title">
            <span class="section-icon">🌙</span>
            Additional Details
          </h2>
        </div>

        <div class="field">
          <input 
            type="number" 
            id="sleepHours" 
            name="sleep_hours" 
            min="0" 
            max="24" 
            step="0.5"
            placeholder="💤 Hours of Sleep Last Night"
            class="sleep-input"
          />
        </div>

        <div class="field">
          <textarea 
            id="reflection" 
            name="reflection" 
            rows="3"
            maxlength="500"
            placeholder="💬 Quick Reflection (Any additional thoughts or notes...)"
            class="reflection-input"
          ></textarea>
          <div class="char-count">
            <span id="reflectionCount">0</span> / 500
          </div>
        </div>
      </div>

      {{-- ══════════════════════════════════════
           SUBMIT
      ══════════════════════════════════════ --}}
      <div class="form-actions">
        <a href="{{ route('dashboard') }}" class="btn-secondary">Cancel</a>
        <button type="submit" class="btn-primary">
          <span class="btn-icon">✓</span>
          Save Mood Entry
        </button>
      </div>

    </form>

  </div>{{-- /entry-card --}}

</div>{{-- /mood-entry-page --}}

@endsection

@push('scripts')
  <script src="{{ asset('js/mood-entry.js') }}" defer></script>
@endpush