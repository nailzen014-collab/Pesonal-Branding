@php
    $name = setting('site_name', 'Abbad Nailun Nabhan');
    $wa = setting('whatsapp_url', 'https://wa.me/6285694595270');
    $ig = setting('instagram_url', 'https://instagram.com/abdnbhn');
    $github = setting('github_url', 'https://github.com/nailzen014-collab');
@endphp

{{--
    Navbar publik:
    - melayang (floating) dengan latar glass, makin solid saat di-scroll
    - penanda halaman aktif berupa pill merah
    - menu hamburger di layar kecil (Alpine.js)
--}}
<header x-data="{ open: false, scrolled: false }"
    x-init="scrolled = window.scrollY > 20"
    @scroll.window="scrolled = window.scrollY > 20"
    class="fixed inset-x-0 top-0 z-50 px-3 pt-3 transition-all duration-500 ease-smooth sm:px-5 sm:pt-4"
    :class="scrolled ? 'opacity-100' : 'opacity-100'">

    <nav class="container-page !max-w-6xl"
        :class="scrolled
            ? 'h-16 rounded-2xl border border-line-soft bg-ink/70 shadow-card-lg backdrop-blur-2xl'
            : 'h-16 rounded-full border border-transparent bg-transparent'"
        aria-label="Navigasi utama">

        <div class="flex h-full items-center justify-between gap-4 px-3 sm:px-5">
            <a href="{{ route('home') }}" class="group flex items-center gap-2.5">
                <span class="relative inline-flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-primary to-primary-dark shadow-glow-soft transition duration-500 group-hover:scale-105">
                    <x-icon name="code" class="h-5 w-5 text-white" />
                </span>
                <span class="font-display text-lg font-bold tracking-tight text-text">
                    {{ $name }}<span class="text-primary">.</span>
                </span>
            </a>

            {{-- Menu desktop --}}
            <ul class="hidden items-center gap-1 rounded-full border border-line-soft bg-surface/60 p-1 backdrop-blur md:flex">
                @foreach ($navItems as $item)
                    @php $active = request()->routeIs($item['route']); @endphp
                    <li>
                        <a href="{{ route($item['route']) }}"
                            @class([
                                'block rounded-full px-4 py-1.5 text-sm font-medium transition duration-300',
                                'bg-primary text-white shadow-glow-soft' => $active,
                                'text-muted hover:bg-surface-2 hover:text-text' => ! $active,
                            ])
                            @if ($active) aria-current="page" @endif>
                            {{ $item['label'] }}
                        </a>
                    </li>
                @endforeach
            </ul>

            <div class="hidden items-center gap-2 md:flex">
                <a href="{{ $github }}" target="_blank" rel="noopener noreferrer" class="icon-button !h-9 !w-9" aria-label="GitHub">
                    <x-icon name="github" class="h-4 w-4" />
                </a>
                <a href="{{ $ig }}" target="_blank" rel="noopener noreferrer" class="icon-button !h-9 !w-9" aria-label="Instagram">
                    <x-icon name="instagram" class="h-4 w-4" />
                </a>
                <x-ui.button :href="$wa" icon="whatsapp" class="!px-4 !py-2 text-xs" target="_blank" rel="noopener noreferrer">
                    Hubungi Saya
                </x-ui.button>
            </div>

            {{-- Tombol hamburger (mobile) --}}
            <button type="button" @click="open = !open" class="icon-button !h-10 !w-10 md:hidden"
                :aria-expanded="open" aria-controls="mobile-menu" aria-label="Buka menu navigasi">
                <x-icon name="menu" x-show="!open" />
                <x-icon name="close" x-show="open" x-cloak />
            </button>
        </div>

        {{-- Menu mobile --}}
        <div id="mobile-menu" x-show="open" x-cloak x-transition.duration.300ms
            class="mx-3 mt-2 overflow-hidden rounded-2xl border border-line-soft bg-ink/95 p-3 shadow-card-lg backdrop-blur-2xl md:hidden">
            <ul class="space-y-1">
                @foreach ($navItems as $item)
                    @php $active = request()->routeIs($item['route']); @endphp
                    <li>
                        <a href="{{ route($item['route']) }}"
                            @class([
                                'block rounded-xl px-4 py-3 text-sm font-medium transition',
                                'bg-primary/10 text-primary' => $active,
                                'text-muted hover:bg-surface-2 hover:text-text' => ! $active,
                            ])>
                            {{ $item['label'] }}
                        </a>
                    </li>
                @endforeach

                <li class="pt-2">
                    <x-ui.button :href="$wa" icon="whatsapp" class="w-full" target="_blank" rel="noopener noreferrer">
                        Hubungi via WhatsApp
                    </x-ui.button>
                </li>
            </ul>
        </div>
    </nav>
</header>