<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="csrf-token" content="{{ csrf_token() }}"/>

  <title>@yield('title', 'MoodTrace') — MoodTrace</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}"/>
  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet"/>

  <!-- DaisyUI v5 -->
  <link href="https://cdn.jsdelivr.net/npm/daisyui@5/themes.css" rel="stylesheet"/>

  <!-- Tailwind -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            display: ['"Playfair Display"', 'serif'],
            body:    ['"DM Sans"', 'sans-serif'],
          },
          colors: {
            ink:   '#0f0e17',
            petal: '#e8c4c4',
            bloom: '#d4a5b5',
            dusk:  '#7c6e8a',
            glow:  '#f4a261',
            mist:  '#a9b4c2',
            teal:  '#4ecdc4',
          },
          keyframes: {
            'float-up':   { '0%':{ opacity:'0', transform:'translateY(28px)' }, '100%':{ opacity:'1', transform:'translateY(0)' } },
            'orb-drift':  { '0%,100%':{ transform:'translate(0,0) scale(1)' }, '40%':{ transform:'translate(40px,-30px) scale(1.07)' }, '70%':{ transform:'translate(-20px,25px) scale(0.95)' } },
            'orb-drift2': { '0%,100%':{ transform:'translate(0,0) scale(1)' }, '33%':{ transform:'translate(-50px,40px) scale(1.1)' }, '66%':{ transform:'translate(30px,-20px) scale(0.92)' } },
            'pulse-glow': { '0%,100%':{ opacity:'0.5', transform:'scale(1)' }, '50%':{ opacity:'0.8', transform:'scale(1.05)' } },
            'spin-slow':  { from:{ transform:'rotate(0deg)' }, to:{ transform:'rotate(360deg)' } },
            shimmer:      { '0%':{ backgroundPosition:'-200% center' }, '100%':{ backgroundPosition:'200% center' } },
            'step-in':    { '0%':{ opacity:'0', transform:'translateX(40px)' }, '100%':{ opacity:'1', transform:'translateX(0)' } },
            'slide-left': { '0%':{ opacity:'0', transform:'translateX(-60px)' }, '100%':{ opacity:'1', transform:'translateX(0)' } },
            'bar-fill':   { '0%':{ width:'0%' }, '100%':{ width:'var(--target-w)' } },
            'check-pop':  { '0%':{ transform:'scale(0) rotate(-10deg)' }, '60%':{ transform:'scale(1.2) rotate(3deg)' }, '100%':{ transform:'scale(1) rotate(0)' } },
          },
          animation: {
            'float-up':   'float-up 0.7s cubic-bezier(.22,1,.36,1) forwards',
            'orb-drift':  'orb-drift 14s ease-in-out infinite',
            'orb-drift2': 'orb-drift2 18s ease-in-out infinite',
            'pulse-glow': 'pulse-glow 4s ease-in-out infinite',
            'spin-slow':  'spin-slow 30s linear infinite',
            shimmer:      'shimmer 3s linear infinite',
            'step-in':    'step-in 0.45s cubic-bezier(.22,1,.36,1) forwards',
            'slide-left': 'slide-left 0.9s cubic-bezier(.22,1,.36,1) forwards',
            'bar-fill':   'bar-fill 2.5s cubic-bezier(.22,1,.36,1) forwards',
            'check-pop':  'check-pop 0.4s cubic-bezier(.22,1,.36,1) forwards',
          },
        }
      }
    }
  </script>

  <!-- Global base CSS -->
  <link rel="stylesheet" href="{{ asset('css/moodtrace-base.css') }}"/>

  <!-- Page-specific CSS -->
  @stack('styles')
</head>
<body class="font-body bg-ink text-white">

  <!-- ═══════════════════════════════════════════
       SHARED ANIMATED BACKGROUND SCENE
       Every auth page shares the same deep-space
       background with drifting orbs + grain.
  ════════════════════════════════════════════ -->
  <div class="bg-scene" aria-hidden="true">

    {{-- Orbs — child views can override via @section('orbs') --}}
    @hasSection('orbs')
      @yield('orbs')
    @else
      <div class="orb orb-purple"></div>
      <div class="orb orb-rose"></div>
      <div class="orb orb-amber"></div>
    @endif

    {{-- Decorative rotating rings --}}
    <div class="ring ring-lg"></div>
    <div class="ring ring-sm"></div>

    {{-- Grain noise --}}
    <div class="grain" aria-hidden="true"></div>
  </div>

  <!-- ═══════════════════════════════════════════
       PAGE CONTENT
  ════════════════════════════════════════════ -->
  <main class="relative z-10">
    @yield('content')
  </main>

  <!-- ═══════════════════════════════════════════
       GLOBAL SCRIPTS
  ════════════════════════════════════════════ -->
  <script src="{{ asset('js/moodtrace-utils.js') }}"></script>

  @stack('scripts')
</body>
</html>