import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// ─── DOM Ready ──────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    initMobileMenu();
    initGlobalSearch();
    initLoadingButtons();
    initBackToTop();
    initFlashAutoDismiss();
    initLazyImages();
    initFocusRing();
});

// ─── 1. Mobile Menu ─────────────────────────────────────────────────────────────────
function initMobileMenu() {
    const btn        = document.getElementById('mobile-menu-btn');
    const menu       = document.getElementById('mobile-menu');
    const hamburger  = document.getElementById('hamburger-icon');
    const closeIcon  = document.getElementById('close-icon');
    if (!btn || !menu) return;

    btn.addEventListener('click', () => {
        const isOpen = !menu.classList.contains('hidden');
        menu.classList.toggle('hidden', isOpen);
        hamburger?.classList.toggle('hidden', !isOpen);
        closeIcon?.classList.toggle('hidden', isOpen);
        btn.setAttribute('aria-expanded', String(!isOpen));
        btn.setAttribute('aria-label', isOpen ? 'Buka menu navigasi' : 'Tutup menu navigasi');
    });

    // Close when clicking outside
    document.addEventListener('click', (e) => {
        if (!btn.contains(e.target) && !menu.contains(e.target)) {
            menu.classList.add('hidden');
            hamburger?.classList.remove('hidden');
            closeIcon?.classList.add('hidden');
            btn.setAttribute('aria-expanded', 'false');
        }
    });
}

// ─── 2. Global Search with Debounce & Suggestions ──────────────────────────
function initGlobalSearch() {
    const input   = document.getElementById('global-search');
    const box     = document.getElementById('search-suggestions');
    const content = document.getElementById('search-suggestions-content');
    const noRes   = document.getElementById('search-no-results');
    const noResQ  = document.getElementById('search-no-results-q');
    const loading = document.getElementById('search-loading');
    if (!input || !box) return;

    let timer = null;
    let activeIndex = -1;

    const show = () => { box.classList.remove('hidden'); input.setAttribute('aria-expanded', 'true'); };
    const hide = () => { box.classList.add('hidden'); input.setAttribute('aria-expanded', 'false'); activeIndex = -1; };

    input.addEventListener('input', () => {
        clearTimeout(timer);
        const q = input.value.trim();
        if (q.length < 2) { hide(); return; }
        loading.classList.remove('hidden');
        content.innerHTML = '';
        noRes.classList.add('hidden');
        show();
        timer = setTimeout(() => fetchSuggestions(q), 280);
    });

    input.addEventListener('keydown', (e) => {
        const items = box.querySelectorAll('[role="option"]');
        if (e.key === 'ArrowDown') { e.preventDefault(); activeIndex = Math.min(activeIndex + 1, items.length - 1); setActive(items, activeIndex); }
        else if (e.key === 'ArrowUp') { e.preventDefault(); activeIndex = Math.max(activeIndex - 1, -1); setActive(items, activeIndex); }
        else if (e.key === 'Enter' && activeIndex >= 0) { e.preventDefault(); items[activeIndex]?.click(); }
        else if (e.key === 'Escape') { hide(); input.blur(); }
        else if (e.key === 'Enter' && activeIndex < 0) {
            const q = input.value.trim();
            if (q.length >= 2) window.location.href = `/search?q=${encodeURIComponent(q)}`;
        }
    });

    document.addEventListener('click', (e) => {
        if (!input.contains(e.target) && !box.contains(e.target)) hide();
    });

    async function fetchSuggestions(q) {
        try {
            const res = await axios.get('/search/suggest', { params: { q } });
            const { results } = res.data;
            loading.classList.add('hidden');
            content.innerHTML = '';
            activeIndex = -1;

            if (!results.length) {
                noResQ.textContent = q;
                noRes.classList.remove('hidden');
                return;
            }

            results.forEach((item, i) => {
                const a = document.createElement('a');
                a.href = item.url;
                a.role = 'option';
                a.id   = `suggestion-${i}`;
                a.className = 'flex items-center gap-3 px-4 py-2.5 text-sm text-gray-800 hover:bg-gray-50 focus:bg-gray-50 focus:outline-none transition-colors';
                a.innerHTML = `<span class="text-base" aria-hidden="true">${item.icon}</span><span>${escapeHtml(item.label)}</span><span class="ml-auto text-xs text-gray-400">${item.type}</span>`;
                content.appendChild(a);
            });

            // "See all results" footer
            const all = document.createElement('a');
            all.href = `/search?q=${encodeURIComponent(q)}`;
            all.className = 'block border-t border-gray-100 px-4 py-2.5 text-center text-xs font-semibold text-brand-600 hover:bg-gray-50 transition-colors';
            all.textContent = `Lihat semua hasil untuk "${q}"`;
            content.appendChild(all);
        } catch {
            loading.classList.add('hidden');
        }
    }

    function setActive(items, idx) {
        items.forEach((el, i) => {
            el.classList.toggle('bg-gray-100', i === idx);
            if (i === idx) el.setAttribute('aria-selected', 'true');
            else el.removeAttribute('aria-selected');
        });
        input.setAttribute('aria-activedescendant', idx >= 0 ? `suggestion-${idx}` : '');
    }

    function escapeHtml(str) {
        return str.replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
    }
}

