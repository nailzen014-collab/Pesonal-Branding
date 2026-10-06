/**
 * Animasi ketik untuk beranda.
 *
 * Teks asli ditulis di HTML agar tetap terbaca mesin pencari dan pengguna
 * tanpa JavaScript. JavaScript membaca frasa dari atribut `data-phrases`
 * (dipisah `|`), mengosongkan elemennya, lalu mengetik ulang satu per satu
 * secara berulang dengan kursor berkedip di belakangnya.
 */

const TYPEWRITER_SELECTOR = '[data-typewriter]';

const TYPE_SPEED = { min: 45, max: 95 };
const DELETE_SPEED = { min: 18, max: 38 };
const HOLD_MS = 1500;
const GAP_MS = 350;

const handled = new WeakSet();

function sleep(ms) {
    return new Promise((resolve) => setTimeout(resolve, ms));
}

function randomBetween({ min, max }) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

/**
 * Kumpulkan daftar frasa dari atribut data, kembali ke isi elemen bila
 * atributnya tidak diisi.
 *
 * @param {HTMLElement} element elemen dengan `data-typewriter`.
 * @returns {string[]} daftar frasa yang tidak kosong.
 */
function readPhrases(element) {
    const raw = element.getAttribute('data-phrases') ?? element.textContent ?? '';
    const phrases = raw
        .split('|')
        .map((phrase) => phrase.trim())
        .filter(Boolean);

    return phrases.length ? phrases : [element.textContent.trim()].filter(Boolean);
}

/**
 * Siklus mengetik: ketik frasa → jeda → hapus → frasa berikutnya, selamanya.
 *
 * @param {HTMLElement} element elemen yang dianimasikan.
 * @param {string[]} phrases daftar frasa.
 */
async function typeLoop(element, phrases) {
    const node = document.createTextNode('');
    let index = 0;

    element.textContent = '';
    element.appendChild(node);
    element.classList.remove('tw-pending');
    element.classList.add('tw-ready');

    while (element.isConnected) {
        const phrase = phrases[index % phrases.length];

        for (let length = 1; length <= phrase.length; length += 1) {
            node.nodeValue = phrase.slice(0, length);
            await sleep(randomBetween(TYPE_SPEED));
        }

        await sleep(HOLD_MS);

        for (let length = phrase.length; length > 0; length -= 1) {
            node.nodeValue = phrase.slice(0, length);
            await sleep(randomBetween(DELETE_SPEED));
        }

        await sleep(GAP_MS);
        index += 1;
    }
}

/**
 * Daftarkan semua elemen `[data-typewriter]` dalam satu wadah.
 *
 * @param {ParentNode} [scope] wadah pencarian, default dokumen.
 * @returns {number} jumlah elemen yang dianimasikan.
 */
export function initTypewriters(scope = document) {
    const elements = scope.querySelectorAll(TYPEWRITER_SELECTOR);

    if (!elements.length) {
        return 0;
    }

    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    let started = 0;

    elements.forEach((element) => {
        if (handled.has(element)) {
            return;
        }

        handled.add(element);

        // Kurangnya animasi: tampilkan teks utuh tanpa kursor ketik.
        if (reduced) {
            element.classList.remove('tw-pending');

            return;
        }

        const phrases = readPhrases(element);

        if (!phrases.length) {
            element.classList.remove('tw-pending');

            return;
        }

        typeLoop(element, phrases);
        started += 1;
    });

    return started;
}
