@props(['siteName'])

<div data-page-loader class="page-loader" role="status" aria-label="Memuat {{ $siteName }}">
    <div class="page-loader__content">
        <button type="button" data-page-loader-enter
            class="page-loader__mark"
            aria-label="Buka {{ $siteName }}">
            <x-application-logo class="h-10 w-10 sm:h-12 sm:w-12" />
        </button>

        <p class="page-loader__name">{{ $siteName }}</p>
        <p class="page-loader__hint">Klik atau ketuk logo untuk masuk</p>
    </div>
</div>

<script>
    (() => {
        const loader = document.querySelector('[data-page-loader]');

        if (!loader) {
            return;
        }

        const navigation = performance.getEntriesByType('navigation')[0];
        const scope = document.documentElement.dataset.themeScope ?? 'public';
        const storageKey = `page-loader-seen:${scope}`;
        const shouldShow = navigation?.type === 'reload' || !sessionStorage.getItem(storageKey);

        if (!shouldShow) {
            loader.remove();
            return;
        }

        sessionStorage.setItem(storageKey, '1');

        let isHiding = false;

        const hideLoader = (animate = true) => {
            if (isHiding) {
                return;
            }

            isHiding = true;
            if (!animate) {
                loader.remove();
                return;
            }

            loader.classList.add('is-entering');
            loader.querySelector('[data-page-loader-enter]')?.classList.add('is-activating');
            window.setTimeout(() => loader.remove(), 900);
        };

        loader.querySelector('[data-page-loader-enter]')?.addEventListener('click', hideLoader);

        window.addEventListener('pageshow', (event) => {
            if (event.persisted) {
                hideLoader(false);
            }
        });
    })();
</script>
