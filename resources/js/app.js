import Alpine from 'alpinejs';
import { observeReveals } from './reveal';
import { initEffects } from './effects';
import { initTypewriters } from './typewriter';

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
    observeReveals();
    initLoadMore();
    initEffects();
    initTypewriters();
});

window.Alpine = Alpine;

Alpine.start();