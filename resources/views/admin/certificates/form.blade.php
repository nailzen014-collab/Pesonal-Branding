@php
    $isEdit = $certificate->exists;
@endphp

@extends('layouts.admin')

@section('title', $isEdit ? 'Edit Sertifikat' : 'Tambah Sertifikat')
@section('subtitle', 'Sertifikat atau pencapaian')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.certificates.index') }}" class="inline-flex items-center gap-2 text-sm text-muted hover:text-primary">
            <span aria-hidden="true">&larr;</span> Kembali ke daftar sertifikat
        </a>
    </div>

    <form method="POST" enctype="multipart/form-data"
        action="{{ $isEdit ? route('admin.certificates.update', $certificate) : route('admin.certificates.store') }}"
        class="max-w-2xl">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        <div class="card space-y-5">
            <x-ui.field label="Nama sertifikat" name="title" :required="true" :value="$certificate->title"
                placeholder="Contoh: Sertifikat Kompetensi Web Programming" />

            <div class="grid gap-5 sm:grid-cols-2">
                <x-ui.field label="Penerbit" name="issuer" :value="$certificate->issuer"
                    placeholder="Contoh: LSP Informatika" />

                <x-ui.field label="Tanggal terbit" name="issued_at" type="date"
                    :value="$certificate->issued_at?->format('Y-m-d')" />
            </div>

            <x-ui.field label="Tautan eksternal" name="url" type="url" :value="$certificate->url"
                placeholder="https://..." hint="Opsional, mis. tautan ke dokumen online." />

            <div>
                <label for="image" class="form-label">Gambar sertifikat (maks 2 MB)</label>
                <input id="image" name="image" type="file" accept="image/*"
                    class="form-input file:mr-4 file:rounded-lg file:border-0 file:bg-primary/10 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-primary">
                <x-input-error :messages="$errors->get('image')" class="mt-1.5" />

                @if ($certificate->image_url)
                    <img src="{{ $certificate->image_url }}" alt="Pratinjau {{ $certificate->title }}"
                        class="mt-4 aspect-video w-full rounded-xl object-cover">
                @endif
            </div>

            <x-ui.field label="Urutan tampil" name="sort_order" type="number" :value="$certificate->sort_order ?? 0" />

            <div class="flex gap-3 border-t border-line pt-6">
                <x-ui.button type="submit" icon="check">{{ $isEdit ? 'Simpan Perubahan' : 'Simpan Sertifikat' }}</x-ui.button>
                <x-ui.button :href="route('admin.certificates.index')" variant="outline">Batal</x-ui.button>
            </div>
        </div>
    </form>
@endsection