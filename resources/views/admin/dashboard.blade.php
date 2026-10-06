@extends('layouts.admin')

@section('title', 'Dashboard')
@section('subtitle', 'Ringkasan isi website')

@section('content')
    {{-- Kartu statistik --}}
    <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ([
            ['label' => 'Proyek Dirilis', 'value' => $stats['published_projects'], 'icon' => 'folder', 'hint' => $stats['projects'].' total, '.$stats['featured_projects'].' unggulan'],
            ['label' => 'Teknologi', 'value' => $stats['technologies'], 'icon' => 'layers', 'hint' => 'Dipakai pada proyek'],
            ['label' => 'Skill', 'value' => $stats['skills'], 'icon' => 'sparkles', 'hint' => 'Terdaftar di halaman skills'],
            ['label' => 'Pesan Belum Dibaca', 'value' => $stats['unread_messages'], 'icon' => 'inbox', 'hint' => 'Total pesan masuk'],
        ] as $card)
            <div class="card">
                <div class="flex items-start justify-between">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-primary/10">
                        <x-icon :name="$card['icon']" class="h-5 w-5 text-primary" />
                    </span>
                    <span class="font-display text-3xl font-bold text-text">{{ $card['value'] }}</span>
                </div>
                <p class="mt-4 text-sm font-medium text-text">{{ $card['label'] }}</p>
                <p class="mt-1 text-2xs text-muted">{{ $card['hint'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- Aksi cepat --}}
    <div class="mt-8 flex flex-wrap gap-3">
        <x-ui.button :href="route('admin.projects.create')" icon="plus">Tambah Proyek</x-ui.button>
        <x-ui.button :href="route('admin.github.index')" variant="secondary" icon="github">Sinkron GitHub</x-ui.button>
        <x-ui.button :href="route('admin.messages.index')" variant="outline" icon="inbox">Kotak Masuk</x-ui.button>
        <x-ui.button :href="route('admin.settings.edit')" variant="outline" icon="cog">Pengaturan</x-ui.button>
    </div>

    <div class="mt-8 grid gap-6 xl:grid-cols-2">
        {{-- Pesan terbaru --}}
        <section class="card">
            <div class="flex items-center justify-between">
                <h2 class="font-display text-lg font-semibold text-text">Pesan Terbaru</h2>
                <a href="{{ route('admin.messages.index') }}" class="text-xs text-primary hover:underline">Lihat semua</a>
            </div>

            @if ($recentMessages->isEmpty())
                <p class="mt-6 text-sm text-muted">Belum ada pesan masuk.</p>
            @else
                <ul class="mt-6 divide-y divide-line">
                    @foreach ($recentMessages as $message)
                        <li class="py-3 first:pt-0 last:pb-0">
                            <a href="{{ route('admin.messages.show', $message) }}" class="group flex items-start gap-3">
                                <span @class([
                                    'mt-1.5 h-2 w-2 shrink-0 rounded-full',
                                    'bg-primary' => ! $message->isRead(),
                                    'bg-line' => $message->isRead(),
                                ])></span>
                                <span class="min-w-0 flex-1">
                                    <span class="flex items-center justify-between gap-3">
                                        <span @class([
                                            'truncate text-sm',
                                            'font-semibold text-text' => ! $message->isRead(),
                                            'text-muted' => $message->isRead(),
                                        ])>{{ $message->name }}</span>
                                        <span class="shrink-0 font-mono text-2xs text-muted">{{ $message->created_at->diffForHumans() }}</span>
                                    </span>
                                    <span class="mt-0.5 block truncate text-xs text-muted group-hover:text-text">
                                        {{ $message->subject }} &mdash; {{ Str::limit($message->body, 60) }}
                                    </span>
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

        {{-- Proyek terbaru --}}
        <section class="card">
            <div class="flex items-center justify-between">
                <h2 class="font-display text-lg font-semibold text-text">Proyek Terbaru</h2>
                <a href="{{ route('admin.projects.index') }}" class="text-xs text-primary hover:underline">Kelola</a>
            </div>

            @if ($recentProjects->isEmpty())
                <p class="mt-6 text-sm text-muted">Belum ada proyek. Tambahkan lewat tombol di atas.</p>
            @else
                <ul class="mt-6 divide-y divide-line">
                    @foreach ($recentProjects as $project)
                        <li class="flex items-center justify-between gap-4 py-3 first:pt-0 last:pb-0">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium text-text">{{ $project->title }}</p>
                                <p class="mt-0.5 text-2xs text-muted">
                                    {{ $project->is_published ? 'Dipublikasikan' : 'Draft' }}
                                    @if ($project->category) &middot; {{ $project->category }} @endif
                                </p>
                            </div>

                            <div class="flex shrink-0 items-center gap-2">
                                <a href="{{ route('admin.projects.edit', $project) }}" class="icon-button" aria-label="Edit {{ $project->title }}">
                                    <x-icon name="edit" class="h-4 w-4" />
                                </a>
                                <a href="{{ route('portofolio.show', $project->slug) }}" target="_blank" rel="noopener noreferrer"
                                    class="icon-button" aria-label="Lihat di website">
                                    <x-icon name="eye" class="h-4 w-4" />
                                </a>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    </div>
@endsection