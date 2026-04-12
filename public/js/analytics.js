

document.addEventListener('DOMContentLoaded', () => {

  const raw = document.getElementById('analyticsData');
  if (!raw) return;

  const { moodEvolution, topFeelings, dayOfWeek } = JSON.parse(raw.textContent);

  // ── Shared defaults ───────────────────────────────────────
  Chart.defaults.color       = 'rgba(169,180,194,0.6)';
  Chart.defaults.font.family = "'DM Sans', sans-serif";
  Chart.defaults.font.size   = 11;
  Chart.defaults.plugins.legend.display = false;

  const gridColor = 'rgba(255,255,255,0.05)';
  const tickColor = 'rgba(169,180,194,0.45)';

  const tooltip = {
    backgroundColor: 'rgba(15,14,23,0.92)',
    borderColor:     'rgba(212,165,181,0.2)',
    borderWidth:     1,
    padding:         12,
    titleFont:       { size: 12, weight: '600' },
    bodyFont:        { size: 12 },
    cornerRadius:    10,
    displayColors:   false,
  };

  // ── 1. Mood Evolution ─────────────────────────────────────
   const moodCtx = document.getElementById('moodEvolutionChart');
  if (moodCtx && moodEvolution.length > 0) {
    const labels     = moodEvolution.map(e => e.date);
    const moodData   = moodEvolution.map(e => e.mood_level);
    const sleepData  = moodEvolution.map(e => e.sleep_hours);

    new Chart(moodCtx, {
      type: 'line',
      data: {
        labels,
        datasets: [
          {
            label:           'Mood',
            data:            moodData,
            yAxisID:         'yMood',
            borderColor:     '#c0587a',
            backgroundColor: 'rgba(192,88,122,0.08)',
            tension:         0.4,
            fill:            true,
            pointRadius:     4,
            pointHoverRadius:7,
            pointBackgroundColor: '#c0587a',
            pointBorderColor:     '#0f0e17',
            pointBorderWidth:     2,
          },
          {
            label:           'Sleep (h)',
            data:            sleepData,
            yAxisID:         'ySleep',
            borderColor:     '#4ecdc4',
            backgroundColor: 'rgba(78,205,196,0.06)',
            tension:         0.4,
            fill:            false,
            borderDash:      [5, 3],
            pointRadius:     3,
            pointHoverRadius:6,
            pointBackgroundColor: '#4ecdc4',
            pointBorderColor:     '#0f0e17',
            pointBorderWidth:     2,
          },
        ],
      },
      options: {
        responsive:          true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        
        scales: {
          yMood: {
            position: 'left',
            min: 1, max: 10,
            ticks:  { stepSize: 1, color: tickColor },
            grid:   { color: gridColor },
            border: { display: false },
          },
          ySleep: {
            position: 'right',
            min: 0, max: 12,
            ticks:  { color: '#4ecdc4', callback: v => v + 'h' },
            grid:   { display: false },
            border: { display: false },
          },    
        },
      },
    });
  }

  // ── 2. Top Feelings (horizontal bar) ─────────────────────
  const feelingsCtx = document.getElementById('feelingsChart');
  if (feelingsCtx && topFeelings.length > 0) {

    new Chart(feelingsCtx, {
      type: 'bar',
      data: {
        labels:   topFeelings.map(f => f.icon + ' ' + f.name),
        datasets: [{
          data:            topFeelings.map(f => f.count),
          backgroundColor: topFeelings.map(f => f.color + 'bb'),
          borderColor:     topFeelings.map(f => f.color),
          borderWidth:     1,
          borderRadius:    6,
        }],
      },
      options: {
        indexAxis:           'y',
        responsive:          true,
        maintainAspectRatio: false,
        plugins: {
          tooltip: {
            ...tooltip,
            callbacks: { label: ctx => `  ${ctx.parsed.x} times` },
          },
        },
        scales: {
          x: {
            grid:   { color: gridColor },
            ticks:  { color: tickColor, stepSize: 1 },
            border: { display: false },
          },
          y: {
            grid:   { display: false },
            ticks:  { color: tickColor },
            border: { display: false },
          },
        },
      },
    });

    // Legend chips
    const legendEl = document.getElementById('feelingsLegend');
    if (legendEl) {
      topFeelings.forEach(f => {
        const chip = document.createElement('span');
        chip.className   = 'feeling-chip-legend';
        chip.textContent = `${f.icon} ${f.name} · ${f.count}`;
        chip.style.cssText = `
          background:  ${f.color}22;
          border-color:${f.color}66;
          color:       ${f.color};
        `;
        legendEl.appendChild(chip);
      });
    }
  }

  // ── 3. Best Day of Week (bar) ─────────────────────────────
  const dowCtx = document.getElementById('dayOfWeekChart');
  if (dowCtx && dayOfWeek.length > 0) {

    const allDays  = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
    const dayMap   = Object.fromEntries(dayOfWeek.map(d => [d.day, d.avg_mood]));
    const values   = allDays.map(d => dayMap[d] ?? null);
    const maxMood  = Math.max(...values.filter(Boolean));

    new Chart(dowCtx, {
      type: 'bar',
      data: {
        labels:   allDays,
        datasets: [{
          data:            values,
          backgroundColor: values.map(v =>
            v === null    ? 'rgba(255,255,255,0.04)' :
            v === maxMood ? 'rgba(192,88,122,0.75)'  : 'rgba(124,62,160,0.45)'
          ),
          borderColor: values.map(v =>
            v === null    ? 'rgba(255,255,255,0.06)' :
            v === maxMood ? '#c0587a'                : '#7c3fa0'
          ),
          borderWidth:  1,
          borderRadius: 8,
        }],
      },
      options: {
        responsive:          true,
        maintainAspectRatio: false,
        plugins: {
          tooltip: {
            ...tooltip,
            callbacks: {
              label: ctx => ctx.parsed.y !== null
                ? `  Avg mood: ${ctx.parsed.y}/10`
                : '  No data',
            },
          },
        },
        scales: {
          y: {
            min: 0, max: 10,
            ticks:  { stepSize: 2, color: tickColor },
            grid:   { color: gridColor },
            border: { display: false },
          },
          x: {
            grid:   { display: false },
            ticks:  { color: tickColor },
            border: { display: false },
          },
        },
      },
    });
  }

});