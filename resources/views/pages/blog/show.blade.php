@extends('layouts.public')

@section('title', $post->title)
@section('subtitle', $post->excerpt)
@section('metaDescription', $post->meta_description ?: $post->excerpt)

@section('content')
    <x-site.page-header :title="$post->title"
        eyebrow="Artikel"
        :subtitle="$post->excerpt" />

    <section class="container-page py-20">
        <nav aria-label="Breadcrumb" class="mb-8 text-xs text-muted">
            <a href="{{ route('home') }}" class="hover:text-primary">Beranda</a>
            <span class="mx-2" aria-hidden="true">/</span>
            <a href="{{ route('blog') }}" class="hover:text-primary">Blog</a>
            <span class="mx-2" aria-hidden="true">/</span>
            <span class="text-text">{{ $post->title }}</span>
        </nav>

        <div class="grid gap-10 lg:grid-cols-[minmax(0,1.6fr)_minmax(0,1fr)]">
            <article class="space-y-10">
                {{-- Thumbnail --}}
                @if ($post->thumbnail_url)
                    <div class="card !p-0">
                        <img src="{{ $post->thumbnail_url }}" alt="{{ $post->title }}" loading="lazy" decoding="async"
                            class="aspect-video w-full rounded-2xl object-cover">
                    </div>
                @endif

                {{-- Metadata --}}
                <div class="reveal flex flex-wrap items-center gap-4 text-sm text-muted">
                    <time datetime="{{ $post->date->toDateString() }}" class="inline-flex items-center gap-1.5">
                        <x-icon name="calendar" class="h-4 w-4 text-primary" />
                        {{ $post->date->format('d F Y') }}
                    </time>
                    @if ($post->category)
                        <span class="pill border-primary/30 text-primary">{{ $post->category }}</span>
                    @endif
                </div>

                {{-- Konten artikel --}}
                <div class="reveal">
                    <x-section-title eyebrow="Isi Artikel" title="Konten" />

                    <div class="article-body mt-6">
                        @if (filled($post->body))
                            {!! $post->body_html !!}
                        @else
                            <p>Konten artikel ini belum tersedia.</p>
                        @endif
                    </div>
                </div>

                @if ($related->isNotEmpty())
                    <div class="reveal">
                        <x-section-title eyebrow="Artikel lain" title="Baca juga" />

                        <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach ($related as $item)
                                <article class="card group flex flex-col !p-0 overflow-hidden">
                                    <a href="{{ route('blog.show', $item->slug) }}" class="block">
                                        @if ($item->thumbnail_url)
                                            <img src="{{ $item->thumbnail_url }}" alt="{{ $item->title }}" loading="lazy" decoding="async"
                                                class="aspect-video w-full object-cover transition duration-500 group-hover:scale-105">
                                        @else
                                            <div class="flex aspect-video w-full items-center justify-center bg-dots">
                                                <x-icon name="document" class="h-8 w-8 text-primary/60" />
                                            </div>
                                        @endif
                                    </a>

                                    <div class="flex flex-1 flex-col p-4">
                                        @if ($item->category)
                                            <span class="pill mb-2 self-start border-primary/30 text-primary text-2xs">{{ $item->category }}</span>
                                        @endif
                                        <a href="{{ route('blog.show', $item->slug) }}">
                                            <h3 class="font-display text-sm font-semibold leading-snug text-text transition group-hover:text-primary">
                                                {{ $item->title }}
                                            </h3>
                                        </a>
                                        <p class="mt-2 text-2xs text-muted">{{ $item->excerpt }}</p>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>
                @endif
            </article>

            <aside class="space-y-6">
                <div class="reveal card sticky top-28">
                    <h2 class="font-display text-lg font-semibold text-text">Info Artikel</h2>

                    <dl class="mt-6 space-y-4 text-sm">
                        @if ($post->category)
                            <div class="flex items-start justify-between gap-4">
                                <dt class="text-muted">Kategori</dt>
                                <dd class="text-text">{{ $post->category }}</dd>
                            </div>
                        @endif

                        <div class="flex items-start justify-between gap-4">
                            <dt class="text-muted">Dipublikasikan</dt>
                            <dd class="text-text">{{ $post->date->format('d M Y') }}</dd>
                        </div>

                        <div class="flex items-start justify-between gap-4">
                            <dt class="text-muted">Diperbarui</dt>
                            <dd class="text-text">{{ $post->updated_at->format('d M Y') }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="reveal card">
                    <h2 class="font-display text-lg font-semibold text-text">Suka dengan artikel ini?</h2>
                    <p class="mt-3 text-sm text-muted">Mau diskusi atau ada pertanyaan? Hubungi saya.</p>
                    <div class="mt-5">
                        <x-ui.button :href="route('kontak')" class="w-full" icon="mail">Hubungi Saya</x-ui.button>
                    </div>
                </div>
            </aside>
        </div>
    </section>
@endsection
