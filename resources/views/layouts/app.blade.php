@if (session('success'))
  <div
    class="flash fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg animate-float-up z-50 flex items-start gap-3">
    <div class="min-w-0">
      {{ session('success') }}
    </div>
    <button type="button" aria-label="Close" class="text-white/90 hover:text-white ml-2 text-lg leading-none"
      onclick="this.closest('.flash').remove()">
      &times;
    </button>
  </div>
@endif

@if (session('error'))
  <div
    class="flash fixed top-4 right-4 bg-red-500 text-white px-4 py-2 rounded-lg shadow-lg animate-float-up z-50 flex items-start gap-3">
    <div class="min-w-0">
      {{ session('error') }}
    </div>
    <button type="button" aria-label="Close" class="text-white/90 hover:text-white ml-2 text-lg leading-none"
      onclick="this.closest('.flash').remove()">
      &times;
    </button>
  </div>
@endif

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />

  <title>@yield('title', 'MoodTrace') — MoodTrace</title>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap"
    rel="stylesheet" />

  <!-- DaisyUI v5 -->
  <link href="https://cdn.jsdelivr.net/npm/daisyui@5/themes.css" rel="stylesheet" />

  <!-- Tailwind -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            display: ['"Playfair Display"', 'serif'],
            body: ['"DM Sans"', 'sans-serif'],
          },
          colors: {
            ink: '#0f0e17',
            petal: '#e8c4c4',
            bloom: '#d4a5b5',
            dusk: '#7c6e8a',
            glow: '#f4a261',
            mist: '#a9b4c2',
            teal: '#4ecdc4',
          },
          keyframes: {
            'float-up': {
              '0%': {
                opacity: '0',
                transform: 'translateY(28px)'
              },
              '100%': {
                opacity: '1',
                transform: 'translateY(0)'
              }
            },
            'orb-drift': {
              '0%,100%': {
                transform: 'translate(0,0) scale(1)'
              },
              '40%': {
                transform: 'translate(40px,-30px) scale(1.07)'
              },
              '70%': {
                transform: 'translate(-20px,25px) scale(0.95)'
              }
            },
            'orb-drift2': {
              '0%,100%': {
                transform: 'translate(0,0) scale(1)'
              },
              '33%': {
                transform: 'translate(-50px,40px) scale(1.1)'
              },
              '66%': {
                transform: 'translate(30px,-20px) scale(0.92)'
              }
            },
            'pulse-glow': {
              '0%,100%': {
                opacity: '0.5',
                transform: 'scale(1)'
              },
              '50%': {
                opacity: '0.8',
                transform: 'scale(1.05)'
              }
            },
            'spin-slow': {
              from: {
                transform: 'rotate(0deg)'
              },
              to: {
                transform: 'rotate(360deg)'
              }
            },
            shimmer: {
              '0%': {
                backgroundPosition: '-200% center'
              },
              '100%': {
                backgroundPosition: '200% center'
              }
            },
            'step-in': {
              '0%': {
                opacity: '0',
                transform: 'translateX(40px)'
              },
              '100%': {
                opacity: '1',
                transform: 'translateX(0)'
              }
            },
            'slide-left': {
              '0%': {
                opacity: '0',
                transform: 'translateX(-60px)'
              },
              '100%': {
                opacity: '1',
                transform: 'translateX(0)'
              }
            },
            'bar-fill': {
              '0%': {
                width: '0%'
              },
              '100%': {
                width: 'var(--target-w)'
              }
            },
            'check-pop': {
              '0%': {
                transform: 'scale(0) rotate(-10deg)'
              },
              '60%': {
                transform: 'scale(1.2) rotate(3deg)'
              },
              '100%': {
                transform: 'scale(1) rotate(0)'
              }
            },
          },
          animation: {
            'float-up': 'float-up 0.7s cubic-bezier(.22,1,.36,1) forwards',
            'orb-drift': 'orb-drift 14s ease-in-out infinite',
            'orb-drift2': 'orb-drift2 18s ease-in-out infinite',
            'pulse-glow': 'pulse-glow 4s ease-in-out infinite',
            'spin-slow': 'spin-slow 30s linear infinite',
            shimmer: 'shimmer 3s linear infinite',
            'step-in': 'step-in 0.45s cubic-bezier(.22,1,.36,1) forwards',
            'slide-left': 'slide-left 0.9s cubic-bezier(.22,1,.36,1) forwards',
            'bar-fill': 'bar-fill 2.5s cubic-bezier(.22,1,.36,1) forwards',
            'check-pop': 'check-pop 0.4s cubic-bezier(.22,1,.36,1) forwards',
          },
        }
      }
    }
  </script>

  @if (
      auth()->check() &&
      auth()->user()->status === 'active'
    )
    <script> window.chtlConfig = { chatbotId: "1838587568" } </script>
    <script async data-id="1838587568" id="chtl-script" type="text/javascript"
      src="https://chatling.ai/js/embed.js"></script>

  @endif

  <!-- Global base CSS -->
  <link rel="stylesheet" href="{{ asset('css/moodtrace-base.css') }}" />

  <!-- Page-specific CSS -->
  @stack('styles')
