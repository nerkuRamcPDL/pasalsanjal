<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

/** Applies the security headers required by SRS 9.7 to every response. */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        // Per-request nonce, generated before the response is built so both
        // the CSP header below and any <script nonce="..."> tags in Blade
        // views (via the shared `$cspNonce` variable / csp_nonce() helper)
        // use the identical value.
        $nonce = base64_encode(random_bytes(16));
        $request->attributes->set('csp_nonce', $nonce);
        View::share('cspNonce', $nonce);

        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        $nonce = $request->attributes->get('csp_nonce', '');
        $csp = "default-src 'self'; "
            ."script-src 'self' 'nonce-{$nonce}'; "
            ."style-src 'self' 'unsafe-inline'; "
            ."img-src 'self' data: https:; "
            ."font-src 'self' data:; "
            ."connect-src 'self'; "
            ."frame-ancestors 'none'; "
            ."base-uri 'self'; "
            ."form-action 'self' https://rc-epay.esewa.com.np https://epay.esewa.com.np https://dev.khalti.com https://khalti.com";
        $response->headers->set('Content-Security-Policy', $csp);

        if ($request->secure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
