<?php

namespace App\Services;

use App\Models\{CreditTransaction, User};
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Carbon\Carbon;

class PulseService
{
    private const DAILY_PULSE_AMOUNT = 500;
    public function getCreditBalance(User $user): int
    {
        try {
            return CreditTransaction::latestBalance($user->id);
        } catch (\Exception $e) {
            Log::error('Failed to fetch credit balance', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            throw new RuntimeException('Failed to fetch credit balance', 0, $e);
        }
    }

    public function canClaimDailyPulse(User $user): bool
    {
        if (!$user->last_pulse_claim) {
            return true;
        }

        return Carbon::parse($user->last_pulse_claim)->addDay()->isPast();
    }

    public function claimDailyPulse(User $user): bool
    {
        if (!$this->canClaimDailyPulse($user)) {
            return false;
        }

        try {
            $user->update(['last_pulse_claim' => now()]);

            $this->addCredits(
                $user,
                self::DAILY_PULSE_AMOUNT,
                'Daily Pulse Claim'
            );

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to claim daily pulse', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function addCredits(User $user, int $amount, ?string $description = null, ?string $reference = null, ?int $pack_id = null): void
    {
        try {
            $currentBalance = CreditTransaction::latestBalance($user->id);
            $newBalance = $currentBalance + $amount;

            $transaction = CreditTransaction::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'type' => CreditTransaction::TYPE_CREDIT,
                'description' => $description,
                'reference' => $reference,
                'pack_id' => $pack_id,
                'running_balance' => $newBalance
            ]);

            Log::info('Credit transaction completed', [
                'user_id' => $user->id,
                'amount' => $amount,
                'new_balance' => $newBalance
            ]);
        } catch (\Exception $e) {
            Log::error('Credit transaction failed', [
                'user_id' => $user->id,
                'amount' => $amount,
                'error' => $e->getMessage()
            ]);
            throw new RuntimeException('Failed to add credits', 0, $e);
        }
    }

    public function deductCredits(User $user, int $amount, ?string $description = null, ?string $reference = null, ?int $pack_id = null): bool
    {
        try {
            $currentBalance = CreditTransaction::latestBalance($user->id);

            if ($currentBalance < $amount) {
                return false;
            }

            $newBalance = $currentBalance - $amount;

            $transaction = CreditTransaction::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'type' => CreditTransaction::TYPE_DEBIT,
                'description' => $description,
                'reference' => $reference,
                'pack_id' => $pack_id,
                'running_balance' => $newBalance
            ]);

            Log::info('Debit transaction completed', [
                'user_id' => $user->id,
                'amount' => $amount,
                'new_balance' => $newBalance
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Debit transaction failed', [
                'user_id' => $user->id,
                'amount' => $amount,
                'error' => $e->getMessage()
            ]);
            throw new RuntimeException('Failed to deduct credits', 0, $e);
        }
    }

    public function getTransactionHistory(User $user, int $limit = 10)
    {
        return CreditTransaction::withBalanceChanges($user->id, $limit);
    }

    public function getNextPulseClaimTime(User $user): ?Carbon
    {
        if (!$user->last_pulse_claim) {
            return null;
        }

        return Carbon::parse($user->last_pulse_claim)->addDay();
    }

    public function getNextPulseClaimTimeString(User $user): ?string
    {
        $nextClaimTime = $this->getNextPulseClaimTime($user);
        return $nextClaimTime ? $nextClaimTime->format('Y-m-d H:i:s') : null;
    }
}
