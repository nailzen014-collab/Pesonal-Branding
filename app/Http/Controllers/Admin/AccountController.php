<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAdminPasswordRequest;
use App\Http\Requests\UpdateEmailRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

/**
 * Akun admin: ganti email dan password milik pengguna yang sedang login.
 */
class AccountController extends Controller
{
    public function edit(Request $request): View
    {
        return view('admin.account.edit', [
            'user' => $request->user(),
        ]);
    }

    public function updateEmail(UpdateEmailRequest $request): RedirectResponse
    {
        $user = $request->user();
        $email = $request->validated('email');

        if ($email === $user->email) {
            return redirect()
                ->route('admin.account.edit')
                ->with('success', 'Email tidak berubah.');
        }

        $user->forceFill([
            'email' => $email,
            'email_verified_at' => null,
        ])->save();

        return redirect()
            ->route('admin.account.edit')
            ->with('success', 'Email berhasil diperbarui.');
    }

    public function updatePassword(UpdateAdminPasswordRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->forceFill([
            'password' => Hash::make($request->validated('password')),
        ])->save();

        // Regenerasi ID sesi agar sesi lama tidak bisa dipakai ulang.
        $request->session()->regenerate();

        return redirect()
            ->route('admin.account.edit')
            ->with('success', 'Password berhasil diperbarui.');
    }
}
