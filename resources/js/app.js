import Alpine from 'alpinejs';
import { initScrollAnimations, observeReveals } from './reveal';
import { initEffects, initTilt } from './effects';
import { initTheme } from './theme';
import { initTypewriters } from './typewriter';
import { initSmoothScroll } from './smooth-scroll';
import { initScrollUI } from './scroll-ui';

const PAGE_TRANSITION_STORAGE_KEY = 'page-transition:enter';
const PAGE_TRANSITION_COVER_DURATION = 420;

function initPageTransitions() {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        return;
    }

    let navigationPending = false;

    document.addEventListener('click', (event) => {
        if (event.defaultPrevented
            || event.button !== 0
            || event.metaKey
            || event.ctrlKey
            || event.shiftKey
            || event.altKey
            || !(event.target instanceof Element)) {
            return;
        }

        const link = event.target.closest('a[href]');

        if (!link
            || link.hasAttribute('download')
            || link.closest('[data-no-transition]')
            || (link.target && link.target.toLowerCase() !== '_self')) {
            return;
        }

        const destination = new URL(link.href, window.location.href);

        if (destination.origin !== window.location.origin
            || !['http:', 'https:'].includes(destination.protocol)
            || destination.href === window.location.href
            || (destination.pathname === window.location.pathname
                && destination.search === window.location.search
                && destination.hash !== '')) {
            return;
        }

        event.preventDefault();

        if (navigationPending) {
            return;
        }

        navigationPending = true;

        try {
            sessionStorage.setItem(PAGE_TRANSITION_STORAGE_KEY, '1');
        } catch (error) {
            console.error('Gagal menyimpan status transisi halaman:', error);
            window.location.assign(destination.href);

            return;
        }

        document.documentElement.classList.add('page-transition-covering');
        const shouldGoBack = link.hasAttribute('data-back-link') && window.history.length > 1;

        window.setTimeout(() => {
            if (shouldGoBack) {
                window.history.back();

                return;
            }

            window.location.assign(destination.href);
        }, PAGE_TRANSITION_COVER_DURATION);
    }, true);
}

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

document.addEventListener('DOMContentLoaded', () => {
    initPageTransitions();
    initSmoothScroll();
    observeReveals();
    initScrollAnimations();
    initLoadMore();
    initEffects();
    initTheme();
    initTypewriters();
    initScrollUI();

    // Penanda untuk safety net di layouts/public.blade.php: bila bundel ini
    // gagal dieksekusi, script inline memaksa konten `.reveal` tampil
    // sehingga halaman tidak pernah kosong.
    document.documentElement.dataset.jsReady = '1';
});

window.Alpine = Alpine;

Alpine.start();