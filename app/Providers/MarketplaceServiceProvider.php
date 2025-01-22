<?php

namespace App\Providers;

use App\Contracts\Marketplace\MarketplaceServiceInterface;
use App\Contracts\Marketplace\PackRepositoryInterface;
use App\Marketplace\Services\MarketplaceService;
use App\Repositories\PackRepository;
use Illuminate\Support\ServiceProvider;

class MarketplaceServiceProvider extends ServiceProvider
{
    /**
     * Register marketplace services.
     */
    public function register(): void
    {
        $this->app->bind(PackRepositoryInterface::class, PackRepository::class);
        $this->app->bind(MarketplaceServiceInterface::class, MarketplaceService::class);
    }

    /**
     * Bootstrap any marketplace services.
     */
    public function boot(): void
    {
        //
    }
}
