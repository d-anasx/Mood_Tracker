@extends('layouts.app')

@section('title', 'Edit Your Mood Entry')

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
      <div class="header-icon">✏️</div>
      <h1 class="entry-title">Edit Your Mood Entry</h1>
      <p class="entry-subtitle">{{ \Carbon\Carbon::parse($entry->entry_date)->format('l, F j, Y') }}</p>
    </div>

    <form action="{{ route('mood.update', $entry->id) }}" method="POST" id="moodEntryForm">
      @csrf
      @method('PUT')

      {{-- ══════════════════════════════════════
           MOOD LEVEL
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
            value="{{ old('mood_level', $entry->mood_level) }}"
            required
            class="mood-slider"
          />
          <div class="mood-value-display">
            <span class="mood-number" id="moodValue">{{ old('mood_level', $entry->mood_level) }}</span>
            <span class="mood-label" id="moodLabel">Good</span>
          </div>
          <div class="mood-scale-labels">
            <span>1 - Very Low</span>
            <span>10 - Excellent</span>
          </div>
        </div>
      </div>

      {{-- ══════════════════════════════════════
           FEELINGS
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
          @php
            $selectedFeelingIds = old('feelings', $entry->feelings->pluck('id')->toArray());
          @endphp
          @foreach($feelings as $feeling)
          <label class="feeling-card" data-feeling-id="{{ $feeling->id }}">
            <input 
              type="checkbox" 
              name="feelings[]" 
              value="{{ $feeling->id }}"
              class="feeling-checkbox"
              {{ in_array($feeling->id, $selectedFeelingIds) ? 'checked' : '' }}
            />
            <span class="feeling-icon" style="color: {{ $feeling->color }};">{{ $feeling->icon }}</span>
            <span class="feeling-name">{{ $feeling->name }}</span>
            <span class="feeling-check">✓</span>
          </label>
          @endforeach
        </div>
      </div>

      {{-- ══════════════════════════════════════
           ADDITIONAL DETAILS
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
            value="{{ old('sleep_hours', $entry->sleep_hours) }}"
            class="sleep-input"
          />
        </div>

        <div class="field">
          <textarea 
            id="reflection" 
            name="reflection" 
            rows="4"
            maxlength="500"
            placeholder="💬 Quick Reflection (Any additional thoughts or notes...)"
            class="reflection-input"
          >{{ old('reflection', $entry->reflection) }}</textarea>
          <div class="char-count">
            <span id="reflectionCount">{{ strlen(old('reflection', $entry->reflection ?? '')) }}</span> / 500
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
          Update Entry
        </button>
      </div>

    </form>

  </div>{{-- /entry-card --}}

</div>{{-- /mood-entry-page --}}

@endsection

@push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const moodSlider = document.getElementById('moodSlider');
      const moodValue = document.getElementById('moodValue');
      const moodLabel = document.getElementById('moodLabel');
      const reflectionInput = document.getElementById('reflection');
      const reflectionCount = document.getElementById('reflectionCount');
      const feelingCards = document.querySelectorAll('.feeling-card');
      
      const moodLabels = {
        1: 'Very Low', 2: 'Low', 3: 'Poor', 4: 'Below Average', 5: 'Fair',
        6: 'Okay', 7: 'Good', 8: 'Great', 9: 'Excellent', 10: 'Amazing'
      };
      
      // Initialize mood label
      moodLabel.textContent = moodLabels[parseInt(moodSlider.value)];
      
      moodSlider.addEventListener('input', () => {
        const value = parseInt(moodSlider.value);
        moodValue.textContent = value;
        moodLabel.textContent = moodLabels[value];
      });
      
      if (reflectionInput) {
        reflectionInput.addEventListener('input', () => {
          reflectionCount.textContent = reflectionInput.value.length;
        });
      }
      
      feelingCards.forEach(card => {
        card.addEventListener('click', () => {
          const checkbox = card.querySelector('.feeling-checkbox');
          checkbox.checked = !checkbox.checked;
        });
      });
    });
  </script>
@endpush