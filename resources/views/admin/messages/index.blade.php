@extends('layouts.admin')

@section('title', 'Kotak Masuk')
@section('subtitle', $unreadCount.' pesan belum dibaca')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex gap-2">
            <a href="{{ route('admin.messages.index') }}"
                @class([
                    'rounded-xl border px-4 py-2 text-sm font-medium transition',
                    'border-primary bg-primary/10 text-primary' => $filter !== 'unread',
                    'border-line text-muted hover:text-text' => $filter === 'unread',
                ])>Semua</a>

            <a href="{{ route('admin.messages.index', ['filter' => 'unread']) }}"
                @class([
                    'rounded-xl border px-4 py-2 text-sm font-medium transition',
                    'border-primary bg-primary/10 text-primary' => $filter === 'unread',
                    'border-line text-muted hover:text-text' => $filter !== 'unread',
                ])>Belum dibaca ({{ $unreadCount }})</a>
        </div>
    </div>

    <div class="mt-6 space-y-3">
        @forelse ($messages as $message)
            <article @class([
                'card !p-0 transition',
                'hover:border-primary/40' => ! $message->isRead(),
                'opacity-70' => $message->isRead(),
            ])>
                <a href="{{ route('admin.messages.show', $message) }}" class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <span @class([
                                'h-2 w-2 rounded-full',
                                'bg-primary' => ! $message->isRead(),
                                'bg-line' => $message->isRead(),
                            ])></span>
                            <p @class([
                                'text-sm',
                                'font-semibold text-text' => ! $message->isRead(),
                                'text-muted' => $message->isRead(),
                            ])>{{ $message->subject }}</p>
                            @unless ($message->isRead())
                                <span class="pill border-primary/40 text-primary">Baru</span>
                            @endunless
                        </div>

                        <p class="mt-2 truncate text-sm text-muted">{{ Str::limit($message->body, 120) }}</p>
                        <p class="mt-2 text-2xs text-muted">
                            Dari {{ $message->name }} &middot;
                            <a href="mailto:{{ $message->email }}" class="hover:text-primary">{{ $message->email }}</a>
                        </p>
                    </div>

                    <div class="flex shrink-0 items-center gap-3">
                        <span class="font-mono text-2xs text-muted">{{ $message->created_at->format('d M Y, H:i') }}</span>

                        <form method="POST" action="{{ route('admin.messages.destroy', $message) }}"
                            onsubmit="return confirm('Hapus pesan ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="icon-button hover:border-red-500/40 hover:text-red-400"
                                aria-label="Hapus pesan">
                                <x-icon name="trash" class="h-4 w-4" />
                            </button>
                        </form>
                    </div>
                </a>
            </article>
        @empty
            <div class="card py-16 text-center">
                <x-icon name="inbox" class="mx-auto h-9 w-9 text-muted" />
                <h2 class="mt-5 font-display text-lg font-semibold text-text">Belum ada pesan</h2>
                <p class="mt-2 text-sm text-muted">Pesan dari form kontak akan muncul di sini.</p>
            </div>
        @endforelse
    </div>

    @if ($messages->hasPages())
        <div class="mt-6">
            {{ $messages->links() }}
        </div>
    @endif
@endsection