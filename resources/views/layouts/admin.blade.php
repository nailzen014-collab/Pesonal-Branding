@php
    $siteName = setting('site_name', 'Abbad Nailun Nabhan');

    $adminMenu = [
        ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => 'sparkles'],
        ['label' => 'Proyek', 'route' => 'admin.projects.index', 'icon' => 'folder'],
        ['label' => 'Sinkron GitHub', 'route' => 'admin.github.index', 'icon' => 'github'],
        ['label' => 'Skills', 'route' => 'admin.skills.index', 'icon' => 'code'],
        ['label' => 'Timeline', 'route' => 'admin.experiences.index', 'icon' => 'clock'],
        ['label' => 'Sertifikat', 'route' => 'admin.certificates.index', 'icon' => 'award'],
        ['label' => 'Pesan', 'route' => 'admin.messages.index', 'icon' => 'inbox'],
        ['label' => 'Pengaturan', 'route' => 'admin.settings.edit', 'icon' => 'cog'],
    ];
@endphp
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Panel Admin — {{ $siteName }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-ink font-sans text-text antialiased">
<div x-data="{ sidebar: false }" class="min-h-screen lg:flex">

    {{-- Sidebar (desktop) --}}
    <aside class="hidden w-72 shrink-0 flex-col border-r border-line bg-surface lg:flex">
        <div class="flex h-20 items-center px-6">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                <x-icon name="code" class="h-5 w-5 text-primary" />
                <span class="font-display text-lg font-bold text-text">{{ $siteName }}</span>
            </a>
        </div>

        <nav class="flex-1 space-y-1 px-4 py-4" aria-label="Menu admin">
            @foreach ($adminMenu as $item)
                @php $active = request()->routeIs($item['route']); @endphp
                <a href="{{ route($item['route']) }}"
                    @class([
                        'flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium transition',
                        'bg-primary/10 text-primary' => $active,
                        'text-muted hover:bg-surface-2 hover:text-text' => ! $active,
                    ])>
                    <x-icon :name="$item['icon']" class="h-5 w-5" />
                    {{ $item['label'] }}

                    @if ($item['route'] === 'admin.messages.index' && ($unreadCount ?? 0) > 0)
                        <span class="ml-auto rounded-full bg-primary px-2 py-0.5 text-2xs font-semibold text-white">
                            {{ $unreadCount }}
                        </span>
                    @endif
                </a>
            @endforeach
        </nav>

        <div class="space-y-3 border-t border-line p-4">
            <a href="{{ route('home') }}" target="_blank" class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm text-muted transition hover:bg-surface-2 hover:text-text">
                <x-icon name="external" class="h-5 w-5" />
                Lihat Website
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex w-full items-center gap-3 rounded-xl px-4 py-2.5 text-sm text-muted transition hover:bg-surface-2 hover:text-primary">
                    <x-icon name="close" class="h-5 w-5" />
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    {{-- Sidebar (mobile) --}}
    <div x-show="sidebar" x-cloak class="fixed inset-0 z-50 lg:hidden">
        <div class="absolute inset-0 bg-black/70" @click="sidebar = false"></div>
        <aside x-transition.duration.200ms
            class="absolute inset-y-0 left-0 flex w-72 flex-col border-r border-line bg-surface">
            <div class="flex h-20 items-center justify-between px-6">
                <span class="font-display text-lg font-bold text-text">{{ $siteName }}</span>
                <button type="button" class="icon-button" @click="sidebar = false" aria-label="Tutup menu">
                    <x-icon name="close" class="h-5 w-5" />
                </button>
            </div>

            <nav class="flex-1 space-y-1 px-4 py-4">
                @foreach ($adminMenu as $item)
                    @php $active = request()->routeIs($item['route']); @endphp
                    <a href="{{ route($item['route']) }}" @click="sidebar = false"
                        @class([
                            'flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium transition',
                            'bg-primary/10 text-primary' => $active,
                            'text-muted hover:bg-surface-2 hover:text-text' => ! $active,
                        ])>
                        <x-icon :name="$item['icon']" class="h-5 w-5" />
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>

            <div class="border-t border-line p-4">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex w-full items-center gap-3 rounded-xl px-4 py-2.5 text-sm text-muted transition hover:bg-surface-2 hover:text-primary">
                        <x-icon name="close" class="h-5 w-5" />
                        Keluar
                    </button>
                </form>
            </div>
        </aside>
    </div>

    {{-- Konten --}}
    <div class="flex min-w-0 flex-1 flex-col">
        <header class="sticky top-0 z-40 flex h-20 items-center gap-4 border-b border-line bg-ink/90 px-5 backdrop-blur sm:px-8">
            <button type="button" class="icon-button lg:hidden" @click="sidebar = true" aria-label="Buka menu">
                <x-icon name="menu" class="h-5 w-5" />
            </button>

            <div class="min-w-0">
                <h1 class="truncate font-display text-lg font-semibold text-text">@yield('title', 'Dashboard')</h1>
                @hasSection('subtitle')
                    <p class="truncate text-xs text-muted">@yield('subtitle')</p>
                @endif
            </div>

            <div class="ml-auto flex items-center gap-3">
                <a href="{{ route('home') }}" target="_blank" class="icon-button" aria-label="Lihat website">
                    <x-icon name="external" class="h-5 w-5" />
                </a>
                <div class="hidden items-center gap-2 rounded-xl border border-line bg-surface px-3 py-2 sm:flex">
                    <x-icon name="users" class="h-4 w-4 text-primary" />
                    <span class="text-xs font-medium text-text">{{ auth()->user()?->name }}</span>
                </div>
            </div>
        </header>

        <main class="flex-1 px-5 py-8 sm:px-8">
            <x-ui.alert />
            <x-ui.alert type="error" />

            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')
</body>
</html>