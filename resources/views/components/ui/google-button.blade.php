@props(['href'])

{{--
    Tombol "Lanjutkan dengan Google" untuk login pengunjung.
    Ikon memakai logo resmi Google berwarna agar mudah dikenali.
--}}
<a href="{{ $href }}"
    {{ $attributes->merge([
        'class' => 'inline-flex items-center justify-center gap-3 rounded-xl border border-line bg-surface-2/80 px-5 py-3 text-sm font-semibold text-text transition duration-400 ease-smooth hover:-translate-y-0.5 hover:border-primary/40 hover:bg-surface-2 hover:shadow-glow-soft',
    ]) }}>
    <svg class="h-5 w-5 shrink-0" viewBox="0 0 48 48" aria-hidden="true">
        <path fill="#EA4335"
            d="M24 9.5c3.5 0 6.6 1.2 9 3.6l6.7-6.7C35.6 2.6 30.2 0 24 0 14.6 0 6.5 5.4 2.6 13.3l7.8 6.1C12.3 13.4 17.7 9.5 24 9.5z" />
        <path fill="#4285F4"
            d="M46.1 24.5c0-1.6-.1-2.8-.4-4.1H24v8.4h12.7c-.3 2.1-1.7 5.2-4.9 7.3l7.6 5.9c4.4-4.1 6.7-10.1 6.7-17.5z" />
        <path fill="#FBBC05"
            d="M10.4 28.6c-.5-1.5-.8-3-.8-4.6s.3-3.1.8-4.6l-7.8-6.1C.9 16.5 0 20.1 0 24s.9 7.5 2.6 10.7l7.8-6.1z" />
        <path fill="#34A853"
            d="M24 48c6.2 0 11.4-2 15.2-5.6l-7.6-5.9c-2 1.4-4.7 2.4-7.6 2.4-6.3 0-11.7-3.9-13.6-9.9l-7.8 6.1C6.5 42.6 14.6 48 24 48z" />
    </svg>

    <span>{{ $slot->isEmpty() ? 'Lanjutkan dengan Google' : $slot }}</span>
</a>
