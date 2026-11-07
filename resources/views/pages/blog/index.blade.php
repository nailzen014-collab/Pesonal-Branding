@extends('layouts.public')

@section('title', 'Blog')
@section('subtitle', 'Artikel seputar web development, Laravel, dan perjalanan belajar')

@section('content')
    @php
        $hasFilter = filled($search) || filled($category);
    @endphp

    <x-site.page-header title="Blog"
        eyebrow="Artikel"
        subtitle="Tulisan seputar web development, Laravel, dan perjalanan belajar." />

    <section class="container-page py-20">
        {{-- Filter & pencarian --}}
        <form method="GET" action="{{ route('blog') }}"
            class="reveal relative overflow-hidden rounded-3xl border-gradient bg-surface/70 p-6 ">
            <div class="pointer-events-none absolute inset-0 bg-dots opacity-30" aria-hidden="true"></div>

            <div class="relative grid gap-4 lg:grid-cols-12">
                <div class="lg:col-span-7">
                    <label for="q" class="sr-only">Cari artikel</label>
                    <div class="relative">
                        <x-icon name="search" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-muted" />
                        <input id="q" name="q" type="search" value="{{ $search }}" placeholder="Cari judul atau ringkasan..."
                            class="form-input pl-10">
                    </div>
                </div>

                <div class="lg:col-span-3">
                    <label for="category" class="sr-only">Filter kategori</label>
                    <select id="category" name="category" class="form-select">
                        <option value="">Semua kategori</option>
                        @foreach ($categories as $item)
                            <option value="{{ $item }}" @selected($category === $item)>{{ $item }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-2 lg:col-span-2">
                    <x-ui.button type="submit" class="flex-1">Terapkan</x-ui.button>
                    @if ($hasFilter)
                        <a href="{{ route('blog') }}" class="icon-button" aria-label="Reset filter">
                            <x-icon name="refresh" class="h-5 w-5" />
                        </a>
                    @endif
                </div>
            </div>

            @if ($hasFilter)
                <p class="relative mt-5 text-xs text-muted">
                    Menampilkan {{ $posts->total() }} hasil
                    @if (filled($search))
                        untuk kata kunci &ldquo;{{ $search }}&rdquo;
                    @endif
                </p>
            @endif
        </form>

        {{-- State kosong --}}
        @if ($posts->isEmpty())
            <div class="relative mt-12 overflow-hidden rounded-3xl border border-dashed border-line bg-surface/60 px-6 py-24 text-center ">
                <div class="pointer-events-none absolute inset-0 bg-grid opacity-40" aria-hidden="true"></div>

                <div class="relative">
                    <span class="mx-auto inline-flex h-16 w-16 items-center justify-center rounded-2xl border border-primary/25 bg-primary/10">
                        <x-icon name="document" class="h-7 w-7 text-primary" />
                    </span>
                    <h2 class="mt-6 font-display text-lg font-semibold text-text">Artikel tidak ditemukan</h2>
                    <p class="mx-auto mt-2 max-w-md text-sm text-muted">Coba kata kunci lain atau reset filter yang aktif.</p>
                    <a href="{{ route('blog') }}" class="mt-7 inline-block">
                        <x-ui.button variant="secondary" icon="refresh">Reset Filter</x-ui.button>
                    </a>
                </div>
            </div>
        @else
            {{-- Daftar artikel --}}
            <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($posts as $post)
                    <article class="reveal card group flex flex-col overflow-hidden !p-0">
                        <a href="{{ route('blog.show', $post->slug) }}" class="block">
                            @if ($post->thumbnail_url)
                                <img src="{{ $post->thumbnail_url }}" alt="{{ $post->title }}" loading="lazy" decoding="async"
                                    class="aspect-video w-full object-cover transition duration-500 group-hover:scale-105">
                            @else
                                <div class="flex aspect-video w-full items-center justify-center bg-dots">
                                    <x-icon name="document" class="h-10 w-10 text-primary/60" />
                                </div>
                            @endif
                        </a>

                        <div class="flex flex-1 flex-col p-5">
                            @if ($post->category)
                                <span class="pill mb-3 self-start border-primary/30 text-primary">{{ $post->category }}</span>
                            @endif

                            <a href="{{ route('blog.show', $post->slug) }}">
                                <h3 class="font-display text-base font-semibold leading-snug text-text transition group-hover:text-primary">
                                    {{ $post->title }}
                                </h3>
                            </a>

                            <p class="mt-3 flex-1 text-sm leading-relaxed text-muted">{{ $post->excerpt }}</p>

                            <div class="mt-5 flex items-center justify-between">
                                <time datetime="{{ $post->date->toDateString() }}" class="font-mono text-2xs text-muted">
                                    {{ $post->date->format('d M Y') }}
                                </time>
                                <a href="{{ route('blog.show', $post->slug) }}" class="inline-flex items-center gap-1 text-xs font-semibold text-primary hover:underline">
                                    Baca
                                    <x-icon name="arrow-right" class="h-3.5 w-3.5" />
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            @if ($posts->hasPages())
                <div class="mt-14">
                    {{ $posts->links() }}
                </div>
            @endif
        @endif
    </section>
@endsection
