@php
    $siteName = setting('site_name', 'Abbad Nailun Nabhan');
    $role = setting('role', 'Web Developer & Fullstack Engineer');
    $defaultDescription = trim($role.' — portofolio, proyek, dan kontak '.$siteName.'.');
    $metaDescription = $metaDescription ?? setting('meta_description', $defaultDescription);

    $title = isset($title)
        ? $title.' — '.$siteName
        : $siteName.' — '.$role;

    $canonical = url()->current();
    $profilePhoto = setting('profile_photo') ?: 'images/profile.jpg';
    $ogImage = str_starts_with($profilePhoto, 'http') ? $profilePhoto : asset('storage/'.$profilePhoto);
@endphp
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title }}</title>
    <meta name="description" content="{{ $metaDescription }}">

    {{-- Open Graph & Twitter Card: tampilan saat link dibagikan di media sosial (NFR-04) --}}
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:url" content="{{ $canonical }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">
    <meta name="twitter:image" content="{{ $ogImage }}">

    <link rel="canonical" href="{{ $canonical }}">

    {{-- Font: Space Grotesk (judul), Inter (isi), JetBrains Mono (kode) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">

    <link rel="icon" href="/favicon.ico" sizes="any">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Tanpa JavaScript, konten animasi tetap tampil utuh --}}
    <noscript>
        <style>
            .reveal {
                opacity: 1 !important;
                transform: none !important;
            }

            [data-typewriter].tw-pending {
                visibility: visible !important;
            }

            .reveal:not(.is-visible) .skill-bar-fill {
                transform: none !important;
            }
        </style>
    </noscript>

    @stack('head')
</head>
<body class="min-h-screen bg-ink text-text">
    <a href="#konten"
        class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-[60] focus:rounded-lg focus:bg-primary focus:px-4 focus:py-2 focus:text-sm focus:text-white">
        Lewati ke konten utama
    </a>

    <x-site.navbar />

    <main id="konten" class="pt-20">
        @yield('content')
    </main>

    <x-site.footer />

    {{-- Animasi `.reveal` ditangani resources/js/reveal.js --}}
    @stack('scripts')

    {{--
        Safety net: bila bundel Vite gagal dimuat atau dieksekusi, paksa
        konten `.reveal` (dan teks ketik) tampil supaya halaman tidak pernah
        kosong. app.js menandai keberhasilan lewat data-js-ready di <html>.
    --}}
    <script>
        window.addEventListener('load', function () {
            if (document.documentElement.dataset.jsReady) {
                return;
            }

            document.querySelectorAll('.reveal').forEach(function (element) {
                element.classList.add('is-visible');
            });

            document.querySelectorAll('.tw-pending').forEach(function (element) {
                element.classList.remove('tw-pending');
            });
        });
    </script>
</body>
</html>