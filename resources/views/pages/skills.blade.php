@extends('layouts.public')

@section('content')
    @php
        $categoryIcons = [
            'frontend' => 'code',
            'backend' => 'layers',
            'database' => 'database',
            'tools' => 'tools',
            'lainnya' => 'sparkles',
        ];

        $labels = App\Models\Skill::CATEGORIES;
        $total = collect($groupedSkills)->flatten(1)->count();
    @endphp

    <x-site.page-header title="Skill & Teknologi"
        eyebrow="Kemampuan"
        subtitle="Teknologi yang saya pakai sehari-hari, dikelompokkan supaya mudah dipindai." />

    <section class="container-page py-20">
        @if ($total === 0)
            <div class="rounded-2xl border border-dashed border-line bg-surface/60 backdrop-blur px-6 py-16 text-center">
                <x-icon name="code" class="mx-auto h-8 w-8 text-muted" />
                <p class="mt-4 text-sm text-muted">Belum ada data skill. Tambahkan lewat panel admin.</p>
            </div>
        @else
            <div class="mb-12 flex flex-wrap items-center gap-3 text-sm text-muted">
                <span class="pill">{{ $total }} skill terdaftar</span>
                <span class="pill">{{ count($groupedSkills) }} kategori</span>
            </div>

            <div class="space-y-16">
                @foreach ($groupedSkills as $category => $skills)
                    <div class="reveal">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-primary/30 bg-surface-2">
                                <x-icon :name="$categoryIcons[$category] ?? 'sparkles'" class="h-5 w-5 text-primary" />
                            </span>
                            <div>
                                <h2 class="font-display text-xl font-semibold text-text">
                                    {{ $labels[$category] ?? ucfirst($category) }}
                                </h2>
                                <p class="text-xs text-muted">{{ $skills->count() }} skill</p>
                            </div>
                        </div>

                        <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach ($skills as $skill)
                                <div class="card card-hover-tilt" data-tilt>
                                    <div class="flex items-center justify-between">
                                        <h3 class="font-display text-base font-semibold text-text">{{ $skill->name }}</h3>
                                        <span class="font-mono text-xs text-primary">{{ $skill->level }}%</span>
                                    </div>

                                    {{-- Bar persentase level; ditambah atribut ARIA agar bisa dibaca screen reader --}}
                                    <div class="mt-4 h-1.5 w-full overflow-hidden rounded-full bg-surface-2"
                                        role="progressbar" aria-valuenow="{{ $skill->level }}" aria-valuemin="0"
                                        aria-valuemax="100" aria-label="Level {{ $skill->name }}">
                                        <div class="skill-bar-fill h-full rounded-full bg-gradient-to-r from-primary-soft to-primary-dark"
                                            style="width: {{ $skill->level }}%"></div>
                                    </div>

                                    <p class="mt-3 text-2xs uppercase tracking-wider text-muted">{{ $skill->category_label }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    <section class="container-page pb-24">
        <div class="reveal relative flex flex-col items-center justify-between gap-6 rounded-3xl border-gradient bg-surface/70 px-8 py-10 backdrop-blur sm:flex-row">
            <div>
                <h2 class="font-display text-2xl font-bold text-text">Ingin lihat karyanya?</h2>
                <p class="mt-2 text-sm text-muted">Semua proyek tersedia di GitHub dan bisa dibuka langsung.</p>
            </div>
            <div class="flex gap-3">
                <x-ui.button :href="route('portofolio')" icon="folder">Buka Portofolio</x-ui.button>
            </div>
        </div>
    </section>
@endsection