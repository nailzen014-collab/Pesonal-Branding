@extends('layouts.admin')

@section('title', 'Kelola Artikel')
@section('subtitle', 'Tambah, ubah, dan hapus artikel blog')

@section('content')
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <form method="GET" action="{{ route('admin.posts.index') }}" class="flex w-full max-w-sm gap-2">
            <label for="q" class="sr-only">Cari artikel</label>
            <input id="q" name="q" type="search" value="{{ $search }}" placeholder="Cari judul artikel..." class="form-input">
            <x-ui.button type="submit" icon="search">Cari</x-ui.button>
        </form>

        <x-ui.button :href="route('admin.posts.create')" icon="plus">Tambah Artikel</x-ui.button>
    </div>

    {{-- Tabel artikel --}}
    <div class="card mt-6 !p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-line text-sm">
                <thead class="bg-surface-2 text-left text-2xs uppercase tracking-wider text-muted">
                    <tr>
                        <th class="px-5 py-4 font-semibold">Judul</th>
                        <th class="px-5 py-4 font-semibold">Kategori</th>
                        <th class="px-5 py-4 font-semibold">Tanggal</th>
                        <th class="px-5 py-4 font-semibold">Status</th>
                        <th class="px-5 py-4 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse ($posts as $post)
                        <tr class="transition hover:bg-surface-2/60">
                            <td class="px-5 py-4">
                                <p class="font-medium text-text">{{ $post->title }}</p>
                                <p class="mt-0.5 font-mono text-2xs text-muted">/blog/{{ $post->slug }}</p>
                            </td>
                            <td class="px-5 py-4 text-muted">{{ $post->category ?? '-' }}</td>
                            <td class="px-5 py-4 text-muted">
                                @if ($post->date)
                                    {{ $post->date->format('d M Y') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                @if ($post->is_published)
                                    <span class="pill border-success/40 text-success">Published</span>
                                @else
                                    <span class="pill">Draft</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    @if ($post->is_published)
                                        <a href="{{ route('blog.show', $post->slug) }}" target="_blank" rel="noopener noreferrer"
                                            class="icon-button" aria-label="Lihat di website">
                                            <x-icon name="eye" class="h-4 w-4" />
                                        </a>
                                    @endif

                                    <a href="{{ route('admin.posts.edit', $post) }}" class="icon-button" aria-label="Edit {{ $post->title }}">
                                        <x-icon name="edit" class="h-4 w-4" />
                                    </a>

                                    <form method="POST" action="{{ route('admin.posts.destroy', $post) }}"
                                        onsubmit="return confirm('Hapus artikel {{ $post->title }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="icon-button hover:border-red-500/40 hover:text-red-400"
                                            aria-label="Hapus {{ $post->title }}">
                                            <x-icon name="trash" class="h-4 w-4" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-16 text-center">
                                <x-icon name="document" class="mx-auto h-8 w-8 text-muted" />
                                <p class="mt-4 text-sm text-muted">Belum ada artikel.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($posts->hasPages())
        <div class="mt-6">
            {{ $posts->links() }}
        </div>
    @endif
@endsection
