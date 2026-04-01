@extends('layouts.app')

@section('title', 'Dashboard')

@section('orbs')
    <div class="orb orb-purple"></div>
    <div class="orb orb-rose"></div>
    <div class="orb orb-amber" style="opacity:0.4;"></div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}" />
@endpush

@section('content')

    <div class="dashboard-page">

        {{-- ══════════════════════════════════════
       TOP NAV — user info + logout
  ══════════════════════════════════════ --}}
        <nav class="dash-nav">
            <div class="dash-nav-left">
                <div class="nav-logo flex justify-center">
                    <img class="w-16 animate-pulse" src="{{ asset('assets/Mood_Tracker.png') }}" alt="">
                    <span class="nav-logo-text">MoodTrace</span>
                </div>
            </div>

            <div class="dash-nav-right">
                <div class="nav-user">
                    <span class="nav-avatar">{{ $user->avatar }}</span>
                    <div class="nav-user-info">
                        <div class="nav-user-name">{{ $user->name }}</div>
                        <div class="nav-user-role">{{ $user->role->name }}</div>
                    </div>
                </div>

                @if ($unreadCount > 0)
                    <a href="#" class="nav-notif" aria-label="Notifications">
                        <span class="notif-icon">🔔</span>
                        <span class="notif-badge">{{ $unreadCount }}</span>
                    </a>
                @endif

                <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="nav-logout">Logout</button>
                </form>
            </div>
        </nav>

        <div class="dash-container">

            {{-- ══════════════════════════════════════
         HEADER — Welcome + Today's Check-in CTA
    ══════════════════════════════════════ --}}
            <header class="dash-header">
                <div class="dash-welcome">
                    <h1 class="dash-title">
                        Welcome back, <span class="name-highlight">{{ explode(' ', $user->name)[0] }}</span>
                    </h1>
                    <p class="dash-subtitle">
                        {{ now()->format('l, F j, Y') }}
                    </p>
                </div>

                @if (!$todayEntry)
                    <a href="{{ route('mood.create') }}" class="btn-checkin">
                        <span class="btn-icon">✨</span>
                        Log Today's Mood
                    </a>
                @else
                    <div class="today-badge">
                        <span class="badge-icon">✓</span>
                        Logged today
                    </div>
                @endif
            </header>

            {{-- ══════════════════════════════════════
         MAIN GRID — Cards layout
    ══════════════════════════════════════ --}}
            <div class="dash-grid">

                {{-- ── TODAY'S ENTRY CARD ── --}}
                @if ($todayEntry)
                    <div class="glass-card card-today p-5">
                        <div class="card-header">
                            <h2 class="card-title">Today's Check-in</h2>
                            <a href="{{ route('mood.edit', $todayEntry->id) }}" class="card-action">Edit</a>
                        </div>

                        <div class="today-mood">
                            <div class="mood-level-display">
                                <div class="mood-number">{{ $todayEntry->mood_level }}</div>
                                <div class="mood-scale">/10</div>
                            </div>
                            <div class="mood-meta">
                                <div class="mood-time">{{ $todayEntry->created_at->format('g:i A') }}</div>
                                @if ($todayEntry->sleep_hours)
                                    <div class="mood-sleep">💤 {{ $todayEntry->sleep_hours }}h sleep</div>
                                @endif
                            </div>
                        </div>

                        @if ($todayEntry->feelings->count() > 0)
                            <div class="feelings-row">
                                @foreach ($todayEntry->feelings as $feeling)
                                    <span class="feeling-chip"
                                        style="background: {{ $feeling->color }}20; color: {{ $feeling->color }};">
                                        {{ $feeling->icon }} {{ $feeling->name }}
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        @if ($todayEntry->reflection)
                            <div class="reflection-box">
                                <div class="reflection-label">Your reflection</div>
                                <p class="reflection-text">{{ $todayEntry->reflection }}</p>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="glass-card card-empty flex flex-col justify-center items-center">
                        <div class="empty-icon">📝</div>
                        <h3 class="empty-title">No entry yet today</h3>
                        <p class="empty-text">Take a moment to check in with yourself.</p>
                        <a href="{{ route('mood.create') }}" class="btn-primary w-1/6 ">
                            Log Your Mood
                        </a>
                    </div>
                @endif

                {{-- ── MOOD CHART CARD ── --}}
                <div class="glass-card card-chart p-5">
                    <div class="card-header">
                        <h2 class="card-title">Your Journey</h2>
                        <div class="card-legend">
                            <span class="legend-item">
                                <span class="legend-dot" style="background: var(--accent-purple);"></span>
                                Mood Level
                            </span>
                        </div>
                    </div>

                    @if ($recentEntries->count() > 0)
                        <div class="chart-container">
                            <canvas id="moodChart"></canvas>
                        </div>
                    @else
                        <div class="chart-empty">
                            <p>Start logging your mood to see your journey over time.</p>
                        </div>
                    @endif
                </div>

                {{-- ── TREND COMPARISON CARD ── --}}
                @if ($trendData)
                    <div class="glass-card card-trend p-5">
                        <div class="card-header">
                            <h2 class="card-title">Your Trends</h2>
                            <div class="card-subtitle">Last 5 vs Previous 5 entries</div>
                        </div>

                        <div class="trend-grid">
                            <div class="trend-item">
                                <div class="trend-label">Mood Average</div>
                                <div class="trend-value">
                                    {{ $trendData['mood']['recent'] }}
                                    @if ($trendData['mood']['trend'] === 'up')
                                        <span class="trend-arrow trend-up">↑</span>
                                    @elseif($trendData['mood']['trend'] === 'down')
                                        <span class="trend-arrow trend-down">↓</span>
                                    @else
                                        <span class="trend-arrow trend-stable">→</span>
                                    @endif
                                </div>
                                <div class="trend-change {{ $trendData['mood']['trend'] }}">
                                    {{ $trendData['mood']['change'] > 0 ? '+' : '' }}{{ $trendData['mood']['change'] }}
                                    from previous
                                </div>
                            </div>

                            <div class="trend-item">
                                <div class="trend-label">Sleep Average</div>
                                <div class="trend-value">
                                    {{ $trendData['sleep']['recent'] }}h
                                    @if ($trendData['sleep']['trend'] === 'up')
                                        <span class="trend-arrow trend-up">↑</span>
                                    @elseif($trendData['sleep']['trend'] === 'down')
                                        <span class="trend-arrow trend-down">↓</span>
                                    @else
                                        <span class="trend-arrow trend-stable">→</span>
                                    @endif
                                </div>
                                <div class="trend-change {{ $trendData['sleep']['trend'] }}">
                                    {{ $trendData['sleep']['change'] > 0 ? '+' : '' }}{{ $trendData['sleep']['change'] }}h
                                    from previous
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- ── MOTIVATIONAL QUOTE CARD ── --}}
                @if ($quote)
                    <div class="glass-card card-quote" id="quoteCard">

                        <div class="quote-header">
                            <div class="quote-icon">💭</div>
                            @if ($quoteSource === 'ai')
                                <span class="quote-badge ai-badge">✨ AI Generated</span>
                            @else
                                <span class="quote-badge db-badge">📚 Curated</span>
                            @endif
                        </div>

                        <blockquote class="quote-text" id="quoteText">
                            "{{ $quote->text }}"
                        </blockquote>

                        <div class="quote-footer">
                            @if ($quote->author)
                                <cite class="quote-author" id="quoteAuthor">— {{ $quote->author }}</cite>
                            @endif

                            {{-- Regenerate button — calls Gemini via AJAX --}}
                            @if ($todayEntry)
                                <button class="quote-refresh" id="refreshQuote" title="Generate new quote"
                                    data-mood="{{ $todayEntry->mood_level }}"
                                    data-feelings="{{ $todayEntry->feelings->pluck('name')->join(',') }}"
                                    data-reflection="{{ $todayEntry->reflection }}">
                                    <span id="refreshIcon">↻</span>
                                </button>
                            @endif
                        </div>

                    </div>
                @endif

                {{-- ── QUICK STATS CARD ── --}}
                <div class="glass-card card-stats p-6">
                    <div class="card-header">
                        <h2 class="card-title">Quick Stats</h2>
                    </div>

                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="stat-number">{{ $user->moodEntries()->count() }}</div>
                            <div class="stat-label">Total Entries</div>
                        </div>

                        <div class="stat-item">
                            <div class="stat-number">
                                {{ $user->moodEntries()->where('entry_date', '>=', now()->subDays(7))->count() }}</div>
                            <div class="stat-label">This Week</div>
                        </div>

                        <div class="stat-item">
                            <div class="stat-number">
                                {{ $user->moodEntries()->count() > 0 ? round($user->moodEntries()->avg('mood_level'), 1) : '—' }}
                            </div>
                            <div class="stat-label">Avg Mood</div>
                        </div>

                        <div class="stat-item">
                            <div class="stat-number">
                                {{ $user->moodEntries()->whereNotNull('sleep_hours')->count() > 0 ? round($user->moodEntries()->avg('sleep_hours'), 1) . 'h' : '—' }}
                            </div>
                            <div class="stat-label">Avg Sleep</div>
                        </div>
                    </div>
                </div>

            </div>{{-- /dash-grid --}}

        </div>{{-- /dash-container --}}

    </div>{{-- /dashboard-page --}}

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        @if ($recentEntries->count() > 0)
            document.addEventListener('DOMContentLoaded', () => {
                const ctx = document.getElementById('moodChart');

                const data = {
                    labels: {!! $recentEntries->pluck('entry_date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('M j')) !!},
                    datasets: [{
                        label: 'Mood Level',
                        data: {!! $recentEntries->pluck('mood_level') !!},
                        borderColor: 'rgb(167, 139, 250)',
                        backgroundColor: 'rgba(167, 139, 250, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 6,
                        pointHoverRadius: 8,
                        pointBackgroundColor: 'rgb(167, 139, 250)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                    }]
                };

                new Chart(ctx, {
                    type: 'line',
                    data: data,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                titleFont: {
                                    size: 14,
                                    weight: '600'
                                },
                                bodyFont: {
                                    size: 13
                                },
                                cornerRadius: 8,
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: false,
                                min: 1,
                                max: 10,
                                ticks: {
                                    stepSize: 1,
                                    color: 'rgba(255, 255, 255, 0.5)',
                                    font: {
                                        size: 12
                                    }
                                },
                                grid: {
                                    color: 'rgba(255, 255, 255, 0.05)',
                                }
                            },
                            x: {
                                ticks: {
                                    color: 'rgba(255, 255, 255, 0.5)',
                                    font: {
                                        size: 11
                                    }
                                },
                                grid: {
                                    display: false,
                                }
                            }
                        },
                        interaction: {
                            intersect: false,
                            mode: 'index',
                        }
                    }
                });
                //quote
                const btn = document.getElementById('refreshQuote');
                if (!btn) return;

                btn.addEventListener('click', async () => {
                    const icon = document.getElementById('refreshIcon');
                    const quoteText = document.getElementById('quoteText');
                    const author = document.getElementById('quoteAuthor');
                    const badge = document.querySelector('.quote-badge');

                    // Loading state
                    icon.style.animation = 'spin-slow 0.6s linear infinite';
                    btn.disabled = true;
                    quoteText.style.opacity = '0.4';

                    try {
                        const feelings = btn.dataset.feelings ?
                            btn.dataset.feelings.split(',').filter(Boolean) :
                            [];

                        const res = await fetch('{{ route('quote.generate') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').content,
                            },
                            body: JSON.stringify({
                                mood_level: parseInt(btn.dataset.mood),
                                feelings: feelings,
                                reflection: btn.dataset.reflection || null,
                            }),
                        });

                        if (!res.ok) throw new Error('Request failed');

                        const data = await res.json();
                        // Swap text with fade
                        quoteText.style.transition = 'opacity 0.3s';
                        quoteText.style.opacity = '0';

                        setTimeout(() => {
                            quoteText.textContent = `"${data.quote}"`;
                            if (author) author.textContent = `— ${data.author}`;

                            // Update badge
                            if (badge) {
                                badge.className =
                                    `quote-badge ${data.source === 'ai' ? 'ai-badge' : 'db-badge'}`;
                                badge.textContent = data.source === 'ai' ? '✨ AI Generated' :
                                    '📚 Curated';
                            }

                            quoteText.style.opacity = '1';
                        }, 300);

                    } catch (err) {
                        quoteText.style.opacity = '1';
                        console.error('Quote generation failed:', err);
                    } finally {
                        icon.style.animation = '';
                        btn.disabled = false;
                    }
                });
            });
        @endif
    </script>
@endpush
