document.addEventListener('DOMContentLoaded', () => {
 
  // ── DOM Elements ──────────────────────────────────────────
  const journalText = document.getElementById('journalText');
  const charCount = document.getElementById('charCount');
  const analyzeBtn = document.getElementById('analyzeBtn');
  const aiAnalysis = document.getElementById('aiAnalysis');
  const moodSlider = document.getElementById('moodSlider');
  const moodValue = document.getElementById('moodValue');
  const moodLabel = document.getElementById('moodLabel');
  const reflectionInput = document.getElementById('reflection');
  const reflectionCount = document.getElementById('reflectionCount');
  const feelingCards = document.querySelectorAll('.feeling-card');
  
  // ── Mood Labels ───────────────────────────────────────────
  const moodLabels = {
    1: 'Very Low',
    2: 'Low',
    3: 'Poor',
    4: 'Below Average',
    5: 'Fair',
    6: 'Okay',
    7: 'Good',
    8: 'Great',
    9: 'Excellent',
    10: 'Amazing'
  };
  
  // ── Character Count for Journal ──────────────────────────
  journalText.addEventListener('input', () => {
    const count = journalText.value.length;
    charCount.textContent = count;
    
    // Enable analyze button if text is >= 10 chars
    analyzeBtn.disabled = count < 10;
  });
  
  // ── Character Count for Reflection ───────────────────────
  if (reflectionInput) {
    reflectionInput.addEventListener('input', () => {
      reflectionCount.textContent = reflectionInput.value.length;
    });
  }
  
  // ── Mood Slider ───────────────────────────────────────────
  moodSlider.addEventListener('input', () => {
    const value = parseInt(moodSlider.value);
    moodValue.textContent = value;
    moodLabel.textContent = moodLabels[value];
  });
  
  // ── AI Analysis ───────────────────────────────────────────
  analyzeBtn.addEventListener('click', async () => {
    const text = journalText.value.trim();
    
    if (text.length < 10) {
      alert('Please write at least 10 characters before analyzing.');
      return;
    }
    
    // Show loading state
    analyzeBtn.classList.add('is-loading');
    analyzeBtn.disabled = true;
    
    try {
      const response = await fetch('/mood/analyze-journal', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({ journal_text: text }),
      });
      
      const data = await response.json();
      
      if (!response.ok || !data.success) {
        throw new Error(data.message || 'Failed to analyze journal');
      }
      
      displayAnalysis(data.analysis);
      
    } catch (error) {
      console.error('Analysis error:', error);
      alert('Failed to analyze your journal. Please try again.\n\n' + error.message);
    } finally {
      analyzeBtn.classList.remove('is-loading');
      analyzeBtn.disabled = false;
    }
  });
  
  // ── Display AI Analysis Results ──────────────────────────
  function displayAnalysis(analysis) {
    // Emotional Tone
    document.getElementById('emotionalTone').textContent = analysis.emotional_tone || 'N/A';
    
    // Detected Emotions
    const emotionsContainer = document.getElementById('detectedEmotions');
    emotionsContainer.innerHTML = '';
    if (analysis.detected_emotions && analysis.detected_emotions.length > 0) {
      analysis.detected_emotions.forEach(emotion => {
        const tag = document.createElement('span');
        tag.className = 'emotion-tag';
        tag.textContent = emotion;
        emotionsContainer.appendChild(tag);
      });
    } else {
      emotionsContainer.textContent = 'No specific emotions detected';
    }
    
    // Suggested Mood Level
    const suggestedMoodContainer = document.getElementById('suggestedMood');
    suggestedMoodContainer.innerHTML = `
      <div>
        <span class="suggested-mood-number">${analysis.mood_level}</span>
        <span class="suggested-mood-label">${moodLabels[analysis.mood_level]}</span>
      </div>
      <button type="button" class="use-suggestion-btn" onclick="useSuggestedMood(${analysis.mood_level})">
        Use This
      </button>
    `;
    
    // AI Advice
    document.getElementById('aiAdvice').textContent = analysis.advice || 'No advice available';
    
    // Auto-select suggested feelings if available
    if (analysis.suggested_feelings && analysis.suggested_feelings.length > 0) {
      autoSelectFeelings(analysis.suggested_feelings);
    }
    
    // Show the analysis panel with animation
    aiAnalysis.style.display = 'block';
    aiAnalysis.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
  }
  
  // ── Use Suggested Mood ────────────────────────────────────
  window.useSuggestedMood = function(value) {
    moodSlider.value = value;
    moodValue.textContent = value;
    moodLabel.textContent = moodLabels[value];
    
    // Smooth scroll to mood section
    document.querySelector('.mood-slider-container').scrollIntoView({
      behavior: 'smooth',
      block: 'center'
    });
    
    // Visual feedback
    const btn = event.target;
    const originalText = btn.textContent;
    btn.textContent = '✓ Applied';
    btn.style.background = 'rgba(16, 185, 129, 0.3)';
    btn.style.borderColor = 'rgb(16, 185, 129)';
    btn.style.color = 'rgb(16, 185, 129)';
    
    setTimeout(() => {
      btn.textContent = originalText;
      btn.style.background = '';
      btn.style.borderColor = '';
      btn.style.color = '';
    }, 2000);
  };
  
  // ── Auto-Select Suggested Feelings ───────────────────────
  function autoSelectFeelings(suggestedFeelings) {
    // Clear existing selections
    document.querySelectorAll('.feeling-checkbox').forEach(cb => {
      cb.checked = false;
    });
    
    // Select matching feelings (case-insensitive)
    suggestedFeelings.forEach(suggestedFeeling => {
      feelingCards.forEach(card => {
        const feelingName = card.querySelector('.feeling-name').textContent.trim();
        if (feelingName.toLowerCase() === suggestedFeeling.toLowerCase()) {
          const checkbox = card.querySelector('.feeling-checkbox');
          checkbox.checked = true;
        }
      });
    });
  }
  
  // ── Feeling Card Interactions ────────────────────────────
  feelingCards.forEach(card => {
    card.addEventListener('click', () => {
      const checkbox = card.querySelector('.feeling-checkbox');
      checkbox.checked = !checkbox.checked;
    });
  });
  
  // ── Form Validation ───────────────────────────────────────
  const form = document.getElementById('moodEntryForm');
  form.addEventListener('submit', (e) => {
    const moodValue = parseInt(moodSlider.value);
    
    if (moodValue < 1 || moodValue > 10) {
      e.preventDefault();
      alert('Please select a mood level between 1 and 10');
      moodSlider.focus();
      return false;
    }
    
    // Show loading state on submit button
    const submitBtn = form.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="btn-icon">⏳</span> Saving...';
  });
  
});