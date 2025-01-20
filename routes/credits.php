<?php

use App\Http\Controllers\CreditTransactionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('credits')->name('credits.')->group(function () {
        Route::get('/balance', [CreditTransactionController::class, 'balance'])
            ->name('balance');
            
        Route::post('/add', [CreditTransactionController::class, 'addCredits'])
            ->name('add');
            
        Route::post('/deduct', [CreditTransactionController::class, 'deductCredits'])
            ->name('deduct');
            
        Route::get('/history', [CreditTransactionController::class, 'history'])
            ->name('history');
    });
});