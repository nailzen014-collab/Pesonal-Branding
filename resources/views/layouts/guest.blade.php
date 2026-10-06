@php
    $siteName = setting('site_name', 'Abbad Nailun Nabhan');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $siteName }} — Panel Admin</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-ink font-sans text-text antialiased">
    <div class="flex min-h-screen">
        <div class="flex min-h-screen w-full max-w-md items-center justify-center px-6 py-12">
            <div class="w-full max-w-sm">
                <a href="{{ route('home') }}" class="mb-8 flex items-center justify-center gap-2">
                    <x-icon name="code" class="h-6 w-6 text-primary" />
                    <span class="font-display text-lg font-bold text-text">{{ $siteName }}</span>
                </a>

                <div class="card">
                    {{ $slot }}
                </div>

                <p class="mt-6 text-center text-2xs text-muted">
                    &copy; {{ date('Y') }} {{ $siteName }}
                </p>
            </div>
        </div>
    </div>
</body>
</html>