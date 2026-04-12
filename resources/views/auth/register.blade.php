@extends('layouts.app')

@section('title', 'Create Account')
@section('hideNav', true)
{{-- Teal-tinted orbs for the register page --}}
@section('orbs')
  <div class="orb orb-purple"></div>
  <div class="orb orb-teal"></div>
  <div class="orb orb-rose" style="opacity:0.3;"></div>
@endsection

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/auth/register.css') }}"/>
@endpush

@section('content')

<div class="register-page">
  <div class="glass-card register-card">

    {{-- ══════════════════════════════════════
         SIDEBAR — logo + step tracker
    ══════════════════════════════════════ --}}
    <aside class="reg-sidebar">

      <div class="reg-logo">
        <div class="reg-logo-icon">🌙</div>
        <span class="reg-logo-text">MoodTrace</span>
      </div>

      <ol class="steps-list" aria-label="Registration steps">

        <li class="step-item">
          <div class="step-circle is-active" id="sc-1" aria-current="step">1</div>
          <div class="step-text">
            <div class="step-name is-active" id="sn-1">Account</div>
            <div class="step-desc is-active"  id="sd-1">Your credentials</div>
          </div>
        </li>

        <li class="step-item">
          <div class="step-circle" id="sc-2">2</div>
          <div class="step-text">
            <div class="step-name" id="sn-2">Profile</div>
            <div class="step-desc" id="sd-2">Name &amp; avatar</div>
          </div>
        </li>

        <li class="step-item">
          <div class="step-circle" id="sc-3">3</div>
          <div class="step-text">
            <div class="step-name" id="sn-3">Mood Baseline</div>
            <div class="step-desc" id="sd-3">How you feel today</div>
          </div>
        </li>

        <li class="step-item">
          <div class="step-circle" id="sc-4">4</div>
          <div class="step-text">
            <div class="step-name" id="sn-4">Preferences</div>
            <div class="step-desc" id="sd-4">Reminders &amp; timezone</div>
          </div>
        </li>

      </ol>

      <div class="sidebar-progress" aria-label="Overall progress">
        <div class="progress-meta">
          <span>Progress</span>
          <span id="progressPct">25%</span>
        </div>
        <div class="progress-track">
          <div class="progress-fill" id="progressFill" style="width:25%;"></div>
        </div>
      </div>

    </aside>

    {{-- ══════════════════════════════════════
         MAIN — multi-step form panels
    ══════════════════════════════════════ --}}
    <div class="reg-main">

      {{-- Master form — all data submitted at once on final step --}}
      <form action="{{ route('register') }}" method="POST" id="registerForm" >
        @csrf

        {{-- ── STEP 1 — Account ── --}}
        <div class="step-panel is-active" id="panel-1" role="group" aria-labelledby="sh-1">
          <div class="step-header">
            <p class="step-eyebrow">Step 1 of 4</p>
            <h2 class="step-title" id="sh-1">Create your account</h2>
            <p class="step-subtitle">Start your emotional wellness journey.</p>
          </div>

          <div class="field @error('email') has-error @enderror">
            <input type="email" id="reg-email" name="email" placeholder="Email"
              value="{{ old('email') }}" autocomplete="email" required/>
            <label for="reg-email">Email address</label>
            @error('email') <span class="field-error">{{ $message }}</span> @enderror
          </div>

          <div class="field-row">
            <div class="field @error('password') has-error @enderror">
              <input type="password" id="reg-pw" name="password" placeholder="Password"
                autocomplete="new-password" required/>
              <label for="reg-pw">Password</label>
              @error('password') <span class="field-error">{{ $message }}</span> @enderror
            </div>
            <div class="field">
              <input type="password" id="reg-pw2" name="password_confirmation"
                placeholder="Confirm" autocomplete="new-password" required/>
              <label for="reg-pw2">Confirm password</label>
            </div>
          </div>

          <div class="pw-strength" aria-label="Password strength">
            <div class="pw-bar" id="pb1"></div>
            <div class="pw-bar" id="pb2"></div>
            <div class="pw-bar" id="pb3"></div>
            <div class="pw-bar" id="pb4"></div>
          </div>
          <p class="pw-hint">Min. 8 characters · uppercase · lowercase · number</p>

          <div class="btn-row">
            <div></div>
            <button type="button" class="btn-primary" style="width:auto;padding:.9rem 2rem;" onclick="goStep(2)">
              Continue →
            </button>
          </div>

          <p class="login-link">Already have an account? <a href="{{ route('login') }}">Sign in</a></p>
        </div>

        {{-- ── STEP 2 — Profile ── --}}
        <div class="step-panel" id="panel-2" role="group" aria-labelledby="sh-2">
          <div class="step-header">
            <p class="step-eyebrow">Step 2 of 4</p>
            <h2 class="step-title" id="sh-2">Build your profile</h2>
            <p class="step-subtitle">Personalise your MoodTrace experience.</p>
          </div>

          <div class="field">
            <input type="text" id="reg-name" name="name" placeholder="Name"
              value="{{ old('name') }}" autocomplete="name" required/>
            <label for="reg-name">Full name</label>
          </div>

          <p class="avatar-hint">Choose an avatar</p>
          <div class="avatar-row" id="avatarRow" role="radiogroup" aria-label="Avatar selection">
            @foreach(['🌙','🌸','☀️','🌊','🦋','🌿','⭐','🔮'] as $i => $emoji)
              <button type="button"
                class="avatar-opt {{ $i === 0 ? 'is-selected' : '' }}"
                data-avatar="{{ $emoji }}"
                aria-label="{{ $emoji }}"
                onclick="selectAvatar(this)">{{ $emoji }}</button>
            @endforeach
          </div>
          {{-- Hidden field carries the chosen avatar emoji --}}
          <input type="hidden" name="avatar" id="avatarInput" value="🌙"/>

          <div class="btn-row">
            <button type="button" class="btn-ghost" onclick="goStep(1)">← Back</button>
            <button type="button" class="btn-primary" style="width:auto;padding:.9rem 2rem;" onclick="goStep(3)">
              Continue →
            </button>
          </div>
        </div>

        {{-- ── STEP 3 — Mood Baseline ── --}}
        <div class="step-panel" id="panel-3" role="group" aria-labelledby="sh-3">
          <div class="step-header">
            <p class="step-eyebrow">Step 3 of 4</p>
            <h2 class="step-title" id="sh-3">How are you feeling?</h2>
            <p class="step-subtitle">Set your mood baseline for today.</p>
          </div>

          <p class="feeling-hint">Pick your current feeling</p>
          <div class="feeling-grid" role="radiogroup" aria-label="Current feeling">
            @foreach([
              ['😊','Happy'],['😌','Calm'],['😔','Sad'],['😰','Anxious'],
              ['🤩','Excited'],['😴','Tired'],['🙏','Grateful'],['😤','Stressed'],
            ] as [$emoji, $label])
              <button type="button" class="feeling-btn" onclick="selectFeeling(this)">
                <span class="emoji">{{ $emoji }}</span>{{ $label }}
              </button>
            @endforeach
          </div>
          <input type="hidden" name="baseline_feeling" id="feelingInput" value=""/>

          <p class="feeling-hint">Mood level (1–10)</p>
          <div class="mood-slider-row">
            <input type="range" id="moodSlider" name="baseline_mood" min="1" max="10" value="7"
              oninput="document.getElementById('moodVal').textContent = this.value"/>
            <span class="mood-val" id="moodVal">7</span>
          </div>

          <div class="btn-row">
            <button type="button" class="btn-ghost" onclick="goStep(2)">← Back</button>
            <button type="button" class="btn-primary" style="width:auto;padding:.9rem 2rem;" onclick="goStep(4)">
              Continue →
            </button>
          </div>
        </div>

        {{-- ── STEP 4 — Preferences ── --}}
        <div class="step-panel" id="panel-4" role="group" aria-labelledby="sh-4">
          <div class="step-header">
            <p class="step-eyebrow">Step 4 of 4</p>
            <h2 class="step-title" id="sh-4">Your preferences</h2>
            <p class="step-subtitle">Set up reminders to stay consistent.</p>
          </div>

          <div class="toggle-row">
            <div>
              <div class="toggle-label">Daily reminder</div>
              <div class="toggle-sub">Get nudged to log your mood each day</div>
            </div>
            <input type="checkbox" class="toggle" id="reminderToggle" name="reminder_enabled"
              value="1" checked onchange="toggleReminderTime()"/>
          </div>

          <div class="field" id="reminderTimeField">
            <input type="time" id="reminderTime" name="reminder_time" value="20:00"
              style="padding:1rem 1.2rem;"/>
            <label for="reminderTime">Reminder time</label>
          </div>

          <div class="field">
            <select id="timezone" name="timezone">
              @foreach([
                'UTC'                 => 'UTC',
                'Europe/Paris'        => 'Europe/Paris (GMT+1)',
                'America/New_York'    => 'America/New York (GMT-5)',
                'America/Los_Angeles' => 'America/Los Angeles (GMT-8)',
                'Asia/Tokyo'          => 'Asia/Tokyo (GMT+9)',
                'Asia/Dubai'          => 'Asia/Dubai (GMT+4)',
                'Australia/Sydney'    => 'Australia/Sydney (GMT+11)',
              ] as $value => $label)
                <option value="{{ $value }}" {{ old('timezone', 'Europe/Paris') === $value ? 'selected' : '' }}>
                  {{ $label }}
                </option>
              @endforeach
            </select>
            <label for="timezone">Timezone</label>
          </div>

          <div class="btn-row">
            <button type="button" class="btn-ghost" onclick="goStep(3)">← Back</button>
            <button type="submit" class="btn-primary" style="width:auto;padding:.9rem 2.2rem;" id="submitBtn">
              Create Account ✦
            </button>
          </div>
        </div>

      </form>{{-- /registerForm --}}

      {{-- ── STEP 5 — Success ── --}}
      <div class="step-panel" id="panel-5" role="status" aria-live="polite">
        <div class="success-wrap">
          <div class="success-icon" aria-hidden="true">✓</div>
          <h2 class="success-title">You're all set!</h2>
          <p class="success-sub">
            Welcome to MoodTrace. Your account has been created and is
            awaiting admin approval. You will be notified once approved.
          </p>
          <a href="{{ route('login') }}" class="btn-primary" style="max-width:260px;display:block;text-align:center;text-decoration:none;">
            Go to Sign In →
          </a>
        </div>
      </div>

    </div>{{-- /reg-main --}}
  </div>{{-- /register-card --}}
</div>{{-- /register-page --}}

@endsection

@push('scripts')
  <script src="{{ asset('js/auth/register.js') }}" defer></script>
@endpush