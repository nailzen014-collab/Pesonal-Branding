@extends('layouts.public')

@section('content')
    <section class="relative flex min-h-[70vh] items-center overflow-hidden py-24">
        <div class="pointer-events-none absolute inset-0 bg-aurora" aria-hidden="true"></div>
        <div class="pointer-events-none absolute inset-0 bg-grid" aria-hidden="true"></div>

        <div class="container-page relative text-center">
            <span class="animate-fade-up font-mono text-7xl font-bold tracking-tight text-shine sm:text-8xl">503</span>

            <h1 class="mt-6 animate-fade-up font-display text-2xl font-semibold text-text sm:text-3xl"
                style="animation-delay: 80ms">
                Situs sedang dalam pemeliharaan
            </h1>

            <p class="mx-auto mt-4 max-w-lg animate-fade-up text-sm leading-relaxed text-muted"
                style="animation-delay: 160ms">
                Kami sedang melakukan pembaruan singkat agar pengalaman kamu lebih baik. Silakan kembali beberapa saat
                lagi.
            </p>

            <div class="mt-9 flex animate-fade-up flex-col justify-center gap-3 sm:flex-row" style="animation-delay: 240ms">
                <x-ui.button :href="route('home')">Kembali ke Beranda</x-ui.button>
            </div>
        </div>
    </section>
@endsection