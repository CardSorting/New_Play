<?php

use App\Http\Controllers\CardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])
    ->prefix('cards')
    ->name('cards.')
    ->controller(CardController::class)
    ->group(function () {
        // Card listing and creation
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        
        // Individual card management
        Route::prefix('{card}')->group(function () {
            Route::get('/', 'show')->name('show');
            Route::get('/edit', 'edit')->name('edit');
            Route::put('/', 'update')->name('update');
            Route::delete('/', 'destroy')->name('destroy');
        });
    });
