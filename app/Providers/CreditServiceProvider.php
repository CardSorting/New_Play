<?php

namespace App\Providers;

use App\Contracts\Credits\{CreditClaimable, TimeBasedValidation, TransactionProcessor};
use App\Services\Credits\{DailyPulseService, PulseClaimValidationService, CreditTransactionService};
use Illuminate\Support\ServiceProvider;

class CreditServiceProvider extends ServiceProvider
{
    /**
     * Register credit-related services
     */
    public function register(): void
    {
        $this->app->bind(TimeBasedValidation::class, PulseClaimValidationService::class);
        $this->app->bind(TransactionProcessor::class, CreditTransactionService::class);
        
        $this->app->bind(CreditClaimable::class, DailyPulseService::class);

        // Singleton registration for services that should persist state
        $this->app->singleton(DailyPulseService::class, function ($app) {
            return new DailyPulseService(
                validator: $app->make(TimeBasedValidation::class),
                transactionProcessor: $app->make(TransactionProcessor::class)
            );
        });
    }
}
