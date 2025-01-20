<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Services\PulseService;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PulseController extends Controller
{
    private PulseService $pulseService;

    public function __construct(PulseService $pulseService)
    {
        $this->pulseService = $pulseService;
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        $canClaim = $this->pulseService->canClaimDailyPulse($user);
        $nextClaimTime = $this->pulseService->getNextPulseClaimTime($user);

        return view('pulse.index', [
            'amount' => 500,
            'canClaim' => $canClaim,
            'nextClaimTime' => $nextClaimTime?->format('Y-m-d H:i:s'),
            'creditBalance' => $this->pulseService->getCreditBalance($user)
        ]);
    }

    public function claim()
    {
        $user = auth()->user();
        
        Log::info('Starting daily pulse claim', [
            'user_id' => $user->id
        ]);

        if (!$this->pulseService->canClaimDailyPulse($user)) {
            Log::info('Daily pulse claim rejected - too soon', [
                'user_id' => $user->id,
                'last_claim' => $user->last_pulse_claim
            ]);

            return response()->json([
                'error' => 'Cannot claim yet',
                'next_claim' => $this->pulseService->getNextPulseClaimTime($user)?->format('Y-m-d H:i:s')
            ], 400);
        }

        try {
            $claimed = $this->pulseService->claimDailyPulse($user);

            if (!$claimed) {
                throw new \RuntimeException('Failed to claim daily pulse');
            }

            Log::info('Daily pulse claimed successfully', [
                'user_id' => $user->id
            ]);

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

    public function checkStatus()
    {
        $user = auth()->user();
        
        return response()->json([
            'can_claim' => $this->pulseService->canClaimDailyPulse($user),
            'next_claim' => $this->pulseService->getNextPulseClaimTime($user)?->format('Y-m-d H:i:s'),
            'credit_balance' => $this->pulseService->getCreditBalance($user)
        ]);
    }
}
