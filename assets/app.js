import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

// assets/app.js

function debounce(fn, delay = 300) {
    let t;
    return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), delay); };
}

function serializeForm(form) {
    return new URLSearchParams(new FormData(form)).toString();
}

function bindAjaxListForms(root = document) {
    root.querySelectorAll('form[data-ajax-list]:not([data-ajax-bound])').forEach(form => {
        const targetSel = form.getAttribute('data-target');
        const delay = parseInt(form.getAttribute('data-delay') || '300', 10);
        const container = targetSel ? document.querySelector(targetSel) : null;
        if (!container) return; // pas de conteneur -> on ne branche pas

        form.setAttribute('data-ajax-bound', '1'); // éviter double-binding
        let currentCtrl = null;

        const fetchAndUpdate = () => {
            const url = new URL(form.getAttribute('action') || window.location.href, window.location.origin);
            url.search = serializeForm(form);
            url.searchParams.set('_fragment', '1'); // force la branche fragment

            history.replaceState(null, '', url.toString());

            if (currentCtrl) currentCtrl.abort();
            currentCtrl = new AbortController();

            fetch(url.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                signal: currentCtrl.signal
            })
                .then(r => r.text())
                .then(html => { container.innerHTML = html; })
                .catch(err => { if (err.name !== 'AbortError') console.error(err); });
        };

        const debouncedFetch = debounce(fetchAndUpdate, delay);

        form.addEventListener('submit', (e) => {
            e.preventDefault();
            fetchAndUpdate();
        });

        form.querySelectorAll('input[type="text"], input[type="search"], input[data-autosubmit]')
            .forEach(input => input.addEventListener('input', debouncedFetch));

        form.querySelectorAll('select, input[type="checkbox"], input[type="radio"], input[type="date"]')
            .forEach(el => el.addEventListener('change', fetchAndUpdate));
    });
}

async function refreshFragment(url, selector) {
    const u = new URL(url, window.location.origin);
    u.searchParams.set('_fragment', '1');
    const resp = await fetch(u.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    const html = await resp.text();
    const tmp  = document.createElement('div');
    tmp.innerHTML = html;
    const fresh   = tmp.querySelector(selector);
    const current = document.querySelector(selector);
    if (fresh && current) {
        current.replaceWith(fresh);
        if (typeof initAll === 'function') initAll();
    }
}

function clearFormErrors(form) {
    form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    form.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
}

function showFormErrors(form, errors) {
    Object.entries(errors || {}).forEach(([field, messages]) => {

        const input =
            form.querySelector(`[name$="[${CSS.escape(field)}]"]`) ||
            form.querySelector(`[name="${CSS.escape(field)}"]`);
        if (!input) return;
        input.classList.add('is-invalid');
        const fb = document.createElement('div');
        fb.className = 'invalid-feedback';
        fb.textContent = messages.join(' ');
        input.insertAdjacentElement('afterend', fb);
    });
}

function bindAjaxSubmitForms(root = document) {
    root.querySelectorAll('form[data-ajax-submit]:not([data-ajax-bound])').forEach(form => {
        form.setAttribute('data-ajax-bound', '1');

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            clearFormErrors(form);

            const res = await fetch(form.action || window.location.href, {
                method: (form.method || 'POST').toUpperCase(),
                body: new FormData(form),
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            if (res.status === 422) {
                let payload = {};
                try { payload = await res.json(); } catch {}
                showFormErrors(form, payload.errors);
                return;
            }

            if (!res.ok && res.status !== 204) {
                alert('Une erreur est survenue.');
                return;
            }

            // ✅ Fermer la/les lignes d'édition ouvertes
            const closeSel = form.dataset.closeTarget;
            if (closeSel) {
                document.querySelectorAll(closeSel).forEach(tr => tr.hidden = true);
            } else {
                document.querySelectorAll('.row-edit:not([hidden])').forEach(tr => tr.hidden = true);
            }

            const refreshUrl = form.dataset.refreshUrl;
            const refreshSelector = form.dataset.refreshSelector;
            if (refreshUrl && refreshSelector) {
                await refreshFragment(refreshUrl, refreshSelector);
            }

            if (form.dataset.clearOnSuccess === '1') {
                form.reset();
            }
        });
    });
}

document.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-edit-toggle]');
    if (!btn) return;
    const sel = btn.getAttribute('data-target');
    const row = document.querySelector(sel);
    if (row) row.hidden = !row.hidden;
});

function initAll(root = document) {
    bindAjaxListForms(root);
    bindAjaxSubmitForms(root);
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => initAll());
} else {
    initAll();
}
window.addEventListener('pageshow', () => initAll());