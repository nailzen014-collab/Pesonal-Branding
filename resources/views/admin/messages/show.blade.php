@extends('layouts.admin')

@section('title', 'Detail Pesan')
@section('subtitle', 'Diterima '.($message->created_at?->format('d F Y, H:i')))

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.messages.index') }}" class="inline-flex items-center gap-2 text-sm text-muted hover:text-primary">
            <span aria-hidden="true">&larr;</span> Kembali ke kotak masuk
        </a>
    </div>

    <div class="grid gap-6 xl:grid-cols-3">
        <article class="card xl:col-span-2">
            <h2 class="font-display text-xl font-semibold text-text">{{ $message->subject }}</h2>

            <div class="mt-4 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-muted">
                <span class="inline-flex items-center gap-2">
                    <x-icon name="users" class="h-4 w-4 text-primary" />
                    {{ $message->name }}
                </span>
                <a href="mailto:{{ $message->email }}" class="inline-flex items-center gap-2 hover:text-primary">
                    <x-icon name="mail" class="h-4 w-4 text-primary" />
                    {{ $message->email }}
                </a>
            </div>

            <div class="mt-6 whitespace-pre-line leading-relaxed text-muted">{{ $message->body }}</div>
        </article>

        <aside class="space-y-6">
            <div class="card">
                <h2 class="font-display text-base font-semibold text-text">Aksi</h2>

                <div class="mt-5 space-y-3">
                    <x-ui.button :href="'mailto:'.$message->email.'?subject='.urlencode('Re: '.$message->subject)"
                        class="w-full" icon="mail">Balas via Email</x-ui.button>

                    @unless ($message->isRead())
                        <form method="POST" action="{{ route('admin.messages.read', $message) }}">
                            @csrf
                            @method('PUT')
                            <x-ui.button type="submit" variant="secondary" class="w-full" icon="check">Tandai Sudah Dibaca</x-ui.button>
                        </form>
                    @endunless

                    <form method="POST" action="{{ route('admin.messages.destroy', $message) }}"
                        onsubmit="return confirm('Hapus pesan ini?')">
                        @csrf
                        @method('DELETE')
                        <x-ui.button type="submit" variant="danger" class="w-full" icon="trash">Hapus Pesan</x-ui.button>
                    </form>
                </div>
            </div>

            <div class="card">
                <h2 class="font-display text-base font-semibold text-text">Status</h2>
                <dl class="mt-4 space-y-3 text-sm">
                    <div class="flex items-center justify-between gap-4">
                        <dt class="text-muted">Dibaca</dt>
                        <dd class="text-text">{{ $message->isRead() ? 'Sudah' : 'Belum' }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-4">
                        <dt class="text-muted">Waktu</dt>
                        <dd class="text-text">{{ $message->created_at->diffForHumans() }}</dd>
                    </div>
                </dl>
            </div>
        </aside>
    </div>
@endsection