<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-6 text-center">
        <h1 class="font-display text-xl font-semibold text-text">Masuk ke Panel Admin</h1>
        <p class="mt-2 text-sm text-muted">Gunakan akun admin yang dibuat melalui seeder.</p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="space-y-5">
            <x-ui.field label="Email" name="email" type="email" required autocomplete="username"
                placeholder="admin@example.com" autofocus />

            <x-ui.field label="Password" name="password" type="password" required
                autocomplete="current-password" placeholder="Minimal 8 karakter" />

            <label class="flex cursor-pointer items-center gap-3 text-sm text-muted">
                <input type="checkbox" name="remember" id="remember"
                    class="rounded border-line bg-surface-2 text-primary focus:ring-primary focus:ring-offset-ink">
                Ingat saya
            </label>
        </div>

        <div class="mt-6 flex flex-col gap-3">
            <x-ui.button type="submit" class="w-full" icon="arrow-right">Masuk</x-ui.button>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-center text-xs text-muted hover:text-primary">
                    Lupa password?
                </a>
            @endif
        </div>
    </form>
</x-guest-layout>