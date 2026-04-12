@extends('layouts.app')

@section('title', 'Analytics & Insights')

@section('orbs')
  <div class="orb orb-purple"></div>
  <div class="orb orb-teal"  style="opacity:0.3;"></div>
  <div class="orb orb-amber" style="opacity:0.2;"></div>
@endsection

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/analytics.css') }}"/>
@endpush

@section('content')

<div class="analytics-container">

  {{-- ══ PAGE HEADER ══ --}}
  <header class="analytics-header">
    <div>
      <p class="eyebrow">Your data</p>
      <h1 class="display-title" style="font-size:2rem;">Analytics & Insights</h1>
      <p class="muted" style="margin-top:.3rem;font-size:.85rem;">Understand your patterns and grow.</p>
    </div>

    {{-- Period selector --}}
    <div class="period-selector">
      @foreach([30, 60, 90] as $p)
      <a href="{{ route('analytics', ['days' => $p]) }}"
         class="period-btn {{ $days == $p ? 'active' : '' }}">
        {{ $p }}d
      </a>
      @endforeach
    </div>
  </header>

  {{-- ══ SUMMARY STRIP ══ --}}
  <div class="stats-strip">

    <div class="stat-card glass-card" style="animation-delay:0ms">
      <div class="stat-icon">📅</div>
      <div class="stat-value">{{ $stats['total_entries'] }}</div>
      <div class="stat-label">Entries logged</div>
      <div class="stat-sub">{{ $stats['completion_pct'] }}% of {{ $stats['days_possible'] }} days</div>
    </div>

    <div class="stat-card glass-card" style="animation-delay:80ms">
      <div class="stat-icon">😊</div>
      <div class="stat-value">{{ $stats['avg_mood'] }}<span class="stat-unit">/10</span></div>
      <div class="stat-label">Average mood</div>
      <div class="stat-sub">Over the last {{ $days }} days</div>
    </div>

    {{-- ── STREAK ── --}}
    <div class="stat-card glass-card streak-stat" style="animation-delay:160ms">
      <div class="stat-icon">🔥</div>
      <div class="stat-value">
        {{ $streak }}<span class="stat-unit">d</span>
      </div>
      <div class="stat-label">Current streak</div>
      <div class="stat-sub">
        @if($streak === 0)
          Log today to start a streak!
        @elseif($streak === 1)
          Good start — keep going!
        @elseif($streak < 7)
          {{ $streak }} days in a row 💪
        @elseif($streak < 14)
          A whole week+ on fire! 🔥
        @else
          {{ $streak }} days straight 🌟
        @endif
      </div>

      {{-- Streak dots — last 7 days visual ──────────────────── --}}
      <div class="streak-dots">
        @php
          $entryDates = auth()->user()->moodEntries()
            ->where('entry_date', '>=', now()->subDays(6)->toDateString())
            ->pluck('entry_date')
            ->map(fn($d) => \Carbon\Carbon::parse($d)->format('Y-m-d'))
            ->toArray();
        @endphp
        @for($i = 6; $i >= 0; $i--)
          @php $d = now()->subDays($i)->format('Y-m-d'); @endphp
          <div class="streak-dot {{ in_array($d, $entryDates) ? 'filled' : '' }}"
               title="{{ now()->subDays($i)->format('D, M j') }}">
          </div>
        @endfor
      </div>
      <div class="streak-dots-label">Last 7 days</div>
    </div>

  </div>

  {{-- ══ CHARTS GRID ══ --}}
  <div class="analytics-grid">

    {{-- ── 1. Mood Evolution ── --}}
    <div class="glass-card chart-card chart-wide" style="animation-delay:100ms">
      <div class="card-head">
        <div>
          <h2 class="card-title">Mood Evolution</h2>
          <p class="card-sub">Your mood over the last {{ $days }} days</p>
        </div>
      </div>
      @if($moodEvolution->count() > 0)
        <div class="chart-wrap"><canvas id="moodEvolutionChart"></canvas></div>
      @else
        <div class="chart-empty">No entries in this period yet. Start logging!</div>
      @endif
    </div>

    {{-- ── 2. Top Feelings ── --}}
    <div class="glass-card chart-card" style="animation-delay:180ms">
      <div class="card-head">
        <div>
          <h2 class="card-title">Top Feelings</h2>
          <p class="card-sub">Your most frequent emotions</p>
        </div>
      </div>
      @if($topFeelings->count() > 0)
        <div class="chart-wrap"><canvas id="feelingsChart"></canvas></div>
        <div class="feelings-chips" id="feelingsLegend"></div>
      @else
        <div class="chart-empty">No feelings logged yet.</div>
      @endif
    </div>

    {{-- ── 3. Best Day of Week ── --}}
    <div class="glass-card chart-card" style="animation-delay:240ms">
      <div class="card-head">
        <div>
          <h2 class="card-title">Best Day of Week</h2>
          <p class="card-sub">Average mood by weekday</p>
        </div>
      </div>
      @if($dayOfWeek->count() > 0)
        @php $bestDay = collect($dayOfWeek)->sortByDesc('avg_mood')->first(); @endphp
        <div class="best-day-badge">
          🏆 Your best day is <strong>{{ $bestDay['day'] }}</strong>
          — avg mood <strong>{{ $bestDay['avg_mood'] }}</strong>
        </div>
        <div class="chart-wrap"><canvas id="dayOfWeekChart"></canvas></div>
      @else
        <div class="chart-empty">Not enough data yet.</div>
      @endif
    </div>

  </div>{{-- /analytics-grid --}}

</div>{{-- /analytics-container --}}

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script id="analyticsData" type="application/json">
{
  "moodEvolution": {!! $moodEvolution->toJson() !!},
  "topFeelings":   {!! $topFeelings->toJson() !!},
  "dayOfWeek":     {!! $dayOfWeek->toJson() !!}
}
</script>

<script src="{{ asset('js/analytics.js') }}" defer></script>
@endpush