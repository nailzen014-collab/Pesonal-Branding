@extends('layouts.admin')

@section('title', 'Sertifikat')
@section('subtitle', 'Sertifikat dan pencapaian yang tampil di halaman /sertifikat')

@section('content')
    <div class="flex items-center justify-between gap-4">
        <p class="text-sm text-muted">Unggah gambar sertifikat (maks 2 MB) atau isi tautan eksternal.</p>
        <x-ui.button :href="route('admin.certificates.create')" icon="plus">Tambah Sertifikat</x-ui.button>
    </div>

    <div class="card mt-6 !p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-line text-sm">
                <thead class="bg-surface-2 text-left text-2xs uppercase tracking-wider text-muted">
                    <tr>
                        <th class="px-5 py-4 font-semibold">Sertifikat</th>
                        <th class="px-5 py-4 font-semibold">Penerbit</th>
                        <th class="px-5 py-4 font-semibold">Tanggal</th>
                        <th class="px-5 py-4 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse ($certificates as $certificate)
                        <tr class="transition hover:bg-surface-2/60">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    @if ($certificate->image_url)
                                        <img src="{{ $certificate->image_url }}" alt=""
                                            class="h-10 w-16 rounded-lg object-cover">
                                    @else
                                        <span class="inline-flex h-10 w-16 items-center justify-center rounded-lg border border-line bg-surface-2">
                                            <x-icon name="award" class="h-4 w-4 text-primary" />
                                        </span>
                                    @endif
                                    <span class="font-medium text-text">{{ $certificate->title }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-muted">{{ $certificate->issuer ?? '-' }}</td>
                            <td class="px-5 py-4 font-mono text-2xs text-muted">
                                {{ $certificate->issued_at?->format('d M Y') ?? '-' }}
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    @if ($certificate->url)
                                        <a href="{{ $certificate->url }}" target="_blank" rel="noopener noreferrer" class="icon-button"
                                            aria-label="Buka {{ $certificate->title }}">
                                            <x-icon name="external" class="h-4 w-4" />
                                        </a>
                                    @endif

                                    <a href="{{ route('admin.certificates.edit', $certificate) }}" class="icon-button"
                                        aria-label="Edit {{ $certificate->title }}">
                                        <x-icon name="edit" class="h-4 w-4" />
                                    </a>

                                    <form method="POST" action="{{ route('admin.certificates.destroy', $certificate) }}"
                                        onsubmit="return confirm('Hapus sertifikat {{ $certificate->title }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="icon-button hover:border-red-500/40 hover:text-red-400"
                                            aria-label="Hapus {{ $certificate->title }}">
                                            <x-icon name="trash" class="h-4 w-4" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-16 text-center">
                                <x-icon name="award" class="mx-auto h-8 w-8 text-muted" />
                                <p class="mt-4 text-sm text-muted">Belum ada sertifikat.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($certificates->hasPages())
        <div class="mt-6">
            {{ $certificates->links() }}
        </div>
    @endif
@endsection