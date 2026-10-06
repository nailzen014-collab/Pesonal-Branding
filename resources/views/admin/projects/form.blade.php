@php
    $isEdit = $project->exists;
@endphp

@extends('layouts.admin')

@section('title', $isEdit ? 'Edit Proyek' : 'Tambah Proyek')
@section('subtitle', $isEdit ? $project->title : 'Isi detail proyek lalu simpan')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.projects.index') }}" class="inline-flex items-center gap-2 text-sm text-muted hover:text-primary">
            <span aria-hidden="true">&larr;</span> Kembali ke daftar proyek
        </a>
    </div>

    {{-- Form satu halaman untuk tambah & edit: tidak ada kode duplikat --}}
    <form method="POST" enctype="multipart/form-data"
        action="{{ $isEdit ? route('admin.projects.update', $project) : route('admin.projects.store') }}">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        <div class="grid gap-6 xl:grid-cols-3">
            <div class="space-y-6 xl:col-span-2">
                <section class="card">
                    <h2 class="font-display text-lg font-semibold text-text">Informasi Utama</h2>

                    <div class="mt-6 space-y-5">
                        <x-ui.field label="Judul proyek" name="title" :required="true" :value="$project->title"
                            placeholder="Contoh: Sistem Informasi Sekolah" />

                        <x-ui.field label="Slug URL" name="slug" :value="$project->slug"
                            hint="Kosongkan untuk dibuat otomatis dari judul. Contoh: sistem-informasi-sekolah" />

                        <div>
                            <label for="summary" class="form-label">
                                Ringkasan <span class="text-primary" aria-hidden="true">*</span>
                            </label>
                            <textarea id="summary" name="summary" rows="2" required maxlength="255"
                                placeholder="Satu kalimat singkat yang tampil di kartu proyek." class="form-input">{{ old('summary', $project->summary) }}</textarea>
                            <x-input-error :messages="$errors->get('summary')" class="mt-1.5" />
                        </div>

                        <div>
                            <label for="description" class="form-label">Deskripsi Lengkap</label>
                            <textarea id="description" name="description" rows="10"
                                placeholder="Jelaskan fitur, teknologi, dan tujuan proyek. Pisahkan paragraf dengan baris kosong."
                                class="form-input font-normal leading-relaxed">{{ old('description', $project->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-1.5" />
                        </div>
                    </div>
                </section>

                <section class="card">
                    <h2 class="font-display text-lg font-semibold text-text">Tautan</h2>

                    <div class="mt-6 grid gap-5 sm:grid-cols-2">
                        <x-ui.field label="URL Repository" name="repo_url" type="url" :value="$project->repo_url"
                            placeholder="https://github.com/..." />

                        <x-ui.field label="URL Demo" name="demo_url" type="url" :value="$project->demo_url"
                            placeholder="https://demo.example.com" />
                    </div>
                </section>

                <section class="card">
                    <h2 class="font-display text-lg font-semibold text-text">Teknologi</h2>
                    <p class="mt-2 text-sm text-muted">Pilih satu atau beberapa teknologi yang dipakai proyek ini.</p>

                    @if ($technologies->isEmpty())
                        <p class="mt-4 text-sm text-muted">Belum ada data teknologi. Jalankan <code class="font-mono text-primary">php artisan db:seed</code> terlebih dahulu.</p>
                    @else
                        <div class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach ($technologies as $technology)
                                <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-line bg-surface-2 px-4 py-3 transition hover:border-primary/40">
                                    <input type="checkbox" name="technologies[]" value="{{ $technology->id }}"
                                        @checked(in_array($technology->id, old('technologies', $project->technologies->pluck('id')->all())))
                                        class="rounded border-line bg-ink text-primary focus:ring-primary focus:ring-offset-ink">
                                    <span class="text-sm text-text">{{ $technology->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    @endif
                </section>
            </div>

            <aside class="space-y-6">
                <section class="card">
                    <h2 class="font-display text-lg font-semibold text-text">Publikasi</h2>

                    <div class="mt-6 space-y-5">
                        <x-ui.field label="Kategori" name="category" :value="$project->category"
                            placeholder="Contoh: Laravel, PHP, Blade" />

                        <div>
                            <label for="source" class="form-label">Sumber Data</label>
                            <select id="source" name="source" class="form-input">
                                <option value="manual" @selected(old('source', $project->source) === 'manual')>Manual</option>
                                <option value="github" @selected(old('source', $project->source) === 'github')>GitHub</option>
                            </select>
                        </div>

                        <x-ui.field label="Urutan Tampil" name="sort_order" type="number" :value="$project->sort_order ?? 0"
                            hint="Angka kecil tampil lebih dulu." />
                    </div>

                    <div class="mt-6 space-y-3 border-t border-line pt-6">
                        <label class="flex cursor-pointer items-start gap-3">
                            <input type="checkbox" name="is_published" value="1"
                                @checked(old('is_published', $project->is_published)) class="mt-0.5 rounded border-line bg-surface-2 text-primary focus:ring-primary focus:ring-offset-ink">
                            <span>
                                <span class="block text-sm text-text">Publikasikan</span>
                                <span class="block text-2xs text-muted">Tampilkan di website.</span>
                            </span>
                        </label>

                        <label class="flex cursor-pointer items-start gap-3">
                            <input type="checkbox" name="is_featured" value="1"
                                @checked(old('is_featured', $project->is_featured)) class="mt-0.5 rounded border-line bg-surface-2 text-primary focus:ring-primary focus:ring-offset-ink">
                            <span>
                                <span class="block text-sm text-text">Jadikan unggulan</span>
                                <span class="block text-2xs text-muted">Muncul di 3 kartu beranda.</span>
                            </span>
                        </label>
                    </div>
                </section>

                <section class="card">
                    <h2 class="font-display text-lg font-semibold text-text">Thumbnail</h2>

                    @if ($project->thumbnail_url)
                        <img src="{{ $project->thumbnail_url }}" alt="Thumbnail {{ $project->title }}"
                            class="mt-5 aspect-video w-full rounded-xl object-cover">
                    @endif

                    <div class="mt-5">
                        <label for="thumbnail" class="form-label">Upload gambar (JPG/PNG, maks 2 MB)</label>
                        <input id="thumbnail" name="thumbnail" type="file" accept="image/*"
                            class="form-input file:mr-4 file:rounded-lg file:border-0 file:bg-primary/10 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-primary">
                        <x-input-error :messages="$errors->get('thumbnail')" class="mt-1.5" />
                    </div>
                </section>

                <div class="flex gap-3">
                    <x-ui.button type="submit" class="flex-1" icon="check">
                        {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Proyek' }}
                    </x-ui.button>
                    <x-ui.button :href="route('admin.projects.index')" variant="outline">Batal</x-ui.button>
                </div>
            </aside>
        </div>
    </form>
@endsection