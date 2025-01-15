<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Services\StripeService;
use Illuminate\Support\Facades\Log;

class PulseController extends Controller
{
    private StripeService $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    public function index()
    {
        return view('pulse.index', [
            'amount' => 500,
            'price' => 45,
            'stripeKey' => config('services.stripe.key')
        ]);
    }

    public function createPaymentIntent(Request $request)
    {
        Log::info('Starting createPaymentIntent', [
            'user_id' => auth()->id() ?? 'unauthenticated',
            'request_data' => $request->all()
        ]);
        try {
            // Validate the request
            $validated = $request->validate([
                'cart' => 'required|array',
                'cart.*.id' => 'required|string',
                'cart.*.quantity' => 'required|integer|min:1',
                'cart.*.price' => 'required|numeric|min:0'
            ]);

            Log::info('Payment intent validation successful', [
                'validated_data' => $validated
            ]);

            // Create Stripe payment intent with cart data
            try {
                $paymentIntent = $this->stripeService->createPaymentIntent($validated['cart']);
                
                Log::info('Payment intent created successfully', [
                    'payment_intent_id' => $paymentIntent['id'] ?? null
                ]);

                return response()->json($paymentIntent, 200, [
                    'Content-Type' => 'application/json'
                ]);
            } catch (\Stripe\Exception\ApiErrorException $e) {
                Log::error('Stripe API error', [
                    'message' => $e->getMessage(),
                    'type' => $e->getError()->type,
                    'code' => $e->getError()->code,
                    'param' => $e->getError()->param,
                ]);
                return response()->json([
                    'error' => 'Stripe API error',
                    'message' => $e->getMessage()
                ], 400, ['Content-Type' => 'application/json']);
            } catch (\Exception $e) {
                Log::error('Stripe create payment intent error', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return response()->json([
                    'error' => 'Failed to create payment intent',
                    'message' => $e->getMessage()
                ], 500, ['Content-Type' => 'application/json']);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Payment intent validation failed', [
                'errors' => $e->errors()
            ]);
            return response()->json([
                'error' => 'Invalid request data',
                'details' => $e->errors()
            ], 422, ['Content-Type' => 'application/json']);
        }
    }

    public function confirmPayment(Request $request, $paymentIntentId)
    {
        Log::info('Starting confirmPayment', [
            'payment_intent_id' => $paymentIntentId,
            'user_id' => auth()->id() ?? 'unauthenticated',
            'request_data' => $request->all()
        ]);
        try {
            // Validate the request
            $validated = $request->validate([
                'amount' => 'required|integer|min:1'
            ]);

            Log::info('Payment confirmation validation successful', [
                'validated_data' => $validated
            ]);

            // Confirm the Stripe payment first
            $result = $this->stripeService->confirmPayment($paymentIntentId);
            
            Log::info('Payment confirmation result', [
                'status' => $result['status'],
                'payment_intent_id' => $paymentIntentId
            ]);

            // Only proceed with credit addition if payment is completed and user is authenticated
            if ($result['status'] === 'COMPLETED') {
                if (!auth()->check()) {
                    Log::error('Cannot add credits - user not authenticated', [
                        'payment_intent_id' => $paymentIntentId
                    ]);
                    return response()->json([
                        'error' => 'Authentication required',
                        'message' => 'Please log in to complete your purchase'
                    ], 401, ['Content-Type' => 'application/json']);
                }

                try {
                    Log::info('Adding credits to user account', [
                        'user_id' => auth()->id(),
                        'amount' => $validated['amount'],
                        'payment_intent' => $paymentIntentId
                    ]);

                    auth()->user()->addCredits(
                        $validated['amount'],
                        'Stripe purchase',
                        $paymentIntentId
                    );

                    Log::info('Credits added successfully');

                    return response()->json([
                        'status' => 'COMPLETED',
                        'message' => 'Payment processed successfully'
                    ], 200, ['Content-Type' => 'application/json']);
                } catch (\Exception $e) {
                    Log::error('Failed to add credits after payment', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                        'user_id' => auth()->id(),
                        'amount' => $validated['amount'],
                        'payment_intent' => $paymentIntentId
                    ]);

                    return response()->json([
                        'error' => 'Payment completed but failed to add credits',
                        'message' => $e->getMessage()
                    ], 500, ['Content-Type' => 'application/json']);
                }
            }
            
            return response()->json([
                'status' => $result['status'],
                'message' => 'Payment not completed'
            ], 400, ['Content-Type' => 'application/json']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Payment confirmation validation failed', [
                'errors' => $e->errors()
            ]);
            return response()->json([
                'error' => 'Invalid request data',
                'details' => $e->errors()
            ], 422, ['Content-Type' => 'application/json']);
        } catch (\Exception $e) {
            Log::error('Payment confirmation error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payment_intent_id' => $paymentIntentId
            ]);
            return response()->json([
                'error' => 'Failed to confirm payment',
                'message' => $e->getMessage()
            ], 500, ['Content-Type' => 'application/json']);
        }
    }
}
