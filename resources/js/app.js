import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

const shouldAutoCapitalize = (el) => {
    if (!(el instanceof HTMLInputElement || el instanceof HTMLTextAreaElement)) return false;

    if (el.hasAttribute('data-no-capitalize')) return false;
    if (el.classList && el.classList.contains('no-capitalize')) return false;

    if (el instanceof HTMLInputElement) {
        const type = (el.getAttribute('type') || 'text').toLowerCase();
        const allowedTypes = new Set(['text', 'search', '']);
        if (!allowedTypes.has(type)) return false;

        const inputMode = (el.getAttribute('inputmode') || '').toLowerCase();
        if (inputMode === 'numeric' || inputMode === 'decimal') return false;

        const autoComplete = (el.getAttribute('autocomplete') || '').toLowerCase();
        if (autoComplete === 'username' || autoComplete === 'email' || autoComplete === 'new-password' || autoComplete === 'current-password') {
            return false;
        }
    }

    return !el.readOnly && !el.disabled;
};

const capitalizeFirstLetter = (value) => {
    const s = String(value ?? '');
    if (!s) return s;

    const i = s.search(/\S/);
    if (i < 0) return s;

    const ch = s[i];
    const upper = ch.toLocaleUpperCase();
    if (upper === ch) return s;

    return s.slice(0, i) + upper + s.slice(i + 1);
};

const applyCapitalizationToElement = (el) => {
    if (!shouldAutoCapitalize(el)) return;
    const next = capitalizeFirstLetter(el.value);
    if (next === el.value) return;

    const start = typeof el.selectionStart === 'number' ? el.selectionStart : null;
    const end = typeof el.selectionEnd === 'number' ? el.selectionEnd : null;

    el.value = next;

    if (start !== null && end !== null && typeof el.setSelectionRange === 'function') {
        el.setSelectionRange(start, end);
    }
};

if (!window.__cidasAutoCapitalizeInit) {
    window.__cidasAutoCapitalizeInit = true;

    document.addEventListener(
        'input',
        (e) => {
            if (e && e.isComposing) return;
            const target = e.target;
            if (!(target instanceof HTMLInputElement || target instanceof HTMLTextAreaElement)) return;
            applyCapitalizationToElement(target);
        },
        true
    );

    document.addEventListener(
        'blur',
        (e) => {
            const target = e.target;
            if (!(target instanceof HTMLInputElement || target instanceof HTMLTextAreaElement)) return;
            applyCapitalizationToElement(target);
        },
        true
    );
}

const normalizeIndianPhoneInput = (value) => {
    const s = String(value ?? '').replace(/[()\s-]/g, '');
    if (!s) return '';
    if (s.startsWith('+')) return `+${s.slice(1).replace(/\D/g, '')}`;
    return s.replace(/\D/g, '');
};

const isValidIndianPhone = (value, { strict }) => {
    const s = normalizeIndianPhoneInput(value);
    if (!s) return true;
    if (strict) return /^(?:\d{10}|91\d{10}|\+91\d{10})$/.test(s);

    if (s.startsWith('+')) {
        if (s.length <= 3) return true;
        if (!s.startsWith('+91')) return false;
        return s.length <= 13;
    }
    if (s.startsWith('91')) return s.length <= 12;
    return s.length <= 10;
};

const normalizeAlphaNumUpper = (value) => String(value ?? '').toUpperCase().replace(/[^A-Z0-9]/g, '');

const isValidPan = (value, { strict }) => {
    const s = normalizeAlphaNumUpper(value);
    if (!s) return true;
    if (!strict && s.length < 10) return true;
    return /^[A-Z]{5}\d{4}[A-Z]$/.test(s);
};

const isValidGst = (value, { strict }) => {
    const s = normalizeAlphaNumUpper(value);
    if (!s) return true;
    if (!strict && s.length < 15) return true;
    return /^\d{2}[A-Z]{5}\d{4}[A-Z][1-9A-Z]Z[A-Z0-9]$/.test(s);
};

const getValidationType = (el) => {
    if (!(el instanceof HTMLInputElement || el instanceof HTMLTextAreaElement)) return null;
    if (el.hasAttribute('data-no-validate')) return null;
    if (el.classList && el.classList.contains('no-validate')) return null;

    const explicit = (el.getAttribute('data-validate') || '').trim().toLowerCase();
    if (explicit === 'phone' || explicit === 'mobile') return 'phone';
    if (explicit === 'pan') return 'pan';
    if (explicit === 'gst' || explicit === 'gstin') return 'gst';

    const key = `${el.getAttribute('name') || ''} ${el.id || ''}`.toLowerCase();
    if (key.includes('gst')) return 'gst';
    if (key.includes('pan')) return 'pan';
    if (key.includes('mobile') || key.includes('phone') || key.includes('contact')) return 'phone';
    return null;
};

const setInvalidState = (el, { invalid, shake }) => {
    if (!(el instanceof HTMLElement)) return;
    el.classList.toggle('field-invalid', Boolean(invalid));
    if (!invalid || !shake) return;
    el.classList.remove('field-shake');
    void el.offsetWidth;
    el.classList.add('field-shake');
};

const formatAndValidateField = (el, { strict, shake }) => {
    const t = getValidationType(el);
    if (!t) return;

    if (t === 'phone') {
        const normalized = normalizeIndianPhoneInput(el.value);
        if (normalized !== el.value) el.value = normalized;
        setInvalidState(el, { invalid: !isValidIndianPhone(el.value, { strict }), shake });
        return;
    }

    if (t === 'pan') {
        let v = normalizeAlphaNumUpper(el.value);
        if (v.length > 10) v = v.slice(0, 10);
        if (v !== el.value) el.value = v;
        setInvalidState(el, { invalid: !isValidPan(el.value, { strict }), shake });
        return;
    }

    if (t === 'gst') {
        let v = normalizeAlphaNumUpper(el.value);
        if (v.length > 15) v = v.slice(0, 15);
        if (v !== el.value) el.value = v;
        setInvalidState(el, { invalid: !isValidGst(el.value, { strict }), shake });
    }
};

if (!window.__cidasInputValidationInit) {
    window.__cidasInputValidationInit = true;

    document.addEventListener(
        'input',
        (e) => {
            if (e && e.isComposing) return;
            const target = e.target;
            if (!(target instanceof HTMLInputElement || target instanceof HTMLTextAreaElement)) return;
            formatAndValidateField(target, { strict: false, shake: false });
        },
        true
    );

    document.addEventListener(
        'blur',
        (e) => {
            const target = e.target;
            if (!(target instanceof HTMLInputElement || target instanceof HTMLTextAreaElement)) return;
            formatAndValidateField(target, { strict: true, shake: true });
        },
        true
    );

    document.addEventListener(
        'submit',
        (e) => {
            const form = e.target;
            if (!(form instanceof HTMLFormElement)) return;
            const fields = form.querySelectorAll('input, textarea');
            fields.forEach((el) => formatAndValidateField(el, { strict: true, shake: true }));
        },
        true
    );
}