// ─── 3. Loading Buttons (forms) ──────────────────────────────────────────────────
function initLoadingButtons() {
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', (e) => {
            // Use the actual submitter (works even when a form has multiple
            // [type=submit] buttons, e.g. "Tinjau" / "Abaikan") instead of
            // always grabbing the first submit button in the DOM.
            const btn = e.submitter || form.querySelector('[type="submit"]');
            if (!btn || btn.dataset.noLoading) return;
            const original = btn.innerHTML;
            btn.dataset.original = original;
            // Defer disabling to the next tick: disabling the submitter
            // synchronously inside the submit handler can strip its
            // name/value pair from the request and silently cancel the
            // submission in some browsers.
            setTimeout(() => {
                btn.disabled = true;
                btn.innerHTML = `<span class="inline-flex items-center gap-2">
                    <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    Memproses...</span>`;
                btn.setAttribute('aria-busy', 'true');
            }, 0);
        });
    });
}

// ─── 4. Back to Top ───────────────────────────────────────────────────────────────────
function initBackToTop() {
    const btn = document.getElementById('back-to-top');
    if (!btn) return;
    window.addEventListener('scroll', () => {
        btn.classList.toggle('hidden', window.scrollY < 400);
        btn.classList.toggle('flex', window.scrollY >= 400);
    }, { passive: true });
}

// ─── 5. Flash Auto-Dismiss ──────────────────────────────────────────────────────────
function initFlashAutoDismiss() {
    const container = document.getElementById('flash-container');
    if (!container) return;
    setTimeout(() => {
        container.querySelectorAll('.flash-msg').forEach(msg => {
            msg.style.transition = 'opacity 0.4s, transform 0.4s';
            msg.style.opacity = '0';
            msg.style.transform = 'translateX(100%)';
            setTimeout(() => msg.remove(), 400);
        });
    }, 4000);
}

// ─── 6. Lazy Image Loading ───────────────────────────────────────────────────────────
function initLazyImages() {
    if (!('IntersectionObserver' in window)) return;
    const obs = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                if (img.dataset.src) { img.src = img.dataset.src; img.removeAttribute('data-src'); }
                obs.unobserve(img);
            }
        });
    }, { rootMargin: '200px' });
    document.querySelectorAll('img[data-src]').forEach(img => obs.observe(img));
}

