{{--
    Tombol kembali ke atas. Muncul setelah halaman digulir cukup jauh;
    status terlihat diatur lewat data-visible oleh resources/js/scroll-ui.js.
--}}
<button type="button" data-back-to-top aria-label="Kembali ke atas" title="Kembali ke atas"
    class="group invisible fixed bottom-24 right-6 z-40 inline-flex h-11 w-11 translate-y-3 items-center justify-center rounded-full border border-line bg-surface-2/90 text-muted opacity-0 shadow-card backdrop-blur transition duration-300 ease-smooth hover:border-primary/50 hover:text-primary hover:shadow-glow-soft active:scale-95 data-[visible=true]:visible data-[visible=true]:translate-y-0 data-[visible=true]:opacity-100">
    <x-icon name="arrow-up" class="h-5 w-5 transition-transform duration-300 group-hover:-translate-y-0.5" />
</button>
