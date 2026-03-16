/**
 * login.js
 * Client-side validation for the MoodTrace login form.
 * Place in: public/js/auth/login.js
 */

document.addEventListener('DOMContentLoaded', () => {

  const form     = document.getElementById('loginForm');
  const emailEl  = document.getElementById('email');
  const pwEl     = document.getElementById('password');
  const submitBtn = document.getElementById('loginBtn');

  // ── Helpers ──────────────────────────────────────────────

  function getField(input) {
    return input.closest('.field');
  }

  function setError(input, message) {
    const field = getField(input);
    field.classList.add('has-error');
    field.classList.remove('has-success');

    let err = field.querySelector('.field-error');
    if (!err) {
      err = document.createElement('span');
      err.className = 'field-error';
      field.appendChild(err);
    }
    err.textContent = message;
    input.setAttribute('aria-invalid', 'true');
  }

  function setSuccess(input) {
    const field = getField(input);
    field.classList.remove('has-error');
    field.classList.add('has-success');

    const err = field.querySelector('.field-error');
    if (err) err.textContent = '';
    input.setAttribute('aria-invalid', 'false');
  }

  function clearState(input) {
    const field = getField(input);
    field.classList.remove('has-error', 'has-success');
    input.removeAttribute('aria-invalid');
  }

  // ── Rules ────────────────────────────────────────────────

  function validateEmail() {
    const val = emailEl.value.trim();

    if (!val) {
      setError(emailEl, 'Please enter your email address.');
      return false;
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(val)) {
      setError(emailEl, 'Please enter a valid email address.');
      return false;
    }

    setSuccess(emailEl);
    return true;
  }

  function validatePassword() {
    const val = pwEl.value;

    if (!val) {
      setError(pwEl, 'Please enter your password.');
      return false;
    }

    if (val.length < 8) {
      setError(pwEl, 'Password must be at least 8 characters.');
      return false;
    }

    setSuccess(pwEl);
    return true;
  }

  // ── Live validation (on blur) ─────────────────────────────

  emailEl.addEventListener('blur', () => {
    if (emailEl.value.trim()) validateEmail();
  });

  emailEl.addEventListener('input', () => {
    if (getField(emailEl).classList.contains('has-error')) validateEmail();
  });

  pwEl.addEventListener('blur', () => {
    if (pwEl.value) validatePassword();
  });

  pwEl.addEventListener('input', () => {
    if (getField(pwEl).classList.contains('has-error')) validatePassword();
  });

  // ── Submit ───────────────────────────────────────────────

  form.addEventListener('submit', (e) => {
    const emailOk = validateEmail();
    const pwOk    = validatePassword();

    if (!emailOk || !pwOk) {
      e.preventDefault();

      // Focus first invalid field
      if (!emailOk) emailEl.focus();
      else           pwEl.focus();
      return;
    }

    // Visual feedback while submitting
    submitBtn.textContent     = 'Signing in…';
    submitBtn.style.opacity   = '0.8';
    submitBtn.style.pointerEvents = 'none';
  });

  // ── Password toggle ──────────────────────────────────────

  const togglePw = document.getElementById('togglePw');
  if (togglePw) {
    togglePw.addEventListener('click', () => {
      const isText   = pwEl.type === 'text';
      pwEl.type      = isText ? 'password' : 'text';
      togglePw.textContent = isText ? '👁' : '🙈';
      togglePw.setAttribute('title', isText ? 'Show password' : 'Hide password');
    });
  }

  // ── Emotion chips ────────────────────────────────────────

  document.querySelectorAll('.emotion-chip').forEach(chip => {
    chip.addEventListener('click', () => {
      document.querySelectorAll('.emotion-chip').forEach(c => c.classList.remove('active'));
      chip.classList.add('active');
    });
  });

  // ── Mood bar animation ───────────────────────────────────

  document.querySelectorAll('.bar-fill').forEach((bar, i) => {
    const target = bar.dataset.target + '%';
    setTimeout(() => { bar.style.width = target; }, 900 + i * 150);
  });

});