// ─── 7. Focus ring (keyboard vs mouse) ────────────────────────────────────────────
function initFocusRing() {
    let usingKeyboard = false;
    document.addEventListener('keydown', (e) => { if (e.key === 'Tab') usingKeyboard = true; });
    document.addEventListener('mousedown', () => { usingKeyboard = false; });
    document.addEventListener('focusin', (e) => {
        if (!usingKeyboard) e.target.classList.add('no-focus-ring');
        else e.target.classList.remove('no-focus-ring');
    });
}

// ─── 8. User Menu Dropdown ─────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    const btn      = document.getElementById('user-menu-btn');
    const menu     = document.getElementById('user-menu');
    const chevron  = document.getElementById('user-menu-chevron');
    if (!btn || !menu) return;

    btn.addEventListener('click', (e) => {
        e.stopPropagation();
        const isOpen = !menu.classList.contains('hidden');
        menu.classList.toggle('hidden', isOpen);
        btn.setAttribute('aria-expanded', String(!isOpen));
        chevron?.classList.toggle('rotate-180', !isOpen);
    });

    document.addEventListener('click', (e) => {
        const container = document.getElementById('user-menu-container');
        if (container && !container.contains(e.target)) {
            menu.classList.add('hidden');
            btn.setAttribute('aria-expanded', 'false');
            chevron?.classList.remove('rotate-180');
        }
    });

    // Keyboard: Escape closes menu
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !menu.classList.contains('hidden')) {
            menu.classList.add('hidden');
            btn.setAttribute('aria-expanded', 'false');
            btn.focus();
        }
    });
});

// ═══════════════════════════════════════════════
// PHASE 4 — FORMS: validation, preview, counters, autosave
// ═══════════════════════════════════════════════

document.addEventListener('DOMContentLoaded', () => {
    initImagePreview();
    initCharCounters();
    initRealtimeValidation();
    initAutoFocusError();
    initAutosave();
    initSlugPreview();
    initSEOCounter();
});

// ─── Image Preview ────────────────────────────────────────
function initImagePreview() {
    document.querySelectorAll('input[type=file][accept*=image]').forEach(input => {
        input.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (!file || !file.type.startsWith('image/')) return;
            const reader = new FileReader();
            reader.onload = (ev) => {
                // Look for existing preview img
                const wrap = input.closest('[data-preview-wrap]') || input.closest('label') || input.parentElement;
                let preview = wrap.closest('div')?.querySelector('img[data-preview], img.preview-img');
                if (!preview) {
                    // Create new preview
                    preview = document.createElement('img');
                    preview.dataset.preview = '1';
                    preview.className = 'preview-img mt-3 h-28 w-40 rounded-xl object-cover shadow-sm border border-gray-200';
                    preview.alt = 'Preview gambar';
                    wrap.after(preview);
                }
                preview.src = ev.target.result;
                preview.classList.remove('hidden');

                // Show file size
                const size = (file.size / 1024).toFixed(0);
                let sizeEl = wrap.closest('div')?.querySelector('.file-size-label');
                if (!sizeEl) {
                    sizeEl = document.createElement('p');
                    sizeEl.className = 'file-size-label mt-1 text-xs text-gray-400';
                    preview.after(sizeEl);
                }
                sizeEl.textContent = `${file.name} (${size} KB)`;
            };
            reader.readAsDataURL(file);
        });
    });
}

// ─── Character Counters ────────────────────────────────────────
function initCharCounters() {
    document.querySelectorAll('textarea[data-counter], input[data-counter]').forEach(el => {
        const max = el.getAttribute('maxlength') || el.dataset.counter;
        if (!max) return;
        let counter = el.parentElement.querySelector('.char-counter');
        if (!counter) {
            counter = document.createElement('span');
            counter.className = 'char-counter text-xs text-gray-400 float-right mt-0.5';
            el.after(counter);
        }
        const update = () => {
            const len = el.value.length;
            counter.textContent = `${len} / ${max}`;
            counter.className = `char-counter text-xs float-right mt-0.5 ${len > max * 0.9 ? 'text-red-500 font-semibold' : len > max * 0.7 ? 'text-yellow-500' : 'text-gray-400'}`;
        };
        el.addEventListener('input', update);
        update();
    });
}

