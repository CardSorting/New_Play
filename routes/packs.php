<?php

use App\Http\Controllers\PackController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])
    ->prefix('packs')
    ->name('packs.')
    ->controller(PackController::class)
    ->group(function () {
        // Pack listing and creation
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        
        // Individual pack management
        Route::prefix('{pack}')->group(function () {
            Route::get('/', 'show')->name('show');
            Route::get('/open', 'open')->name('open')->middleware('can:view,pack');
            Route::post('/complete-open', 'completeOpen')->name('complete-open')->middleware('can:view,pack');
            
            // Pack building (requires pack to not be sealed)
            Route::middleware('can:update,pack')->group(function () {
                Route::post('/add-card', 'addCard')->name('add-card');
                Route::post('/seal', 'seal')->name('seal');
            });
        });
    });
