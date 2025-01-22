<?php

namespace App\Services\Credits;

use App\Models\User;
use App\Models\CreditTransaction;
use App\Contracts\Credits\TransactionProcessor;
use Illuminate\Support\Facades\{DB, Log};
use RuntimeException;

class CreditTransactionService implements TransactionProcessor
{
    /**
     * {@inheritdoc}
     */
    public function processTransaction(User $user, int $amount, string $description, ?string $reference = null): bool
    {
        try {
            return DB::transaction(function () use ($user, $amount, $description, $reference) {
                // Get current balance with lock
                $currentBalance = CreditTransaction::lockForUpdate()
                    ->where('user_id', $user->id)
                    ->latest('created_at')
                    ->value('running_balance') ?? 0;

                // Create credit transaction
                CreditTransaction::create([
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'type' => $amount > 0 ? CreditTransaction::TYPE_CREDIT : CreditTransaction::TYPE_DEBIT,
                    'description' => $description,
                    'reference' => $reference,
                    'running_balance' => $currentBalance + $amount
                ]);

                Log::info('Credit transaction processed', [
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'description' => $description,
                    'reference' => $reference,
                    'new_balance' => $currentBalance + $amount
                ]);

                return true;
            });
        } catch (\Exception $e) {
            Log::error('Failed to process credit transaction', [
                'user_id' => $user->id,
                'amount' => $amount,
                'description' => $description,
                'error' => $e->getMessage()
            ]);

            throw new RuntimeException('Failed to process credit transaction', 0, $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getBalance(User $user): int
    {
        try {
            return CreditTransaction::where('user_id', $user->id)
                ->latest('created_at')
                ->value('running_balance') ?? 0;
        } catch (\Exception $e) {
            Log::error('Failed to fetch credit balance', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            throw new RuntimeException('Failed to fetch credit balance', 0, $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getTransactionHistory(User $user, int $limit = 10): array
    {
        return CreditTransaction::where('user_id', $user->id)
            ->latest('created_at')
            ->take($limit)
            ->get()
            ->toArray();
    }
}
