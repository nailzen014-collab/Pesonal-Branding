/**
 * Animasi muncul saat elemen `.reveal` masuk viewport.
 *
 * Fungsi ini dipanggil ulang setiap kali elemen baru disisipkan (misalnya
 * oleh tombol "muat lebih banyak"), supaya kartu tambahan ikut terlihat.
 * Tanpa itu, elemen baru tetap `opacity: 0` karena CSS `.reveal` menunggu
 * kelas `.is-visible`.
 */

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