<?php

namespace App\Contracts\Services;

use App\Models\User;
use App\ValueObjects\PaymentIntent;
use App\ValueObjects\Cart;

interface PaymentServiceInterface
{
    /**
     * Create a new payment intent
     *
     * @param Cart $cart Cart items for the payment
     * @param string $idempotencyKey Unique key to prevent duplicate payments
     * @return PaymentIntent
     * @throws \App\Exceptions\PaymentException
     */
    public function createPaymentIntent(Cart $cart, string $idempotencyKey): PaymentIntent;

    /**
     * Confirm a payment intent
     *
     * @param string $paymentIntentId The Stripe payment intent ID
     * @param User $user The user making the payment
     * @param int $amount Amount of credits to add
     * @return array<string, mixed>
     * @throws \App\Exceptions\PaymentException
     */
    public function confirmPayment(string $paymentIntentId, User $user, int $amount): array;

    /**
     * Handle webhook events from payment provider
     *
     * @param array<string, mixed> $payload The webhook payload
     * @return void
     * @throws \App\Exceptions\WebhookException
     */
    public function handleWebhook(array $payload): void;
}