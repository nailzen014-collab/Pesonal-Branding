@props(['project'])

{{--
    Kartu proyek yang dipakai di beranda, daftar portofolio, dan detail.
    Satu komponen agar tampilan selalu konsisten (FR-05).
--}}
<article class="card card-hover-tilt group flex h-full flex-col !p-0" data-tilt>
    <a href="{{ route('portofolio.show', $project->slug) }}" class="card-media block" tabindex="-1" aria-hidden="true">
        @if ($project->thumbnail_url)
            <img src="{{ $project->thumbnail_url }}" alt="" loading="lazy" decoding="async"
                class="h-full w-full object-cover transition duration-700 ease-smooth group-hover:scale-110">
        @else
            {{-- Placeholder elegan saat proyek belum punya screenshot --}}
            <div class="flex h-full w-full items-center justify-center bg-dots">
                <div class="relative text-center">
                    <div class="absolute inset-0 -z-10 bg-primary/15 blur-2xl" aria-hidden="true"></div>
                    <x-icon name="code" class="mx-auto h-9 w-9 text-primary transition duration-500 group-hover:scale-110" />
                    <p class="mt-3 font-mono text-2xs uppercase tracking-[0.2em] text-muted">
                        {{ $project->category ?? 'Web App' }}
                    </p>
                </div>
            </div>
        @endif

        <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-ink/70 via-transparent to-transparent opacity-0 transition duration-500 group-hover:opacity-100"></div>

        @if ($project->is_featured)
            <span class="absolute left-4 top-4 rounded-full bg-primary px-3 py-1 text-2xs font-semibold uppercase tracking-wider text-white shadow-glow-soft">
                Unggulan
            </span>
        @endif
    </a>

    <div class="flex flex-1 flex-col p-6">
        <div class="flex items-start justify-between gap-3">
            <h3 class="font-display text-lg font-semibold text-text">
                <a href="{{ route('portofolio.show', $project->slug) }}" class="transition hover:text-primary">
                    {{ $project->title }}
                </a>
            </h3>
        </div>

        @if ($project->summary)
            <p class="mt-3 line-clamp-3 text-sm leading-relaxed text-muted">{{ $project->summary }}</p>
        @endif

        <div class="mt-5 flex flex-wrap gap-2">
            @foreach ($project->technologies as $technology)
                <span class="pill transition duration-300 hover:border-primary/40 hover:text-text">{{ $technology->name }}</span>
            @endforeach
        </div>

        {{-- mt-auto membuat tombol selalu menempel di bawah kartu --}}
        <div class="mt-auto flex items-center gap-3 border-t border-line-soft pt-5">
            @if ($project->repo_url)
                <x-ui.button :href="$project->repo_url" variant="secondary" icon="github" target="_blank" rel="noopener noreferrer">
                    Lihat Repo
                </x-ui.button>
            @endif

            @if ($project->demo_url)
                <x-ui.button :href="$project->demo_url" variant="ghost" icon="external" target="_blank" rel="noopener noreferrer">
                    Demo
                </x-ui.button>
            @endif

            <a href="{{ route('portofolio.show', $project->slug) }}"
                class="ml-auto inline-flex items-center gap-1.5 text-sm font-medium text-muted transition duration-300 hover:gap-2.5 hover:text-primary">
                Detail
                <span aria-hidden="true">&rarr;</span>
            </a>
        </div>
    </div>
</article>