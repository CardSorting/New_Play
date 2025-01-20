<?php

namespace App\Http\Controllers;

use App\Contracts\Services\PaymentServiceInterface;
use Illuminate\Http\{Request, Response};
use Illuminate\Support\Facades\Log;
use Stripe\WebhookSignature;

class StripeWebhookController extends Controller
{
    public function __construct(
        private readonly PaymentServiceInterface $paymentService
    ) {}

    public function handle(Request $request): Response
    {
        try {
            $payload = $request->all();
            $sigHeader = $request->header('Stripe-Signature');
            
            if (!$sigHeader) {
                Log::warning('Missing Stripe signature header');
                return response('Missing signature', 400);
            }

            // Verify webhook signature
            try {
                WebhookSignature::verifyHeader(
                    json_encode($payload),
                    $sigHeader,
                    config('services.stripe.webhook_secret'),
                    300 // Tolerance in seconds
                );
            } catch (\Exception $e) {
                Log::warning('Invalid webhook signature', [
                    'error' => $e->getMessage()
                ]);
                return response('Invalid signature', 400);
            }

            // Process the webhook
            $this->paymentService->handleWebhook($payload);

            return response('Webhook processed', 200);
        } catch (\Exception $e) {
            Log::error('Error processing webhook', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response('Webhook processing failed', 500);
        }
    }
}