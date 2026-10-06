@props([
    'variant' => 'primary',
    'href' => null,
    'icon' => null,
])

@php
    // Varian tombol mengikuti PRD 11.2:
    // primary = latar merah, secondary = outline merah, ghost = teks saja.
    $base = 'group/btn relative inline-flex items-center justify-center gap-2 overflow-hidden rounded-xl px-5 py-2.5 text-sm font-semibold
        transition duration-400 ease-smooth disabled:cursor-not-allowed disabled:opacity-50';

    $styles = match ($variant) {
        'primary' => 'bg-primary text-white shadow-glow-soft hover:-translate-y-0.5 hover:bg-primary-dark hover:shadow-glow-primary',
        'secondary' => 'border border-primary/60 bg-primary/5 text-primary hover:-translate-y-0.5 hover:border-primary hover:bg-primary/15 hover:shadow-glow-soft',
        'outline' => 'border border-line bg-surface-2/60 text-text hover:-translate-y-0.5 hover:border-primary/50 hover:text-primary',
        'ghost' => 'text-muted hover:-translate-y-0.5 hover:text-text',
        'danger' => 'border border-red-500/60 text-red-400 hover:-translate-y-0.5 hover:bg-red-500/10',
        default => 'bg-primary text-white hover:-translate-y-0.5 hover:bg-primary-dark',
    };

    $tag = $href ? 'a' : 'button';
@endphp

<{{ $tag }}
    @if ($tag === 'a') href="{{ $href }}"
    @else type="{{ $attributes->get('type', 'submit') }}"
    @endif
    {{ $attributes->except('type')->merge(['class' => $base.' '.$styles]) }}
>
    {{-- Kilau yang menyapu saat hover (hanya tombol dengan latar) --}}
    @if (in_array($variant, ['primary', 'secondary'], true))
        <span class="pointer-events-none absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/25 to-transparent transition-transform duration-700 group-hover/btn:translate-x-full"
            aria-hidden="true"></span>
    @endif

    @if ($icon)
        <x-icon :name="$icon" class="h-4 w-4 transition-transform duration-400 group-hover/btn:scale-110" />
    @endif

    <span class="relative">{{ $slot }}</span>
</{{ $tag }}>