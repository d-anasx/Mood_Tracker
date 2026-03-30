@extends('layouts.app')

@section('title', 'Log Your Mood')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/mood-form.css') }}"/>
@endpush

@section('content')

<div class="mood-page">

  <div class="mood-card glass-card">

    {{-- Header --}}
    <div class="mood-card-header">
      <a href="{{ route('dashboard') }}" class="back-link">← Dashboard</a>
      <div class="eyebrow">{{ now()->format('l, F j') }}</div>
      <h1 class="display-title" style="font-size:1.9rem;">How are you feeling today?</h1>
      <p class="muted" style="margin-top:.3rem;font-size:.88rem;">Take a moment — this is your space.</p>
    </div>

    <form action="{{ route('mood.store') }}" method="POST" id="moodForm">
      @csrf

      {{-- ── MOOD LEVEL ── --}}
      <div class="form-section">
        <label class="section-label">Mood Level</label>
        <div class="mood-slider-wrap">
          <div class="slider-emojis">
            <span>😞</span><span>😐</span><span>😊</span><span>🤩</span>
          </div>
          <input type="range" name="mood_level" id="moodLevel"
            min="1" max="10" value="{{ old('mood_level', 5) }}"
            class="mood-range" oninput="updateMoodDisplay(this.value)"/>
          <div class="slider-labels">
            <span>1</span><span>5</span><span>10</span>
          </div>
        </div>
        <div class="mood-level-badge" id="moodBadge">
          <span id="moodNum">{{ old('mood_level', 5) }}</span>
          <span class="mood-badge-label" id="moodLabel">Neutral</span>
        </div>
        @error('mood_level') <p class="field-error" style="display:block;">{{ $message }}</p> @enderror
      </div>

      {{-- ── FEELINGS ── --}}
      <div class="form-section">
        <label class="section-label">Feelings <span class="optional">(optional)</span></label>
        <div class="feelings-picker">
          @foreach($feelings as $feeling)
          <label class="feeling-toggle">
            <input type="checkbox" name="feelings[]" value="{{ $feeling->id }}"
              {{ in_array($feeling->id, old('feelings', [])) ? 'checked' : '' }}/>
            <span class="feeling-chip-opt" style="--chip-color: {{ $feeling->color }};">
              {{ $feeling->icon }} {{ $feeling->name }}
            </span>
          </label>
          @endforeach
        </div>
        @error('feelings') <p class="field-error" style="display:block;">{{ $message }}</p> @enderror
      </div>

      {{-- ── SLEEP HOURS ── --}}
      <div class="form-section">
        <label class="section-label" for="sleepHours">Sleep Hours <span class="optional">(optional)</span></label>
        <div class="field" style="margin-bottom:0;">
          <input type="number" id="sleepHours" name="sleep_hours"
            placeholder="Hours slept"
            min="0" max="24" step="0.5"
            value="{{ old('sleep_hours') }}"/>
          <label for="sleepHours">Hours slept last night</label>
        </div>
        @error('sleep_hours') <p class="field-error" style="display:block;">{{ $message }}</p> @enderror
      </div>

      {{-- ── REFLECTION ── --}}
      <div class="form-section">
        <label class="section-label" for="reflection">Reflection <span class="optional">(optional)</span></label>
        <div class="textarea-wrap">
          <textarea id="reflection" name="reflection"
            placeholder="What's on your mind today?"
            maxlength="500" rows="4"
            oninput="updateCharCount(this)">{{ old('reflection') }}</textarea>
          <span class="char-count" id="charCount">
            {{ strlen(old('reflection', '')) }}/500
          </span>
        </div>
        @error('reflection') <p class="field-error" style="display:block;">{{ $message }}</p> @enderror
      </div>

      {{-- ── ACTIONS ── --}}
      <div class="form-actions">
        <a href="{{ route('dashboard') }}" class="btn-ghost">Cancel</a>
        <button type="submit" class="btn-primary" style="width:auto;padding:.9rem 2.2rem;">
          Save Entry ✦
        </button>
      </div>

    </form>
  </div>
</div>

@endsection

@push('scripts')
<script>
  const moodLabels = {
    1:'Very Low', 2:'Low', 3:'Below Average', 4:'Neutral Low',
    5:'Neutral', 6:'Neutral High', 7:'Good', 8:'Great', 9:'Excellent', 10:'Outstanding'
  };

  function updateMoodDisplay(val) {
    document.getElementById('moodNum').textContent   = val;
    document.getElementById('moodLabel').textContent = moodLabels[val] || '';

    // Colour shift based on value
    const badge = document.getElementById('moodBadge');
    const hue   = Math.round((val - 1) / 9 * 120); // 0 = red, 120 = green
    badge.style.background = `hsla(${hue}, 60%, 40%, 0.2)`;
    badge.style.borderColor = `hsla(${hue}, 60%, 60%, 0.35)`;
    badge.style.color = `hsl(${hue}, 70%, 75%)`;
  }

  function updateCharCount(el) {
    document.getElementById('charCount').textContent = el.value.length + '/500';
  }

  // Init on load
  updateMoodDisplay(document.getElementById('moodLevel').value);
</script>
@endpush