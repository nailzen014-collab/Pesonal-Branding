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
        <nav aria-label="Breadcrumb" class="mb-7 flex items-center gap-2 text-xs text-muted">
            <a href="{{ route('home') }}" class="transition hover:text-primary">Beranda</a>
            <span aria-hidden="true">/</span>
            <span class="text-text">{{ $title }}</span>
        </nav>

        <div class="reveal max-w-3xl">
            @if ($eyebrow)
                <span class="eyebrow">
                    <span class="h-px w-6 bg-primary"></span>
                    {{ $eyebrow }}
                </span>
            @endif

            <h1 class="mt-5 font-display text-3xl font-bold leading-[1.1] text-text sm:text-5xl lg:text-6xl">{{ $title }}</h1>

            @if ($subtitle)
                <p class="mt-6 max-w-2xl text-base leading-relaxed text-muted sm:text-lg">{{ $subtitle }}</p>
            @endif
        </div>
    </div>
</section>