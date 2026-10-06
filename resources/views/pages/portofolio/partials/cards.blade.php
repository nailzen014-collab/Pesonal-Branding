{{--
    Potongan HTML daftar proyek untuk tombol "muat lebih banyak".

    Controller mengirim:
    - $projects : paginator halaman yang sedang dimuat
    - $nextPage : nomor halaman berikutnya, atau null bila sudah habis
    - $query    : filter aktif supaya permintaan berikutnya konsisten
--}}
<div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3" data-card-grid>
    @foreach ($projects as $project)
        <div class="reveal">
            <x-project-card :project="$project" />
        </div>
    @endforeach
</div>

@if ($nextPage)
    <div class="mt-14 flex justify-center">
        <x-ui.button variant="secondary" icon="refresh" type="button" data-load-more
            data-next-page="{{ $nextPage }}" data-query="{{ http_build_query($query) }}"
            data-loading-label="Memuat...">
            Muat lebih banyak
        </x-ui.button>
    </div>
@endif