<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center gap-2 rounded-xl border border-line px-5 py-2.5 text-sm font-semibold text-text transition hover:border-primary/50 hover:text-primary focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:ring-offset-ink']) }}>
    {{ $slot }}
</button>