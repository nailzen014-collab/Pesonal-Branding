@php
    $isEdit = $experience->exists;
@endphp

@extends('layouts.admin')

@section('title', $isEdit ? 'Edit Timeline' : 'Tambah Timeline')
@section('subtitle', 'Pendidikan, pengalaman, atau organisasi')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.experiences.index') }}" class="inline-flex items-center gap-2 text-sm text-muted hover:text-primary">
            <span aria-hidden="true">&larr;</span> Kembali ke timeline
        </a>
    </div>

    <form method="POST" action="{{ $isEdit ? route('admin.experiences.update', $experience) : route('admin.experiences.store') }}"
        class="max-w-2xl">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        <div class="card space-y-5">
            <div>
                <label for="type" class="form-label">
                    Jenis <span class="text-primary" aria-hidden="true">*</span>
                </label>
                <select id="type" name="type" class="form-input">
                    @foreach (App\Models\Experience::TYPES as $value => $label)
                        <option value="{{ $value }}" @selected(old('type', $experience->type) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('type')" class="mt-1.5" />
            </div>

            <x-ui.field label="Judul" name="title" :required="true" :value="$experience->title"
                placeholder="Contoh: Siswa SMA, Jurusan RPL" />

            <x-ui.field label="Nama sekolah / perusahaan" name="organization" :value="$experience->organization"
                placeholder="Contoh: SMK Negeri 1" />

            <div class="grid gap-5 sm:grid-cols-2">
                <x-ui.field label="Tanggal mulai" name="start_date" type="date" :required="true"
                    :value="$experience->start_date?->format('Y-m-d')" />

                <x-ui.field label="Tanggal selesai" name="end_date" type="date"
                    :value="$experience->end_date?->format('Y-m-d')" hint="Kosongkan jika masih berjalan." />
            </div>

            <div>
                <label for="description" class="form-label">Deskripsi</label>
                <textarea id="description" name="description" rows="4" class="form-input"
                    placeholder=".Activity singkat, materi yang dipelajari, atau tanggung jawab.">{{ old('description', $experience->description) }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-1.5" />
            </div>

            <x-ui.field label="Urutan tampil" name="sort_order" type="number" :value="$experience->sort_order ?? 0" />

            <div class="flex gap-3 border-t border-line pt-6">
                <x-ui.button type="submit" icon="check">{{ $isEdit ? 'Simpan Perubahan' : 'Simpan Timeline' }}</x-ui.button>
                <x-ui.button :href="route('admin.experiences.index')" variant="outline">Batal</x-ui.button>
            </div>
        </div>
    </form>
@endsection