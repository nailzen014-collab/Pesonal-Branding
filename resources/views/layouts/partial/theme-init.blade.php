{{--
    Terapkan tema tersimpan sebelum CSS dimuat supaya tidak ada kedipan
    mode gelap saat pengguna memilih mode terang.

    Preferensi publik dan admin dipisah lewat atribut data-theme-scope pada
    <html>, jadi mengganti mode di website tidak ikut mengubah panel admin.
--}}
<script>
    (function () {
        try {
            var scope = document.documentElement.dataset.themeScope === 'admin' ? 'admin' : 'public';
            var key = scope === 'admin' ? 'theme-admin' : 'theme';

            if (localStorage.getItem(key) === 'light') {
                document.documentElement.classList.add('light');
            }
        } catch (error) {
            // localStorage bisa diblokir; biarkan mode gelap sebagai bawaan.
        }
    })();
</script>
