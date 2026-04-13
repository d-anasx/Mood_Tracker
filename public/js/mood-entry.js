document.addEventListener('DOMContentLoaded', () => {
 
  // DOM Elements
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
  
  // Store the latest analysis for "Use This" button
  let latestAnalysis = null;
  
  // Mood labels
  const moodLabels = {
    1: 'Very Low', 2: 'Low', 3: 'Poor', 4: 'Below Average', 5: 'Fair',
    6: 'Okay', 7: 'Good', 8: 'Great', 9: 'Excellent', 10: 'Amazing'
  };
  
  // Helper: Update button state based on text length
  function updateAnalyzeButton() {
    if (!journalText || !analyzeBtn) return;
    const length = journalText.value.length;
    analyzeBtn.disabled = length < 10;
    if (charCount) charCount.textContent = length;
  }
  
  // Initial update
  updateAnalyzeButton();
  
  // Journal text listener
  if (journalText) {
    journalText.addEventListener('input', updateAnalyzeButton);
  }
  
  // Reflection character count
  if (reflectionInput && reflectionCount) {
    reflectionInput.addEventListener('input', () => {
      reflectionCount.textContent = reflectionInput.value.length;
    });
    reflectionCount.textContent = reflectionInput.value.length;
  }
  
  // Mood slider
  if (moodSlider) {
    moodSlider.addEventListener('input', () => {
      const value = parseInt(moodSlider.value);
      moodValue.textContent = value;
      moodLabel.textContent = moodLabels[value];
    });
    const initVal = parseInt(moodSlider.value);
    moodValue.textContent = initVal;
    moodLabel.textContent = moodLabels[initVal];
  }
  
  // ── AI Analysis ──
  if (analyzeBtn) {
    analyzeBtn.addEventListener('click', async () => {
      const text = journalText.value.trim();
      
      if (text.length < 10) {
        alert('Please write at least 10 characters before analyzing.');
        return;
      }
      
      // Loading state
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
        
        // Store the analysis for later use
        latestAnalysis = data.analysis;
        displayAnalysis(data.analysis);
        
      } catch (error) {
        console.error('Analysis error:', error);
        alert('Failed to analyze your journal. Please try again.\n\n' + error.message);
      } finally {
        analyzeBtn.classList.remove('is-loading');
        analyzeBtn.disabled = journalText.value.length < 10;
      }
    });
  }
  
  // ── Display AI Analysis Results ──
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
    
    // Suggested Mood Level with "Use This" button that applies BOTH mood AND feelings
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
            ✨ Use This
          </button>
        </div>
      `;
      const useBtn = suggestedMoodContainer.querySelector('.use-suggestion-btn');
      if (useBtn) {
        useBtn.addEventListener('click', (e) => {
          // Apply BOTH mood level AND suggested feelings
          applyAllSuggestions(analysis);
        });
      }
    }
    
    // AI Advice
    const adviceEl = document.getElementById('aiAdvice');
    if (adviceEl) adviceEl.textContent = analysis.advice || 'No advice available';
    
    // Show a toast with suggested feelings (without auto-selecting)
    if (analysis.suggested_feelings && analysis.suggested_feelings.length > 0) {
      showToast(`💡 Suggested feelings: ${analysis.suggested_feelings.join(', ')}. Click "Use This" to apply.`);
    }
    
    // Show the analysis panel
    if (aiAnalysis) {
      aiAnalysis.style.display = 'block';
      aiAnalysis.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
  }
  
  // ── Apply ALL suggestions (Mood + Feelings) ──
  function applyAllSuggestions(analysis) {
    // 1. Apply mood level
    if (moodSlider && analysis.mood_level) {
      const moodValueNum = analysis.mood_level;
      moodSlider.value = moodValueNum;
      moodValue.textContent = moodValueNum;
      moodLabel.textContent = moodLabels[moodValueNum];
      
      // Visual feedback on mood section
      const moodContainer = document.querySelector('.mood-slider-container');
      if (moodContainer) {
        moodContainer.style.transition = 'all 0.3s ease';
        moodContainer.style.boxShadow = '0 0 0 2px rgba(16, 185, 129, 0.5)';
        setTimeout(() => {
          moodContainer.style.boxShadow = '';
        }, 1000);
      }
    }
    
    // 2. Apply suggested feelings (clear existing first, then select suggested)
    if (analysis.suggested_feelings && analysis.suggested_feelings.length > 0) {
      // First, clear all selected feelings
      document.querySelectorAll('.feeling-checkbox').forEach(cb => {
        cb.checked = false;
      });
      
      // Then select the suggested ones
      analysis.suggested_feelings.forEach(suggestedFeeling => {
        const normalizedSuggested = suggestedFeeling.trim().toLowerCase();
        document.querySelectorAll('.feeling-card').forEach(card => {
          const feelingNameSpan = card.querySelector('.feeling-name');
          if (feelingNameSpan) {
            const feelingName = feelingNameSpan.textContent.trim().toLowerCase();
            if (feelingName === normalizedSuggested) {
              const checkbox = card.querySelector('.feeling-checkbox');
              if (checkbox) {
                checkbox.checked = true;
                // Add visual feedback to the card
                card.style.transform = 'scale(1.02)';
                setTimeout(() => {
                  card.style.transform = '';
                }, 200);
              }
            }
          }
        });
      });
      
      showToast(`✓ Applied mood level ${analysis.mood_level}/10 and ${analysis.suggested_feelings.length} feeling(s)`);
    } else {
      showToast(`✓ Applied mood level ${analysis.mood_level}/10`);
    }
    
    // Scroll to the mood section so user can see the changes
    document.querySelector('.mood-slider-container')?.scrollIntoView({ 
      behavior: 'smooth', 
      block: 'center' 
    });
  }
  
  // ── Toast notification helper ──
  function showToast(message) {
    // Remove existing toast if any
    const existingToast = document.querySelector('.moodtrace-toast');
    if (existingToast) existingToast.remove();
    
    const toast = document.createElement('div');
    toast.className = 'moodtrace-toast';
    toast.textContent = message;
    toast.style.cssText = `
      position: fixed;
      bottom: 30px;
      left: 50%;
      transform: translateX(-50%);
      background: rgba(16, 185, 129, 0.95);
      color: white;
      padding: 12px 24px;
      border-radius: 50px;
      font-size: 0.9rem;
      font-weight: 500;
      z-index: 9999;
      box-shadow: 0 4px 15px rgba(0,0,0,0.3);
      animation: slideUpFade 0.3s ease-out forwards;
      white-space: nowrap;
    `;
    
    // Add animation styles if not already present
    if (!document.querySelector('#toast-styles')) {
      const style = document.createElement('style');
      style.id = 'toast-styles';
      style.textContent = `
        @keyframes slideUpFade {
          from {
            opacity: 0;
            transform: translateX(-50%) translateY(20px);
          }
          to {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
          }
        }
      `;
      document.head.appendChild(style);
    }
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
      toast.style.opacity = '0';
      toast.style.transform = 'translateX(-50%) translateY(20px)';
      setTimeout(() => toast.remove(), 300);
    }, 3000);
  }
  
  // ── Feeling Card Interactions (allow manual toggling) ──
  const feelingCardsElements = document.querySelectorAll('.feeling-card');
  feelingCardsElements.forEach(card => {
    const checkbox = card.querySelector('.feeling-checkbox');
    
    card.addEventListener('click', (e) => {
      if (e.target.type !== 'checkbox') {
        e.preventDefault();
        if (checkbox) {
          checkbox.checked = !checkbox.checked;
          if (checkbox.checked) {
            card.style.transform = 'scale(1.02)';
            setTimeout(() => {
              card.style.transform = '';
            }, 200);
          }
        }
      }
    });
  });
  
  // ── Form Validation ──
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
      const submitBtn = form.querySelector('button[type="submit"]');
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="btn-icon">⏳</span> Saving...';
      }
    });
  }
  
});