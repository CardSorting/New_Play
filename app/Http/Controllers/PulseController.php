<?php

namespace App\Http\Controllers;

use App\Services\Credits\DailyPulseService;
use Illuminate\Http\{JsonResponse, Request};
use Illuminate\Support\Facades\Log;

class PulseController extends Controller
{
    public function __construct(
        private readonly DailyPulseService $pulseService
    ) {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        $pulseData = $this->pulseService->getPulseClaimData($user);

        return view('pulse.index', [
            'amount' => $pulseData->amount,
            'canClaim' => $pulseData->canClaim,
            'nextClaimTime' => $pulseData->getNextClaimTimeString(),
            'creditBalance' => $this->pulseService->getCreditBalance($user)
        ]);
    }

    public function claim(): JsonResponse
    {
        $user = auth()->user();
        
        Log::info('Starting daily pulse claim', [
            'user_id' => $user->id
        ]);

        try {
            $claimed = $this->pulseService->claim($user);

            if (!$claimed) {
                $pulseData = $this->pulseService->getPulseClaimData($user);
                
                return response()->json([
                    'error' => 'Cannot claim yet',
                    'next_claim' => $pulseData->getNextClaimTimeString()
                ], 400);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Daily pulse claimed successfully',
                'new_balance' => $this->pulseService->getCreditBalance($user)
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to claim daily pulse', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Failed to claim daily pulse',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function checkStatus(): JsonResponse
    {
        $user = auth()->user();
        $pulseData = $this->pulseService->getPulseClaimData($user);
        
        return response()->json([
            'can_claim' => $pulseData->canClaim,
            'next_claim' => $pulseData->getNextClaimTimeString(),
            'credit_balance' => $this->pulseService->getCreditBalance($user)
        ]);
    }
}
