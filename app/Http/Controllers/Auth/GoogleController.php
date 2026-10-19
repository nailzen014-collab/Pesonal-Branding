<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Visitor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

/**
 * Login pengunjung lewat Google (OAuth 2.0).
 *
 * Setelah berhasil masuk, pengunjung memakai guard "visitor" yang
 * terpisah dari admin. Pesan kontak selalu memakai nama & email dari
 * akun Google sehingga tidak bisa dipalsukan dari form.
 */
class GoogleController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(Request $request): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable $exception) {
            report($exception);

            return redirect()
                ->route('kontak')
                ->with('error', 'Gagal masuk dengan Google. Silakan coba lagi.');
        }

        $email = $googleUser->getEmail();

        if (! $email) {
            return redirect()
                ->route('kontak')
                ->with('error', 'Akun Google Anda tidak membagikan alamat email.');
        }

        // Cocokkan lewat google_id, atau email bila akun pernah dibuat
        // sebelum google_id tersimpan (mis. login ulang setelah reset).
        $visitor = Visitor::query()
            ->where('google_id', $googleUser->getId())
            ->orWhere('email', $email)
            ->first();

        $attributes = [
            'google_id' => $googleUser->getId(),
            'name' => $googleUser->getName() ?: $email,
            'email' => $email,
            'avatar' => $googleUser->getAvatar(),
        ];

        if ($visitor) {
            $visitor->update($attributes);
        } else {
            $visitor = Visitor::create($attributes);
        }

        Auth::guard('visitor')->login($visitor, true);

        $request->session()->regenerate();

        return redirect()
            ->intended(route('kontak'))
            ->with('success', 'Berhasil masuk sebagai '.$visitor->email.'.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('visitor')->logout();

        // Regenerasi sesi (bukan invalidate) agar sesi admin yang mungkin
        // aktif di browser yang sama tidak ikut terhapus.
        $request->session()->regenerate();

        return redirect()
            ->route('kontak')
            ->with('success', 'Anda telah keluar dari akun Google.');
    }
}
