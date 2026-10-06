@extends('layouts.admin')

@section('title', 'Timeline')
@section('subtitle', 'Pendidikan, pengalaman, dan organisasi')

@section('content')
    <div class="flex items-center justify-between gap-4">
        <p class="text-sm text-muted">Data ini tampil di beranda dan halaman Tentang Saya.</p>
        <x-ui.button :href="route('admin.experiences.create')" icon="plus">Tambah Timeline</x-ui.button>
    </div>

    <div class="card mt-6 !p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-line text-sm">
                <thead class="bg-surface-2 text-left text-2xs uppercase tracking-wider text-muted">
                    <tr>
                        <th class="px-5 py-4 font-semibold">Judul</th>
                        <th class="px-5 py-4 font-semibold">Jenis</th>
                        <th class="px-5 py-4 font-semibold">Organisasi</th>
                        <th class="px-5 py-4 font-semibold">Periode</th>
                        <th class="px-5 py-4 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse ($experiences as $experience)
                        <tr class="transition hover:bg-surface-2/60">
                            <td class="px-5 py-4 font-medium text-text">{{ $experience->title }}</td>
                            <td class="px-5 py-4">
                                <span class="pill">{{ $experience->type_label }}</span>
                            </td>
                            <td class="px-5 py-4 text-muted">{{ $experience->organization ?? '-' }}</td>
                            <td class="px-5 py-4 font-mono text-2xs text-muted">{{ $experience->period }}</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.experiences.edit', $experience) }}" class="icon-button"
                                        aria-label="Edit {{ $experience->title }}">
                                        <x-icon name="edit" class="h-4 w-4" />
                                    </a>

                                    <form method="POST" action="{{ route('admin.experiences.destroy', $experience) }}"
                                        onsubmit="return confirm('Hapus timeline {{ $experience->title }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="icon-button hover:border-red-500/40 hover:text-red-400"
                                            aria-label="Hapus {{ $experience->title }}">
                                            <x-icon name="trash" class="h-4 w-4" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-16 text-center">
                                <x-icon name="clock" class="mx-auto h-8 w-8 text-muted" />
                                <p class="mt-4 text-sm text-muted">Belum ada data timeline.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($experiences->hasPages())
        <div class="mt-6">
            {{ $experiences->links() }}
        </div>
    @endif
@endsection