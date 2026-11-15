{{--
    Indikator progres baca.
    Di layar kecil tampil sebagai garis horizontal di atas; di layar lebih
    besar menjadi garis vertikal di tepi kiri. Nilai --scroll-progress diisi
    resources/js/scroll-ui.js (0–1).
--}}
<div data-scroll-progress aria-hidden="true" class="pointer-events-none fixed inset-0 z-[60]">
    <div class="absolute inset-x-0 top-0 h-[3px] bg-line/30 sm:hidden">
        <div class="h-full origin-left bg-gradient-to-r from-primary via-gold to-primary"
            style="transform: scaleX(var(--scroll-progress, 0));"></div>
    </div>

    <div class="absolute inset-y-0 left-0 hidden w-[3px] bg-line/30 sm:block">
        <div class="h-full origin-top bg-gradient-to-b from-primary via-gold to-primary"
            style="transform: scaleY(var(--scroll-progress, 0));"></div>
    </div>
</div>
