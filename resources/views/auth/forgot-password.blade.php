<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-6 text-center">
        <h1 class="font-display text-xl font-semibold text-text">Lupa Password</h1>
        <p class="mt-2 text-sm text-muted">
            Masukkan email akun admin Anda. Kami akan mengirim tautan untuk mengatur ulang password.
        </p>
    </div>

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <x-ui.field label="Email" name="email" type="email" required autofocus autocomplete="username"
            placeholder="admin@example.com" />

        <div class="mt-6 flex flex-col gap-3">
            <x-ui.button type="submit" class="w-full" icon="mail">Kirim Tautan Reset</x-ui.button>
            <a href="{{ route('login') }}" class="text-center text-xs text-muted hover:text-primary">
                Kembali ke halaman masuk
            </a>
        </div>
    </form>
</x-guest-layout>