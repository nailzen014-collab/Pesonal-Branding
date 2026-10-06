@props(['type' => 'success'])

@php
    $styles = match ($type) {
        'error' => 'border-red-500/40 bg-red-500/10 text-red-300',
        'warning' => 'border-warning/40 bg-warning/10 text-warning',
        default => 'border-success/40 bg-success/10 text-success',
    };

    $icon = match ($type) {
        'error' => 'close',
        'warning' => 'shield',
        default => 'check',
    };
@endphp

@if (session($type === 'error' ? 'error' : 'success'))
    <div role="status"
        {{ $attributes->merge(['class' => 'mb-6 flex items-start gap-3 rounded-xl border px-4 py-3 text-sm '.$styles]) }}>
        <x-icon :name="$icon" class="mt-0.5 h-4 w-4 shrink-0" />
        <p>{{ session($type === 'error' ? 'error' : 'success') }}</p>
    </div>
@endif