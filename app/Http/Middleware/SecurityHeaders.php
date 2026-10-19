<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): SymfonyResponse
    {
        $response = $next($request);

        $this->addSecurityHeaders($response);

        return $response;
    }

    protected function addSecurityHeaders(SymfonyResponse $response): void
    {
        $isHttps = request()->isSecure() || request()->headers->get('X-Forwarded-Proto') === 'https';

        // Content Security Policy.
        // Di lingkungan lokal, Vite dev server melayani asset dari origin
        // dan WebSocket berbeda, jadi CSP ketat hanya diterapkan di produksi
        // agar halaman tidak tampil tanpa style saat `npm run dev`.
        if (app()->environment('production')) {
            $response->headers->set('Content-Security-Policy', $this->buildCsp($isHttps));
        }

        // HTTP Strict Transport Security (HSTS) - only for HTTPS
        if ($isHttps) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        // Prevent clickjacking
        $response->headers->set('X-Frame-Options', 'DENY');

        // Prevent MIME type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Referrer Policy
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Permissions Policy (Feature Policy)
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');

        // Cross-Origin policies
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        $response->headers->set('Cross-Origin-Resource-Policy', 'same-origin');

        // Remove server information
        $response->headers->remove('Server');
        $response->headers->remove('X-Powered-By');
    }

    protected function buildCsp(bool $isHttps): string
    {
        $directives = [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.tailwindcss.com",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
            "font-src 'self' data: https://fonts.gstatic.com",
            "img-src 'self' data: https:",
            "connect-src 'self'",
            "frame-src 'none'",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'none'",
            'upgrade-insecure-requests',
        ];

        if ($isHttps) {
            $directives[] = 'block-all-mixed-content';
        }

        return implode('; ', $directives);
    }
}
