<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Pastikan pengunjung sudah masuk dengan Google sebelum mengirim pesan.
 *
 * Redirect diarahkan ke halaman kontak (bukan /login admin) agar
 * pengunjung tidak bingung dengan form login pemilik website.
 */
class EnsureVisitorAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::guard('visitor')->check()) {
            return redirect()
                ->route('kontak')
                ->with('error', 'Silakan masuk dengan Google terlebih dahulu untuk mengirim pesan.');
        }

        return $next($request);
    }
}
