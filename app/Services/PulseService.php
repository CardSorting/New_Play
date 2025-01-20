<?php

namespace App\Services;

use App\Models\{CreditTransaction, User};
use Illuminate\Support\Facades\{DB, Log};
use RuntimeException;

class PulseService
{
    public function getCreditBalance(User $user): int
    {
        try {
            $latestTransaction = CreditTransaction::latestBalance($user->id);
            return $latestTransaction?->running_balance ?? 0;
        } catch (\Exception $e) {
            Log::error('Failed to fetch credit balance', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            throw new RuntimeException('Failed to fetch credit balance', 0, $e);
        }
    }

    public function addCredits(User $user, int $amount, ?string $description = null, ?string $reference = null, ?int $pack_id = null): void
    {
        try {
            DB::transaction(function () use ($user, $amount, $description, $reference, $pack_id) {
                // Lock using Laravel's sharedLock mechanism
                $currentBalance = CreditTransaction::where('user_id', $user->id)
                    ->lockForUpdate()
                    ->latest()
                    ->value('running_balance') ?? 0;

                $newBalance = $currentBalance + $amount;

                $transaction = new CreditTransaction([
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'type' => CreditTransaction::TYPE_CREDIT,
                    'description' => $description,
                    'reference' => $reference,
                    'pack_id' => $pack_id,
                    'running_balance' => $newBalance
                ]);

                if (!$transaction->save()) {
                    throw new RuntimeException('Failed to create credit transaction');
                }

                Log::info('Credit transaction completed', [
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'new_balance' => $newBalance
                ]);
            }, 5);
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
            return DB::transaction(function () use ($user, $amount, $description, $reference, $pack_id) {
                // Lock using Laravel's sharedLock mechanism
                $currentBalance = CreditTransaction::where('user_id', $user->id)
                    ->lockForUpdate()
                    ->latest()
                    ->value('running_balance') ?? 0;

                if ($currentBalance < $amount) {
                    return false;
                }

                $newBalance = $currentBalance - $amount;

                $transaction = new CreditTransaction([
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'type' => CreditTransaction::TYPE_DEBIT,
                    'description' => $description,
                    'reference' => $reference,
                    'pack_id' => $pack_id,
                    'running_balance' => $newBalance
                ]);

                if (!$transaction->save()) {
                    throw new RuntimeException('Failed to create debit transaction');
                }

                Log::info('Debit transaction completed', [
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'new_balance' => $newBalance
                ]);

                return true;
            }, 5);
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
}
