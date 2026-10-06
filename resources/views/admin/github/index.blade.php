@extends('layouts.admin')

@section('title', 'Sinkron GitHub')
@section('subtitle', 'Impor repository GitHub menjadi proyek')

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
        <form method="POST" action="{{ route('admin.github.store') }}">
            @csrf

            <div class="flex flex-wrap items-center justify-between gap-4">
                <p class="text-sm text-muted">Pilih repository yang ingin dijadikan proyek.</p>

                <div class="flex gap-2">
                    <x-ui.button type="submit" name="publish" value="1" variant="secondary" icon="check">
                        Simpan &amp; Publikasikan
                    </x-ui.button>
                    <x-ui.button type="submit" icon="download">Impor sebagai Draft</x-ui.button>
                </div>
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