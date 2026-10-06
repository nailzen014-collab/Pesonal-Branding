@props([
    'title',
    'subtitle' => null,
    'eyebrow' => null,
    'center' => false,
])

{{-- Judul bagian dengan garis aksen merah, konsisten di semua halaman. --}}
<div {{ $attributes->merge(['class' => ($center ? 'mx-auto max-w-2xl text-center ' : 'max-w-2xl ').'reveal']) }}>
    @if ($eyebrow)
        <span @class([
            'eyebrow',
            'justify-center' => $center,
        ])>
            @unless ($center)
                <span class="h-px w-6 bg-primary"></span>
            @endunless
            {{ $eyebrow }}
            @unless ($center)
                <span class="h-px w-6 bg-primary"></span>
            @endunless
        </span>
    @endif

    <h2 class="text-3xl font-bold text-text sm:text-4xl lg:text-[2.75rem] lg:leading-[1.1]">{{ $title }}</h2>

    @if ($subtitle)
        <p class="mt-5 text-base leading-relaxed text-muted">{{ $subtitle }}</p>
    @endif

    <div @class([
        'mt-7 h-1 rounded-full bg-gradient-to-r from-primary to-primary-dark',
        'w-16' => ! $center,
        'mx-auto' => $center,
    ])></div>
</div>