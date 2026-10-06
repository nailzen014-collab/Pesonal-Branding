@extends('layouts.public')

@section('content')
    @php
        $siteName = setting('site_name', 'Abbad Nailun Nabhan');
        $role = setting('role', 'Web Developer & Fullstack Engineer');

        $photo = setting('profile_photo') ?: 'images/profile.jpg';
        $photoUrl = str_starts_with($photo, 'http') ? $photo : (str_starts_with($photo, 'images/') ? asset($photo) : asset('storage/'.$photo));

        $github = setting('github_url', 'https://github.com/nailzen014-collab');
        $wa = setting('whatsapp_url', 'https://wa.me/6285694595270');
        $ig = setting('instagram_url', 'https://instagram.com/abdnbhn');
        $education = setting('education_level');

        $typeIcons = [
            'education' => 'school',
            'experience' => 'briefcase',
            'organization' => 'users',
        ];
    @endphp

    <x-site.page-header title="Tentang Saya"
        eyebrow="Profil"
        subtitle="Sedikit cerita tentang saya, cara saya belajar, dan apa yang saya kerjakan." />

    {{-- Profil singkat --}}
    <section class="container-page py-20">
        <div class="grid gap-14 lg:grid-cols-[minmax(0,1fr)_minmax(0,1.4fr)]">
            <div class="reveal">
                <div class="sticky top-28 rounded-3xl border-gradient relative bg-surface/70 p-6 backdrop-blur">
                    <div class="rounded-2xl border-gradient bg-gradient-to-b from-surface-2/80 to-surface/60 p-2 backdrop-blur">
                        <img src="{{ $photoUrl }}" alt="Foto profil {{ $siteName }}" loading="lazy" decoding="async"
                            class="h-64 w-full rounded-xl object-cover object-top">
                    </div>

                    <p class="mt-5 font-display text-lg font-semibold text-text">{{ $siteName }}</p>
                    <p class="text-sm text-primary-soft">{{ $role }}</p>

                    <dl class="mt-6 space-y-3 text-sm">
                        @if ($education)
                            <div class="flex items-start gap-3">
                                <x-icon name="school" class="h-4 w-4 shrink-0 text-primary" />
                                <div>
                                    <dt class="text-muted">Jenjang</dt>
                                    <dd class="text-text">{{ $education }}</dd>
                                </div>
                            </div>
                        @endif

                        <div class="flex items-start gap-3">
                            <x-icon name="github" class="h-4 w-4 shrink-0 text-primary" />
                            <div>
                                <dt class="text-muted">GitHub</dt>
                                <dd>
                                    <a href="{{ $github }}" target="_blank" rel="noopener noreferrer"
                                        class="text-text hover:text-primary">@{{ ltrim(parse_url($github, PHP_URL_PATH) ?? '', '/') }}</a>
                                </dd>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <x-icon name="whatsapp" class="h-4 w-4 shrink-0 text-primary" />
                            <div>
                                <dt class="text-muted">WhatsApp</dt>
                                <dd>
                                    <a href="{{ $wa }}" target="_blank" rel="noopener noreferrer"
                                        class="text-text hover:text-primary">Chat langsung</a>
                                </dd>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <x-icon name="instagram" class="h-4 w-4 shrink-0 text-primary" />
                            <div>
                                <dt class="text-muted">Instagram</dt>
                                <dd>
                                    <a href="{{ $ig }}" target="_blank" rel="noopener noreferrer"
                                        class="text-text hover:text-primary">{{ ltrim(parse_url($ig, PHP_URL_PATH) ?? '', '/') }}</a>
                                </dd>
                            </div>
                        </div>
                    </dl>

                    <div class="mt-6">
                        <x-ui.button :href="route('portofolio')" class="w-full" icon="folder">Lihat Portofolio</x-ui.button>
                    </div>
                </div>
            </div>

            <div class="space-y-14">
                <div class="reveal">
                    <x-section-title eyebrow="Bio" title="Singkat Tentang Saya" />
                    <div class="mt-6 space-y-4 leading-relaxed text-muted">
                        {{-- Bio diambil dari tabel settings, bisa diedit admin tanpa ubah kode --}}
                        <p>{{ setting('bio', 'Saya adalah '.$role.' yang sedang belajar membangun aplikasi web dari nol menggunakan Laravel, PHP, dan JavaScript.') }}</p>
                        <p>
                            Saya belajar mandiri dan mengerjakan proyek nyata, karena cara belajar
                            yang paling cepat adalah mengerjakan. Setiap proyek yang saya buat disimpan
                            di GitHub agar bisa dibaca, ditinjau, dan digunakan ulang.
                        </p>
                    </div>
                </div>

                <div class="reveal">
                    <x-section-title eyebrow="Timeline" title="Pendidikan, Pengalaman, Organisasi" />

                    <div class="mt-10 space-y-12">
                        @foreach ([
                            'education' => $educations,
                            'experience' => $works,
                            'organization' => $organizations,
                        ] as $type => $items)
                            @continue($items->isEmpty())

                            <div>
                                <h3 class="flex items-center gap-2 font-display text-lg font-semibold text-text">
                                    <x-icon :name="$typeIcons[$type]" class="h-5 w-5 text-primary" />
                                    {{ App\Models\Experience::TYPES[$type] }}
                                </h3>

                                <ol class="relative mt-6 space-y-8 border-l border-line pl-8">
                                    @foreach ($items as $item)
                                        <li class="relative">
                                            <span class="absolute -left-[2.35rem] flex h-5 w-5 items-center justify-center rounded-full border border-primary/40 bg-ink">
                                                <span class="h-1.5 w-1.5 rounded-full bg-primary"></span>
                                            </span>
                                            <p class="font-mono text-2xs uppercase tracking-wider text-primary">{{ $item->period }}</p>
                                            <h4 class="mt-1 font-display text-base font-semibold text-text">{{ $item->title }}</h4>
                                            @if ($item->organization)
                                                <p class="text-sm text-muted">{{ $item->organization }}</p>
                                            @endif
                                            @if ($item->description)
                                                <p class="mt-2 text-sm leading-relaxed text-muted">{{ $item->description }}</p>
                                            @endif
                                        </li>
                                    @endforeach
                                </ol>
                            </div>
                        @endforeach

                        @if (collect([$educations, $works, $organizations])->every(fn ($items) => $items->isEmpty()))
                            <div class="rounded-2xl border border-dashed border-line bg-surface/60 backdrop-blur px-6 py-12 text-center">
                                <x-icon name="clock" class="mx-auto h-7 w-7 text-muted" />
                                <p class="mt-4 text-sm text-muted">Timeline belum diisi. Tambahkan lewat panel admin.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="container-page pb-24">
        <div class="reveal relative flex flex-col items-center justify-between gap-6 rounded-3xl border-gradient bg-surface/70 px-8 py-10 backdrop-blur sm:flex-row">
            <div>
                <h2 class="font-display text-2xl font-bold text-text">Tertarik bekerja sama?</h2>
                <p class="mt-2 text-sm text-muted">Kirim pesan, saya akan membalas lewat email atau WhatsApp.</p>
            </div>
            <x-ui.button :href="route('kontak')" icon="mail">Halaman Kontak</x-ui.button>
        </div>
    </section>
@endsection
