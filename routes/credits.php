<?php

use App\Http\Controllers\{CreditTransactionController, StripeWebhookController};
use Illuminate\Support\Facades\Route;

// Public routes (no auth required)
Route::post('webhooks/stripe', [StripeWebhookController::class, 'handle'])
    ->name('webhooks.stripe')
    ->middleware('stripe.webhook'); // You may want to create this middleware to verify Stripe signatures

// Protected routes
Route::middleware(['auth:sanctum'])->group(function () {
    // Payment routes
    Route::prefix('dashboard/api')->group(function () {
        Route::post('/payment-intent', [CreditTransactionController::class, 'createPaymentIntent'])
            ->name('credits.payment-intent')
            ->middleware('throttle:6,1'); // Limit to 6 requests per minute
            
        Route::post('/payment-intent/{paymentIntentId}/confirm', [CreditTransactionController::class, 'confirmPayment'])
            ->name('credits.payment-intent.confirm')
            ->middleware('throttle:6,1');
    });

    // Credit management routes
    Route::prefix('credits')->name('credits.')->group(function () {
        Route::get('/balance', [CreditTransactionController::class, 'balance'])
            ->name('balance');
            
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