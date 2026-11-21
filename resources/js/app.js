import Alpine from 'alpinejs';
import { initScrollAnimations, observeReveals } from './reveal';
import { initEffects, initTilt } from './effects';
import { initTheme } from './theme';
import { initTypewriters } from './typewriter';
import { initSmoothScroll } from './smooth-scroll';
import { initScrollUI } from './scroll-ui';

/**
 * Tombol "muat lebih banyak" ala GitHub pada halaman /portofolio.
 *
 * Cara kerja: setiap klik mengambil potongan HTML dari route yang sama dengan
 * parameter `partial=1&page=N`, lalu kartunya dipindahkan ke dalam grid yang
 * sudah ada. Query filter dibuat ulang di server, jadi pencarian dan filter
 * kategori tetap berlaku untuk halaman-halaman berikutnya.
 */
function initLoadMore() {
    const wrapper = document.querySelector('[data-load-more-grid]');
    const button = wrapper?.querySelector('[data-load-more]');

    if (!wrapper || !button) {
        return;
    }

    const baseUrl = wrapper.dataset.loadMoreUrl;
    const grid = wrapper.querySelector('[data-card-grid]');
    const label = button.querySelector('span:last-of-type');
    const originalLabel = label?.textContent.trim() ?? '';
    const loadingLabel = button.dataset.loadingLabel ?? 'Memuat...';

    button.addEventListener('click', async () => {
        if (button.disabled) {
            return;
        }

        button.disabled = true;
        button.classList.add('pointer-events-none', 'opacity-70');

        if (label) {
            label.textContent = loadingLabel;
        }

        try {
            const query = button.dataset.query ? `&${button.dataset.query}` : '';
            const response = await fetch(`${baseUrl}&page=${button.dataset.nextPage}${query}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });

            if (!response.ok) {
                throw new Error(`Permintaan gagal (${response.status})`);
            }

            const holder = document.createElement('div');

            holder.innerHTML = (await response.text()).trim();

            const newGrid = holder.querySelector('[data-card-grid]');
            const cards = [...(newGrid?.children ?? [])];
            const nextControl = holder.querySelector('[data-load-more]');

            // Kartu masuk ke grid yang sudah ada supaya kelas grid tetap berlaku.
            cards.forEach((card) => grid?.append(card));

            if (nextControl) {
                button.dataset.nextPage = nextControl.dataset.nextPage;
                button.dataset.query = nextControl.dataset.query ?? button.dataset.query;

                if (label) {
                    label.textContent = originalLabel;
                }

                button.disabled = false;
                button.classList.remove('pointer-events-none', 'opacity-70');
            } else {
                // Proyek habis: blok tombol dihapus, grid tetap menampilkan semua.
                button.parentElement?.remove();
            }

            // Kartu baru ikut dianimasikan saat masuk viewport.
            observeReveals(wrapper);
            initTilt(grid ?? wrapper);
        } catch (error) {
            console.error('Gagal memuat proyek berikutnya:', error);

            if (label) {
                label.textContent = 'Gagal memuat. Coba lagi';
            }

            button.disabled = false;
            button.classList.remove('pointer-events-none', 'opacity-70');
        }
    });
}

function initAdminMotion() {
    const adminShell = document.querySelector('.admin-shell');

    if (!adminShell) {
        return;
    }

    adminShell.addEventListener('pointerdown', (event) => {
        if (!(event.target instanceof Element)) {
            return;
        }

        const target = event.target.closest('button, a, [role="button"]');

        if (!target || target.getAttribute('aria-disabled') === 'true'
            || (target instanceof HTMLButtonElement && target.disabled)) {
            return;
        }

        const bounds = target.getBoundingClientRect();
        const diameter = Math.max(bounds.width, bounds.height) * 2;
        const ripple = document.createElement('span');

        ripple.className = 'admin-click-ripple';
        ripple.setAttribute('aria-hidden', 'true');
        ripple.style.left = `${event.clientX - bounds.left}px`;
        ripple.style.top = `${event.clientY - bounds.top}px`;
        ripple.style.width = `${diameter}px`;
        ripple.style.height = `${diameter}px`;

        target.classList.add('admin-press-target');
        target.append(ripple);
        ripple.addEventListener('animationend', () => {
            ripple.remove();

            if (!target.querySelector('.admin-click-ripple')) {
                target.classList.remove('admin-press-target');
            }
        }, { once: true });
    }, { passive: true });
}

function initPublicLogoMotion() {
    if (document.documentElement.dataset.themeScope !== 'public') {
        return;
    }

    document.querySelectorAll('[data-site-logo]').forEach((logo) => {
        logo.addEventListener('click', (event) => {
            if (event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey
                || logo.target === '_blank') {
                return;
            }

            event.preventDefault();
            logo.classList.add('site-logo-transition');

            if (new URL(logo.href).pathname === window.location.pathname) {
                window.setTimeout(() => window.scrollTo({ top: 0, behavior: 'smooth' }), 360);

                return;
            }

            window.setTimeout(() => window.location.assign(logo.href), 360);
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initSmoothScroll();
    observeReveals();
    initScrollAnimations();
    initLoadMore();
    initEffects();
    initTheme();
    initTypewriters();
    initScrollUI();
    initAdminMotion();
    initPublicLogoMotion();

    // Penanda untuk safety net di layouts/public.blade.php: bila bundel ini
    // gagal dieksekusi, script inline memaksa konten `.reveal` tampil
    // sehingga halaman tidak pernah kosong.
    document.documentElement.dataset.jsReady = '1';
});

window.Alpine = Alpine;

Alpine.start();