@props([
    'label' => null,
    'name' => null,
    'type' => 'text',
    'value' => null,
    'required' => false,
    'hint' => null,
])

{{--
    Form field standar: label + input + pesan error.
    Frontend form memakai komponen ini supaya semua input konsisten.
--}}
<div {{ $attributes->only('class') }}>
    @if ($label)
        <label class="form-label" for="{{ $name }}">
            {{ $label }}
            @if ($required)
                <span class="text-primary" aria-hidden="true">*</span>
            @endif
        </label>
    @endif

    <input
        id="{{ $name }}"
        name="{{ $name }}"
        type="{{ $type }}"
        value="{{ old($name, $value) }}"
        @if ($required) required @endif
        {{ $attributes->except('class')->merge(['class' => 'form-input']) }}
    >

    @if ($hint)
        <p class="mt-1.5 text-2xs text-muted">{{ $hint }}</p>
    @endif

    <x-input-error :messages="$errors->get($name)" class="mt-1.5" />
</div>