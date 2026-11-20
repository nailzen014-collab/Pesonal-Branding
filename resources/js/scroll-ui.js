/**
 * Progres baca dan tombol kembali ke atas.
 *
 * Progres dihitung dari posisi gulir lalu ditulis sebagai CSS variable
 * `--scroll-progress` (0–1); elemen indikator cukup memakai `scaleX`/`scaleY`
 * sehingga tidak ada layout yang dihitung ulang tiap frame. Tombol kembali ke
 * atas muncul setelah halaman digulir cukup jauh dan statusnya diatur lewat
 * atribut `data-visible`.
 */

/** Jarak gulir (px) sebelum tombol kembali ke atas tampil. */
const BACK_TO_TOP_OFFSET = 400;

let frame = null;
let root = null;
let backToTopButtons = [];

function update() {
    const max = root.scrollHeight - window.innerHeight;
    const progress = max > 0 ? Math.min(Math.max(window.scrollY / max, 0), 1) : 0;

    root.style.setProperty('--scroll-progress', progress.toFixed(4));

    const visible = window.scrollY > BACK_TO_TOP_OFFSET ? 'true' : 'false';

    backToTopButtons.forEach((button) => {
        if (button.dataset.visible !== visible) {
            button.dataset.visible = visible;
        }
    });
}

function schedule() {
    if (frame !== null) {
        return;
    }

    frame = requestAnimationFrame(() => {
        frame = null;
        update();
    });
}

function scrollToTop() {
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    window.scrollTo({ top: 0, behavior: reduced ? 'auto' : 'smooth' });
}

export function initScrollUI() {
    root = document.documentElement;
    backToTopButtons = [...document.querySelectorAll('[data-back-to-top]')];

    window.addEventListener('scroll', schedule, { passive: true });
    window.addEventListener('resize', schedule, { passive: true });
    window.addEventListener('load', schedule, { passive: true });

    backToTopButtons.forEach((button) => {
        button.addEventListener('click', scrollToTop);
    });

    document.querySelectorAll('[data-back-link]').forEach((link) => {
        link.addEventListener('click', (event) => {
            if (event.defaultPrevented) {
                return;
            }

            // Tanpa riwayat sebelumnya (mis. dibuka dari tautan luar), link
            // tetap memakai href sebagai tujuan cadangan.
            if (window.history.length > 1) {
                event.preventDefault();
                window.history.back();
            }
        });
    });

    update();
}
