<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="font-display text-xl font-semibold text-text">Atur Ulang Password</h1>
        <p class="mt-2 text-sm text-muted">Gunakan password baru yang mudah Anda ingat.</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="space-y-5">
            <x-ui.field label="Email" name="email" type="email" required autofocus autocomplete="username"
                :value="old('email', $request->email)" />

            <x-ui.field label="Password Baru" name="password" type="password" required autocomplete="new-password"
                placeholder="Minimal 8 karakter" />

            <x-ui.field label="Konfirmasi Password" name="password_confirmation" type="password" required
                autocomplete="new-password" placeholder="Ulangi password baru" />
        </div>

        <x-ui.button type="submit" class="mt-6 w-full" icon="check">Simpan Password Baru</x-ui.button>
    </form>
</x-guest-layout>