</head>

<body class="font-body bg-ink text-white">

  <!-- ═══════════════════════════════════════════
       BACKGROUND SCENE
  ════════════════════════════════════════════ -->
  <div class="bg-scene" aria-hidden="true">
    @hasSection('orbs')
      @yield('orbs')
    @else
      <div class="orb orb-purple"></div>
      <div class="orb orb-rose"></div>
      <div class="orb orb-amber"></div>
    @endif
    <div class="ring ring-lg"></div>
    <div class="ring ring-sm"></div>
    <div class="grain" aria-hidden="true"></div>
  </div>


  @unless (View::hasSection('hideNav'))
    @auth
      <nav class="app-nav" aria-label="Main navigation">

        {{-- Left: logo + page links --}}
        <div class="app-nav-left">
          <a href="{{ route('dashboard') }}" class="nav-logo" aria-label="MoodTrace home">
            <img class="w-16 animate-pulse" src="{{ asset('assets/Mood_Tracker.png') }}" alt="">

            <span class="nav-logo-text">MoodTrace</span>
          </a>

          <div class="nav-links" role="navigation">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
              Dashboard
            </a>
            <a href="{{ route('analytics') }}" class="nav-link {{ request()->routeIs('analytics') ? 'active' : '' }}">
              Analytics
            </a>
            <a href="{{ route('mood.create') }}" class="nav-link {{ request()->routeIs('mood.*') ? 'active' : '' }}">
              Log Mood
            </a>
            @if (auth()->user()->isAdmin())
              <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}">
                Admin
              </a>
            @endif
          </div>
        </div>

        {{-- Right: notifications + user dropdown --}}
        <div class="app-nav-right flex items-center gap-4">

          {{-- Notification Bell Wrapper --}}
          <div class="relative" id="notifWrap">
            @php
              $notifications = auth()->user()->notifications()->latest()->take(5)->get();
              $unreadCount = auth()->user()->notifications()->where('is_read', false)->count();
            @endphp

            <button id="notifBtn" class="nav-notif relative p-2 transition-transform hover:scale-110"
              aria-label="{{ $unreadCount }} unread notifications">
              <span class="notif-icon text-xl">🔔</span>
              @if ($unreadCount > 0)
                <span
                  class="absolute top-1 right-1 bg-bloom text-ink text-[7px] font-bold p-0.5 rounded-full min-w-[18px] text-center border border-ink">
                  {{ $unreadCount }}
                </span>
              @endif
            </button>

            {{-- Mini Model (Dropdown) --}}
            <div id="notifDropdown"
              class="hidden absolute right-0 mt-3 w-80 bg-ink/95 border border-white/10 backdrop-blur-xl rounded-2xl shadow-2xl z-50 overflow-hidden animate-float-up">
              <div class="p-4 border-b border-white/5 flex justify-between items-center">
                <h3 class="font-display text-petal font-bold">Notifications</h3>
                <span class="text-[10px] uppercase tracking-widest text-mist">{{ $unreadCount }} New</span>
              </div>

              <div class="max-h-[350px] overflow-y-auto custom-scrollbar">
                @forelse($notifications as $notif)
                  <div
                    class="p-4 border-b border-white/5 hover:bg-white/5 transition-colors cursor-pointer group {{ !$notif->is_read ? 'bg-bloom/5' : '' }}">
                    <div class="flex justify-between items-start gap-2">
                      <span class="font-medium text-sm text-white group-hover:text-petal transition-colors">
                        {{ $notif->title ?? 'Notification' }}
                      </span>
                      <span class="text-[10px] text-mist whitespace-nowrap">{{ $notif->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-xs text-mist mt-1 leading-relaxed line-clamp-2">
                      {{ $notif->message }}
                    </p>
                  </div>
                @empty
                  <div class="p-8 text-center text-mist text-sm italic">
                    No notifications yet
                  </div>
                @endforelse
              </div>

              <a href=""
                class="block p-3 text-center text-xs font-bold text-petal hover:bg-white/5 transition-colors border-t border-white/5">
                View All Activity
              </a>
            </div>
          </div>

          {{-- User avatar dropdown --}}
          <div class="relative" id="navUserWrap">
            <button id="navUserBtn"
              class="nav-user-btn flex items-center gap-2 p-2 rounded-full transition-transform hover:scale-110"
              aria-haspopup="true" aria-expanded="false">
              <span>
                {{ auth()->user()->avatar }}
              </span>
              <span class="user-name hidden sm:block">{{ auth()->user()->name }}</span>
              <svg class="nav-chevron w-3 h-3 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
              </svg>
            </button>

            {{-- User Dropdown - Only shows Logout --}}
            <div id="navDropdown"
              class="nav-dropdown hidden absolute right-0 mt-3 w-56 bg-ink/95 border border-white/10 backdrop-blur-xl rounded-2xl shadow-2xl z-50 py-2"
              aria-hidden="true">

              {{-- User info header --}}
              <div class="px-4 py-3 border-b border-white/10">
                <p class="text-sm font-medium text-white">{{ auth()->user()->name }}</p>
                <p class="text-xs text-mist mt-1">{{ auth()->user()->email }}</p>
              </div>

              {{-- Divider --}}
              <div class="border-t border-white/10 my-1"></div>

              {{-- Logout only --}}
              <form method="POST" action="{{ route('logout') }}" class="block">
                @csrf
                <button type="submit"
                  class="w-full text-left px-4 py-2.5 text-sm text-red-400 hover:bg-white/5 transition-colors flex items-center gap-2">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                  </svg>
                  Logout
                </button>
              </form>
            </div>
          </div>
      </nav>
    @endauth
  @endunless

  <!-- ═══════════════════════════════════════════
       PAGE CONTENT
  ════════════════════════════════════════════ -->
  <main class="relative z-10">
    @yield('content')
  </main>

  <!-- ═══════════════════════════════════════════
       GLOBAL SCRIPTS
  ════════════════════════════════════════════ -->
  <script>
    document.addEventListener('DOMContentLoaded', function () {

      // --- User Dropdown Elements ---
      const userBtn = document.getElementById('navUserBtn');
      const userDropdown = document.getElementById('navDropdown');

      // --- Notification Dropdown Elements ---
      const notifBtn = document.getElementById('notifBtn');
      const notifDropdown = document.getElementById('notifDropdown');

      // Helper to close all dropdowns
      function closeAllDropdowns() {
        if (userDropdown) {
          userDropdown.classList.remove('open');
          userDropdown.classList.add('hidden');
          if (userBtn) userBtn.setAttribute('aria-expanded', 'false');
        }
        if (notifDropdown) {
          notifDropdown.classList.add('hidden');
        }
      }

      // Helper to toggle user dropdown
      function toggleUserDropdown(e) {
        if (e) e.stopPropagation();

        // Close notification dropdown first
        if (notifDropdown && !notifDropdown.classList.contains('hidden')) {
          notifDropdown.classList.add('hidden');
        }

        if (userDropdown) {
          const isHidden = userDropdown.classList.contains('hidden');

          // Reset state
          userDropdown.classList.remove('open');

          if (isHidden) {
            // Show dropdown
            userDropdown.classList.remove('hidden');
            userDropdown.classList.add('open');
            if (userBtn) userBtn.setAttribute('aria-expanded', 'true');
          } else {
            // Hide dropdown
            userDropdown.classList.add('hidden');
            if (userBtn) userBtn.setAttribute('aria-expanded', 'false');
          }
        }
      }

      // Toggle notification dropdown
      function toggleNotifDropdown(e) {
        if (e) e.stopPropagation();

        // Close user dropdown first
        if (userDropdown && !userDropdown.classList.contains('hidden')) {
          userDropdown.classList.add('hidden');
          userDropdown.classList.remove('open');
          if (userBtn) userBtn.setAttribute('aria-expanded', 'false');
        }

        if (notifDropdown) {
          notifDropdown.classList.toggle('hidden');
        }
      }

      // Attach event listeners
      if (userBtn) {
        userBtn.addEventListener('click', toggleUserDropdown);
      }

      if (notifBtn) {
        notifBtn.addEventListener('click', toggleNotifDropdown);
      }

      // Close dropdowns when clicking outside
      document.addEventListener('click', function (e) {
        // Close user dropdown if clicking outside
        if (userDropdown && userBtn && !userBtn.contains(e.target) && !userDropdown.contains(e.target)) {
          userDropdown.classList.add('hidden');
          userDropdown.classList.remove('open');
          if (userBtn) userBtn.setAttribute('aria-expanded', 'false');
        }

        // Close notification dropdown if clicking outside
        if (notifDropdown && notifBtn && !notifBtn.contains(e.target) && !notifDropdown.contains(e.target)) {
          notifDropdown.classList.add('hidden');
        }
      });

      // Prevent dropdown from closing when clicking inside
      if (userDropdown) {
        userDropdown.addEventListener('click', function (e) {
          e.stopPropagation();
        });
      }

      if (notifDropdown) {
        notifDropdown.addEventListener('click', function (e) {
          e.stopPropagation();
        });
      }

    });
  </script>

  @stack('scripts')
</body>

</html>