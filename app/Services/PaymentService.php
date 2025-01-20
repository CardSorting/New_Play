<?php

namespace App\Services;

use App\Contracts\Services\PaymentServiceInterface;
use App\Enums\PaymentStatus;
use App\Exceptions\PaymentException;
use App\Models\User;
use App\ValueObjects\{Cart, PaymentIntent};
use Illuminate\Support\Facades\{Cache, Log};
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentIntent as StripePaymentIntent;
use Stripe\Stripe;

class PaymentService implements PaymentServiceInterface
{
    public function __construct(
        private readonly StripeService $stripeService,
        private readonly PulseService $pulseService
    ) {
        Stripe::setApiKey(config('stripe.secret'));
    }

    /**
     * @inheritdoc
     */
    public function createPaymentIntent(Cart $cart, string $idempotencyKey): PaymentIntent
    {
        // Check idempotency cache
        $cacheKey = $this->getIdempotencyCacheKey($idempotencyKey);
        if ($cached = Cache::get($cacheKey)) {
            Log::info('Returning cached payment intent', [
                'idempotencyKey' => $idempotencyKey,
                'intentId' => $cached['id']
            ]);
            return PaymentIntent::fromStripe($cached);
        }

        try {
            $amount = $cart->getTotalAmount();
            if ($amount < config('stripe.minimum_amount')) {
                throw PaymentException::invalidCart(
                    "Amount must be at least $" . config('stripe.minimum_amount')
                );
            }

            $firstItem = $cart->getItems()->first();
            $params = [
                'amount' => (int) ($amount * 100), // Convert to cents
                'currency' => config('stripe.currency'),
                'automatic_payment_methods' => config('stripe.automatic_payment_methods'),
                'metadata' => [
                    'item_id' => $firstItem?->getId(),
                    'quantity' => $firstItem?->getQuantity(),
                ]
            ];

            Log::info('Creating Stripe payment intent', $params);
            
            $paymentIntent = StripePaymentIntent::create($params);
            
            // Cache the result
            Cache::put(
                $cacheKey,
                $paymentIntent->toArray(),
                config('stripe.cache.ttl.payment_intent')
            );
            
            Log::info('Created payment intent', [
                'id' => $paymentIntent->id,
                'amount' => $amount,
                'idempotencyKey' => $idempotencyKey
            ]);

            return PaymentIntent::fromStripe($paymentIntent->toArray());

        } catch (ApiErrorException $e) {
            Log::error('Stripe API error creating payment intent', [
                'error' => $e->getMessage(),
                'type' => $e->getError()->type ?? null,
                'code' => $e->getError()->code ?? null,
                'cart' => $cart->toArray()
            ]);
            throw PaymentException::stripeError(
                'Failed to create payment intent',
                ['stripe_error' => $e->getMessage()],
                $e
            );
        } catch (\Exception $e) {
            Log::error('Unexpected error creating payment intent', [
                'error' => $e->getMessage(),
                'cart' => $cart->toArray()
            ]);
            throw PaymentException::paymentFailed(
                'Failed to process payment',
                ['error' => $e->getMessage()],
                $e
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function confirmPayment(string $paymentIntentId, User $user, int $amount): array
    {
        $confirmCacheKey = $this->getConfirmationCacheKey($paymentIntentId);
        
        try {
            // Check if payment was already confirmed
            if (Cache::has($confirmCacheKey)) {
                Log::info('Payment already confirmed', [
                    'paymentIntentId' => $paymentIntentId,
                    'userId' => $user->id
                ]);
                return ['status' => PaymentStatus::COMPLETED->value];
            }

            $paymentIntent = StripePaymentIntent::retrieve($paymentIntentId);
            $status = PaymentStatus::fromStripeStatus($paymentIntent->status);

            if ($status === PaymentStatus::COMPLETED) {
                try {
                    // Add credits to user's account
                    $this->pulseService->addCredits(
                        user: $user,
                        amount: $amount,
                        description: 'Credit purchase',
                        reference: $paymentIntentId
                    );

                    // Mark payment as confirmed
                    Cache::put(
                        $confirmCacheKey,
                        true,
                        config('stripe.cache.ttl.payment_intent')
                    );

                    Log::info('Payment confirmed and credits added', [
                        'paymentIntentId' => $paymentIntentId,
                        'userId' => $user->id,
                        'amount' => $amount
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to add credits after payment', [
                        'error' => $e->getMessage(),
                        'paymentIntentId' => $paymentIntentId,
                        'userId' => $user->id,
                    ]);
                    throw PaymentException::paymentFailed(
                        'Payment completed but failed to add credits',
                        ['error' => $e->getMessage()],
                        $e
                    );
                }
            }

            return ['status' => $status->value];

        } catch (ApiErrorException $e) {
            Log::error('Stripe API error confirming payment', [
                'error' => $e->getMessage(),
                'type' => $e->getError()->type ?? null,
                'paymentIntentId' => $paymentIntentId,
            ]);
            throw PaymentException::stripeError(
                'Failed to confirm payment with Stripe',
                ['stripe_error' => $e->getMessage()],
                $e
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function handleWebhook(array $payload): void
    {
        try {
            if ($payload['type'] !== 'payment_intent.succeeded') {
                return;
            }

            $paymentIntent = $payload['data']['object'];
            $metadata = $paymentIntent['metadata'] ?? [];
            
            if (isset($metadata['userId'], $metadata['amount'])) {
                $user = User::find($metadata['userId']);
                if ($user) {
                    $this->confirmPayment(
                        $paymentIntent['id'],
                        $user,
                        (int) $metadata['amount']
                    );
                }
            }

            Log::info('Webhook processed', [
                'type' => $payload['type'],
                'paymentIntentId' => $paymentIntent['id'] ?? null
            ]);
        } catch (\Exception $e) {
            Log::error('Webhook processing failed', [
                'error' => $e->getMessage(),
                'payload' => $payload
            ]);
            throw PaymentException::webhookError(
                'Failed to process webhook',
                ['error' => $e->getMessage()],
                $e
            );
        }
    }

    private function getIdempotencyCacheKey(string $key): string
    {
        return config('stripe.cache.prefix') . 'idempotency:' . $key;
    }

    private function getConfirmationCacheKey(string $paymentIntentId): string
    {
        return config('stripe.cache.prefix') . 'confirm:' . $paymentIntentId;
    }
}