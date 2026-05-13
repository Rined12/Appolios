(() => {
  const EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  const NUMBER_RE = /^-?\d+(\.\d+)?$/;

  function normalizeValue(field) {
    if (!field) return '';
    return String(field.value ?? '').trim();
  }

  function ensureErrorBox(form) {
    let box = form.querySelector('.custom-form-errors');
    if (!box) {
      box = document.createElement('div');
      box.className = 'custom-form-errors';
      box.style.display = 'none';
      box.style.marginBottom = '12px';
      box.style.padding = '10px 12px';
      box.style.borderRadius = '8px';
      box.style.background = '#fee2e2';
      box.style.color = '#991b1b';
      box.style.fontSize = '0.9rem';
      box.style.lineHeight = '1.45';
      form.insertBefore(box, form.firstChild);
    }
    return box;
  }

  function labelFor(field) {
    const id = field.getAttribute('id');
    if (id) {
      const label = document.querySelector(`label[for="${id}"]`);
      if (label) return label.textContent?.trim() || field.name || 'Field';
    }
    return field.name || field.getAttribute('aria-label') || 'Field';
  }

  function bootstrapFieldRules(field) {
    if (!(field instanceof HTMLElement)) return;

    if (field.hasAttribute('required')) {
      field.dataset.jsRequired = '1';
      field.removeAttribute('required');
    }
    if (field.hasAttribute('minlength')) {
      field.dataset.jsMinLength = field.getAttribute('minlength') || '';
      field.removeAttribute('minlength');
    }
    if (field.hasAttribute('maxlength')) {
      field.dataset.jsMaxLength = field.getAttribute('maxlength') || '';
      field.removeAttribute('maxlength');
    }
    if (field.hasAttribute('min')) {
      field.dataset.jsMin = field.getAttribute('min') || '';
      field.removeAttribute('min');
    }
    if (field.hasAttribute('max')) {
      field.dataset.jsMax = field.getAttribute('max') || '';
      field.removeAttribute('max');
    }
    if (field.hasAttribute('pattern')) {
      field.dataset.jsPattern = field.getAttribute('pattern') || '';
      field.removeAttribute('pattern');
    }
  }

  function validateField(field) {
    const errors = [];
    const value = normalizeValue(field);
    const type = (field.getAttribute('type') || '').toLowerCase();
    const name = (field.getAttribute('name') || '').toLowerCase();
    const humanName = labelFor(field);

    const isRequired = field.dataset.jsRequired === '1';
    if (isRequired && value === '') {
      errors.push(`${humanName} is required.`);
      return errors;
    }
    if (value === '') return errors;

    const minLength = Number(field.dataset.jsMinLength || 0);
    if (Number.isFinite(minLength) && minLength > 0 && value.length < minLength) {
      errors.push(`${humanName} must contain at least ${minLength} characters.`);
    }

    const maxLength = Number(field.dataset.jsMaxLength || 0);
    if (Number.isFinite(maxLength) && maxLength > 0 && value.length > maxLength) {
      errors.push(`${humanName} must not exceed ${maxLength} characters.`);
    }

    if (type === 'email' || name.includes('email')) {
      if (!EMAIL_RE.test(value)) errors.push('Please enter a valid email address.');
    }

    if (type === 'number' || field.dataset.jsMin !== undefined || field.dataset.jsMax !== undefined) {
      if (!NUMBER_RE.test(value)) {
        errors.push(`${humanName} must be a valid number.`);
      } else {
        const numericValue = Number(value);
        const min = field.dataset.jsMin === '' ? null : Number(field.dataset.jsMin);
        const max = field.dataset.jsMax === '' ? null : Number(field.dataset.jsMax);
        if (min !== null && Number.isFinite(min) && numericValue < min) {
          errors.push(`${humanName} must be greater than or equal to ${min}.`);
        }
        if (max !== null && Number.isFinite(max) && numericValue > max) {
          errors.push(`${humanName} must be less than or equal to ${max}.`);
        }
      }
    }

    if (field.dataset.jsPattern) {
      try {
        const regex = new RegExp(field.dataset.jsPattern);
        if (!regex.test(value)) {
          errors.push(`${humanName} has an invalid format.`);
        }
      } catch (_err) {
        // Ignore invalid pattern definitions instead of blocking submit.
      }
    }

    return errors;
  }

  function setupForm(form) {
    if (!(form instanceof HTMLFormElement)) return;
    if (form.hasAttribute('data-skip-validation')) return;
    form.setAttribute('novalidate', 'novalidate');
    form.noValidate = true;

    const fields = Array.from(form.querySelectorAll('input, textarea, select'))
      .filter((el) => !(el instanceof HTMLInputElement && ['hidden', 'submit', 'button', 'reset', 'file'].includes((el.type || '').toLowerCase())));

    fields.forEach(bootstrapFieldRules);

    form.addEventListener('submit', (event) => {
      const errors = [];
      fields.forEach((field) => {
        if (field instanceof HTMLElement && !field.hasAttribute('disabled')) {
          errors.push(...validateField(field));
        }
      });

      const box = ensureErrorBox(form);
      if (errors.length > 0) {
        event.preventDefault();
        box.style.display = 'block';
        box.innerHTML = `<strong>Please correct the form:</strong><br>${errors.join('<br>')}`;
        return;
      }

      box.style.display = 'none';
      box.innerHTML = '';
    });
  }

  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('form').forEach(setupForm);
  });
})();
