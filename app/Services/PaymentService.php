<?php

namespace App\Services;

use App\Contracts\Services\PaymentServiceInterface;
use App\Enums\PaymentStatus;
use App\Exceptions\PaymentException;
use App\Models\User;
use App\ValueObjects\{Cart, PaymentIntent};
use Illuminate\Database\ConnectionResolver;
use Illuminate\Support\Facades\{Cache, DB, Log};
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentIntent as StripePaymentIntent;
use Stripe\Stripe;

class PaymentService implements PaymentServiceInterface
{
    private const CACHE_TTL = 172800; // 48 hours

    public function __construct(
        private readonly PulseService $pulseService
    ) {
        // Use services.stripe.secret consistently across the application
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * @inheritdoc
     */
    public function createPaymentIntent(Cart $cart, string $idempotencyKey): PaymentIntent
    {
        // Use DynamoDB for idempotency check
        $cacheKey = $this->getIdempotencyCacheKey($idempotencyKey);
        
        // First, try to get from cache
        if ($cached = Cache::lock($cacheKey, 10)->get(function () use ($cacheKey) {
            return Cache::get($cacheKey);
        })) {
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
                'automatic_payment_methods' => [
                    'enabled' => true
                ],
                'metadata' => [
                    'item_id' => $firstItem?->getId(),
                    'quantity' => $firstItem?->getQuantity(),
                ],
                // Add idempotency key for Stripe API
                'idempotency_key' => $idempotencyKey,
            ];

            Log::info('Creating Stripe payment intent', $params);
            
            $paymentIntent = StripePaymentIntent::create($params);
            
            // Cache with distributed lock to prevent race conditions
            Cache::lock($cacheKey, 10)->get(function () use ($cacheKey, $paymentIntent) {
                Cache::put(
                    $cacheKey,
                    $paymentIntent->toArray(),
                    self::CACHE_TTL
                );
            });
            
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
            // Use distributed locking for confirmation
            return Cache::lock($confirmCacheKey, 10)->get(function () use ($confirmCacheKey, $paymentIntentId, $user, $amount) {
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
                        // Wrap in transaction with retry for serverless environment
                        return DB::transaction(function () use ($user, $amount, $paymentIntentId, $confirmCacheKey) {
                            // Add credits to user's account
                            $this->pulseService->addCredits(
                                user: $user,
                                amount: $amount,
                                description: 'Credit purchase',
                                reference: $paymentIntentId
                            );

                            // Mark payment as confirmed in cache
                            Cache::put(
                                $confirmCacheKey,
                                true,
                                self::CACHE_TTL
                            );

                            Log::info('Payment confirmed and credits added', [
                                'paymentIntentId' => $paymentIntentId,
                                'userId' => $user->id,
                                'amount' => $amount
                            ]);

                            return ['status' => PaymentStatus::COMPLETED->value];
                        }, 3); // Retry up to 3 times
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
            });
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
        if ($payload['type'] !== 'payment_intent.succeeded') {
            return;
        }

        $paymentIntent = $payload['data']['object'];
        $metadata = $paymentIntent['metadata'] ?? [];
        
        if (!isset($metadata['userId'], $metadata['amount'])) {
            Log::warning('Webhook received without required metadata', [
                'paymentIntentId' => $paymentIntent['id'] ?? null
            ]);
            return;
        }

        // Use a shorter cache TTL for webhook processing status
        $webhookKey = "webhook:{$paymentIntent['id']}";
        
        try {
            // Ensure webhook is processed only once with distributed lock
            Cache::lock($webhookKey, 10)->get(function () use ($paymentIntent, $metadata, $webhookKey) {
                // Check if webhook was already processed
                if (Cache::has($webhookKey)) {
                    Log::info('Webhook already processed', [
                        'paymentIntentId' => $paymentIntent['id']
                    ]);
                    return;
                }

                DB::transaction(function () use ($paymentIntent, $metadata) {
                    $user = User::find($metadata['userId']);
                    if (!$user) {
                        Log::error('User not found for webhook', [
                            'userId' => $metadata['userId'],
                            'paymentIntentId' => $paymentIntent['id']
                        ]);
                        return;
                    }

                    $this->confirmPayment(
                        $paymentIntent['id'],
                        $user,
                        (int) $metadata['amount']
                    );
                }, 3); // Retry up to 3 times

                // Mark webhook as processed
                Cache::put($webhookKey, true, 3600); // Keep for 1 hour

                Log::info('Webhook processed successfully', [
                    'type' => 'payment_intent.succeeded',
                    'paymentIntentId' => $paymentIntent['id']
                ]);
            });
        } catch (\Exception $e) {
            Log::error('Webhook processing failed', [
                'error' => $e->getMessage(),
                'paymentIntentId' => $paymentIntent['id'] ?? null
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