@extends('layouts.admin')

@section('title', 'Pengaturan Situs')
@section('subtitle', 'Data profil, tautan sosial, foto, dan CV')

@section('content')
    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="max-w-4xl">
        @csrf
        @method('PUT')

        <div class="grid gap-6 lg:grid-cols-2">
            <div class="space-y-6">
                <section class="card">
                    <h2 class="font-display text-lg font-semibold text-text">Identitas</h2>

                    <div class="mt-6 space-y-5">
                        <x-ui.field label="Nama" name="site_name" :required="true" :value="$settings['site_name'] ?? null" />

                        <x-ui.field label="Role / Jabatan" name="role" :required="true" :value="$settings['role'] ?? null"
                            placeholder="Web Developer & Fullstack Engineer" />

                        <div>
                            <label for="tagline" class="form-label">
                                Tagline <span class="text-primary" aria-hidden="true">*</span>
                            </label>
                            <textarea id="tagline" name="tagline" rows="2" required maxlength="255" class="form-input">{{ old('tagline', $settings['tagline'] ?? '') }}</textarea>
                            <x-input-error :messages="$errors->get('tagline')" class="mt-1.5" />
                        </div>

                        <div>
                            <label for="bio" class="form-label">Bio Singkat</label>
                            <textarea id="bio" name="bio" rows="6" maxlength="2000" class="form-input"
                                placeholder="Ceritakan singkat tentang diri Anda.">{{ old('bio', $settings['bio'] ?? '') }}</textarea>
                            <x-input-error :messages="$errors->get('bio')" class="mt-1.5" />
                        </div>

                        <x-ui.field label="Meta Description" name="meta_description" :value="$settings['meta_description'] ?? null"
                            hint="Deskripsi singkat untuk hasil pencarian Google (maks 255 karakter)." />
                    </div>
                </section>

                <section class="card">
                    <h2 class="font-display text-lg font-semibold text-text">Tautan Sosial</h2>

                    <div class="mt-6 space-y-5">
                        <x-ui.field label="GitHub" name="github_url" type="url" :value="$settings['github_url'] ?? null"
                            placeholder="https://github.com/username" />

                        <x-ui.field label="Instagram" name="instagram_url" type="url" :value="$settings['instagram_url'] ?? null"
                            placeholder="https://instagram.com/username" />

                        <x-ui.field label="WhatsApp" name="whatsapp_url" type="url" :value="$settings['whatsapp_url'] ?? null"
                            placeholder="https://wa.me/628xxxxxxxxxx" />

                        <x-ui.field label="LinkedIn" name="linkedin_url" type="url" :value="$settings['linkedin_url'] ?? null"
                            placeholder="https://linkedin.com/in/username" />
                    </div>
                </section>
            </div>

            <div class="space-y-6">
                <section class="card">
                    <h2 class="font-display text-lg font-semibold text-text">Pendidikan</h2>
                    <div class="mt-6">
                        <x-ui.field label="Jenjang" name="education_level" :value="$settings['education_level'] ?? null"
                            placeholder="Contoh: SMA / SMK / Universitas" hint="Tampil di hero dan halaman Tentang." />
                    </div>
                </section>

                <section class="card">
                    <h2 class="font-display text-lg font-semibold text-text">Foto Profil</h2>

                    @if (! empty($settings['profile_photo']))
                        <img src="{{ asset('storage/'.$settings['profile_photo']) }}" alt="Foto profil saat ini"
                            class="mt-5 h-40 w-40 rounded-2xl object-cover object-top">
                        <label class="mt-4 flex cursor-pointer items-center gap-3 text-sm text-muted">
                            <input type="checkbox" name="remove_profile_photo" value="1" class="rounded border-line bg-surface-2 text-primary focus:ring-primary focus:ring-offset-ink">
                            Hapus foto saat ini
                        </label>
                    @endif

                    <div class="mt-5">
                        <label for="profile_photo" class="form-label">Upload foto baru (maks 2 MB)</label>
                        <input id="profile_photo" name="profile_photo" type="file" accept="image/*"
                            class="form-input file:mr-4 file:rounded-lg file:border-0 file:bg-primary/10 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-primary">
                        <x-input-error :messages="$errors->get('profile_photo')" class="mt-1.5" />
                    </div>
                </section>

                <section class="card">
                    <h2 class="font-display text-lg font-semibold text-text">Curriculum Vitae</h2>

                    @if (! empty($settings['cv_file']))
                        <p class="mt-5 flex items-center gap-2 text-sm text-muted">
                            <x-icon name="check" class="h-4 w-4 text-success" />
                            CV tersimpan:
                            <a href="{{ asset('storage/'.$settings['cv_file']) }}" target="_blank" rel="noopener noreferrer"
                                class="text-primary hover:underline">lihat file</a>
                        </p>
                        <label class="mt-4 flex cursor-pointer items-center gap-3 text-sm text-muted">
                            <input type="checkbox" name="remove_cv_file" value="1" class="rounded border-line bg-surface-2 text-primary focus:ring-primary focus:ring-offset-ink">
                            Hapus file CV
                        </label>
                    @endif

                    <div class="mt-5">
                        <label for="cv_file" class="form-label">Upload CV (PDF, maks 4 MB)</label>
                        <input id="cv_file" name="cv_file" type="file" accept="application/pdf"
                            class="form-input file:mr-4 file:rounded-lg file:border-0 file:bg-primary/10 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-primary">
                        <x-input-error :messages="$errors->get('cv_file')" class="mt-1.5" />
                    </div>
                </section>

                <div class="flex gap-3">
                    <x-ui.button type="submit" icon="check">Simpan Pengaturan</x-ui.button>
                    <x-ui.button :href="route('admin.dashboard')" variant="outline">Batal</x-ui.button>
                </div>
            </div>
        </div>
    </form>
@endsection