<?php

namespace App\Providers;

use App\Contracts\Marketplace\MarketplaceServiceInterface;
use App\Contracts\Marketplace\PackRepositoryInterface;
use App\Contracts\Marketplace\PurchaseHistoryRepositoryInterface;
use App\Contracts\Marketplace\SalesHistoryRepositoryInterface;
use App\Marketplace\Services\MarketplaceService;
use App\Repositories\PackRepository;
use App\Repositories\PurchaseHistoryRepository;
use App\Repositories\SalesHistoryRepository;
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
        $this->app->bind(
            SalesHistoryRepositoryInterface::class,
            \App\Repositories\Marketplace\SalesHistoryRepository::class
        );
        $this->app->bind(
            PurchaseHistoryRepositoryInterface::class,
            PurchaseHistoryRepository::class
        );
    }

    /**
     * Bootstrap any marketplace services.
     */
    public function boot(): void
    {
        //
    }
}
