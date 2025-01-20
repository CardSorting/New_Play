<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        // Configure rate limiters
        $this->configureRateLimiting();

        $this->routes(function () {
            // Web Routes - Load in specific order to ensure proper route resolution
            Route::middleware('web')
                ->group(function () {
                    // Public routes first
                    require base_path('routes/web.php');
                    
                    // Authentication routes
                    require base_path('routes/auth.php');
                    
                    // Protected routes
                    Route::middleware(['auth', 'verified'])->group(function () {
                        // Dashboard routes (including cards, packs, and credits)
                        Route::prefix('dashboard')->group(function () {
                            require base_path('routes/dashboard.php');
                            require base_path('routes/cards.php');
                            require base_path('routes/packs.php');
                            require base_path('routes/credits.php');
                        });
                        
                        // Marketplace routes with rate limiting
                        Route::middleware(\App\Http\Middleware\MarketplaceRateLimiter::class)->group(function () {
                            require base_path('routes/marketplace.php');
                        });
                    });
                });
        });
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        // API rate limiting
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
