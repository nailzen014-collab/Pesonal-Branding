<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="font-display text-xl font-semibold text-text">Konfirmasi Password</h1>
        <p class="mt-2 text-sm text-muted">
            Ini area aman. Masukkan password Anda lagi untuk melanjutkan.
        </p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <x-ui.field label="Password" name="password" type="password" required autofocus
            autocomplete="current-password" />

        <div class="mt-6 flex flex-col gap-3">
            <x-ui.button type="submit" class="w-full" icon="shield">Konfirmasi</x-ui.button>
            <a href="{{ route('admin.dashboard') }}" class="text-center text-xs text-muted hover:text-primary">
                Batalkan
            </a>
        </div>
    </form>
</x-guest-layout>