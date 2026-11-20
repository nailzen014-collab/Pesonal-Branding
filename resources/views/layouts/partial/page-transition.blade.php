<script>
    (() => {
        const storageKey = 'page-transition:enter';
        const root = document.documentElement;
        const openDuration = 650;

        const openCurtain = () => {
            root.classList.remove('page-transition-covering');
            root.classList.remove('page-transition-pending');
            root.classList.add('page-transition-entering');
            window.setTimeout(() => root.classList.remove('page-transition-entering'), openDuration);
        };

        try {
            if (sessionStorage.getItem(storageKey) === '1') {
                sessionStorage.removeItem(storageKey);
                root.classList.add('page-transition-pending');

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', openCurtain, { once: true });
                } else {
                    openCurtain();
                }
            }
        } catch (error) {
            console.error('Gagal memulai transisi halaman:', error);
        }

        window.addEventListener('pageshow', (event) => {
            if (!event.persisted) {
                return;
            }

            try {
                sessionStorage.removeItem(storageKey);
            } catch (error) {
                console.error('Gagal membersihkan status transisi halaman:', error);
            }

            openCurtain();
        });
    })();
</script>
