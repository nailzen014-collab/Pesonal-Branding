@extends('layouts.public')

@section('content')
    @php
        $name = setting('site_name', 'Abbad Nailun Nabhan');
        $wa = setting('whatsapp_url', 'https://wa.me/6285694595270');
        $ig = setting('instagram_url', 'https://instagram.com/abdnbhn');
        $github = setting('github_url', 'https://github.com/nailzen014-collab');
        $linkedin = setting('linkedin_url');
        $email = setting('email');
    @endphp

    <x-site.page-header title="Kontak"
        eyebrow="Hubungi Saya"
        subtitle="Punya proyek, tawaran kerja sama, atau sekadar ingin berdiskusi? Silakan kirim pesan." />

    <section class="container-page py-20">
        <div class="grid gap-10 lg:grid-cols-[minmax(0,1fr)_minmax(0,1.2fr)]">
            {{-- Kontak cepat --}}
            <aside class="space-y-6">
                <div class="reveal card !p-7">
                    <h2 class="font-display text-lg font-semibold text-text">Kontak Cepat</h2>
                    <p class="mt-2 text-sm text-muted">Pilih cara yang paling nyaman untuk Anda.</p>

                    <ul class="mt-6 space-y-3">
                        <li>
                            <a href="{{ $wa }}" target="_blank" rel="noopener noreferrer"
                                class="group flex items-center gap-4 rounded-xl border border-line bg-surface-2/60 px-4 py-3 backdrop-blur transition duration-400 ease-smooth hover:-translate-y-0.5 hover:border-primary/40 hover:bg-surface-2 hover:shadow-glow-soft">
                                <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-primary/20 bg-primary/10 transition duration-400 group-hover:border-primary/50">
                                    <x-icon name="whatsapp" class="h-5 w-5 text-primary" />
                                </span>
                                <span>
                                    <span class="block text-sm font-medium text-text">WhatsApp</span>
                                    <span class="block text-xs text-muted">Respons tercepat</span>
                                </span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ $ig }}" target="_blank" rel="noopener noreferrer"
                                class="group flex items-center gap-4 rounded-xl border border-line bg-surface-2/60 px-4 py-3 backdrop-blur transition duration-400 ease-smooth hover:-translate-y-0.5 hover:border-primary/40 hover:bg-surface-2 hover:shadow-glow-soft">
                                <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-primary/20 bg-primary/10 transition duration-400 group-hover:border-primary/50">
                                    <x-icon name="instagram" class="h-5 w-5 text-primary" />
                                </span>
                                <span>
                                    <span class="block text-sm font-medium text-text">Instagram</span>
                                    <span class="block text-xs text-muted">{{ ltrim(parse_url($ig, PHP_URL_PATH) ?? '', '/') }}</span>
                                </span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ $github }}" target="_blank" rel="noopener noreferrer"
                                class="group flex items-center gap-4 rounded-xl border border-line bg-surface-2/60 px-4 py-3 backdrop-blur transition duration-400 ease-smooth hover:-translate-y-0.5 hover:border-primary/40 hover:bg-surface-2 hover:shadow-glow-soft">
                                <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-primary/20 bg-primary/10 transition duration-400 group-hover:border-primary/50">
                                    <x-icon name="github" class="h-5 w-5 text-primary" />
                                </span>
                                <span>
                                    <span class="block text-sm font-medium text-text">GitHub</span>
                                    <span class="block text-xs text-muted">Lihat seluruh repository</span>
                                </span>
                            </a>
                        </li>

                        @if ($linkedin)
                            <li>
                                <a href="{{ $linkedin }}" target="_blank" rel="noopener noreferrer"
                                    class="group flex items-center gap-4 rounded-xl border border-line bg-surface-2/60 px-4 py-3 backdrop-blur transition duration-400 ease-smooth hover:-translate-y-0.5 hover:border-primary/40 hover:bg-surface-2">
                                    <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-primary/20 bg-primary/10 transition duration-400 group-hover:border-primary/50">
                                        <x-icon name="linkedin" class="h-5 w-5 text-primary" />
                                    </span>
                                    <span class="text-sm font-medium text-text">LinkedIn</span>
                                </a>
                            </li>
                        @endif

                        @if ($email)
                            <li>
                                <a href="mailto:{{ $email }}"
                                    class="group flex items-center gap-4 rounded-xl border border-line bg-surface-2/60 px-4 py-3 backdrop-blur transition duration-400 ease-smooth hover:-translate-y-0.5 hover:border-primary/40 hover:bg-surface-2">
                                    <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-primary/20 bg-primary/10 transition duration-400 group-hover:border-primary/50">
                                        <x-icon name="mail" class="h-5 w-5 text-primary" />
                                    </span>
                                    <span class="text-sm font-medium text-text">{{ $email }}</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>

                <div class="reveal card !p-7">
                    <h2 class="font-display text-lg font-semibold text-text">Informasi</h2>
                    <dl class="mt-5 space-y-3 text-sm">
                        <div class="flex items-start justify-between gap-4">
                            <dt class="text-muted">Waktu balas</dt>
                            <dd class="text-text">1&ndash;2 hari kerja</dd>
                        </div>
                        <div class="flex items-start justify-between gap-4">
                            <dt class="text-muted">Topik</dt>
                            <dd class="text-text">Proyek, kolaborasi, lowongan</dd>
                        </div>
                        <div class="flex items-start justify-between gap-4">
                            <dt class="text-muted">Pemilik</dt>
                            <dd class="text-text">{{ $name }}</dd>
                        </div>
                    </dl>
                </div>
            </aside>

            {{-- Form pesan --}}
            <div class="reveal card !p-7">
                <h2 class="font-display text-lg font-semibold text-text">Kirim Pesan</h2>

                @if (session('success'))
                    <div class="mt-6 flex items-start gap-3 rounded-xl border border-success/40 bg-success/10 px-4 py-3 text-sm text-success">
                        <x-icon name="check" class="mt-0.5 h-4 w-4 shrink-0" />
                        <p>{{ session('success') }}</p>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mt-6 flex items-start gap-3 rounded-xl border border-red-500/40 bg-red-500/10 px-4 py-3 text-sm text-red-400">
                        <x-icon name="shield" class="mt-0.5 h-4 w-4 shrink-0" />
                        <p>{{ session('error') }}</p>
                    </div>
                @endif

                @if ($visitor)
                    <p class="mt-2 text-sm text-muted">Isi subjek dan pesan Anda. Nama dan email diambil dari akun Google.</p>

                    {{-- Identitas pengirim: read-only, berasal dari akun Google --}}
                    <div class="mt-6 flex flex-col gap-3 rounded-xl border border-line bg-surface-2/60 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex min-w-0 items-center gap-3">
                            @if ($visitor->avatar)
                                <img src="{{ $visitor->avatar }}" alt=""
                                    class="h-10 w-10 shrink-0 rounded-full border border-line object-cover"
                                    referrerpolicy="no-referrer">
                            @else
                                <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-primary/20 bg-primary/10 text-sm font-semibold text-primary">
                                    {{ mb_substr($visitor->name, 0, 1) }}
                                </span>
                            @endif

                            <span class="min-w-0">
                                <span class="block truncate text-sm font-medium text-text">{{ $visitor->name }}</span>
                                <span class="flex items-center gap-1.5 text-xs text-muted">
                                    <x-icon name="check" class="h-3.5 w-3.5 shrink-0 text-success" />
                                    <span class="truncate">{{ $visitor->email }}</span>
                                </span>
                            </span>
                        </div>

                        <form method="POST" action="{{ route('visitor.google.logout') }}">
                            @csrf
                            <button type="submit"
                                class="text-xs font-medium text-muted underline-offset-4 transition hover:text-primary hover:underline">
                                Keluar
                            </button>
                        </form>
                    </div>

                    <form method="POST" action="{{ route('kontak.store') }}" class="mt-6 space-y-5">
                        @csrf

                        <x-ui.field label="Subjek" name="subject" :required="true" placeholder="Contoh: Kolaborasi proyek website" />

                        <div>
                            <label for="body" class="form-label">
                                Pesan <span class="text-primary" aria-hidden="true">*</span>
                            </label>
                            <textarea id="body" name="body" rows="6" required minlength="10" maxlength="2000"
                                placeholder="Ceritakan kebutuhan Anda, minimal 10 karakter."
                                class="form-input @error('body') border-red-500 @enderror">{{ old('body') }}</textarea>
                            <x-input-error :messages="$errors->get('body')" class="mt-1.5" />
                        </div>

                        {{--
                            Honeypot: input tersembunyi dari manusia, tapi bot akan mengisinya.
                            Aturan validasi "size:0" memastikan field ini tetap kosong.
                        --}}
                        <div class="hidden" aria-hidden="true">
                            <label for="website">Website</label>
                            <input id="website" name="website" type="text" tabindex="-1" autocomplete="off">
                        </div>

                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <p class="text-2xs text-muted">"Punya <b>ide kolaborasi</b> atau <b>pertanyaan</b> terkait layanan saya? <br>Silakan tinggalkan pesan Anda di sini."</p>
                            <x-ui.button type="submit" icon="mail">Kirim Pesan</x-ui.button>
                        </div>
                    </form>
                @else
                    <p class="mt-2 text-sm text-muted">
                        Untuk mencegah spam dan memastikan pesan bisa dibalas, silakan masuk terlebih dahulu.
                    </p>

                    <div class="mt-6 rounded-xl border border-line bg-surface-2/60 p-5">
                        <div class="flex items-start gap-3">
                            <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-primary/20 bg-primary/10">
                                <x-icon name="shield" class="h-5 w-5 text-primary" />
                            </span>
                            <div>
                                <p class="text-sm font-medium text-text">Masuk dengan akun Google</p>
                                <p class="mt-1 text-xs leading-relaxed text-muted">
                                    Nama dan email terverifikasi dipakai untuk membalas pesan Anda.
                                    Tidak ada password baru yang perlu dibuat.
                                </p>
                            </div>
                        </div>

                        <x-ui.google-button :href="route('visitor.google.redirect')" class="mt-5 w-full" />
                    </div>

                    <p class="mt-4 flex items-center gap-2 text-2xs text-muted">
                        <x-icon name="shield" class="h-3.5 w-3.5 shrink-0" />
                        Identitas Anda hanya digunakan untuk keperluan pesan ini.
                    </p>
                @endif
            </div>
        </div>
    </section>
@endsection
