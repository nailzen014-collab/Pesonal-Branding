/**
 * Penggulungan halus untuk roda mouse / trackpad.
 *
 * Chrome dan beberapa browser lain melompat ke posisi scroll di setiap
 * notch roda, sehingga halaman terasa "patah-patah". Modul ini menahan
 * posisi gulir lalu mendekatinya bertahap (lerp) di setiap frame lewat
 * `requestAnimationFrame`, memberi gerak yang mulus tanpa library tambahan.
 *
 * Tidak diaktifkan bila:
 * - pengguna meminta kurangnya animasi,
 * - perangkat memakai layar sentuh (jari sudah menghasikan gulir yang halus),
 * - perangkat hanya punya pointer kasar (mis. tablet),
 * - ada `prefers-reduced-motion`, atau overlay admin (data-theme-scope).
 */

/** Seberapa cepat posisi mendekati target tiap frame; 0–1. */
const EASING = 0.12;

/** Sisa jarak (px) yang dianggap sudah sampai. */
const EPSILON = 0.5;

/** Roda dengan deltaMode baris/halaman dikonversi ke piksel. */
const LINE_HEIGHT = 16;
const PAGE_HEIGHT = 800;

let target = 0;
let current = 0;
let frame = null;
let running = false;

/** True selama frame ini menulis posisi scroll lewat scrollTo. */
let isWriting = false;

/** False sampai animasi benar-benar menulis posisi pertamanya. */
let written = false;

function prefersReducedMotion() {
    return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
}

function shouldEnable() {
    if (document.documentElement.dataset.themeScope === 'admin') {
        return false;
    }

    if (prefersReducedMotion()) {
        return false;
    }

    return window.matchMedia('(pointer: fine)').matches
        && window.matchMedia('(hover: hover)').matches;
}

function maxScroll() {
    return Math.max(0, document.documentElement.scrollHeight - window.innerHeight);
}

function clamp(value) {
    return Math.min(Math.max(value, 0), maxScroll());
}

function stop() {
    if (frame !== null) {
        cancelAnimationFrame(frame);
        frame = null;
    }

    running = false;
    isWriting = false;
}

function tick() {
    // Frame pertama dijalankan langsung setelah wheel, sebelum event `scroll`
    // dari scrollTo sebelumnya sempat diproses — jadi tanda tulis baru
    // dilepas mulai pada frame kedua.
    isWriting = written ? false : isWriting;
    written = true;

    // Posisi halaman berubah dari luar (scrollbar, keyboard, anchor,
    // koreksi batas atas/bawah): hentikan animasi dan ikuti posisi nyata.
    if (Math.abs(window.scrollY - current) > 1) {
        current = window.scrollY;
        target = current;
        stop();

        return;
    }

    const distance = target - current;

    if (Math.abs(distance) < EPSILON) {
        current = target;
        isWriting = true;
        window.scrollTo({ top: target, behavior: 'instant' });
        running = false;
        frame = null;

        return;
    }

    current += distance * EASING;

    // `isWriting` tetap true sampai frame berikutnya: event `scroll` yang
    // dipancarkan browser setelah scrollTo diproses asinkron, dan tidak boleh
    // dianggap sebagai gulir dari pengguna.
    isWriting = true;
    window.scrollTo({ top: current, behavior: 'instant' });

    frame = requestAnimationFrame(tick);
}

function normalizeDelta(event) {
    if (event.deltaMode === 1) {
        return event.deltaY * LINE_HEIGHT;
    }

    if (event.deltaMode === 2) {
        return event.deltaY * PAGE_HEIGHT;
    }

    return event.deltaY;
}

/**
 * Scroll apa saja yang bukan berasal dari roda (klik anchor, keyboard,
 * scrollbar) membatalkan animasi supaya posisi tidak "ditarik kembali".
 */
function syncFromPage() {
    if (isWriting) {
        // Event scroll hasil tulis kita sendiri; abaikan selama animasi.
        return;
    }

    if (running) {
        stop();
    }

    current = window.scrollY;
    target = current;
}

function onWheel(event) {
    // Biarkan zoom (ctrl + roda) dan area yang menggulir sendiri
    // (dropdown, panel admin, elemen overflow) mengikuti perilaku bawaan.
    if (event.ctrlKey || event.defaultPrevented || event.target.closest?.('[data-native-scroll]')) {
        return;
    }

    event.preventDefault();

    if (!running) {
        syncFromPage();
    }

    target = clamp(target + normalizeDelta(event));

    if (frame === null) {
        running = true;
        written = false;
        current = window.scrollY;
        frame = requestAnimationFrame(tick);
    }
}

export function initSmoothScroll() {
    if (!shouldEnable()) {
        return;
    }

    document.documentElement.classList.add('has-smooth-scroll');

    window.addEventListener('wheel', onWheel, { passive: false });
    window.addEventListener('resize', () => {
        target = clamp(target);
    }, { passive: true });

    // Klik anchor tetap memakai scroll-behavior CSS lalu disinkronkan.
    window.addEventListener('scroll', syncFromPage, { passive: true });
}
