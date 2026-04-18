/**
 * register.js
 * Client-side validation for the MoodTrace multi-step registration form.
 * Place in: public/js/auth/register.js
 */

document.addEventListener('DOMContentLoaded', () => {

  // ── State ─────────────────────────────────────────────────
  let currentStep = 1;
  const TOTAL     = 3;

  // ── DOM refs ──────────────────────────────────────────────
  const form        = document.getElementById('registerForm');
  const progressFill = document.getElementById('progressFill');
  const progressPct  = document.getElementById('progressPct');

  // Step 1
  const emailEl  = document.getElementById('reg-email');
  const pwEl     = document.getElementById('reg-pw');
  const pw2El    = document.getElementById('reg-pw2');

  // Step 2
  const nameEl   = document.getElementById('reg-name');


  // ── Generic helpers ───────────────────────────────────────

  function getField(input) {
    return input.closest('.field');
  }

  function setError(input, message) {
    const field = getField(input);
    if (!field) return;
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
    if (!field) return;
    field.classList.remove('has-error');
    field.classList.add('has-success');

    const err = field.querySelector('.field-error');
    if (err) err.textContent = '';
    input.setAttribute('aria-invalid', 'false');
  }

  // ── Step 1 — Validators ───────────────────────────────────

  function validateEmail() {
    const val = emailEl.value.trim();

    if (!val) {
      setError(emailEl, 'An email address is required.');
      return false;
    }
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
      setError(emailEl, 'Please enter a valid email address.');
      return false;
    }
    setSuccess(emailEl);
    return true;
  }

  function validatePassword() {
    const val = pwEl.value;

    if (!val) {
      setError(pwEl, 'A password is required.');
      return false;
    }
    if (val.length < 8) {
      setError(pwEl, 'Password must be at least 8 characters.');
      return false;
    }
    if (!/[A-Z]/.test(val)) {
      setError(pwEl, 'Password must contain at least one uppercase letter.');
      return false;
    }
    if (!/[a-z]/.test(val)) {
      setError(pwEl, 'Password must contain at least one lowercase letter.');
      return false;
    }
    if (!/[0-9]/.test(val)) {
      setError(pwEl, 'Password must contain at least one number.');
      return false;
    }
    setSuccess(pwEl);
    return true;
  }

  function validateConfirm() {
    const val = pw2El.value;

    if (!val) {
      setError(pw2El, 'Please confirm your password.');
      return false;
    }
    if (val !== pwEl.value) {
      setError(pw2El, 'Passwords do not match.');
      return false;
    }
    setSuccess(pw2El);
    return true;
  }

  function validateStep1() {
    const e = validateEmail();
    const p = validatePassword();
    const c = validateConfirm();
    return e && p && c;
  }

  // ── Step 2 — Validators ───────────────────────────────────

  function validateName() {
    const val = nameEl.value.trim();

    if (!val) {
      setError(nameEl, 'Your name is required.');
      return false;
    }
    if (val.length < 2) {
      setError(nameEl, 'Name must be at least 2 characters.');
      return false;
    }
    if (val.length > 100) {
      setError(nameEl, 'Name cannot exceed 100 characters.');
      return false;
    }
    setSuccess(nameEl);
    return true;
  }

  function validateStep2() {
    return validateName();
  }

  // ── Step 3 — Validators ───────────────────────────────────
  // Mood level and feeling are optional per cahier des charges
  // but we can warn if nothing was selected

  function validateStep3() {
    return true; // both fields are optional
  }

  // // ── Step 4 — Validators ───────────────────────────────────

  // function validateReminderTime() {
  //   if (!reminderToggle.checked) return true;

  //   const val = reminderTimeEl.value;
  //   if (!val) {
  //     setError(reminderTimeEl, 'Please set a reminder time.');
  //     return false;
  //   }
  //   setSuccess(reminderTimeEl);
  //   return true;
  // }

  // function validateStep4() {
  //   return validateReminderTime();
  // }

  // ── Step navigation ───────────────────────────────────────

  const validators = {
    1: validateStep1,
    2: validateStep2,
    3: validateStep3,
  };

  window.goStep = function (n) {
    // Validate the CURRENT step before advancing
    if (n > currentStep && !validators[currentStep]()) {
      // Shake the Continue button
      const btn = document.querySelector(`#panel-${currentStep} .btn-primary`);
      if (btn) shakeBtn(btn);
      return;
    }

    // Hide current panel
    document.getElementById(`panel-${currentStep}`).classList.remove('is-active');

    // Update sidebar indicators
    for (let i = 1; i <= TOTAL; i++) {
      const sc = document.getElementById(`sc-${i}`);
      const sn = document.getElementById(`sn-${i}`);
      const sd = document.getElementById(`sd-${i}`);

      sc.classList.remove('is-active', 'is-done');
      sn.classList.remove('is-active', 'is-done');
      sd.classList.remove('is-active');

      if (i < n) {
        sc.classList.add('is-done');
        sc.textContent = '✓';
        sn.classList.add('is-done');
      } else if (i === n) {
        sc.classList.add('is-active');
        sc.textContent = i;
        sc.setAttribute('aria-current', 'step');
        sn.classList.add('is-active');
        sd.classList.add('is-active');
      } else {
        sc.textContent = i;
        sc.removeAttribute('aria-current');
      }
    }

    currentStep = n;
    document.getElementById(`panel-${n}`).classList.add('is-active');

    const pct = Math.round((n / TOTAL) * 100);
    progressFill.style.width    = pct + '%';
    progressPct.textContent     = pct + '%';
  };

  // ── Final submit ──────────────────────────────────────────

  form.addEventListener('submit', (e) => {
    // Validate step 4 before submitting
    if (!validateStep4()) {
      e.preventDefault();
      reminderTimeEl.focus();
      return;
    }

    // Show loading state
    const btn = document.getElementById('submitBtn');
    btn.textContent         = 'Creating account…';
    btn.style.opacity       = '0.8';
    btn.style.pointerEvents = 'none';

    // ✅ Allow the form to submit normally to Laravel
    // The form will POST to /register route and redirect
    // No preventDefault() here!
  });

  // ── Password strength meter ───────────────────────────────

  pwEl.addEventListener('input', () => {
    const val   = pwEl.value;
    let score   = 0;
    if (val.length >= 8)          score++;
    if (/[A-Z]/.test(val))        score++;
    if (/[0-9]/.test(val))        score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;

    const levelMap = { 0:'', 1:'is-weak', 2:'is-weak', 3:'is-fair', 4:'is-strong' };
    const level    = levelMap[score];

    ['pb1','pb2','pb3','pb4'].forEach((id, i) => {
      const bar = document.getElementById(id);
      bar.className = 'pw-bar';
      if (i < score) bar.classList.add(level);
    });

    // Re-validate confirm if already touched
    if (pw2El.value) validateConfirm();

    // Clear password error while typing
    if (getField(pwEl).classList.contains('has-error')) validatePassword();
  });

  // ── Live validation on blur ───────────────────────────────

  emailEl.addEventListener('blur',  validateEmail);
  emailEl.addEventListener('input', () => {
    if (getField(emailEl).classList.contains('has-error')) validateEmail();
  });

  pwEl.addEventListener('blur', validatePassword);

  pw2El.addEventListener('blur', validateConfirm);
  pw2El.addEventListener('input', () => {
    if (getField(pw2El).classList.contains('has-error')) validateConfirm();
  });

  nameEl.addEventListener('blur', validateName);
  nameEl.addEventListener('input', () => {
    if (getField(nameEl).classList.contains('has-error')) validateName();
  });

  // ── Avatar selection ──────────────────────────────────────

  window.selectAvatar = function (el) {
    document.querySelectorAll('.avatar-opt').forEach(a => a.classList.remove('is-selected'));
    el.classList.add('is-selected');
    document.getElementById('avatarInput').value = el.dataset.avatar;
  };

  // ── Feeling selection ─────────────────────────────────────

  window.selectFeeling = function (el) {
    document.querySelectorAll('.feeling-btn').forEach(b => b.classList.remove('is-selected'));
    el.classList.add('is-selected');
    const emoji = el.querySelector('.emoji').textContent;
    const label = el.textContent.replace(emoji, '').trim();
    document.getElementById('feelingInput').value = emoji + ' ' + label;
  };

  // ── Reminder toggle ───────────────────────────────────────

  window.toggleReminderTime = function () {
    reminderTimeField.style.display = reminderToggle.checked ? 'block' : 'none';
    if (!reminderToggle.checked) {
      // Clear errors when hidden
      getField(reminderTimeEl)?.classList.remove('has-error');
    }
  };

  // ── Mood slider ───────────────────────────────────────────

  const moodSlider = document.getElementById('moodSlider');
  const moodVal    = document.getElementById('moodVal');
  if (moodSlider) {
    moodSlider.addEventListener('input', () => {
      moodVal.textContent = moodSlider.value;
    });
  }

  // ── Shake animation on invalid continue ──────────────────

  function shakeBtn(btn) {
    btn.style.transition  = 'transform 0.1s ease';
    btn.style.transform   = 'translateX(-6px)';
    setTimeout(() => { btn.style.transform = 'translateX(6px)'; },  100);
    setTimeout(() => { btn.style.transform = 'translateX(-4px)'; }, 200);
    setTimeout(() => { btn.style.transform = 'translateX(4px)'; },  300);
    setTimeout(() => { btn.style.transform = 'translateX(0)'; },    400);
  }

});