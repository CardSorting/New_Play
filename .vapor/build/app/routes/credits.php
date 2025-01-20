<?php

use App\Http\Controllers\CreditTransactionController;
use Illuminate\Support\Facades\Route;

// Protected routes
Route::middleware(['auth:sanctum'])->group(function () {
    // Credit management routes
    Route::prefix('credits')->name('credits.')->group(function () {
        Route::get('/balance', [CreditTransactionController::class, 'balance'])
            ->name('balance');
            
        Route::post('/claim-pulse', [CreditTransactionController::class, 'claimPulse'])
            ->name('claim-pulse')
            ->middleware('throttle:30,1');
            
        Route::post('/add', [CreditTransactionController::class, 'addCredits'])
            ->name('add')
            ->middleware('throttle:30,1');
            
        Route::post('/deduct', [CreditTransactionController::class, 'deductCredits'])
            ->name('deduct')
            ->middleware('throttle:30,1');
            
        Route::get('/history', [CreditTransactionController::class, 'history'])
            ->name('history');
    });
});