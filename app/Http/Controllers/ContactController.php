<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMessageRequest;
use App\Models\Message;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Halaman kontak + penyimpanan pesan (FR-08, FR-09).
 *
 * Pengirim wajib masuk dengan Google. Nama dan email diambil dari akun
 * Google (guard "visitor"), bukan dari input form, supaya tidak bisa
 * dipalsukan dan setiap pesan punya alamat email terverifikasi.
 */
class ContactController extends Controller
{
    public function index(): View
    {
        return view('pages.kontak', [
            'visitor' => Auth::guard('visitor')->user(),
        ]);
    }

    public function store(StoreMessageRequest $request): RedirectResponse
    {
        $visitor = Auth::guard('visitor')->user();

        Message::create([
            ...$request->safe()->only(['subject', 'body']),
            'name' => $visitor->name,
            'email' => $visitor->email,
        ]);

        return redirect()
            ->route('kontak')
            ->with('success', 'Pesan berhasil terkirim. Saya akan membalas melalui email atau WhatsApp.');
    }
}
