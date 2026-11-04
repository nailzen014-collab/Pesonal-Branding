@php
    $isEdit = $post->exists;
@endphp

@extends('layouts.admin')

@section('title', $isEdit ? 'Edit Artikel' : 'Tambah Artikel')
@section('subtitle', $isEdit ? $post->title : 'Tulis artikel lalu simpan')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.posts.index') }}" class="inline-flex items-center gap-2 text-sm text-muted hover:text-primary">
            <span aria-hidden="true">&larr;</span> Kembali ke daftar artikel
        </a>
    </div>

    <form method="POST" enctype="multipart/form-data"
        action="{{ $isEdit ? route('admin.posts.update', $post) : route('admin.posts.store') }}">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        <div class="grid gap-6 xl:grid-cols-3">
            <div class="space-y-6 xl:col-span-2">
                <section class="card">
                    <h2 class="font-display text-lg font-semibold text-text">Informasi Utama</h2>

                    <div class="mt-6 space-y-5">
                        <x-ui.field label="Judul artikel" name="title" :required="true" :value="$post->title"
                            placeholder="Contoh: Cara Install Laravel Breeze" />

                        <x-ui.field label="Slug URL" name="slug" :value="$post->slug"
                            hint="Kosongkan untuk dibuat otomatis dari judul. Contoh: cara-install-laravel-breeze" />

                        <div>
                            <label for="excerpt" class="form-label">Ringkasan</label>
                            <textarea id="excerpt" name="excerpt" rows="2" maxlength="500"
                                placeholder="Satu kalimat singkat yang tampil di kartu artikel." class="form-input">{{ old('excerpt', $post->excerpt) }}</textarea>
                            <x-input-error :messages="$errors->get('excerpt')" class="mt-1.5" />
                        </div>

                        <div>
                            <label for="body" class="form-label">Konten Artikel</label>
                            <textarea id="body" name="body" rows="15"
                                placeholder="Tulis isi artikel di sini. Pisahkan paragraf dengan baris kosong."
                                class="form-input font-normal leading-relaxed">{{ old('body', $post->body) }}</textarea>
                            <x-input-error :messages="$errors->get('body')" class="mt-1.5" />
                        </div>
                    </div>
                </section>
            </div>

            <aside class="space-y-6">
                <section class="card">
                    <h2 class="font-display text-lg font-semibold text-text">Publikasi</h2>

                    <div class="mt-6 space-y-5">
                        <x-ui.field label="Kategori" name="category" :value="$post->category"
                            placeholder="Contoh: Laravel, PHP, Tutorial" />

                        <x-ui.field label="Tanggal Publikasi" name="published_at" type="datetime-local" :value="$post->published_at?->format('Y-m-d\TH:i')"
                            hint="Kosongkan untuk mengisi otomatis saat dipublikasikan." />

                        <x-ui.field label="Urutan Tampil" name="sort_order" type="number" :value="$post->sort_order ?? 0"
                            hint="Angka kecil tampil lebih dulu." />
                    </div>

                    <div class="mt-6 space-y-3 border-t border-line pt-6">
                        <label class="flex cursor-pointer items-start gap-3">
                            <input type="checkbox" name="is_published" value="1"
                                @checked(old('is_published', $post->is_published)) class="mt-0.5 rounded border-line bg-surface-2 text-primary focus:ring-primary focus:ring-offset-ink">
                            <span>
                                <span class="block text-sm text-text">Publikasikan</span>
                                <span class="block text-2xs text-muted">Tampilkan di website.</span>
                            </span>
                        </label>
                    </div>
                </section>

                <section class="card">
                    <h2 class="font-display text-lg font-semibold text-text">SEO</h2>

                    <div class="mt-6">
                        <x-ui.field label="Meta Description" name="meta_description" :value="$post->meta_description"
                            hint="Deskripsi untuk search engine. Kosongkan untuk pakai ringkasan." />
                    </div>
                </section>

                <section class="card">
                    <h2 class="font-display text-lg font-semibold text-text">Thumbnail</h2>

                    @if ($post->thumbnail_url)
                        <img src="{{ $post->thumbnail_url }}" alt="Thumbnail {{ $post->title }}"
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
                        {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Artikel' }}
                    </x-ui.button>
                    <x-ui.button :href="route('admin.posts.index')" variant="outline">Batal</x-ui.button>
                </div>
            </aside>
        </div>
    </form>
@endsection
