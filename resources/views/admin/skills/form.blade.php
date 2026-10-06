@php
    $isEdit = $skill->exists;
@endphp

@extends('layouts.admin')

@section('title', $isEdit ? 'Edit Skill' : 'Tambah Skill')
@section('subtitle', $isEdit ? $skill->name : 'Tambahkan satu skill baru')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.skills.index') }}" class="inline-flex items-center gap-2 text-sm text-muted hover:text-primary">
            <span aria-hidden="true">&larr;</span> Kembali ke daftar skill
        </a>
    </div>

    <form method="POST" action="{{ $isEdit ? route('admin.skills.update', $skill) : route('admin.skills.store') }}"
        class="max-w-2xl">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        <div class="card space-y-5">
            <x-ui.field label="Nama skill" name="name" :required="true" :value="$skill->name"
                placeholder="Contoh: Laravel" />

            <div>
                <label for="category" class="form-label">
                    Kategori <span class="text-primary" aria-hidden="true">*</span>
                </label>
                <select id="category" name="category" class="form-input">
                    @foreach (App\Models\Skill::CATEGORIES as $value => $label)
                        <option value="{{ $value }}" @selected(old('category', $skill->category) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('category')" class="mt-1.5" />
            </div>

            <div>
                <label for="level" class="form-label">
                    Level <span class="text-primary" aria-hidden="true">*</span>
                </label>
                <div class="flex items-center gap-4">
                    <input id="level" name="level" type="range" min="0" max="100" step="5"
                        value="{{ old('level', $skill->level ?? 75) }}"
                        class="h-2 flex-1 cursor-pointer appearance-none rounded-full bg-surface-2 accent-primary">
                    <span class="w-14 rounded-lg border border-line bg-surface-2 px-3 py-1.5 text-center font-mono text-xs text-primary">
                        {{ old('level', $skill->level ?? 75) }}%
                    </span>
                </div>
                <x-input-error :messages="$errors->get('level')" class="mt-1.5" />
            </div>

            <x-ui.field label="Urutan tampil" name="sort_order" type="number" :value="$skill->sort_order ?? 0"
                hint="Angka kecil tampil lebih dulu." />

            <div class="flex gap-3 border-t border-line pt-6">
                <x-ui.button type="submit" icon="check">{{ $isEdit ? 'Simpan Perubahan' : 'Simpan Skill' }}</x-ui.button>
                <x-ui.button :href="route('admin.skills.index')" variant="outline">Batal</x-ui.button>
            </div>
        </div>
    </form>
@endsection