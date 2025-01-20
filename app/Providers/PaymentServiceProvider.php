<?php

namespace App\Providers;

use App\Contracts\Services\PaymentServiceInterface;
use App\Services\PaymentService;
use Illuminate\Support\ServiceProvider;
use Stripe\Stripe;

class PaymentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register our PaymentService implementation
        $this->app->singleton(PaymentServiceInterface::class, PaymentService::class);

        // Register response macros
        $this->registerResponseMacros();
    }

    public function boot(): void
    {
        // Configure Stripe with our settings
        Stripe::setApiKey(config('stripe.secret'));
        
        // Set Stripe API version if specified
        if ($apiVersion = config('stripe.api_version')) {
            Stripe::setApiVersion($apiVersion);
        }

        // Merge stripe config
        $this->mergeConfigFrom(
            __DIR__.'/../../config/stripe.php', 'stripe'
        );

        // Register custom exception handling
        $this->registerExceptionHandling();
    }

    private function registerResponseMacros(): void
    {
        \Illuminate\Support\Facades\Response::macro('payment', function ($data) {
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        });

        \Illuminate\Support\Facades\Response::macro('paymentError', function (
            string $message,
            array $errors = [],
            int $status = 400
        ) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'errors' => $errors
            ], $status);
        });
    }

    private function registerExceptionHandling(): void
    {
        $this->app->make(\Illuminate\Contracts\Debug\ExceptionHandler::class)
            ->renderable(function (\App\Exceptions\PaymentException $e, $request) {
                if ($request->expectsJson()) {
                    return response()->paymentError(
                        $e->getMessage(),
                        $e->getContext(),
                        $e->getCode() ?: 400
                    );
                }
            });
    }
}