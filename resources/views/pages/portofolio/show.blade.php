@extends('layouts.public')

@section('content')
    @php
        $repoLabel = $project->source === 'github' ? 'Lihat di GitHub' : 'Repository';
    @endphp

    <x-site.page-header :title="$project->title"
        eyebrow="Detail Proyek"
        :subtitle="$project->summary" />

    <section class="container-page py-20">
        <nav aria-label="Breadcrumb" class="mb-8 text-xs text-muted">
            <a href="{{ route('home') }}" class="hover:text-primary">Beranda</a>
            <span class="mx-2" aria-hidden="true">/</span>
            <a href="{{ route('portofolio') }}" class="hover:text-primary">Portofolio</a>
            <span class="mx-2" aria-hidden="true">/</span>
            <span class="text-text">{{ $project->title }}</span>
        </nav>

        <div class="grid gap-10 lg:grid-cols-[minmax(0,1.6fr)_minmax(0,1fr)]">
            <article class="space-y-10">
                {{-- Thumbnail --}}
                <div class="card !p-0">
                    @if ($project->thumbnail_url)
                        <img src="{{ $project->thumbnail_url }}" alt="Tampilan proyek {{ $project->title }}" loading="lazy" decoding="async"
                            class="aspect-video w-full rounded-t-2xl object-cover">
                    @else
                        <div class="flex aspect-video w-full items-center justify-center rounded-t-2xl bg-dots">
                            <div class="relative text-center">
                                <div class="absolute inset-0 -z-10 bg-primary/15 blur-2xl" aria-hidden="true"></div>
                                <x-icon name="code" class="mx-auto h-12 w-12 text-primary/80" />
                                <p class="mt-3 font-mono text-2xs uppercase tracking-[0.2em] text-muted">
                                    {{ $project->category ?? 'Web App' }}
                                </p>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="reveal">
                    <x-section-title eyebrow="Tentang Proyek" title="Deskripsi" />
                    <div class="mt-6 space-y-4 leading-relaxed text-muted">
                        @foreach (preg_split('/\n{2,}/', (string) $project->description) as $paragraph)
                            @if (trim($paragraph) !== '')
                                <p>{{ trim($paragraph) }}</p>
                            @endif
                        @endforeach

                        @if (blank($project->description))
                            <p>Deskripsi lengkap untuk proyek ini belum tersedia. Silakan buka repository GitHub untuk melihat kode sumbernya.</p>
                        @endif
                    </div>
                </div>

                @if ($related->isNotEmpty())
                    <div class="reveal">
                        <x-section-title eyebrow="Proyek lain" title="Mungkin kamu suka" />

                        <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach ($related as $item)
                                <x-project-card :project="$item" />
                            @endforeach
                        </div>
                    </div>
                @endif
            </article>

            <aside class="space-y-6">
                <div class="reveal card sticky top-28">
                    <h2 class="font-display text-lg font-semibold text-text">Informasi Proyek</h2>

                    <dl class="mt-6 space-y-4 text-sm">
                        @if ($project->category)
                            <div class="flex items-start justify-between gap-4">
                                <dt class="text-muted">Kategori</dt>
                                <dd class="text-text">{{ $project->category }}</dd>
                            </div>
                        @endif

                        <div class="flex items-start justify-between gap-4">
                            <dt class="text-muted">Sumber</dt>
                            <dd class="text-text">{{ $project->source === 'github' ? 'GitHub' : 'Manual' }}</dd>
                        </div>

                        <div class="flex items-start justify-between gap-4">
                            <dt class="text-muted">Terakhir diperbarui</dt>
                            <dd class="text-text">{{ $project->updated_at->format('d M Y') }}</dd>
                        </div>
                    </dl>

                    <div class="mt-6 space-y-3">
                        @if ($project->repo_url)
                            <x-ui.button :href="$project->repo_url" class="w-full" icon="github" target="_blank" rel="noopener noreferrer">
                                {{ $repoLabel }}
                            </x-ui.button>
                        @endif

                        @if ($project->demo_url)
                            <x-ui.button :href="$project->demo_url" variant="secondary" class="w-full" icon="external" target="_blank" rel="noopener noreferrer">
                                Buka Demo
                            </x-ui.button>
                        @endif
                    </div>

                    @if ($project->technologies->isNotEmpty())
                        <div class="mt-8 border-t border-line pt-6">
                            <h3 class="font-display text-sm font-semibold uppercase tracking-wider text-text">Teknologi</h3>
                            <div class="mt-4 flex flex-wrap gap-2">
                                @foreach ($project->technologies as $technology)
                                    <span class="pill">{{ $technology->name }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <div class="reveal card">
                    <h2 class="font-display text-lg font-semibold text-text">Tertarik dengan proyek ini?</h2>
                    <p class="mt-3 text-sm text-muted">Punya kebutuhan serupa? Mari diskusikan.</p>
                    <div class="mt-5">
                        <x-ui.button :href="route('kontak')" class="w-full" icon="mail">Hubungi Saya</x-ui.button>
                    </div>
                </div>
            </aside>
        </div>
    </section>
@endsection