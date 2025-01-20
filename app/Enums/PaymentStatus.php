<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
    case REQUIRES_ACTION = 'requires_action';

    /**
     * Check if the payment is in a final state
     */
    public function isFinal(): bool
    {
        return in_array($this, [
            self::COMPLETED,
            self::FAILED,
        ]);
    }

    /**
     * Get the status from a Stripe payment intent status
     */
    public static function fromStripeStatus(string $stripeStatus): self
    {
        return match ($stripeStatus) {
            'succeeded' => self::COMPLETED,
            'requires_payment_method' => self::FAILED,
            'requires_action', 'requires_confirmation' => self::REQUIRES_ACTION,
            'processing' => self::PROCESSING,
            default => self::PENDING,
        };
    }
}