/**
 * Animasi scroll dua arah:
 * - `.reveal` di dalam `.scroll-stage` muncul dari bawah saat masuk viewport,
 *   memudar saat lewat ke atas, dan muncul lagi bila digulir balik ke bawah;
 * - `.scroll-stage` diredupkan + tepinya diberi kabut saat keluar viewport.
 *
 * Semua elemen di-cache sekali saat didaftarkan. Setiap frame scroll hanya
 * membaca posisi elemen dalam satu fase lalu menulis kelasnya di fase kedua,
 * sehingga tidak ada layout berulang (read-write-read) di tengah scroll.
 */

/** Sudut (px) sebagai jarak munculnya elemen dari bawah. */
const SHIFT = 28;

/** Sisa margin: elemen dianggap masuk setelah 10% dari bawah layar. */
const ENTER_MARGIN = 0.1;

/** Elemen mulai memudar setelah lewat 25% dari atas layar. */
const LEAVE_MARGIN = 0.25;

/** Elemen yang sudah diikat listener-nya. */
const bound = new WeakSet();

/** Daftar elemen aktif; dicache agar tidak query DOM tiap frame. */
const reveals = [];
let stages = [];

let schedulerRunning = false;

/** Posisi scroll saat pembaruan terakhir, untuk melewati frame yang sama. */
let lastScrollY = -1;

/** Paksa pembaruan meski posisi scroll belum berubah (resize / elemen baru). */
let forceUpdate = true;

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

/**
 * Baca semua posisi lebih dulu, lalu terapkan kelasnya. Dua fase terpisah
 * mencegah browser menghitung ulang layout di antara pembacaan dan
 * penulisan untuk setiap elemen.
 */
function updateAll() {
    if (!forceUpdate && window.scrollY === lastScrollY) {
        return;
    }

    forceUpdate = false;
    lastScrollY = window.scrollY;

    const viewport = window.innerHeight;
    const enterLine = viewport * (1 - ENTER_MARGIN);
    const leaveLine = viewport * LEAVE_MARGIN;

    const revealStates = reveals.map((element) => {
        const rect = element.getBoundingClientRect();

        return {
            element,
            entered: rect.top < enterLine,
            passed: rect.bottom < leaveLine,
        };
    });

    const stageStates = stages.map((stage) => {
        const rect = stage.getBoundingClientRect();

        return {
            stage,
            leavingTop: rect.bottom < viewport * 0.45,
            leavingBottom: rect.top > viewport * 0.55,
        };
    });

    // Fase tulis: hanya ubah kelas saat statusnya benar-benar berubah.
    revealStates.forEach(({ element, entered, passed }) => {
        const isVisible = element.classList.contains('is-visible');
        const isPassed = element.classList.contains('is-passed');

        if (entered && !passed) {
            if (isPassed) {
                element.classList.remove('is-passed');
            }

            if (!isVisible) {
                const delay = staggerDelay(element);

                element.style.transitionDelay = delay ? `${delay}ms` : '';
                element.classList.add('is-visible');
            }
        } else if (passed) {
            if (isVisible || !isPassed) {
                element.classList.remove('is-visible');
                element.classList.add('is-passed');
                element.style.transitionDelay = '';
                element.style.setProperty('--reveal-shift', '-16px');
            }
        } else if (isVisible && !isPassed) {
            // Masih di layar, tapi sudah mendekati tepi atas: memudar pelan.
            element.classList.add('is-passed');
            element.style.transitionDelay = '';
        }
    });

    stageStates.forEach(({ stage, leavingTop, leavingBottom }) => {
        const isOut = leavingTop || leavingBottom;

        if (stage.classList.contains('is-leaving-top') !== leavingTop) {
            stage.classList.toggle('is-leaving-top', leavingTop);
        }

        if (stage.classList.contains('is-leaving-bottom') !== leavingBottom) {
            stage.classList.toggle('is-leaving-bottom', leavingBottom);
        }

        if (stage.classList.contains('is-out') !== isOut) {
            stage.classList.toggle('is-out', isOut);
        }
    });
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

function onResize() {
    forceUpdate = true;
    schedule();
}

/**
 * Refresh daftar `.scroll-stage` (murah, hanya saat konten baru dimuat).
 */
function collectStages() {
    stages = [...document.querySelectorAll('.scroll-stage')];
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
        reveals.push(element);
        primeReveal(element);
    });

    collectStages();

    if (!document.documentElement.dataset.revealBound) {
        document.documentElement.dataset.revealBound = '1';
        window.addEventListener('scroll', onScroll, { passive: true });
        window.addEventListener('resize', onResize, { passive: true });
    }

    forceUpdate = true;
    schedule();

    return elements.length;
}
