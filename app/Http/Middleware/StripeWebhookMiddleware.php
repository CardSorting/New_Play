<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use Stripe\WebhookSignature;
use Symfony\Component\HttpFoundation\Response;

class StripeWebhookMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->isMethod('POST')) {
            Log::warning('Invalid webhook method', [
                'method' => $request->method()
            ]);
            return response('Invalid method', 405);
        }

        $signature = $request->header('Stripe-Signature');
        if (!$signature) {
            Log::warning('Missing Stripe signature');
            return response('Missing signature', 400);
        }

        try {
            // Get the raw payload
            $payload = $request->getContent();
            
            // Verify webhook signature
            WebhookSignature::verifyHeader(
                $payload,
                $signature,
                config('services.stripe.webhook_secret'),
                300 // Tolerance in seconds
            );

            // Store the parsed payload in the request for the controller to use
            $request->merge(['stripe_payload' => json_decode($payload, true)]);

            return $next($request);
        } catch (SignatureVerificationException $e) {
            Log::warning('Invalid Stripe signature', [
                'error' => $e->getMessage()
            ]);
            return response('Invalid signature', 400);
        } catch (\Exception $e) {
            Log::error('Webhook verification failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response('Webhook verification failed', 400);
        }
    }
}