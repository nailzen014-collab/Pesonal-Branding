/**
 * Efek visual berbasis pointer: spotlight glossy pada kartu dan kemiringan
 * 3D pada bingkai foto profil serta kartu portofolio/skills. Dinonaktifkan
 * otomatis untuk perangkat sentuh maupun saat pengguna meminta kurangnya
 * animasi.
 */

const CARD_SELECTOR = '.card';
const MAX_TILT = 6;

let pendingFrame = null;
let lastPointerEvent = null;

/** Kartu yang sedang di bawah kursor beserta cache rectangle-nya. */
let activeCard = null;
let activeRect = null;

/** Cache rectangle elemen tilt, dibuang saat scroll/resize. */
let tiltRects = new WeakMap();

/** Elemen `[data-tilt]` yang sudah dipasangi listener. */
const tiltBound = new WeakSet();

function supportsFinePointer() {
    return window.matchMedia('(pointer: fine)').matches;
}

function prefersReducedMotion() {
    return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
}

/**
 * Buang seluruh cache rectangle. Dipanggil saat halaman discroll atau
 * diresize supaya posisi kursor dihitung ulang terhadap elemen baru.
 */
function invalidateRects() {
    activeCard = null;
    activeRect = null;
    tiltRects = new WeakMap();
}

function scheduleFrame(callback) {
    if (pendingFrame === null) {
        pendingFrame = requestAnimationFrame(() => {
            pendingFrame = null;
            callback();
        });
    }
}

/**
 * Terapkan posisi spotlight ke kartu yang berada di bawah kursor.
 *
 * Rectangle hanya dibaca ulang saat kartu yang disapu berganti, sehingga
 * pergerakan pointer tidak memicu layout berulang (reflow) setiap frame.
 * Nilainya piksel, dipakai CSS untuk transform GPU murni.
 */
function applySpotlight() {
    const event = lastPointerEvent;
    const card = event?.target?.closest?.(CARD_SELECTOR);

    if (!card) {
        activeCard = null;
        activeRect = null;

        return;
    }

    if (card !== activeCard) {
        activeCard = card;
        activeRect = card.getBoundingClientRect();
    }

    const x = event.clientX - activeRect.left;
    const y = event.clientY - activeRect.top;

    card.style.setProperty('--mx', `${x.toFixed(1)}px`);
    card.style.setProperty('--my', `${y.toFixed(1)}px`);
}

/**
 * Pasang pelacak spotlight sekali di dokumen (event delegation) sehingga
 * kartu yang dimuat dinamis lewat tombol "muat lebih banyak" ikut terkena.
 * Scroll dan resize memicu hitung ulang tanpa menunggu pointer bergerak.
 */
function initSpotlight() {
    document.addEventListener('pointermove', (event) => {
        lastPointerEvent = event;
        scheduleFrame(applySpotlight);
    }, { passive: true });

    const refresh = () => {
        invalidateRects();

        if (lastPointerEvent !== null) {
            scheduleFrame(applySpotlight);
        }
    };

    window.addEventListener('scroll', refresh, { passive: true, capture: true });
    window.addEventListener('resize', refresh, { passive: true });
}

/**
 * Kemiringan 3D halus untuk elemen `[data-tilt]`, misalnya bingkai foto
 * profil dan kartu portofolio/skills. Elemen kembali lurus saat kursor
 * meninggalkannya. Elemen yang sudah terpasang dilewati agar aman dipanggil
 * ulang setelah konten baru dimuat.
 *
 * @param {ParentNode} [scope] wadah pencarian, default dokumen.
 * @returns {number} jumlah elemen yang dipasangi listener.
 */
export function initTilt(scope = document) {
    let bound = 0;

    // Pintu yang sama dengan initEffects, supaya pemanggilan ulang dari
    // tombol "muat lebih banyak" tetap menghormati perangkat sentuh.
    if (!supportsFinePointer() || prefersReducedMotion()) {
        return bound;
    }

    scope.querySelectorAll('[data-tilt]').forEach((element) => {
        if (tiltBound.has(element)) {
            return;
        }

        tiltBound.add(element);
        bound += 1;

        element.addEventListener('pointermove', (event) => {
            let rect = tiltRects.get(element);

            if (!rect) {
                rect = element.getBoundingClientRect();
                tiltRects.set(element, rect);
            }

            const offsetX = (event.clientX - rect.left) / rect.width - 0.5;
            const offsetY = (event.clientY - rect.top) / rect.height - 0.5;

            element.style.setProperty('--ry', `${(offsetX * MAX_TILT * 2).toFixed(2)}deg`);
            element.style.setProperty('--rx', `${(-offsetY * MAX_TILT * 2).toFixed(2)}deg`);
        });

        element.addEventListener('pointerleave', () => {
            element.style.setProperty('--rx', '0deg');
            element.style.setProperty('--ry', '0deg');
        });
    });

    return bound;
}

/**
 * Angka statistik berandanya menghitung naik dari nol sekali saat masuk
 * viewport. Nilai asli dari server tetap dipakai sebagai teks akhir sehingga
 * format tidak berubah.
 *
 * @param {ParentNode} [scope] wadah pencarian, default dokumen.
 */
export function initCounters(scope = document) {
    const elements = scope.querySelectorAll('[data-count-to]:not([data-count-done])');

    if (!elements.length) {
        return;
    }

    const animate = (element) => {
        element.setAttribute('data-count-done', 'true');

        const original = element.textContent.trim();
        const target = element.getAttribute('data-count-to') ?? original;

        if (!/^\d+$/.test(target)) {
            return;
        }

        const total = Number.parseInt(target, 10);
        const duration = 1400;
        const start = performance.now();

        const tick = (now) => {
            const progress = Math.min((now - start) / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3);

            element.textContent = progress === 1
                ? original
                : String(Math.round(total * eased));

            if (progress < 1) {
                requestAnimationFrame(tick);
            }
        };

        requestAnimationFrame(tick);
    };

    if (prefersReducedMotion() || !('IntersectionObserver' in window)) {
        elements.forEach((element) => element.setAttribute('data-count-done', 'true'));

        return;
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                animate(entry.target);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.4 });

    elements.forEach((element) => observer.observe(element));
}

/**
 * Jalankan seluruh efek pointer dan angka.
 *
 * @param {ParentNode} [scope] wadah pencarian, default dokumen.
 */
export function initEffects(scope = document) {
    if (prefersReducedMotion()) {
        return;
    }

    if (supportsFinePointer()) {
        initSpotlight();
        initTilt(scope);
    }

    initCounters(scope);
}
