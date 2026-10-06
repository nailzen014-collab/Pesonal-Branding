@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'rounded-xl border border-success/40 bg-success/10 px-4 py-3 text-sm text-success']) }}>
        {{ $status }}
    </div>
@endif