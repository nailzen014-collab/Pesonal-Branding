@extends('layouts.public')

@section('content')
    <x-site.page-header title="Sertifikat & Pencapaian"
        eyebrow="Bukti"
        subtitle="Sertifikat dan pencapaian yang saya miliki, sebagai lampiran dari kemampuan saya." />

    <section class="container-page py-20">
        @if ($certificates->isEmpty())
            <div class="relative overflow-hidden rounded-3xl border border-dashed border-line bg-surface/60 px-6 py-24 text-center backdrop-blur">
                <div class="pointer-events-none absolute inset-0 bg-grid opacity-40" aria-hidden="true"></div>

                <div class="relative">
                    <span
                        class="mx-auto inline-flex h-16 w-16 items-center justify-center rounded-2xl border border-primary/25 bg-primary/10">
                        <x-icon name="award" class="h-7 w-7 text-primary" />
                    </span>
                    <h2 class="mt-6 font-display text-lg font-semibold text-text">Belum ada sertifikat</h2>
                    <p class="mx-auto mt-2 max-w-md text-sm text-muted">
                        Sertifikat akan ditambahkan setelah diunggah lewat panel admin.
                    </p>
                </div>
            </div>
        @else
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($certificates as $certificate)
                    <article class="reveal card card-hover group/cert flex flex-col">
                        @if ($certificate->image_url)
                            <img src="{{ $certificate->image_url }}" alt="Sertifikat {{ $certificate->title }}" loading="lazy"
                                decoding="async" class="mb-6 aspect-video w-full rounded-xl object-cover">
                        @else
                            <div class="relative mb-6 flex aspect-video w-full items-center justify-center overflow-hidden rounded-xl bg-dots">
                                <span
                                    class="absolute inset-0 bg-primary/10 blur-2xl transition duration-400 group-hover/cert:bg-primary/20"
                                    aria-hidden="true"></span>
                                <x-icon name="award" class="relative h-10 w-10 text-primary" />
                            </div>
                        @endif

                        <div class="flex flex-1 flex-col">
                            <h2 class="font-display text-base font-semibold text-text transition duration-400 group-hover/cert:text-primary-soft">
                                {{ $certificate->title }}
                            </h2>

                            @if ($certificate->issuer)
                                <p class="mt-1 text-sm text-muted">{{ $certificate->issuer }}</p>
                            @endif

                            @if ($certificate->issued_at)
                                <p class="mt-4 flex items-center gap-2 font-mono text-2xs uppercase tracking-wider text-muted">
                                    <x-icon name="calendar" class="h-4 w-4 text-primary" />
                                    {{ $certificate->issued_at->format('M Y') }}
                                </p>
                            @endif
                        </div>

                        @if ($certificate->url)
                            <a href="{{ $certificate->url }}" target="_blank" rel="noopener noreferrer"
                                class="mt-6 inline-flex items-center gap-2 border-t border-line pt-5 text-sm font-medium text-primary transition duration-400 hover:gap-3 hover:text-primary-soft">
                                <x-icon name="external" class="h-4 w-4" />
                                Lihat detail
                            </a>
                        @endif
                    </article>
                @endforeach
            </div>
        @endif
    </section>
@endsection