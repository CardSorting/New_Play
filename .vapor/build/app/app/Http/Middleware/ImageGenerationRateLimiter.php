<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Symfony\Component\HttpFoundation\Response;

class ImageGenerationRateLimiter
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
        $key = 'generate:' . ($request->user()?->id ?: $request->ip());

        // Create or get the rate limiter
        RateLimiter::for($key, function() {
            return Limit::perMinute(1);
        });

        // Check if we've exceeded the rate limit
        if (RateLimiter::tooManyAttempts($key, 1)) {
            $seconds = RateLimiter::availableIn($key);
            abort(429, "Please wait {$seconds} seconds before generating another image.");
        }

        RateLimiter::hit($key);
        return $next($request);
    }
}
