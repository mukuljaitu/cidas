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
