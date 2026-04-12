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
  
  // Initialize character counts
  if (journalText) {
    charCount.textContent = journalText.value.length;
    analyzeBtn.disabled = journalText.value.length < 10;
  }
  
  if (reflectionInput && reflectionCount) {
    reflectionCount.textContent = reflectionInput.value.length;
  }
  
  // ── Character Count for Journal ──────────────────────────
  if (journalText) {
    journalText.addEventListener('input', () => {
      const count = journalText.value.length;
      charCount.textContent = count;
      analyzeBtn.disabled = count < 10;
    });
  }
  
  // ── Character Count for Reflection ───────────────────────
  if (reflectionInput && reflectionCount) {
    reflectionInput.addEventListener('input', () => {
      reflectionCount.textContent = reflectionInput.value.length;
    });
  }
  
  // ── Mood Slider ───────────────────────────────────────────
  if (moodSlider) {
    moodSlider.addEventListener('input', () => {
      const value = parseInt(moodSlider.value);
      moodValue.textContent = value;
      moodLabel.textContent = moodLabels[value];
    });
  }
  
  // ── AI Analysis ───────────────────────────────────────────
  if (analyzeBtn) {
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
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
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
  }
  
  // ── Display AI Analysis Results ──────────────────────────
  function displayAnalysis(analysis) {
    // Emotional Tone
    const emotionalToneEl = document.getElementById('emotionalTone');
    if (emotionalToneEl) emotionalToneEl.textContent = analysis.emotional_tone || 'N/A';
    
    // Detected Emotions
    const emotionsContainer = document.getElementById('detectedEmotions');
    if (emotionsContainer) {
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
    }
    
    // Suggested Mood Level with fixed event handling
    const suggestedMoodContainer = document.getElementById('suggestedMood');
    if (suggestedMoodContainer) {
      const moodLevel = analysis.mood_level || 5;
      suggestedMoodContainer.innerHTML = `
        <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
          <div>
            <span class="suggested-mood-number">${moodLevel}</span>
            <span class="suggested-mood-label">${moodLabels[moodLevel] || 'Good'}</span>
          </div>
          <button type="button" class="use-suggestion-btn" data-mood-value="${moodLevel}">
            Use This
          </button>
        </div>
      `;
      
      // Attach event listener to the newly created button
      const useBtn = suggestedMoodContainer.querySelector('.use-suggestion-btn');
      if (useBtn) {
        useBtn.addEventListener('click', (e) => {
          const value = parseInt(useBtn.dataset.moodValue);
          useSuggestedMood(value, useBtn);
        });
      }
    }
    
    // AI Advice
    const adviceEl = document.getElementById('aiAdvice');
    if (adviceEl) adviceEl.textContent = analysis.advice || 'No advice available';
    
    // Auto-select suggested feelings if available
    if (analysis.suggested_feelings && analysis.suggested_feelings.length > 0) {
      autoSelectFeelings(analysis.suggested_feelings);
    }
    
    // Show the analysis panel with animation
    if (aiAnalysis) {
      aiAnalysis.style.display = 'block';
      aiAnalysis.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
  }
  
  // ── Use Suggested Mood (fixed event handling) ────────────────────
  function useSuggestedMood(value, buttonElement) {
    if (moodSlider) {
      moodSlider.value = value;
      moodValue.textContent = value;
      moodLabel.textContent = moodLabels[value];
      
      // Smooth scroll to mood section
      document.querySelector('.mood-slider-container')?.scrollIntoView({
        behavior: 'smooth',
        block: 'center'
      });
      
      // Visual feedback on the button
      if (buttonElement) {
        const originalText = buttonElement.textContent;
        buttonElement.textContent = '✓ Applied';
        buttonElement.style.background = 'rgba(16, 185, 129, 0.3)';
        buttonElement.style.borderColor = 'rgb(16, 185, 129)';
        buttonElement.style.color = 'rgb(16, 185, 129)';
        
        setTimeout(() => {
          buttonElement.textContent = originalText;
          buttonElement.style.background = '';
          buttonElement.style.borderColor = '';
          buttonElement.style.color = '';
        }, 2000);
      }
    }
  }
  
  // Make function available globally for any dynamic buttons
  window.useSuggestedMood = useSuggestedMood;
  
  // ── Auto-Select Suggested Feelings (case-insensitive) ───────────────────────
  function autoSelectFeelings(suggestedFeelings) {
    // Clear existing selections
    document.querySelectorAll('.feeling-checkbox').forEach(cb => {
      cb.checked = false;
    });
    
    // Select matching feelings (case-insensitive trim comparison)
    suggestedFeelings.forEach(suggestedFeeling => {
      const normalizedSuggested = suggestedFeeling.trim().toLowerCase();
      feelingCards.forEach(card => {
        const feelingNameSpan = card.querySelector('.feeling-name');
        if (feelingNameSpan) {
          const feelingName = feelingNameSpan.textContent.trim().toLowerCase();
          if (feelingName === normalizedSuggested) {
            const checkbox = card.querySelector('.feeling-checkbox');
            if (checkbox) checkbox.checked = true;
          }
        }
      });
    });
  }
  
  // ── Feeling Card Interactions ────────────────────────────
  feelingCards.forEach(card => {
    card.addEventListener('click', (e) => {
      // Prevent toggling if clicking directly on checkbox (already toggles naturally)
      if (e.target.type !== 'checkbox') {
        const checkbox = card.querySelector('.feeling-checkbox');
        if (checkbox) checkbox.checked = !checkbox.checked;
      }
    });
  });
  
  // ── Form Validation ───────────────────────────────────────
  const form = document.getElementById('moodEntryForm');
  if (form) {
    form.addEventListener('submit', (e) => {
      const moodValueNum = parseInt(moodSlider?.value);
      
      if (isNaN(moodValueNum) || moodValueNum < 1 || moodValueNum > 10) {
        e.preventDefault();
        alert('Please select a mood level between 1 and 10');
        moodSlider?.focus();
        return false;
      }
      
      // Show loading state on submit button
      const submitBtn = form.querySelector('button[type="submit"]');
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="btn-icon">⏳</span> Saving...';
      }
    });
  }
  
});