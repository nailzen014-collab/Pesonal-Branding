/**
 * Animasi muncul saat elemen `.reveal` masuk viewport.
 *
 * Fungsi ini dipanggil ulang setiap kali elemen baru disisipkan (misalnya
 * oleh tombol "muat lebih banyak"), supaya kartu tambahan ikut terlihat.
 * Tanpa itu, elemen baru tetap `opacity: 0` karena CSS `.reveal` menunggu
 * kelas `.is-visible`.
 */

/**
 * Hitung jeda masuk bergiliran (stagger) berdasarkan posisi elemen di
 * antara saudara `.reveal` lain dalam induk yang sama. Kartu dalam satu
 * grid muncul satu per satu, bukan serentak. Elemen tunggal mendapat
 * jeda nol.
 *
 * @param {HTMLElement} element elemen `.reveal`.
 * @returns {string} nilai `transition-delay`, misalnya `150ms`.
 */
function staggerDelay(element) {
    const parent = element.parentElement;

    if (!parent) {
        return '';
    }

    const peers = [...parent.children].filter((child) => child.classList.contains('reveal'));
    const index = peers.indexOf(element);

    if (index < 1) {
        return '';
    }

    return `${Math.min(index, 8) * 75}ms`;
}

let observer = null;

function getObserver() {
    if (observer) {
        return observer;
    }

    if (!('IntersectionObserver' in window)) {
        return null;
    }

    observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                const delay = staggerDelay(entry.target);

                // Jeda hanya untuk animasi masuk, dilepas kembali setelahnya
                // supaya transisi hover kartu tidak ikut tertunda.
                if (delay) {
                    entry.target.style.transitionDelay = delay;
                    entry.target.addEventListener('transitionend', () => {
                        entry.target.style.transitionDelay = '';
                    }, { once: true });
                }

                entry.target.classList.add('is-visible');
                observer?.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px' });

    return observer;
}

/**
 * Daftarkan elemen `.reveal` yang belum dipantau.
 *
 * @param {ParentNode} [scope] cukup elemen induknya saja. Default: dokumen.
 * @returns {number} jumlah elemen yang dipantau.
 */
export function observeReveals(scope = document) {
    const elements = scope.querySelectorAll('.reveal:not(.is-visible)');

    if (!elements.length) {
        return 0;
    }

    const instance = getObserver();

    if (!instance) {
        elements.forEach((element) => element.classList.add('is-visible'));

        return elements.length;
    }

    elements.forEach((element) => instance.observe(element));

    return elements.length;
}