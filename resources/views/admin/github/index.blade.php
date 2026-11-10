@extends('layouts.admin')

@section('title', 'Sinkron GitHub')
@section('subtitle', 'Impor repository GitHub menjadi proyek dan artikel blog')

@section('content')
    <x-ui.alert />

    @unless ($apiAvailable)
        <div class="card">
            <div class="flex flex-col items-center py-14 text-center">
                <x-icon name="github" class="h-9 w-9 text-muted" />
                <h2 class="mt-5 font-display text-lg font-semibold text-text">GitHub belum terhubung</h2>
                <p class="mt-2 max-w-md text-sm text-muted">
                    Isi <code class="font-mono text-primary">GITHUB_TOKEN</code> dan
                    <code class="font-mono text-primary">GITHUB_USERNAME</code> pada file
                    <code class="font-mono text-primary">.env</code>, lalu jalankan
                    <code class="font-mono text-primary">php artisan config:clear</code>.
                </p>
            </div>
        </div>
    @else
        <form method="POST" action="{{ route('admin.github.sync') }}">
            @csrf

            <div class="flex flex-wrap items-center justify-between gap-4">
                <p class="text-sm text-muted">
                    Pilih repository yang ingin dijadikan proyek. Sinkronisasi
                    otomatis berjalan setiap jam dan sekaligus mengambil artikel
                    dari file <code class="font-mono text-primary">blog/*.md</code>
                    di repository milik akun ini dan di repo tambahan pada
                    <code class="font-mono text-primary">GITHUB_BLOG_REPOS</code>;
                    tombol di samping menjalankannya sekarang juga.
                </p>

                <x-ui.button type="submit" icon="refresh">
                    Sinkronkan Semua
                </x-ui.button>
            </div>
        </form>

        @if ($report = session('blog_sync'))
            <div class="card mt-6">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h2 class="font-display text-lg font-semibold text-text">Hasil analisis artikel</h2>
                    <div class="flex flex-wrap gap-2 text-2xs">
                        <span class="pill border-success/40 text-success">{{ $report['created'] }} baru</span>
                        <span class="pill">{{ $report['updated'] }} diperbarui</span>
                        <span class="pill">{{ $report['skipped'] }} dilewati</span>
                        @if ($report['conflicts'] > 0)
                            <span class="pill border-warning/40 text-warning">{{ $report['conflicts'] }} konflik slug</span>
                        @endif
                        @if ($report['errors'] > 0)
                            <span class="pill border-primary/40 text-primary">{{ $report['errors'] }} gagal dibaca</span>
                        @endif
                    </div>
                </div>

                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full divide-y divide-line text-sm">
                        <thead class="text-left text-2xs uppercase tracking-wider text-muted">
                            <tr>
                                <th class="py-3 pr-5 font-semibold">Repository</th>
                                <th class="px-5 py-3 text-right font-semibold">File</th>
                                <th class="px-5 py-3 text-right font-semibold">Baru</th>
                                <th class="px-5 py-3 text-right font-semibold">Update</th>
                                <th class="px-5 py-3 text-right font-semibold">Dilewati</th>
                                <th class="px-5 py-3 text-right font-semibold">Konflik</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line">
                            @forelse ($report['details'] as $row)
                                <tr>
                                    <td class="py-3 pr-5">
                                        <span class="font-mono text-xs text-text">{{ $row['repo'] ?: '(tanpa nama)' }}</span>
                                        @if ($row['error'])
                                            <p class="mt-1 text-2xs text-primary">{{ $row['error'] }}</p>
                                        @elseif ($row['conflict_slugs'] !== [] || $row['skipped_files'] !== [])
                                            <p class="mt-1 text-2xs text-muted">
                                                @foreach ($row['conflict_slugs'] as $file)
                                                    <span class="text-warning">Konflik: {{ $file }}</span><br>
                                                @endforeach
                                                @foreach ($row['skipped_files'] as $file)
                                                    Dilewati: {{ $file }}<br>
                                                @endforeach
                                            </p>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3 text-right font-mono text-xs text-muted">{{ $row['files'] }}</td>
                                    <td class="px-5 py-3 text-right font-mono text-xs text-success">{{ $row['created'] }}</td>
                                    <td class="px-5 py-3 text-right font-mono text-xs text-muted">{{ $row['updated'] }}</td>
                                    <td class="px-5 py-3 text-right font-mono text-xs text-muted">{{ $row['skipped'] }}</td>
                                    <td class="px-5 py-3 text-right font-mono text-xs {{ $row['conflicts'] > 0 ? 'text-warning' : 'text-muted' }}">{{ $row['conflicts'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-6 text-center text-sm text-muted">
                                        Belum ada repository yang dibaca.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <p class="mt-4 text-2xs text-muted">
                    Slug yang dipakai lebih dari satu repo memakai versi repo pertama,
                    sisanya dilewati. Ganti slug artikelnya lalu sinkronkan ulang.
                </p>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.github.store') }}" class="mt-6">
            @csrf

            <div class="flex flex-wrap items-center justify-end gap-2">
                <x-ui.button type="submit" name="publish" value="1" variant="secondary" icon="check">
                    Simpan &amp; Publikasikan
                </x-ui.button>
                <x-ui.button type="submit" icon="download">Impor sebagai Draft</x-ui.button>
            </div>

            <div class="card mt-6 !p-0">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-line text-sm">
                        <thead class="bg-surface-2 text-left text-2xs uppercase tracking-wider text-muted">
                            <tr>
                                <th class="w-12 px-5 py-4">
                                    <span class="sr-only">Pilih</span>
                                </th>
                                <th class="px-5 py-4 font-semibold">Repository</th>
                                <th class="px-5 py-4 font-semibold">Bahasa</th>
                                <th class="px-5 py-4 font-semibold">Fork</th>
                                <th class="px-5 py-4 font-semibold">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line">
                            @foreach ($repositories as $repo)
                                @php $imported = in_array($repo['id'], $importedIds); @endphp
                                <tr class="transition hover:bg-surface-2/60">
                                    <td class="px-5 py-4">
                                        <input type="checkbox" name="repos[]" value="{{ $repo['id'] }}"
                                            @checked($imported) @disabled($imported)
                                            class="rounded border-line bg-surface-2 text-primary focus:ring-primary focus:ring-offset-ink">
                                    </td>
                                    <td class="px-5 py-4">
                                        <a href="{{ $repo['url'] }}" target="_blank" rel="noopener noreferrer"
                                            class="inline-flex items-center gap-2 font-medium text-text hover:text-primary">
                                            {{ $repo['name'] }}
                                            <x-icon name="external" class="h-3.5 w-3.5 text-muted" />
                                        </a>
                                        @if ($repo['description'])
                                            <p class="mt-1 line-clamp-2 text-2xs text-muted">{{ $repo['description'] }}</p>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4">
                                        @if ($repo['language'])
                                            <span class="pill">{{ $repo['language'] }}</span>
                                        @else
                                            <span class="text-2xs text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 font-mono text-2xs text-muted">{{ $repo['forks'] ?? 0 }}</td>
                                    <td class="px-5 py-4">
                                        @if ($imported)
                                            <span class="pill border-success/40 text-success">Sudah diimpor</span>
                                        @else
                                            <span class="pill">Belum diimpor</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </form>
    @endunless
@endsection