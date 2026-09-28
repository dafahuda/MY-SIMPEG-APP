<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Header keamanan standar untuk semua respons.
 *
 * - X-Frame-Options: DENY             -> cegah clickjacking (halaman tidak bisa di-iframe)
 * - X-Content-Type-Options: nosniff  -> cegah browser membaca teks sebagai file lain (MIME sniffing)
 * - Referrer-Policy                  -> batasi informasi URL yang bocor ke situs lain
 * - Permissions-Policy               -> matikan akses kamera/mikrofon/lokasi (tidak dipakai aplikasi)
 * - Content-Security-Policy           -> batasi sumber script/gaya; Tailwind + Livewire inline style
 *                                      tetap diizinkan
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        $response->headers->set(
            'Content-Security-Policy',
            "default-src 'self'; "
            . "script-src 'self' 'unsafe-inline' 'unsafe-eval'; "
            . "style-src 'self' 'unsafe-inline'; "
            . "img-src 'self' data: blob:; "
            . "font-src 'self' data:; "
            . "connect-src 'self'; "
            . "frame-ancestors 'none'; "
            . "object-src 'none'"
        );

        return $response;
    }
}
