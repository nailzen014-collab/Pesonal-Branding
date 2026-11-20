@extends('layouts.admin')

@section('title', 'Dashboard')
@section('subtitle', 'Ringkasan isi website')

@section('content')
    {{-- Kartu statistik --}}
    <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ([
            ['label' => 'Proyek Dirilis', 'value' => $stats['published_projects'], 'icon' => 'folder', 'hint' => $stats['projects'].' total, '.$stats['featured_projects'].' unggulan'],
            ['label' => 'Artikel Blog', 'value' => $stats['published_posts'], 'icon' => 'document', 'hint' => $stats['posts'].' total artikel'],
            ['label' => 'Skill', 'value' => $stats['skills'], 'icon' => 'sparkles', 'hint' => 'Terdaftar di halaman skills'],
            ['label' => 'Pesan Belum Dibaca', 'value' => $stats['unread_messages'], 'icon' => 'inbox', 'hint' => 'Total pesan masuk'],
        ] as $card)
            <div class="card dashboard-gloss">
                <div class="flex items-start justify-between">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-primary/10">
                        <x-icon :name="$card['icon']" class="h-5 w-5 text-primary" />
                    </span>
                    <span class="font-display text-3xl font-bold text-text">{{ $card['value'] }}</span>
                </div>
                <p class="mt-4 text-sm font-medium text-text">{{ $card['label'] }}</p>
                <p class="mt-1 text-2xs text-muted">{{ $card['hint'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- Aksi cepat --}}
    <div class="mt-8 flex flex-wrap gap-3">
        <x-ui.button :href="route('admin.projects.create')" icon="plus">Tambah Proyek</x-ui.button>
        <x-ui.button :href="route('admin.posts.create')" icon="document">Tambah Artikel</x-ui.button>
        <x-ui.button :href="route('admin.github.index')" variant="secondary" icon="github">Sinkron GitHub</x-ui.button>
        <x-ui.button :href="route('admin.messages.index')" variant="outline" icon="inbox">Kotak Masuk</x-ui.button>
        <x-ui.button :href="route('admin.settings.edit')" variant="outline" icon="cog">Pengaturan</x-ui.button>
    </div>

    @php
        $radarAxes = [
            ['x' => 160, 'y' => 38],
            ['x' => 270, 'y' => 138],
            ['x' => 160, 'y' => 238],
            ['x' => 50, 'y' => 138],
        ];
        $radarDataPoints = [];

        foreach ($skillChart as $index => $skill) {
            $ratio = min(100, max(0, $skill['average_level'])) / 100;
            $radarDataPoints[] = [
                'x' => 160 + (($radarAxes[$index]['x'] - 160) * $ratio),
                'y' => 138 + (($radarAxes[$index]['y'] - 138) * $ratio),
            ];
        }

        $radarPoints = collect($radarDataPoints)
            ->map(fn (array $point): string => number_format($point['x'], 2, '.', '').','.number_format($point['y'], 2, '.', ''))
            ->implode(' ');
        $maxSkillCount = max(1, $skillChart->max('count'));
    @endphp

    {{-- Grafik skill --}}
    <div class="relative mt-8 grid gap-6 xl:grid-cols-2" x-data="{ activeSkill: null }">
        <div class="dashboard-spike pointer-events-none absolute -left-3 top-28 hidden h-12 w-8 animate-drift bg-gradient-to-r from-primary/70 to-primary/10 opacity-70 sm:block" aria-hidden="true"></div>
        <div class="dashboard-spike pointer-events-none absolute -right-3 bottom-20 hidden h-14 w-9 rotate-180 animate-float bg-gradient-to-r from-gold/60 to-primary/10 opacity-60 sm:block" aria-hidden="true"></div>

        <section class="card dashboard-gloss">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="font-display text-lg font-semibold text-text">Level Skill per Kategori</h2>
                    <p class="mt-1 text-xs text-muted">Rata-rata level skill pada skala 0–100.</p>
                </div>
                <span class="rounded-full border border-line px-3 py-1 text-2xs text-muted">Radar</span>
            </div>

            <div class="mt-5 grid items-center gap-5 sm:grid-cols-[minmax(0,1fr)_auto]">
                <svg viewBox="0 0 320 276" class="mx-auto w-full max-w-sm cursor-crosshair" role="img" aria-labelledby="skills-radar-title skills-radar-description">
                    <title id="skills-radar-title">Rata-rata level skill per kategori</title>
                    <desc id="skills-radar-description">Grafik radar membandingkan rata-rata level Frontend, Backend, Database, dan Tools.</desc>

                    <g class="fill-none stroke-line/70" stroke-width="1">
                        <circle cx="160" cy="138" r="25"></circle>
                        <circle cx="160" cy="138" r="50"></circle>
                        <circle cx="160" cy="138" r="75"></circle>
                        <circle cx="160" cy="138" r="100"></circle>
                    </g>
                    <g class="fill-current text-muted/70" font-family="JetBrains Mono, monospace" font-size="8">
                        <text x="164" y="135">25</text>
                        <text x="164" y="110">50</text>
                        <text x="164" y="85">75</text>
                        <text x="164" y="60">100</text>
                    </g>

                    <g class="text-line" fill="none" stroke="currentColor" stroke-width="1">
                        <polygon points="160,113 187.5,138 160,163 132.5,138"></polygon>
                        <polygon points="160,88 215,138 160,188 105,138"></polygon>
                        <polygon points="160,63 242.5,138 160,213 77.5,138"></polygon>
                        <polygon points="160,38 270,138 160,238 50,138"></polygon>
                        <line x1="160" y1="138" x2="160" y2="38"></line>
                        <line x1="160" y1="138" x2="270" y2="138"></line>
                        <line x1="160" y1="138" x2="160" y2="238"></line>
                        <line x1="160" y1="138" x2="50" y2="138"></line>
                    </g>

                    <polygon
                        points="{{ $radarPoints }}"
                        class="dashboard-radar-polygon fill-primary/20 stroke-primary transition-all"
                        :class="{ 'fill-primary/35 stroke-primary': activeSkill !== null }"
                        stroke-width="2"
                    ></polygon>
                    @foreach ($radarDataPoints as $index => $point)
                        <g
                            @mouseenter="activeSkill = {{ $index }}"
                            @mouseleave="activeSkill = null"
                            @click="activeSkill = activeSkill === {{ $index }} ? null : {{ $index }}"
                            class="cursor-pointer"
                        >
                            <circle cx="{{ $point['x'] }}" cy="{{ $point['y'] }}" r="11" fill="transparent"></circle>
                            <circle
                                cx="{{ $point['x'] }}"
                                cy="{{ $point['y'] }}"
                                r="4"
                                class="dashboard-radar-point fill-primary stroke-surface transition-all"
                                :class="activeSkill === {{ $index }} ? 'scale-[1.8]' : ''"
                                style="transform-origin: {{ $point['x'] }}px {{ $point['y'] }}px"
                                stroke-width="2"
                            >
                                <title>{{ $skillChart[$index]['label'] }}: {{ round($skillChart[$index]['average_level']) }}%</title>
                            </circle>
                        </g>
                    @endforeach

                    <g class="fill-current text-muted" font-size="10" text-anchor="middle">
                        <text x="160" y="18">{{ $skillChart[0]['label'] }}</text>
                        <text x="296" y="141">{{ $skillChart[1]['label'] }}</text>
                        <text x="160" y="270">{{ $skillChart[2]['label'] }}</text>
                        <text x="24" y="141">{{ $skillChart[3]['label'] }}</text>
                    </g>
                </svg>

                <ul class="grid grid-cols-2 gap-x-5 gap-y-3 sm:grid-cols-1">
                    @foreach ($skillChart as $index => $skill)
                        <li
                            @mouseenter="activeSkill = {{ $index }}"
                            @mouseleave="activeSkill = null"
                            @click="activeSkill = activeSkill === {{ $index }} ? null : {{ $index }}"
                            class="flex cursor-pointer items-center justify-between gap-5 rounded-lg px-2 py-1 text-xs transition"
                            :class="activeSkill === {{ $index }} ? 'bg-primary/10' : ''"
                        >
                            <span class="text-muted">{{ $skill['label'] }}</span>
                            <span class="font-mono font-medium text-text">{{ round($skill['average_level']) }}%</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>

        <section class="card dashboard-gloss">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="font-display text-lg font-semibold text-text">Jumlah Skill per Kategori</h2>
                    <p class="mt-1 text-xs text-muted">Sebaran skill yang sudah terdaftar.</p>
                </div>
                <span class="rounded-full border border-line px-3 py-1 text-2xs text-muted">Bar</span>
            </div>

            <ul class="mt-7 space-y-5">
                @foreach ($skillChart as $index => $skill)
                    <li
                        @mouseenter="activeSkill = {{ $index }}"
                        @mouseleave="activeSkill = null"
                        @click="activeSkill = activeSkill === {{ $index }} ? null : {{ $index }}"
                        class="-mx-2 cursor-pointer rounded-xl px-2 py-1 transition"
                        :class="activeSkill === {{ $index }} ? 'bg-primary/5' : ''"
                    >
                        <div class="mb-2 flex items-center justify-between gap-4 text-sm">
                            <span class="font-medium text-text">{{ $skill['label'] }}</span>
                            <span class="font-mono text-xs text-muted">{{ $skill['count'] }} skill</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-surface-2">
                            <div
                                class="dashboard-bar-fill h-full rounded-full bg-gradient-to-r from-primary to-gold transition-all duration-300"
                                :class="activeSkill === {{ $index }} ? 'brightness-125 saturate-150' : ''"
                                style="width: {{ ($skill['count'] / $maxSkillCount) * 100 }}%"
                            ></div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </section>
    </div>

    <div class="mt-8 grid gap-6 xl:grid-cols-3">
        {{-- Pesan terbaru --}}
        <section class="card dashboard-gloss">
            <div class="flex items-center justify-between">
                <h2 class="font-display text-lg font-semibold text-text">Pesan Terbaru</h2>
                <a href="{{ route('admin.messages.index') }}" class="text-xs text-primary hover:underline">Lihat semua</a>
            </div>

            @if ($recentMessages->isEmpty())
                <p class="mt-6 text-sm text-muted">Belum ada pesan masuk.</p>
            @else
                <ul class="mt-6 divide-y divide-line" data-admin-stagger>
                    @foreach ($recentMessages as $message)
                        <li class="py-3 first:pt-0 last:pb-0">
                            <a href="{{ route('admin.messages.show', $message) }}" class="group flex items-start gap-3">
                                <span @class([
                                    'mt-1.5 h-2 w-2 shrink-0 rounded-full',
                                    'bg-primary' => ! $message->isRead(),
                                    'bg-line' => $message->isRead(),
                                ])></span>
                                <span class="min-w-0 flex-1">
                                    <span class="flex items-center justify-between gap-3">
                                        <span @class([
                                            'truncate text-sm',
                                            'font-semibold text-text' => ! $message->isRead(),
                                            'text-muted' => $message->isRead(),
                                        ])>{{ $message->name }}</span>
                                        <span class="shrink-0 font-mono text-2xs text-muted">{{ $message->created_at->diffForHumans() }}</span>
                                    </span>
                                    <span class="mt-0.5 block truncate text-xs text-muted group-hover:text-text">
                                        {{ $message->subject }} &mdash; {{ Str::limit($message->body, 60) }}
                                    </span>
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

        {{-- Proyek terbaru --}}
        <section class="card dashboard-gloss">
            <div class="flex items-center justify-between">
                <h2 class="font-display text-lg font-semibold text-text">Proyek Terbaru</h2>
                <a href="{{ route('admin.projects.index') }}" class="text-xs text-primary hover:underline">Kelola</a>
            </div>

            @if ($recentProjects->isEmpty())
                <p class="mt-6 text-sm text-muted">Belum ada proyek. Tambahkan lewat tombol di atas.</p>
            @else
                <ul class="mt-6 divide-y divide-line" data-admin-stagger>
                    @foreach ($recentProjects as $project)
                        <li class="flex items-center justify-between gap-4 py-3 first:pt-0 last:pb-0">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium text-text">{{ $project->title }}</p>
                                <p class="mt-0.5 text-2xs text-muted">
                                    {{ $project->is_published ? 'Dipublikasikan' : 'Draft' }}
                                    @if ($project->category) &middot; {{ $project->category }} @endif
                                </p>
                            </div>

                            <div class="flex shrink-0 items-center gap-2">
                                <a href="{{ route('admin.projects.edit', $project) }}" class="icon-button" aria-label="Edit {{ $project->title }}">
                                    <x-icon name="edit" class="h-4 w-4" />
                                </a>
                                <a href="{{ route('portofolio.show', $project->slug) }}" target="_blank" rel="noopener noreferrer"
                                    class="icon-button" aria-label="Lihat di website">
                                    <x-icon name="eye" class="h-4 w-4" />
                                </a>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

        {{-- Artikel terbaru --}}
        <section class="card dashboard-gloss">
            <div class="flex items-center justify-between">
                <h2 class="font-display text-lg font-semibold text-text">Artikel Terbaru</h2>
                <a href="{{ route('admin.posts.index') }}" class="text-xs text-primary hover:underline">Kelola</a>
            </div>

            @if ($recentPosts->isEmpty())
                <p class="mt-6 text-sm text-muted">Belum ada artikel.</p>
            @else
                <ul class="mt-6 divide-y divide-line" data-admin-stagger>
                    @foreach ($recentPosts as $post)
                        <li class="flex items-center justify-between gap-4 py-3 first:pt-0 last:pb-0">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium text-text">{{ $post->title }}</p>
                                <p class="mt-0.5 text-2xs text-muted">
                                    {{ $post->is_published ? 'Dipublikasikan' : 'Draft' }}
                                    @if ($post->category) &middot; {{ $post->category }} @endif
                                </p>
                            </div>

                            <div class="flex shrink-0 items-center gap-2">
                                <a href="{{ route('admin.posts.edit', $post) }}" class="icon-button" aria-label="Edit {{ $post->title }}">
                                    <x-icon name="edit" class="h-4 w-4" />
                                </a>
                                @if ($post->is_published)
                                    <a href="{{ route('blog.show', $post->slug) }}" target="_blank" rel="noopener noreferrer"
                                        class="icon-button" aria-label="Lihat di website">
                                        <x-icon name="eye" class="h-4 w-4" />
                                    </a>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    </div>
@endsection