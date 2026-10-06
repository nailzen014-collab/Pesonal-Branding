@extends('layouts.public')

@section('content')
    @php
        $hasFilter = filled($search) || filled($category) || filled($technology);
    @endphp

    <x-site.page-header title="Portofolio"
        eyebrow="Karya"
        subtitle="Kumpulan proyek beserta tautan repository GitHub. Semua kode bisa dibuka dan ditinjau." />

    <section class="container-page py-20">
        {{-- Filter & pencarian (FR-07): diproses di server agar tetap ringan --}}
        <form method="GET" action="{{ route('portofolio') }}"
            class="reveal relative overflow-hidden rounded-3xl border-gradient bg-surface/70 p-6 backdrop-blur">
            <div class="pointer-events-none absolute inset-0 bg-dots opacity-30" aria-hidden="true"></div>

            <div class="relative grid gap-4 lg:grid-cols-12">
                <div class="lg:col-span-5">
                    <label for="q" class="sr-only">Cari proyek</label>
                    <div class="relative">
                        <x-icon name="search" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-muted" />
                        <input id="q" name="q" type="search" value="{{ $search }}" placeholder="Cari judul atau deskripsi..."
                            class="form-input pl-10">
                    </div>
                </div>

                <div class="lg:col-span-3">
                    <label for="category" class="sr-only">Filter kategori</label>
                    <select id="category" name="category" class="form-input">
                        <option value="">Semua kategori</option>
                        @foreach ($categories as $item)
                            <option value="{{ $item }}" @selected($category === $item)>{{ $item }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="lg:col-span-2">
                    <label for="technology" class="sr-only">Filter teknologi</label>
                    <select id="technology" name="technology" class="form-input">
                        <option value="">Semua teknologi</option>
                        @foreach ($technologies as $item)
                            <option value="{{ $item }}" @selected($technology === $item)>{{ $item }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-2 lg:col-span-2">
                    <x-ui.button type="submit" class="flex-1">Terapkan</x-ui.button>
                    @if ($hasFilter)
                        <a href="{{ route('portofolio') }}" class="icon-button" aria-label="Reset filter">
                            <x-icon name="refresh" class="h-5 w-5" />
                        </a>
                    @endif
                </div>
            </div>

            @if ($hasFilter)
                <p class="relative mt-5 text-xs text-muted">
                    Menampilkan {{ $projects->total() }} hasil
                    @if (filled($search))
                        untuk kata kunci &ldquo;{{ $search }}&rdquo;
                    @endif
                </p>
            @endif
        </form>

        {{-- State kosong yang ramah (prinsip UX 11.3) --}}
        @if ($projects->isEmpty())
            <div class="relative mt-12 overflow-hidden rounded-3xl border border-dashed border-line bg-surface/60 px-6 py-24 text-center backdrop-blur">
                <div class="pointer-events-none absolute inset-0 bg-grid opacity-40" aria-hidden="true"></div>

                <div class="relative">
                    <span
                        class="mx-auto inline-flex h-16 w-16 items-center justify-center rounded-2xl border border-primary/25 bg-primary/10">
                        <x-icon name="folder" class="h-7 w-7 text-primary" />
                    </span>
                    <h2 class="mt-6 font-display text-lg font-semibold text-text">Proyek tidak ditemukan</h2>
                    <p class="mx-auto mt-2 max-w-md text-sm text-muted">Coba kata kunci lain atau reset filter yang aktif.</p>
                    <a href="{{ route('portofolio') }}" class="mt-7 inline-block">
                        <x-ui.button variant="secondary" icon="refresh">Reset Filter</x-ui.button>
                    </a>
                </div>
            </div>
        @else
            {{--
                Daftar proyek dimuat bertahap ala GitHub: 9 proyek pertama langsung
                tampil, sisanya lewat tombol "muat lebih banyak" (FR-07).
                Query yang sama dipakai partial dan halaman penuh, jadi filter
                tetap berlaku di setiap permintaan berikutnya.
            --}}
            <div id="portfolio-grid" class="mt-12" data-load-more-grid
                data-load-more-url="{{ route('portofolio', ['partial' => 1]) }}">
                @include('pages.portofolio.partials.cards', [
                    'projects' => $projects,
                    'nextPage' => $projects->hasMorePages() ? $projects->currentPage() + 1 : null,
                    'query' => request()->only(['q', 'category', 'technology']),
                ])
            </div>

            {{-- Fallback tanpa JavaScript: paginasi halaman biasa --}}
            <noscript>
                @if ($projects->hasPages())
                    <div class="mt-14">
                        {{ $projects->links() }}
                    </div>
                @endif
            </noscript>
        @endif
    </section>
@endsection