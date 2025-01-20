<?php

namespace App\Providers;

use App\Services\PulseService;
use App\Services\PayPalService;
use App\Marketplace\Components\PackCard;
use Illuminate\Support\ServiceProvider;
use Illuminate\Redis\RedisManager;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(PayPalService::class, function ($app) {
            return new PayPalService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register marketplace components
        Blade::component('marketplace-browse-available-pack-card', \App\Marketplace\Components\Browse\AvailablePackCard::class);
        Blade::component('marketplace.seller.listed-pack-card', \App\Marketplace\Components\Seller\ListedPackCard::class);

        View::composer('layouts.navigation', function ($view) {
            $pulseBalance = 0;
            
            if (Auth::check()) {
                $user = Auth::user();
                $cacheKey = 'user_pulse_balance:' . $user->id;
                
                try {
                    $pulseBalance = $user->getCreditBalance();
                } catch (\Exception $e) {
                    // Keep default 0 balance on error
                    \Log::error('Failed to fetch pulse balance: ' . $e->getMessage());
                }
            }

            $view->with([
                'pulseBalance' => $pulseBalance,
                'showPulseButton' => !request()->routeIs('pulse.index'),
            ]);
        });
    }
}
