@extends('layouts.public')

@section('content')
    @php
        $siteName = setting('site_name', 'Abbad Nailun Nabhan');
        $role = setting('role', 'Web Developer & Fullstack Engineer');
        $tagline = setting('tagline', 'Membangun web modern dengan Laravel, PHP, dan JavaScript.');
        $education = setting('education_level');

        $photo = setting('profile_photo') ?: 'images/profile.jpg';
        $photoUrl = str_starts_with($photo, 'http') ? $photo : (str_starts_with($photo, 'images/') ? asset($photo) : asset('storage/'.$photo));

        // Frasa animasi ketik diturunkan dari role agar bisa diedit lewat panel admin.
        $rolePhrases = array_values(array_unique(array_filter(array_merge(
            [$role],
            preg_split('/\s*[&|,\/]+\s*/u', $role) ?: []
        ))));

        $skillIcons = [
            'frontend' => 'code',
            'backend' => 'layers',
            'database' => 'database',
            'tools' => 'tools',
        ];
    @endphp

    {{-- Hero: foto, nama, role, tagline, dan dua CTA utama (FR-01) --}}
<section class="relative overflow-hidden pb-24 pt-32 sm:pt-40">
    <div class="pointer-events-none absolute inset-0 bg-aurora" aria-hidden="true"></div>
    <div class="pointer-events-none absolute inset-0 bg-grid" aria-hidden="true"></div>

    <div class="pointer-events-none absolute -left-32 top-24 h-72 w-72 animate-drift rounded-full bg-primary/20 blur-[100px]" aria-hidden="true"></div>
    <div class="pointer-events-none absolute -right-24 bottom-0 h-80 w-80 animate-drift rounded-full bg-primary-deep/30 blur-[110px]" aria-hidden="true"></div>

    <div class="container-page relative grid items-center gap-14 lg:grid-cols-2">
        <div class="order-2 lg:order-1">
            <span class="eyebrow animate-fade-up">
                <span class="relative inline-flex h-2 w-2">
                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-primary opacity-75"></span>
                    <span class="relative inline-flex h-2 w-2 rounded-full bg-primary"></span>
                </span>
                Tersedia untuk proyek & kolaborasi
            </span>

            <h1 class="mt-7 text-shine font-display text-4xl font-bold leading-[1.05] sm:text-6xl lg:text-7xl animate-fade-up"
                style="animation-delay: 80ms">
                {{ $siteName }}
            </h1>

            <p class="tw-pending mt-5 animate-fade-up font-display text-lg text-primary-soft sm:text-2xl"
                style="animation-delay: 160ms"
                data-typewriter
                data-phrases="{{ implode('|', $rolePhrases) }}">{{ $role }}</p>

            <p class="mt-6 max-w-xl text-base leading-relaxed text-muted animate-fade-up" style="animation-delay: 240ms">
                {{ $tagline }}
                @if ($education)
                    Saya sedang menempuh pendidikan di tingkat {{ $education }}.
                @endif
            </p>

            <div class="mt-10 flex flex-col gap-3 sm:flex-row animate-fade-up" style="animation-delay: 320ms">
                <x-ui.button :href="route('portofolio')" icon="folder" class="!px-7 !py-3">Lihat Portofolio</x-ui.button>
                <x-ui.button :href="route('kontak')" variant="secondary" icon="mail" class="!px-7 !py-3">Hubungi Saya</x-ui.button>

                @if ($cv = setting('cv_file'))
                    <a href="{{ asset('storage/'.$cv) }}" download
                        class="inline-flex items-center justify-center gap-2 rounded-xl px-6 py-3 text-sm font-semibold text-muted transition duration-300 hover:-translate-y-0.5 hover:text-primary">
                        <x-icon name="download" class="h-4 w-4" />
                        Unduh CV
                    </a>
                @endif
            </div>

            {{-- Marquee teknologi: penanda bahwa stack-nya teknis --}}
            <div class="mt-14 overflow-hidden border-y border-line-soft py-4 animate-fade-up" style="animation-delay: 400ms">
                <div class="flex w-max animate-marquee gap-10 pr-10">
                    @for ($copy = 0; $copy < 2; $copy++)
                        @foreach (['Laravel', 'PHP', 'Blade', 'Tailwind CSS', 'JavaScript', 'Alpine.js', 'MySQL', 'Git'] as $tech)
                            <span class="flex shrink-0 items-center gap-2 font-mono text-xs uppercase tracking-[0.2em] text-muted/80">
                                <span class="h-1.5 w-1.5 rounded-full bg-primary"></span>
                                {{ $tech }}
                            </span>
                        @endforeach
                    @endfor
                </div>
            </div>
        </div>

        {{-- Foto dengan bingkai gradien merah-hitam supaya menyatu dengan tema --}}
        <div class="order-1 flex justify-center lg:order-2">
            <div class="relative animate-fade-up" style="animation-delay: 200ms">
                <div class="absolute -inset-6 animate-drift rounded-[2.5rem] bg-gradient-to-br from-primary/35 via-primary-deep/10 to-transparent blur-3xl" aria-hidden="true"></div>

                <div class="border-gradient relative rounded-[2rem] bg-gradient-to-b from-surface-2/80 to-surface/60 p-2 shadow-card-lg backdrop-blur"
                    data-tilt>
                    <img src="{{ $photoUrl }}" alt="Foto profil {{ $siteName }}"
                        width="512" height="512" fetchpriority="high" decoding="async"
                        class="h-64 w-64 rounded-[1.5rem] object-cover object-top sm:h-80 sm:w-80">

                    <div class="pointer-events-none absolute inset-2 rounded-[1.5rem] bg-gradient-to-t from-ink/80 via-transparent to-transparent" aria-hidden="true"></div>

                    <span class="sheen-band" aria-hidden="true"></span>

                    <div class="absolute bottom-6 left-6 right-6 flex flex-wrap items-center gap-2">
                        <span class="pill border-primary/30 bg-ink/70 text-text backdrop-blur">
                            <span class="h-1.5 w-1.5 rounded-full bg-primary"></span>
                            Web Developer
                        </span>
                        <span class="pill border-primary/30 bg-ink/70 text-text backdrop-blur">Fullstack Engineer</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Statistik singkat (bagian 3 Beranda) --}}
