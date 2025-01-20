<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreditTransactionRequest;
use App\Services\PulseService;
use Illuminate\Http\{JsonResponse, Request};
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CreditTransactionController extends Controller
{
    public function __construct(
        private readonly PulseService $pulseService
    ) {}

    /**
     * Get user's credit balance
     */
    public function balance(): JsonResponse
    {
        return response()->json([
            'balance' => $this->pulseService->getCreditBalance(Auth::user())
        ]);
    }

    /**
     * Claim daily pulse credits
     */
    public function claimPulse(): JsonResponse
    {
        $user = Auth::user();
        
        if (!$this->pulseService->canClaimDailyPulse($user)) {
            $nextClaimTime = $this->pulseService->getNextPulseClaimTime($user);
            return response()->json([
                'message' => 'Cannot claim pulse yet',
                'next_claim_time' => $nextClaimTime->toISOString(),
            ], Response::HTTP_BAD_REQUEST);
        }

        if ($this->pulseService->claimDailyPulse($user)) {
            return response()->json([
                'message' => 'Daily pulse claimed successfully',
                'balance' => $this->pulseService->getCreditBalance($user)
            ]);
        }

        return response()->json([
            'message' => 'Failed to claim daily pulse',
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
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

            return response()->json([
                'message' => 'Credits added successfully',
                'balance' => $this->pulseService->getCreditBalance(Auth::user())
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to add credits',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
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
                return response()->json([
                    'message' => 'Insufficient credits',
                    'balance' => $this->pulseService->getCreditBalance(Auth::user())
                ], Response::HTTP_BAD_REQUEST);
            }

            return response()->json([
                'message' => 'Credits deducted successfully',
                'balance' => $this->pulseService->getCreditBalance(Auth::user())
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to deduct credits',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get transaction history
     */
    public function history(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 10);

        return response()->json([
            'transactions' => $this->pulseService->getTransactionHistory(Auth::user(), $limit)
        ]);
    }
}