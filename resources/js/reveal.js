/**
 * Animasi scroll dua arah:
 * - `.reveal` di dalam `.scroll-stage` muncul dari bawah saat masuk viewport,
 *   memudar saat lewat ke atas, dan muncul lagi bila digulir balik ke bawah;
 * - `.scroll-stage` diredupkan + tepinya diberi kabut saat keluar viewport.
 *
 * Fungsi ini dipanggil ulang setiap kali elemen baru disisipkan (misalnya
 * oleh tombol "muat lebih banyak"), supaya kartu tambahan ikut dianimasikan.
 */

/** Sudut (px) sebagai jarak munculnya elemen dari bawah. */
const SHIFT = 28;

/** Sisa margin: elemen dianggap masuk setelah 10% dari bawah layar. */
const ENTER_MARGIN = 0.1;

/** Elemen mulai memudar setelah lewat 25% dari atas layar. */
const LEAVE_MARGIN = 0.25;

/** Elemen yang sudah diikat listener-nya. */
const bound = new WeakSet();

let schedulerRunning = false;

/**
 * Hitung jeda masuk bergiliran (stagger) berdasarkan posisi elemen di
 * antara saudara `.reveal` lain dalam induk yang sama. Kartu dalam satu
 * grid muncul satu per satu, bukan serentak.
 *
 * @param {HTMLElement} element elemen `.reveal`.
 * @returns {number} jeda dalam milidetik.
 */
function staggerDelay(element) {
    const peers = [...(element.parentElement?.children ?? [])]
        .filter((child) => child.classList.contains('reveal'));

    return Math.min(Math.max(peers.indexOf(element), 0), 8) * 60;
}

/**
 * Jalankan pembaruan elemen dalam satu rAF agar penulisan style tidak
 * terjadi di tengah event scroll.
 *
 * @param {HTMLElement} element elemen `.reveal`.
 */
function updateReveal(element) {
    const rect = element.getBoundingClientRect();
    const viewport = window.innerHeight;
    const entered = rect.top < viewport * (1 - ENTER_MARGIN);
    const passed = rect.bottom < viewport * LEAVE_MARGIN;

    if (entered && !passed) {
        const delay = staggerDelay(element);

        element.classList.remove('is-passed');
        element.style.transitionDelay = delay ? `${delay}ms` : '';

        if (!element.classList.contains('is-visible')) {
            element.classList.add('is-visible');
        }
    } else if (passed) {
        // Sudah lewat atas layar: meluncur turun sedikit & memudar ke
        // balik kabut, lalu muncul lagi dari sana saat digulir ke bawah.
        element.classList.remove('is-visible');
        element.classList.add('is-passed');
        element.style.transitionDelay = '';
        element.style.setProperty('--reveal-shift', '-16px');
    } else if (element.classList.contains('is-visible')) {
        // Masih di layar, tapi sudah mendekati tepi atas: memudar pelan.
        element.classList.add('is-passed');
        element.style.transitionDelay = '';
    }
}

/**
 * Elemen baru dari server belum punya class; posisi awalnya dibuat sesuai
 * arah datangnya (bawah → naik).
 */
function primeReveal(element) {
    const rect = element.getBoundingClientRect();

    if (rect.top < window.innerHeight * 0.5) {
        element.classList.add('is-visible');
        element.style.setProperty('--reveal-shift', '0px');

        return;
    }

    element.classList.remove('is-visible');
    element.style.setProperty('--reveal-shift', `${SHIFT}px`);
}

function updateStage(stage) {
    const rect = stage.getBoundingClientRect();
    const viewport = window.innerHeight;
    const leavingTop = rect.bottom < viewport * 0.45;
    const leavingBottom = rect.top > viewport * 0.55;

    stage.classList.toggle('is-leaving-top', leavingTop);
    stage.classList.toggle('is-leaving-bottom', leavingBottom);
    stage.classList.toggle('is-out', leavingTop || leavingBottom);
}

function updateAll() {
    document.querySelectorAll('.reveal').forEach(updateReveal);
    document.querySelectorAll('.scroll-stage').forEach(updateStage);
}

function schedule() {
    if (schedulerRunning) {
        return;
    }

    schedulerRunning = true;

    requestAnimationFrame(() => {
        schedulerRunning = false;
        updateAll();
    });
}

function onScroll() {
    schedule();
}

/**
 * Daftarkan anime scroll untuk elemen di dalam `scope`.
 *
 * @param {ParentNode} [scope] cukup elemen induknya saja. Default: dokumen.
 * @returns {number} jumlah elemen `.reveal` yang tersedia.
 */
export function observeReveals(scope = document) {
    const elements = scope.querySelectorAll('.reveal');

    if (!elements.length) {
        return 0;
    }

    if (!('requestAnimationFrame' in window)
        || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        elements.forEach((element) => element.classList.add('is-visible'));

        return elements.length;
    }

    elements.forEach((element) => {
        if (bound.has(element)) {
            return;
        }

        bound.add(element);
        primeReveal(element);
    });

    if (!document.documentElement.dataset.revealBound) {
        document.documentElement.dataset.revealBound = '1';
        window.addEventListener('scroll', onScroll, { passive: true });
        window.addEventListener('resize', onScroll, { passive: true });
    }

    schedule();

    return elements.length;
}