<section class="border-y border-line-soft bg-ink-soft/60">
    <div class="container-page">
        <div class="grid grid-cols-2 gap-px overflow-hidden rounded-2xl border border-line-soft bg-line-soft lg:grid-cols-4">
            @foreach ([
                ['value' => $stats['projects'], 'label' => 'Proyek Dirilis', 'icon' => 'folder'],
                ['value' => $stats['technologies'], 'label' => 'Teknologi', 'icon' => 'layers'],
                ['value' => $stats['repositories'], 'label' => 'Repository GitHub', 'icon' => 'github'],
                ['value' => $stats['skills'], 'label' => 'Skill Terdaftar', 'icon' => 'sparkles'],
            ] as $stat)
                <div class="group relative bg-surface/80 px-6 py-10 text-center transition duration-500 hover:bg-surface-2/80">
                    <span class="inline-flex h-11 w-11 items-center justify-center rounded-xl border border-line bg-surface-2/70 backdrop-blur text-primary transition duration-500 group-hover:-translate-y-1 group-hover:border-primary/40 group-hover:shadow-glow-soft">
                        <x-icon :name="$stat['icon']" class="h-5 w-5" />
                    </span>
                    <p class="mt-4 font-display text-3xl font-bold text-text sm:text-4xl"
                        data-count-to="{{ $stat['value'] }}">{{ $stat['value'] }}</p>
                    <p class="mt-1 text-xs uppercase tracking-wider text-muted">{{ $stat['label'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Tentang singkat --}}
<section class="container-page py-28">
    <div class="grid gap-14 lg:grid-cols-2 lg:items-center">
        <div class="reveal">
            <x-section-title eyebrow="Tentang Saya"
                title="Karya yang bisa dibuka, diuji, dan dinilai" />
            <p class="mt-6 leading-relaxed text-muted">
                {{ setting('bio', 'Saya membangun aplikasi web dari nol: mulai dari desain basis data, backend, sampai antarmuka yang rapi. Setiap proyek di bawah ini tersedia di GitHub sehingga siapa pun bisa melihat kode dan cara kerjanya.') }}
            </p>

            <div class="mt-8 grid gap-4 sm:grid-cols-2">
                <div class="card card-hover">
                    <x-icon name="rocket" class="h-5 w-5 text-primary" />
                    <p class="mt-4 font-display text-sm font-semibold text-text">Fokus belajar</p>
                    <p class="mt-1 text-sm text-muted">Laravel, PHP, JavaScript, dan Tailwind CSS.</p>
                </div>
                <div class="card card-hover">
                    <x-icon name="check" class="h-5 w-5 text-primary" />
                    <p class="mt-4 font-display text-sm font-semibold text-text">Cara kerja</p>
                    <p class="mt-1 text-sm text-muted">Rapi, terdokumentasi, dan selalu versi terbaru di GitHub.</p>
                </div>
            </div>

            <div class="mt-8">
                <x-ui.button :href="route('tentang')" variant="outline" icon="arrow-right">Selengkapnya</x-ui.button>
            </div>
        </div>

        <div class="reveal grid gap-4 sm:grid-cols-2">
            <div class="card">
                <x-icon name="shield" class="h-6 w-6 text-primary" />
                <p class="mt-4 font-display text-lg font-semibold text-text">Kode yang mudah dibaca</p>
                <p class="mt-2 text-sm text-muted">Struktur MVC, validasi di Form Request, dan eager loading.</p>
            </div>
            <div class="card">
                <x-icon name="rocket" class="h-6 w-6 text-primary" />
                <p class="mt-4 font-display text-lg font-semibold text-text">UI yang konsisten</p>
                <p class="mt-2 text-sm text-muted">Design system sendiri: token warna, komponen Blade reusable.</p>
            </div>
            <div class="card sm:col-span-2">
                <x-icon name="github" class="h-6 w-6 text-primary" />
                <p class="mt-4 font-display text-lg font-semibold text-text">Open source</p>
                <p class="mt-2 text-sm text-muted">
                    Semua proyek saya simpan di
                    <a href="{{ setting('github_url', 'https://github.com/nailzen014-collab') }}" target="_blank" rel="noopener noreferrer"
                        class="text-primary hover:underline">GitHub</a>
                    agar mudah direview siapa saja.
                </p>
            </div>
        </div>
    </div>
</section>

{{-- Skills ringkas --}}
@if ($skills->isNotEmpty())
    <section class="relative border-y border-line-soft bg-ink-soft/60 py-24">
        <div class="container-page">
            <div class="reveal flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
                <x-section-title eyebrow="Skill" title="Teknologi yang Saya Kuasai" />
                <x-ui.button :href="route('skills')" variant="ghost" icon="arrow-right">Semua skill</x-ui.button>
            </div>

            <div class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($skills as $skill)
                    <div class="reveal card card-hover">
                        <div class="flex items-center justify-between">
                            <span class="inline-flex h-11 w-11 items-center justify-center rounded-xl border border-line bg-surface-2/70 backdrop-blur text-primary">
                                <x-icon :name="$skillIcons[$skill->category] ?? 'code'" class="h-5 w-5" />
                            </span>
                            <span class="font-mono text-2xs text-muted">{{ $skill->level }}%</span>
                        </div>
                        <p class="mt-4 font-display text-base font-semibold text-text">{{ $skill->name }}</p>
                        <p class="mt-0.5 text-2xs uppercase tracking-wider text-muted">{{ $skill->category_label }}</p>

                        <div class="mt-4 h-1.5 w-full overflow-hidden rounded-full bg-surface-2">
                            <div class="h-full rounded-full bg-gradient-to-r from-primary-soft to-primary-dark"
                                style="width: {{ $skill->level }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif

{{-- Proyek unggulan --}}
<section class="container-page py-28">
    <div class="reveal flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
        <x-section-title eyebrow="Portofolio" title="Proyek Unggulan" />
        <x-ui.button :href="route('portofolio')" variant="ghost" icon="arrow-right">Lihat semua proyek</x-ui.button>
    </div>

    @if ($featuredProjects->isEmpty())
        <div class="mt-12 rounded-2xl border border-dashed border-line bg-surface/60 backdrop-blur px-6 py-16 text-center">
            <x-icon name="folder" class="mx-auto h-8 w-8 text-muted" />
            <p class="mt-4 text-sm text-muted">Belum ada proyek unggulan. Tambahkan lewat panel admin.</p>
        </div>
    @else
        <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($featuredProjects as $project)
                <div class="reveal">
                    <x-project-card :project="$project" />
                </div>
            @endforeach
        </div>
    @endif
</section>

{{-- Timeline singkat --}}
@if ($experiences->isNotEmpty())
    <section class="relative border-y border-line-soft bg-ink-soft/60 py-24">
        <div class="container-page grid gap-14 lg:grid-cols-[minmax(0,1fr)_minmax(0,1.2fr)]">
            <div class="reveal">
                <x-section-title eyebrow="Perjalanan" title="Pendidikan & Pengalaman" />
                <p class="mt-6 leading-relaxed text-muted">
                    Riwayat pendidikan, pengalaman, dan organisasi yang pernah saya ikuti.
                </p>
                <div class="mt-8">
                    <x-ui.button :href="route('tentang')" variant="outline" icon="arrow-right">Lihat timeline lengkap</x-ui.button>
                </div>
            </div>

            <ol class="reveal relative space-y-8 border-l border-line pl-8">
                @foreach ($experiences as $experience)
                    <li class="relative">
                        <span class="absolute -left-[2.6rem] flex h-6 w-6 items-center justify-center rounded-full border border-primary/40 bg-ink">
                            <span class="h-2 w-2 rounded-full bg-primary"></span>
                        </span>
                        <p class="font-mono text-2xs uppercase tracking-wider text-primary">{{ $experience->period }}</p>
                        <h3 class="mt-1 font-display text-base font-semibold text-text">{{ $experience->title }}</h3>
                        @if ($experience->organization)
                            <p class="text-sm text-muted">{{ $experience->organization }}</p>
                        @endif
                        @if ($experience->description)
                            <p class="mt-2 text-sm leading-relaxed text-muted">{{ $experience->description }}</p>
                        @endif
                    </li>
                @endforeach
            </ol>
        </div>
    </section>
@endif

{{-- CTA kontak (bagian 8 Beranda) --}}
<section class="container-page py-28">
    <div class="reveal relative overflow-hidden rounded-3xl border-gradient bg-surface/70 backdrop-blur px-6 py-16 text-center sm:px-12">
        <div class="pointer-events-none absolute inset-0 bg-hero-glow opacity-70" aria-hidden="true"></div>

        <div class="relative">
            <span class="eyebrow">Mari Kerja Sama</span>
            <h2 class="mx-auto mt-5 max-w-2xl font-display text-3xl font-bold text-text sm:text-4xl">
                Punya proyek yang ingin dikerjakan?<br class="hidden sm:block">
                <span class="text-gradient">Saya siap membantu.</span>
            </h2>
            <p class="mx-auto mt-5 max-w-xl text-base text-muted">
                Kirim brief singkat lewat form kontak, atau langsung chat via WhatsApp untuk jawaban lebih cepat.
            </p>

            <div class="mt-9 flex flex-col justify-center gap-3 sm:flex-row">
                <x-ui.button :href="route('kontak')" icon="mail">Kirim Pesan</x-ui.button>
                <x-ui.button :href="setting('whatsapp_url', 'https://wa.me/6285694595270')" variant="secondary" icon="whatsapp"
                    target="_blank" rel="noopener noreferrer">Chat WhatsApp</x-ui.button>
            </div>
        </div>
    </div>
    </section>

    {{-- Tanpa JavaScript, teks ketik tetap tampil utuh --}}
    @push('head')
        <noscript>
            <style>[data-typewriter].tw-pending { visibility: visible; }</style>
        </noscript>
    @endpush
@endsection
