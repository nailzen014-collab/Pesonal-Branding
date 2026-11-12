@extends('layouts.admin')

@section('title', 'Kotak Masuk')
@section('subtitle', $unreadCount.' pesan belum dibaca')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="inline-flex rounded-xl border border-line bg-surface p-1" role="group" aria-label="Filter pesan">
            <a href="{{ route('admin.messages.index') }}"
                @class([
                    'rounded-lg px-4 py-1.5 text-sm font-medium transition',
                    'bg-primary/10 text-primary' => $filter !== 'unread',
                    'text-muted hover:text-text' => $filter === 'unread',
                ])>Semua</a>

            <a href="{{ route('admin.messages.index', ['filter' => 'unread']) }}"
                @class([
                    'rounded-lg px-4 py-1.5 text-sm font-medium transition',
                    'bg-primary/10 text-primary' => $filter === 'unread',
                    'text-muted hover:text-text' => $filter !== 'unread',
                ])>
                Belum dibaca
                <span @class([
                    'ms-1 rounded-full px-1.5 py-0.5 text-2xs font-semibold',
                    'bg-primary text-white' => $unreadCount > 0,
                    'bg-surface-2 text-muted' => $unreadCount === 0,
                ])>{{ $unreadCount }}</span>
            </a>
        </div>

        <p class="text-sm text-muted">{{ $messages->total() }} pesan</p>
    </div>

    <div class="mt-6 space-y-3" data-admin-stagger>
        @forelse ($messages as $message)
            <article @class([
                'card !p-0',
                'hover:border-primary/40' => ! $message->isRead(),
                'opacity-70' => $message->isRead(),
            ])>
                <div class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex min-w-0 items-start gap-3">
                        <span @class([
                            'mt-1.5 h-2 w-2 shrink-0 rounded-full',
                            'bg-primary' => ! $message->isRead(),
                            'bg-line' => $message->isRead(),
                        ])></span>

                        <div class="min-w-0">
                            <a href="{{ route('admin.messages.show', $message) }}" class="group block focus-visible:rounded-lg">
                                <span class="flex flex-wrap items-center gap-2">
                                    <span @class([
                                        'text-sm',
                                        'font-semibold text-text' => ! $message->isRead(),
                                        'text-muted' => $message->isRead(),
                                    ])>{{ $message->subject }}</span>

                                    @unless ($message->isRead())
                                        <span class="pill border-primary/40 text-primary">Baru</span>
                                    @endunless
                                </span>

                                <span class="mt-1 block truncate text-sm text-muted transition-colors group-hover:text-text">
                                    {{ Str::limit($message->body, 120) }}
                                </span>
                            </a>

                            <p class="mt-1.5 truncate text-2xs text-muted">
                                Dari {{ $message->name }} &middot;
                                <a href="mailto:{{ $message->email }}" class="hover:text-primary">{{ $message->email }}</a>
                            </p>
                        </div>
                    </div>

                    <div class="flex shrink-0 items-center justify-between gap-3 border-t border-line/60 pt-3 sm:flex-col sm:items-end sm:border-0 sm:pt-0">
                        <span class="font-mono text-2xs text-muted">{{ $message->created_at->format('d M Y, H:i') }}</span>

                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.messages.show', $message) }}" class="icon-button"
                                aria-label="Buka pesan {{ $message->subject }}">
                                <x-icon name="eye" class="h-4 w-4" />
                            </a>

                            <form method="POST" action="{{ route('admin.messages.destroy', $message) }}"
                                onsubmit="return confirm('Hapus pesan ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="icon-button hover:border-red-500/40 hover:text-red-400"
                                    aria-label="Hapus pesan {{ $message->subject }}">
                                    <x-icon name="trash" class="h-4 w-4" />
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
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
