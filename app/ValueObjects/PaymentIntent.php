<?php

namespace App\ValueObjects;

use App\Enums\PaymentStatus;

class PaymentIntent
{
    public function __construct(
        private readonly string $id,
        private readonly string $clientSecret,
        private readonly PaymentStatus $status,
        private readonly float $amount,
        private readonly ?string $error = null
    ) {}

    public function getId(): string
    {
        return $this->id;
    }

    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    public function getStatus(): PaymentStatus
    {
        return $this->status;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function isSuccessful(): bool
    {
        return $this->status === PaymentStatus::COMPLETED;
    }

    public function requiresAction(): bool
    {
        return $this->status === PaymentStatus::REQUIRES_ACTION;
    }

    /**
     * Create a PaymentIntent from a Stripe payment intent
     *
     * @param array<string, mixed> $stripeIntent
     */
    public static function fromStripe(array $stripeIntent): self
    {
        return new self(
            id: $stripeIntent['id'],
            clientSecret: $stripeIntent['client_secret'],
            status: PaymentStatus::fromStripeStatus($stripeIntent['status']),
            amount: $stripeIntent['amount'] / 100, // Convert from cents
            error: $stripeIntent['last_payment_error']['message'] ?? null
        );
    }

    /**
     * Convert to array for API response
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'clientSecret' => $this->clientSecret,
            'status' => $this->status->value,
            'amount' => $this->amount,
            'error' => $this->error,
            'requires_action' => $this->requiresAction(),
        ];
    }
}