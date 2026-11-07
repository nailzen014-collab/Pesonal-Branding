@extends('layouts.admin')

@section('title', 'Akun Admin')
@section('subtitle', 'Ubah email dan password untuk masuk ke panel admin')

@section('content')
    <div class="grid max-w-4xl gap-6 lg:grid-cols-2">
        <section class="card">
            <h2 class="font-display text-lg font-semibold text-text">Email</h2>
            <p class="mt-2 text-sm text-muted">
                Email ini dipakai untuk masuk ke panel admin.
            </p>

            <form method="POST" action="{{ route('admin.account.email.update') }}" class="mt-6 space-y-5">
                @csrf
                @method('PUT')

                <x-ui.field label="Email saat ini" :value="$user->email" readonly aria-readonly="true"
                    class="cursor-not-allowed opacity-60" />

                <x-ui.field label="Email baru" name="email" type="email" :required="true" autocomplete="email"
                    placeholder="nama@example.com" hint="Gunakan huruf kecil dan alamat email aktif." />

                <x-ui.field label="Password saat ini" name="current_password" type="password" :required="true"
                    autocomplete="current-password" hint="Dipakai untuk memastikan benar ini Anda." />

                <x-ui.button type="submit" icon="mail">Simpan Email</x-ui.button>
            </form>
        </section>

        <section class="card">
            <h2 class="font-display text-lg font-semibold text-text">Password</h2>
            <p class="mt-2 text-sm text-muted">
                Setelah password diubah, sesi ini tetap login dan ID sesi diperbarui.
            </p>

            <form method="POST" action="{{ route('admin.account.password.update') }}" class="mt-6 space-y-5">
                @csrf
                @method('PUT')

                <x-ui.field label="Password saat ini" name="current_password" type="password" :required="true"
                    autocomplete="current-password" />

                <x-ui.field label="Password baru" name="password" type="password" :required="true"
                    autocomplete="new-password" hint="Minimal 8 karakter." />

                <x-ui.field label="Konfirmasi password baru" name="password_confirmation" type="password" :required="true"
                    autocomplete="new-password" />

                <x-ui.button type="submit" icon="shield">Simpan Password</x-ui.button>
            </form>
        </section>
    </div>
@endsection
