@php
    $name = setting('site_name', 'Abbad Nailun Nabhan');
    $role = setting('role', 'Web Developer & Fullstack Engineer');
    $tagline = setting('tagline', 'Membangun web modern dengan Laravel, PHP, dan JavaScript.');
    $wa = setting('whatsapp_url', 'https://wa.me/6285694595270');
    $ig = setting('instagram_url', 'https://instagram.com/abdnbhn');
    $github = setting('github_url', 'https://github.com/nailzen014-collab');
    $linkedin = setting('linkedin_url');
@endphp

{{-- Footer publik: ringkasan, navigasi, kontak, dan tautan sosial (FR-02). --}}
<footer class="relative overflow-hidden border-t border-line-soft bg-ink-soft">
    <div class="pointer-events-none absolute inset-0 bg-aurora opacity-60" aria-hidden="true"></div>
    <div class="pointer-events-none absolute inset-0 bg-dots opacity-40" aria-hidden="true"></div>

    <div class="container-page relative grid gap-12 py-16 md:grid-cols-2 lg:grid-cols-4 lg:py-20">
        <div class="lg:col-span-2">
            <a href="{{ route('home') }}" class="group inline-flex items-center gap-3">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-primary to-primary-dark shadow-glow-soft transition duration-500 group-hover:scale-105">
                    <x-icon name="code" class="h-5 w-5 text-white" />
                </span>
                <span class="font-display text-2xl font-bold tracking-tight text-text">
                    {{ $name }}<span class="text-primary">.</span>
                </span>
            </a>

            <p class="mt-5 max-w-md text-sm leading-relaxed text-muted">{{ $tagline }}</p>

            <div class="mt-7 flex items-center gap-2">
                <a href="{{ $github }}" target="_blank" rel="noopener noreferrer" class="icon-button" aria-label="GitHub">
                    <x-icon name="github" />
                </a>
                <a href="{{ $ig }}" target="_blank" rel="noopener noreferrer" class="icon-button" aria-label="Instagram">
                    <x-icon name="instagram" />
                </a>
                @if ($linkedin)
                    <a href="{{ $linkedin }}" target="_blank" rel="noopener noreferrer" class="icon-button" aria-label="LinkedIn">
                        <x-icon name="linkedin" />
                    </a>
                @endif
                <a href="mailto:{{ setting('email', 'halo@example.com') }}" class="icon-button" aria-label="Email">
                    <x-icon name="mail" />
                </a>
            </div>
        </div>

        <div>
            <h2 class="eyebrow">Navigasi</h2>
            <ul class="mt-5 space-y-3">
                @foreach ($navItems as $item)
                    <li>
                        <a href="{{ route($item['route']) }}" class="link-underline text-sm">{{ $item['label'] }}</a>
                    </li>
                @endforeach
            </ul>
        </div>

        <div>
            <h2 class="eyebrow">Kontak</h2>
            <ul class="mt-5 space-y-3 text-sm text-muted">
                <li class="flex items-start gap-2">
                    <x-icon name="whatsapp" class="mt-0.5 h-4 w-4 shrink-0 text-primary" />
                    <a href="{{ $wa }}" target="_blank" rel="noopener noreferrer" class="link-underline">WhatsApp</a>
                </li>
                <li class="flex items-start gap-2">
                    <x-icon name="instagram" class="mt-0.5 h-4 w-4 shrink-0 text-primary" />
                    <a href="{{ $ig }}" target="_blank" rel="noopener noreferrer" class="link-underline">Instagram</a>
                </li>
                <li class="flex items-start gap-2">
                    <x-icon name="github" class="mt-0.5 h-4 w-4 shrink-0 text-primary" />
                    <a href="{{ $github }}" target="_blank" rel="noopener noreferrer" class="link-underline">GitHub</a>
                </li>
            </ul>

            <x-ui.button :href="$wa" icon="whatsapp" class="mt-6 w-full" target="_blank" rel="noopener noreferrer">
                Chat Sekarang
            </x-ui.button>
        </div>
    </div>

    <div class="relative border-t border-line-soft">
        <div class="container-page flex flex-col items-center justify-between gap-3 py-6 text-2xs text-muted sm:flex-row">
            <p>&copy; {{ date('Y') }} {{ $name }}. {{ $role }}.</p>
            <p>Dibangun dengan Laravel, Tailwind CSS, dan Alpine.js.</p>
        </div>
    </div>
</footer>

{{-- Tombol WhatsApp mengambang: kontak selalu terjangkau dari halaman mana pun --}}
<a href="{{ $wa }}" target="_blank" rel="noopener noreferrer" aria-label="Hubungi via WhatsApp"
    class="group fixed bottom-6 right-6 z-40 flex items-center gap-2 rounded-full bg-primary px-4 py-3 text-white shadow-glow-primary transition duration-500 ease-smooth hover:-translate-y-1 hover:bg-primary-dark">
    <x-icon name="whatsapp" class="h-5 w-5" />
    <span class="hidden text-sm font-semibold sm:inline">Chat WA</span>
</a>