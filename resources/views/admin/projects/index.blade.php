@extends('layouts.admin')

@section('title', 'Kelola Proyek')
@section('subtitle', 'Tambah, ubah, hapus, dan atur urutan tampil proyek')

@section('content')
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <form method="GET" action="{{ route('admin.projects.index') }}" class="flex w-full max-w-sm gap-2">
            <label for="q" class="sr-only">Cari proyek</label>
            <input id="q" name="q" type="search" value="{{ $search }}" placeholder="Cari judul proyek..." class="form-input">
            <x-ui.button type="submit" icon="search">Cari</x-ui.button>
        </form>

        <div class="flex gap-2">
            <x-ui.button :href="route('admin.github.index')" variant="secondary" icon="github">Sinkron GitHub</x-ui.button>
            <x-ui.button :href="route('admin.projects.create')" icon="plus">Tambah Proyek</x-ui.button>
        </div>
    </div>

    {{-- Tabel proyek --}}
    <div class="card mt-6 !p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-line text-sm">
                <thead class="bg-surface-2 text-left text-2xs uppercase tracking-wider text-muted">
                    <tr>
                        <th class="px-5 py-4 font-semibold">Judul</th>
                        <th class="px-5 py-4 font-semibold">Kategori</th>
                        <th class="px-5 py-4 font-semibold">Teknologi</th>
                        <th class="px-5 py-4 font-semibold">Status</th>
                        <th class="px-5 py-4 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse ($projects as $project)
                        <tr class="transition hover:bg-surface-2/60">
                            <td class="px-5 py-4">
                                <p class="font-medium text-text">{{ $project->title }}</p>
                                <p class="mt-0.5 font-mono text-2xs text-muted">/{{ $project->slug }}</p>
                            </td>
                            <td class="px-5 py-4 text-muted">{{ $project->category ?? '-' }}</td>
                            <td class="px-5 py-4">
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach ($project->technologies as $technology)
                                        <span class="pill">{{ $technology->name }}</span>
                                    @endforeach
                                    @if ($project->technologies->isEmpty())
                                        <span class="text-2xs text-muted">-</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex flex-wrap gap-1.5">
                                    @if ($project->is_published)
                                        <span class="pill border-success/40 text-success">Published</span>
                                    @else
                                        <span class="pill">Draft</span>
                                    @endif

                                    @if ($project->is_featured)
                                        <span class="pill border-primary/40 text-primary">Unggulan</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    @if ($project->is_published)
                                        <a href="{{ route('portofolio.show', $project->slug) }}" target="_blank" rel="noopener noreferrer"
                                            class="icon-button" aria-label="Lihat di website">
                                            <x-icon name="eye" class="h-4 w-4" />
                                        </a>
                                    @endif

                                    <a href="{{ route('admin.projects.edit', $project) }}" class="icon-button" aria-label="Edit {{ $project->title }}">
                                        <x-icon name="edit" class="h-4 w-4" />
                                    </a>

                                    <form method="POST" action="{{ route('admin.projects.destroy', $project) }}"
                                        onsubmit="return confirm('Hapus proyek {{ $project->title }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="icon-button hover:border-red-500/40 hover:text-red-400"
                                            aria-label="Hapus {{ $project->title }}">
                                            <x-icon name="trash" class="h-4 w-4" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-16 text-center">
                                <x-icon name="folder" class="mx-auto h-8 w-8 text-muted" />
                                <p class="mt-4 text-sm text-muted">Belum ada proyek.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($projects->hasPages())
        <div class="mt-6">
            {{ $projects->links() }}
        </div>
    @endif
@endsection