// ─── Realtime Validation ───────────────────────────────────────
function initRealtimeValidation() {
    document.querySelectorAll('form[data-validate]').forEach(form => {
        form.querySelectorAll('input[required], textarea[required], select[required]').forEach(field => {
            field.addEventListener('blur', () => { field._touched = true; validateField(field); });
            field.addEventListener('input', () => { if (field._touched) validateField(field); });
        });
        form.addEventListener('submit', (e) => {
            let firstInvalid = null;
            form.querySelectorAll('input[required], textarea[required], select[required]').forEach(field => {
                field._touched = true;
                validateField(field);
                if (!field.checkValidity() && !firstInvalid) firstInvalid = field;
            });
            if (firstInvalid) {
                e.preventDefault();
                firstInvalid.focus();
                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    });
}

function validateField(field) {
    let err = field.parentElement.querySelector('.inline-error');
    if (field.checkValidity()) {
        field.classList.remove('border-red-500'); field.classList.add('border-green-500');
        err?.remove();
    } else {
        field.classList.add('border-red-500'); field.classList.remove('border-green-500');
        if (!err) {
            err = document.createElement('p');
            err.className = 'inline-error text-xs text-red-500 mt-1';
            field.after(err);
        }
        err.textContent = getErrMsg(field);
        err.setAttribute('role', 'alert');
        field.setAttribute('aria-invalid', 'true');
        field.setAttribute('aria-describedby', (err.id = 'err-' + field.name));
    }
}

function getErrMsg(f) {
    if (f.validity.valueMissing)    return 'Wajib diisi.';
    if (f.validity.typeMismatch)    return `Format ${f.type === 'email' ? 'email' : f.type} tidak valid.`;
    if (f.validity.tooShort)        return `Minimal ${f.minLength} karakter.`;
    if (f.validity.tooLong)         return `Maksimal ${f.maxLength} karakter.`;
    if (f.validity.patternMismatch) return f.title || 'Format tidak valid.';
    return 'Nilai tidak valid.';
}

// ─── Auto-focus first error ─────────────────────────────────
function initAutoFocusError() {
    const errEl = document.querySelector('[aria-invalid=true], .border-red-500');
    if (errEl) {
        errEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
        setTimeout(() => errEl.focus?.(), 300);
    }
}

// ─── Autosave (Phase 6) ─────────────────────────────────────
function initAutosave() {
    const form = document.querySelector('form[data-autosave]');
    if (!form) return;
    const key = form.dataset.autosave;
    const indicator = document.getElementById('autosave-indicator');

    // Restore saved data (only if value is empty to avoid overwriting server data)
    try {
        const saved = JSON.parse(localStorage.getItem(key) || 'null');
        if (saved) {
            Object.entries(saved).forEach(([name, value]) => {
                const el = form.querySelector(`[name="${name}"]`);
                if (el && !el.value && el.type !== 'file' && el.type !== 'hidden') el.value = value;
            });
            if (indicator) { indicator.textContent = 'Draft tersimpan'; indicator.classList.remove('hidden'); }
        }
    } catch {}

    // Save every 30s
    const save = () => {
        const data = {};
        form.querySelectorAll('input:not([type=file]):not([type=hidden]):not([type=submit]), textarea, select').forEach(el => {
            if (el.name) data[el.name] = el.value;
        });
        localStorage.setItem(key, JSON.stringify(data));
        if (indicator) {
            indicator.textContent = '✓ Draft disimpan otomatis ' + new Date().toLocaleTimeString('id-ID', {hour:'2-digit',minute:'2-digit'});
            indicator.classList.remove('hidden');
        }
    };

    setInterval(save, 30000);
    form.querySelectorAll('textarea, input:not([type=file])').forEach(el => {
        el.addEventListener('input', () => clearTimeout(el._saveTimer) || (el._saveTimer = setTimeout(save, 5000)));
    });
    form.addEventListener('submit', () => localStorage.removeItem(key));
}

// ─── Slug Preview (Phase 6) ─────────────────────────────
function initSlugPreview() {
    const titleInput = document.getElementById('article-title-input');
    const slugPreview = document.getElementById('slug-preview');
    if (!titleInput || !slugPreview) return;

    titleInput.addEventListener('input', () => {
        const slug = titleInput.value.toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '')
            .trim().replace(/\s+/g, '-')
            .substring(0, 80);
        slugPreview.textContent = slug || 'judul-artikel-kamu';
    });
}

// ─── SEO counters ─────────────────────────────────
function initSEOCounter() {
    [
        { id: 'meta_title', max: 60 },
        { id: 'meta_description', max: 160 },
        { id: 'meta_keywords', max: 200 },
    ].forEach(({ id, max }) => {
        const el = document.getElementById(id);
        const cnt = document.getElementById(id + '_count');
        if (!el || !cnt) return;
        const update = () => {
            const len = el.value.length;
            cnt.textContent = len + ' / ' + max;
            cnt.className = `text-xs ${len > max ? 'text-red-500' : len > max * 0.85 ? 'text-yellow-500' : 'text-gray-400'}`;
        };
        el.addEventListener('input', update);
        update();
    });
}

// ═══════════════════════════════════════════════
// PHASE 7 — DARK MODE, TOOLTIPS, ANIMATIONS
// PHASE 8 — MEDIA: lazy images, blur, fallback
// ═══════════════════════════════════════════════

// ─── Dark Mode ─────────────────────────────────────────────────────────────────
// Defaults to light mode for first-time visitors; dark mode only activates
// when the user explicitly toggles it (persisted in localStorage). We no
// longer auto-follow the OS color-scheme preference.
(function initDarkMode() {
    if (localStorage.getItem('si-pedia-theme') === 'dark') {
        document.documentElement.classList.add('dark');
    }
})();

document.addEventListener('DOMContentLoaded', () => {
    // Toggle button click
    document.querySelectorAll('[data-dark-toggle]').forEach(btn => {
        btn.addEventListener('click', () => {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('si-pedia-theme', isDark ? 'dark' : 'light');
            // Update aria-label
            btn.setAttribute('aria-label', isDark ? 'Switch to light mode' : 'Switch to dark mode');
            btn.setAttribute('aria-pressed', String(isDark));
        });
        // Set initial aria state
        const isDark = document.documentElement.classList.contains('dark');
        btn.setAttribute('aria-pressed', String(isDark));
        btn.setAttribute('aria-label', isDark ? 'Switch to light mode' : 'Switch to dark mode');
    });
});

// ─── Phase 8: Image blur placeholder ─────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('img[loading=lazy]').forEach(img => {
        if (img.complete) return;
        img.classList.add('img-blur-load');
        img.addEventListener('load', () => img.classList.add('loaded'), { once: true });
        img.addEventListener('error', () => {
            // Fallback: replace broken image with placeholder
            const w = img.getAttribute('width') || img.offsetWidth || 400;
            const h = img.getAttribute('height') || img.offsetHeight || 250;
            img.src = `https://placehold.co/${w}x${h}/f1f5f9/94a3b8?text=SI-Pedia`;
            img.alt = img.alt || 'Gambar tidak tersedia';
            img.classList.remove('img-blur-load');
        });
    });
});

// ─── Phase 7: Keyboard shortcut for dark mode (Ctrl+Shift+D) ─────────────
document.addEventListener('keydown', (e) => {
    if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'D') {
        e.preventDefault();
        const isDark = document.documentElement.classList.toggle('dark');
        localStorage.setItem('si-pedia-theme', isDark ? 'dark' : 'light');
    }
});
