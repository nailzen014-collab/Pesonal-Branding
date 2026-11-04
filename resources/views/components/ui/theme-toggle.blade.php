{{--
    Tombol ganti mode terang/gelap.
    Ikon yang tampil diatur lewat CSS berdasarkan kelas `light` di <html>.
--}}
<button type="button" data-theme-toggle {{ $attributes->merge(['class' => 'icon-button group']) }}
    aria-label="Aktifkan mode terang" title="Mode terang" aria-pressed="false">
    <x-icon name="sun" class="theme-icon-sun h-4 w-4 transition-transform duration-300 group-hover:rotate-45" />
    <x-icon name="moon" class="theme-icon-moon h-4 w-4 transition-transform duration-300 group-hover:-rotate-12" />
</button>
