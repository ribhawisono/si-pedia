import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// ─── DOM Ready ────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    initMobileMenu();
    initGlobalSearch();
    initLoadingButtons();
    initBackToTop();
    initFlashAutoDismiss();
    initLazyImages();
    initFocusRing();
});

// ─── 1. Mobile Menu ───────────────────────────────────────────────────────────
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

// ─── 2. Global Search with Debounce & Suggestions ─────────────────────────────
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

// ─── 3. Loading Buttons (forms) ───────────────────────────────────────────────
function initLoadingButtons() {
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', () => {
            const btn = form.querySelector('[type="submit"]');
            if (!btn || btn.dataset.noLoading) return;
            btn.disabled = true;
            const original = btn.innerHTML;
            btn.dataset.original = original;
            btn.innerHTML = `<span class="inline-flex items-center gap-2">
                <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                Memproses...</span>`;
            btn.setAttribute('aria-busy', 'true');
        });
    });
}

// ─── 4. Back to Top ───────────────────────────────────────────────────────────
function initBackToTop() {
    const btn = document.getElementById('back-to-top');
    if (!btn) return;
    window.addEventListener('scroll', () => {
        btn.classList.toggle('hidden', window.scrollY < 400);
        btn.classList.toggle('flex', window.scrollY >= 400);
    }, { passive: true });
}

// ─── 5. Flash Auto-Dismiss ────────────────────────────────────────────────────
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

// ─── 6. Lazy Image Loading ────────────────────────────────────────────────────
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

// ─── 7. Focus ring (keyboard vs mouse) ───────────────────────────────────────
function initFocusRing() {
    let usingKeyboard = false;
    document.addEventListener('keydown', (e) => { if (e.key === 'Tab') usingKeyboard = true; });
    document.addEventListener('mousedown', () => { usingKeyboard = false; });
    document.addEventListener('focusin', (e) => {
        if (!usingKeyboard) e.target.classList.add('no-focus-ring');
        else e.target.classList.remove('no-focus-ring');
    });
}

// ─── 8. User Menu Dropdown ────────────────────────────────────────────────────
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
