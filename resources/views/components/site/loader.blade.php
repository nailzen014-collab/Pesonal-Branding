@props(['siteName'])

<div data-page-loader class="page-loader" role="status" aria-label="Memuat {{ $siteName }}">
    <div class="page-loader__content">
        <div class="page-loader__mark" aria-hidden="true">
            <x-icon name="code" class="h-7 w-7 text-gold-soft" />
        </div>

        <p class="page-loader__name">{{ $siteName }}</p>
        <span class="sr-only">Memuat halaman...</span>
        <div class="page-loader__bar" aria-hidden="true">
            <span></span>
        </div>
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

        const displayDuration = 3500;
        let isHiding = false;

        const hideLoader = () => {
            if (isHiding) {
                return;
            }

            isHiding = true;
            loader.classList.add('is-hidden');
            window.setTimeout(() => loader.remove(), 400);
        };

        window.setTimeout(hideLoader, displayDuration);

        window.addEventListener('pageshow', (event) => {
            if (event.persisted) {
                hideLoader();
            }
        });
    })();
</script>
