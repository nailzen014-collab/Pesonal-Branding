<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMessageRequest;
use App\Models\Message;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Halaman kontak + penyimpanan pesan (FR-08, FR-09).
 */
class ContactController extends Controller
{
    public function index(): View
    {
        return view('pages.kontak');
    }

    public function store(StoreMessageRequest $request): RedirectResponse
    {
        Message::create($request->safe()->only(['name', 'email', 'subject', 'body']));

        return redirect()
            ->route('kontak')
            ->with('success', 'Pesan berhasil terkirim. Saya akan membalas melalui email atau WhatsApp.');
    }
}
