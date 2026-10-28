<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Mencegah clickjacking.
        $response->headers->set(
            'X-Frame-Options',
            'DENY'
        );

        // Mencegah MIME-type sniffing.
        $response->headers->set(
            'X-Content-Type-Options',
            'nosniff'
        );

        // Membatasi informasi referrer.
        $response->headers->set(
            'Referrer-Policy',
            'strict-origin-when-cross-origin'
        );

        // Content Security Policy aktif.
        $response->headers->set(
            'Content-Security-Policy',
            "default-src 'self'; " .
            "script-src 'self'; " .
            "style-src 'self'; " .
            "img-src 'self' data:; " .
            "font-src 'self'; " .
            "object-src 'none'; " .
            "base-uri 'self'; " .
            "form-action 'self'; " .
            "frame-ancestors 'none';"
        );

        return $response;
    }
}
