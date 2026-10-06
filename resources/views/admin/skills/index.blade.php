@extends('layouts.admin')

@section('title', 'Kelola Skill')
@section('subtitle', 'Daftar skill yang tampil di halaman /skills')

@section('content')
    <div class="flex items-center justify-between gap-4">
        <p class="text-sm text-muted">Skill dikelompokkan per kategori: {{ implode(', ', array_values(App\Models\Skill::CATEGORIES)) }}.</p>
        <x-ui.button :href="route('admin.skills.create')" icon="plus">Tambah Skill</x-ui.button>
    </div>

    <div class="card mt-6 !p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-line text-sm">
                <thead class="bg-surface-2 text-left text-2xs uppercase tracking-wider text-muted">
                    <tr>
                        <th class="px-5 py-4 font-semibold">Nama</th>
                        <th class="px-5 py-4 font-semibold">Kategori</th>
                        <th class="px-5 py-4 font-semibold">Level</th>
                        <th class="px-5 py-4 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse ($skills as $skill)
                        <tr class="transition hover:bg-surface-2/60">
                            <td class="px-5 py-4 font-medium text-text">{{ $skill->name }}</td>
                            <td class="px-5 py-4">
                                <span class="pill">{{ $skill->category_label }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-1.5 w-28 overflow-hidden rounded-full bg-surface-2">
                                        <div class="h-full rounded-full bg-gradient-to-r from-primary-soft to-primary-dark"
                                            style="width: {{ $skill->level }}%"></div>
                                    </div>
                                    <span class="font-mono text-2xs text-muted">{{ $skill->level }}%</span>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.skills.edit', $skill) }}" class="icon-button" aria-label="Edit {{ $skill->name }}">
                                        <x-icon name="edit" class="h-4 w-4" />
                                    </a>

                                    <form method="POST" action="{{ route('admin.skills.destroy', $skill) }}"
                                        onsubmit="return confirm('Hapus skill {{ $skill->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="icon-button hover:border-red-500/40 hover:text-red-400"
                                            aria-label="Hapus {{ $skill->name }}">
                                            <x-icon name="trash" class="h-4 w-4" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-16 text-center">
                                <x-icon name="code" class="mx-auto h-8 w-8 text-muted" />
                                <p class="mt-4 text-sm text-muted">Belum ada data skill.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($skills->hasPages())
        <div class="mt-6">
            {{ $skills->links() }}
        </div>
    @endif
@endsection