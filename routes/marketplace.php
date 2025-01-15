<?php

use App\Marketplace\Controllers\Browse\BrowseMarketplaceController;
use App\Marketplace\Controllers\Seller\SellerDashboardController;
use App\Marketplace\Controllers\Purchase\PurchaseHistoryController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', \App\Http\Middleware\MarketplaceRateLimiter::class])
    ->prefix('marketplace')
    ->name('marketplace.')
    ->group(function () {
        // Browse Marketplace
        Route::get('/', [BrowseMarketplaceController::class, 'index'])->name('index');
        Route::post('/packs/{pack}/purchase', [BrowseMarketplaceController::class, 'purchasePack'])
            ->name('purchase');

        // Seller Dashboard
        Route::prefix('seller')->name('seller.')->group(function () {
            Route::get('/', [SellerDashboardController::class, 'index'])->name('dashboard');
            Route::post('/packs/{pack}/list', [SellerDashboardController::class, 'listPack'])->name('list');
            Route::post('/packs/{pack}/unlist', [SellerDashboardController::class, 'unlistPack'])->name('unlist');
            Route::get('/sales', [SellerDashboardController::class, 'salesHistory'])->name('sales');
        });

        // Purchase History
        Route::prefix('purchases')->name('purchase.')->group(function () {
            Route::get('/', [PurchaseHistoryController::class, 'index'])->name('history');
        });
    });
