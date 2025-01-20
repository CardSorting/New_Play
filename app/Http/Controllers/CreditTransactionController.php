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

    public function balance(): JsonResponse
    {
        $balance = $this->pulseService->getCreditBalance(Auth::user());

        return response()->json([
            'balance' => $balance
        ]);
    }

    public function addCredits(CreditTransactionRequest $request): JsonResponse
    {
        try {
            $this->pulseService->addCredits(
                user: Auth::user(),
                amount: $request->validated('amount'),
                description: $request->validated('description'),
                reference: $request->validated('reference'),
                pack_id: $request->validated('pack_id')
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

    public function deductCredits(CreditTransactionRequest $request): JsonResponse
    {
        try {
            $success = $this->pulseService->deductCredits(
                user: Auth::user(),
                amount: $request->validated('amount'),
                description: $request->validated('description'),
                reference: $request->validated('reference'),
                pack_id: $request->validated('pack_id')
            );

            if (!$success) {
                return response()->json([
                    'message' => 'Insufficient credits'
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

    public function history(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 10);

        return response()->json([
            'transactions' => $this->pulseService->getTransactionHistory(Auth::user(), $limit)
        ]);
    }
}