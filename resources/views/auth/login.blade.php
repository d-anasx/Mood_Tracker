@extends('layouts.app')

@section('title', 'Sign In')
@section('hideNav', true)
{{-- Override orbs for the login page colour scheme --}}
@section('orbs')
  <div class="orb orb-purple"></div>
  <div class="orb orb-rose"></div>
  <div class="orb orb-amber"></div>
@endsection

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/auth/login.css') }}" />
@endpush

@section('content')

  <div class="login-layout">

    {{-- Vertical separator --}}
    <div class="vline" aria-hidden="true"></div>

    {{-- ══════════════════════════════════════
    LEFT PANEL — Hero + mood bars preview
    ══════════════════════════════════════ --}}
    <div class="login-left">

      <div class="mood-pill">
        <span class="dot"></span>
        Your daily companion
      </div>

      <h1 class="hero-title">
        Track how<br />
        you <em>truly</em><br />
        feel — daily.
      </h1>

      <p class="hero-sub">
        MoodTrace helps you understand your emotional patterns,
        sleep, and well-being over time — with beautiful insights
        built just for you.
      </p>

      {{-- Live mood bars --}}
      <div class="mood-bars" aria-hidden="true">
        <div class="mood-bar-row">
          <span class="bar-label">Mon</span>
          <div class="bar-track">
            <div class="bar-fill" data-target="82" style="background:linear-gradient(90deg,#c0587a,#f4a261);"></div>
          </div>
          <span class="bar-val">8.2</span>
        </div>
        <div class="mood-bar-row">
          <span class="bar-label">Tue</span>
          <div class="bar-track">
            <div class="bar-fill" data-target="65" style="background:linear-gradient(90deg,#7c3fa0,#c0587a);"></div>
          </div>
          <span class="bar-val">6.5</span>
        </div>
        <div class="mood-bar-row">
          <span class="bar-label">Wed</span>
          <div class="bar-track">
            <div class="bar-fill" data-target="79" style="background:linear-gradient(90deg,#3f7ca0,#7c3fa0);"></div>
          </div>
          <span class="bar-val">7.9</span>
        </div>
        <div class="mood-bar-row">
          <span class="bar-label">Thu</span>
          <div class="bar-track">
            <div class="bar-fill" data-target="91" style="background:linear-gradient(90deg,#c0587a,#f4a261);"></div>
          </div>
          <span class="bar-val">9.1</span>
        </div>
      </div>

    </div>{{-- /login-left --}}

    {{-- ══════════════════════════════════════
    RIGHT PANEL — Glass card form
    ══════════════════════════════════════ --}}
    <div class="login-right">
      <div class="glass-card login-card">

        <p class="card-eyebrow">Welcome back</p>
        <h2 class="card-title">Sign in</h2>
        <p class="card-sub">Continue your emotional journey.</p>

        {{-- Emotion chips --}}
        <div class="emotion-row" aria-label="Pick your current mood">
          <button class="emotion-chip" type="button">😊 Happy</button>
          <button class="emotion-chip" type="button">😌 Calm</button>
          <button class="emotion-chip" type="button">🌟 Hopeful</button>
        </div>

        {{-- ── Login Form ── --}}
        <form action="{{ route('login') }}" method="POST" id="loginForm" novalidate>
          @csrf

          {{-- Email --}}
          <div class="field @error('email') has-error @enderror">
            <input type="email" id="email" name="email" placeholder="Email" value="{{ old('email') }}"
              autocomplete="email" required />
            <label for="email">Email address</label>
            <span class="field-icon" aria-hidden="true">✉</span>
            @error('email')
              <span class="field-error">{{ $message }}</span>
            @enderror
          </div>

          {{-- Password --}}
          <div class="field @error('password') has-error @enderror">
            <input type="password" id="password" name="password" placeholder="Password" autocomplete="current-password"
              required />
            <label for="password">Password</label>
            <span class="field-icon" id="togglePw" title="Show / hide password"
              aria-label="Toggle password visibility">👁</span>
            @error('password')
              <span class="field-error">{{ $message }}</span>
            @enderror
          </div>

          {{-- Remember me + Forgot password --}}


          <button type="submit" class="btn-primary" id="loginBtn">
            Sign in to MoodTrace
          </button>

        </form>

        <div class="divider-text">or continue with</div>

        <a href="{{ route('google.login') }}" class="btn-google"
          style="display: flex; align-items: center; justify-content: center; gap: 12px; width: 100%; padding: 0.85rem; border-radius: var(--radius-btn); background: rgba(255, 255, 255, 0.04); border: 1px solid rgba(255, 255, 255, 0.09); color: rgba(240, 230, 238, 0.8); font-family: 'DM Sans', sans-serif; font-size: 0.88rem; text-decoration: none; transition: all 0.2s ease;">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path
              d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
              fill="#4285F4" />
            <path
              d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
              fill="#34A853" />
            <path
              d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"
              fill="#FBBC05" />
            <path
              d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
              fill="#EA4335" />
          </svg>
          Sign in with Google
        </a>

        <p class="register-link">
          New to MoodTrace? <a href="{{ route('register') }}">Create your account →</a>
        </p>

      </div>{{-- /login-card --}}
    </div>{{-- /login-right --}}

  </div>{{-- /login-layout --}}

@endsection

@push('scripts')
  <script src="{{ asset('js/auth/login.js') }}" defer></script>
@endpush