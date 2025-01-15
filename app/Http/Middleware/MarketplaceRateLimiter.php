<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Symfony\Component\HttpFoundation\Response;

class MarketplaceRateLimiter
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response 
    {
        $key = 'marketplace:' . ($request->user()?->id ?: $request->ip());

        // Create or get the rate limiter
        RateLimiter::for($key, function() {
            return Limit::perMinute(120);
        });

        // Check if we've exceeded the rate limit
        if (RateLimiter::tooManyAttempts($key, 120)) {
            $seconds = RateLimiter::availableIn($key);
            abort(429, "Too many marketplace requests. Please wait {$seconds} seconds before trying again.");
        }

        RateLimiter::hit($key);
        return $next($request);
    }
}
