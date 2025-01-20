<?php

namespace App\Http\Controllers;

use App\Contracts\Services\PaymentServiceInterface;
use App\Http\Requests\{CreditTransactionRequest, CreatePaymentIntentRequest};
use App\Services\PulseService;
use App\ValueObjects\Cart;
use Illuminate\Http\{JsonResponse, Request};
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CreditTransactionController extends Controller
{
    public function __construct(
        private readonly PulseService $pulseService,
        private readonly PaymentServiceInterface $paymentService
    ) {}

    /**
     * Create a new payment intent for credit purchase
     */
    public function createPaymentIntent(CreatePaymentIntentRequest $request): JsonResponse
    {
        try {
            $cart = Cart::fromRequest($request->validated());
            
            $paymentIntent = $this->paymentService->createPaymentIntent(
                cart: $cart,
                idempotencyKey: $request->validated('idempotencyKey')
            );

            return response()->payment($paymentIntent->toArray());
        } catch (\Exception $e) {
            // PaymentException handling is done by our service provider
            throw $e;
        }
    }

    /**
     * Confirm a payment and add credits
     */
    public function confirmPayment(Request $request, string $paymentIntentId): JsonResponse
    {
        try {
            $amount = $request->validate([
                'amount' => 'required|integer|min:1'
            ])['amount'];

            $result = $this->paymentService->confirmPayment(
                paymentIntentId: $paymentIntentId,
                user: Auth::user(),
                amount: $amount
            );

            return response()->payment($result);
        } catch (\Exception $e) {
            // PaymentException handling is done by our service provider
            throw $e;
        }
    }

    /**
     * Get user's credit balance
     */
    public function balance(): JsonResponse
    {
        return response()->payment([
            'balance' => $this->pulseService->getCreditBalance(Auth::user())
        ]);
    }

    /**
     * Add credits to user's account
     */
    public function addCredits(CreditTransactionRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            
            $this->pulseService->addCredits(
                user: Auth::user(),
                amount: $validated['amount'],
                description: $validated['description'],
                reference: $validated['reference'],
                pack_id: $validated['pack_id']
            );

            return response()->payment([
                'message' => 'Credits added successfully',
                'balance' => $this->pulseService->getCreditBalance(Auth::user())
            ]);
        } catch (\Exception $e) {
            return response()->paymentError(
                'Failed to add credits',
                ['error' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Deduct credits from user's account
     */
    public function deductCredits(CreditTransactionRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            
            $success = $this->pulseService->deductCredits(
                user: Auth::user(),
                amount: $validated['amount'],
                description: $validated['description'],
                reference: $validated['reference'],
                pack_id: $validated['pack_id']
            );

            if (!$success) {
                return response()->paymentError(
                    'Insufficient credits',
                    [],
                    Response::HTTP_BAD_REQUEST
                );
            }

            return response()->payment([
                'message' => 'Credits deducted successfully',
                'balance' => $this->pulseService->getCreditBalance(Auth::user())
            ]);
        } catch (\Exception $e) {
            return response()->paymentError(
                'Failed to deduct credits',
                ['error' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Get transaction history
     */
    public function history(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 10);

        return response()->payment([
            'transactions' => $this->pulseService->getTransactionHistory(Auth::user(), $limit)
        ]);
    }
}