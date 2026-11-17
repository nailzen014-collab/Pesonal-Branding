@props([
    'title',
    'subtitle' => null,
    'eyebrow' => null,
])

{{-- Header halaman dalam (dipakai semua halaman selain beranda). --}}
<section class="relative overflow-hidden border-b border-line-soft bg-ink-soft pb-20 pt-32 sm:pb-24 sm:pt-40">
    <div class="pointer-events-none absolute inset-0 bg-aurora opacity-70" aria-hidden="true"></div>
    <div class="pointer-events-none absolute inset-0 bg-grid opacity-70" aria-hidden="true"></div>

    <div class="container-page relative">
        <div class="mb-7 flex flex-wrap items-center justify-between gap-3">
            <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-xs text-muted">
                <a href="{{ route('home') }}" class="transition hover:text-primary">Beranda</a>
                <span aria-hidden="true">/</span>
                <span class="text-text">{{ $title }}</span>
            </nav>

            <a href="{{ route('home') }}" data-back-link
                class="pill group gap-1.5 transition duration-300 hover:border-primary/40 hover:text-primary">
                <x-icon name="arrow-left" class="h-3.5 w-3.5 transition-transform duration-300 group-hover:-translate-x-0.5" />
                Kembali
            </a>
        </div>

        <div class="reveal max-w-3xl">
            @if ($eyebrow)
                <span class="eyebrow">
                    <span class="h-px w-6 bg-primary"></span>
                    {{ $eyebrow }}
                </span>
            @endif

            <h1 class="mt-5 text-display-page font-bold text-text">{{ $title }}</h1>

            @if ($subtitle)
                <p class="mt-6 max-w-2xl text-base leading-relaxed text-muted sm:text-lg">{{ $subtitle }}</p>
            @endif
        </div>
    </div>
</section>