<?php

use App\Http\Controllers\PackController;
use Illuminate\Support\Facades\Route;

Route::prefix('packs')
    ->name('packs.')
    ->controller(PackController::class)
    ->group(function () {
        // Pack listing and creation
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        
        // Individual pack operations with model binding
        Route::group(['prefix' => '/{pack}', 'where' => ['pack' => '[0-9]+']], function () {
            Route::get('/', 'show')->name('show');
            Route::post('/open', 'open')->name('open')->middleware('can:open,pack');
            Route::delete('/', 'destroy')->name('destroy')->middleware('can:delete,pack');
            Route::post('/seal', 'seal')->name('seal')->middleware('can:seal,pack');
            Route::post('/add-card', 'addCard')->name('add-card')->middleware('can:update,pack');
        });
    });
