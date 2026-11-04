function storageKey() {
    return document.documentElement.dataset.themeScope === 'admin' ? 'theme-admin' : 'theme';
}

/**
 * Terapkan tema ke <html> dan perbarui atribut aksesibilitas tombol.
 */
function applyTheme(theme) {
    const isLight = theme === 'light';
    const root = document.documentElement;

    root.classList.toggle('light', isLight);
    root.style.colorScheme = isLight ? 'light' : 'dark';

    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        button.setAttribute('aria-pressed', isLight ? 'true' : 'false');
        button.setAttribute('aria-label', isLight ? 'Aktifkan mode gelap' : 'Aktifkan mode terang');
        button.setAttribute('title', isLight ? 'Mode gelap' : 'Mode terang');
    });
}

function storedTheme() {
    try {
        return localStorage.getItem(storageKey()) === 'light' ? 'light' : 'dark';
    } catch (error) {
        return 'dark';
    }
}

/**
 * Tombol tema memakai localStorage agar pilihan bertahan antar halaman.
 * Mode gelap tetap menjadi bawaan, sesuai tema lama website.
 */
export function initTheme() {
    applyTheme(storedTheme());

    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const next = document.documentElement.classList.contains('light') ? 'dark' : 'light';

            try {
                localStorage.setItem(storageKey(), next);
            } catch (error) {
                // Tetap ganti tema meski preferensi gagal disimpan.
            }

            applyTheme(next);
        });
    });
}
