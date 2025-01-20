<?php

namespace App\Http\Controllers;

use App\Services\PulseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CreditController extends Controller
{
    private PulseService $pulseService;

    public function __construct(PulseService $pulseService)
    {
        $this->pulseService = $pulseService;
    }

    public function getBalance(): JsonResponse
    {
        return response()->json([
            'balance' => auth()->user()->getCreditBalance()
        ]);
    }

    public function addCredits(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount' => 'required|integer|min:1',
            'description' => 'nullable|string|max:255',
            'reference' => 'nullable|string|max:255'
        ]);

        auth()->user()->addCredits(
            $validated['amount'],
            $validated['description'] ?? null,
            $validated['reference'] ?? null
        );

        return response()->json([
            'message' => 'Credits added successfully',
            'balance' => auth()->user()->getCreditBalance()
        ]);
    }

    public function deductCredits(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount' => 'required|integer|min:1',
            'description' => 'nullable|string|max:255',
            'reference' => 'nullable|string|max:255'
        ]);

        $success = auth()->user()->deductCredits(
            $validated['amount'],
            $validated['description'] ?? null,
            $validated['reference'] ?? null
        );

        if (!$success) {
            return response()->json([
                'message' => 'Insufficient credits',
                'balance' => auth()->user()->getCreditBalance()
            ], 400);
        }

        return response()->json([
            'message' => 'Credits deducted successfully',
            'balance' => auth()->user()->getCreditBalance()
        ]);
    }

    public function getHistory(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 10);
        
        return response()->json([
            'transactions' => auth()->user()->getCreditHistory($limit)
        ]);
    }
}
