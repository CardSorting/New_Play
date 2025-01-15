<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeadersMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // External service domains
        $allowedDomains = [
            // PayPal
            'https://*.paypal.com',
            'https://*.paypalobjects.com',
            'https://*.sandbox.paypal.com',
            'https://www.sandbox.paypal.com',
            'https://www.paypal.com',
            'https://t.paypal.com',
            'https://c.paypal.com',
            'https://c.sandbox.paypal.com',
            // Stripe
            'https://*.stripe.com',
            'https://js.stripe.com',
            'https://api.stripe.com',
            // hCaptcha
            'https://*.hcaptcha.com',
            'https://hcaptcha.com',
            'https://newassets.hcaptcha.com'
        ];

        // Build CSP
        $csp = [
            "default-src" => ["'self'", "'unsafe-inline'", "'unsafe-eval'", ...$allowedDomains],
            "script-src" => [
                "'self'",
                "'unsafe-inline'",
                "'unsafe-eval'",
                // PayPal
                "https://www.paypal.com",
                "https://www.sandbox.paypal.com",
                "https://*.paypal.com",
                "https://*.paypalobjects.com",
                // Stripe
                "https://*.stripe.com",
                "https://js.stripe.com",
                // hCaptcha
                "https://*.hcaptcha.com",
                "https://hcaptcha.com",
                "https://newassets.hcaptcha.com"
            ],
            "style-src" => ["'self'", "'unsafe-inline'", "https://fonts.bunny.net"],
            "img-src" => [
                "'self'",
                "data:",
                "https:",
                "http:",
                "https://*.paypal.com",
                "https://*.paypalobjects.com",
                "https://*.stripe.com"
            ],
            "font-src" => ["'self'", "https://fonts.bunny.net", "https://*.paypalobjects.com"],
            "frame-src" => [
                "'self'",
                // PayPal
                "https://www.sandbox.paypal.com",
                "https://www.paypal.com",
                "https://*.paypal.com",
                // Stripe
                "https://*.stripe.com",
                "https://js.stripe.com",
                // hCaptcha
                "https://*.hcaptcha.com",
                "https://newassets.hcaptcha.com"
            ],
            "connect-src" => [
                "'self'",
                // PayPal
                "https://www.sandbox.paypal.com",
                "https://www.paypal.com",
                "https://*.paypal.com",
                "https://*.paypalobjects.com",
                // Stripe
                "https://*.stripe.com",
                "https://api.stripe.com",
                // hCaptcha
                "https://*.hcaptcha.com",
                "https://api.hcaptcha.com"
            ],
            "form-action" => ["'self'"],
            "frame-ancestors" => ["'self'"],
            "base-uri" => ["'self'"],
        ];

        // Build CSP header string
        $cspString = collect($csp)->map(function ($values, $directive) {
            return $directive . ' ' . implode(' ', $values);
        })->implode('; ');

        $response->headers->set('Content-Security-Policy', $cspString);
        
        // Add other security headers
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        return $response;
    }
}
