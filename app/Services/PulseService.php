<?php

namespace App\Services;

use App\Models\CreditTransaction;
use App\Models\User;
use Illuminate\Support\Facades\{DB, Log};

class PulseService
{
    public function getCreditBalance(User $user): int
    {
        return $this->calculateBalanceFromDb($user);
    }

    public function addCredits(User $user, int $amount, ?string $description = null, ?string $reference = null, ?int $pack_id = null): void
    {
        Log::info('Starting addCredits process', [
            'user_id' => $user->id,
            'amount' => $amount,
            'description' => $description,
            'reference' => $reference
        ]);

        if ($amount <= 0) {
            Log::error('Invalid credit amount', ['amount' => $amount]);
            throw new \InvalidArgumentException('Credit amount must be positive');
        }

        try {
            DB::beginTransaction();

            Log::info('Creating credit transaction', [
                'user_id' => $user->id,
                'amount' => $amount,
                'type' => 'credit'
            ]);

            $transaction = CreditTransaction::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'type' => 'credit',
                'description' => $description,
                'reference' => $reference,
                'pack_id' => $pack_id
            ]);

            if (!$transaction) {
                Log::error('Transaction creation returned null');
                DB::rollBack();
                throw new \Exception('Failed to create credit transaction');
            }

            Log::info('Credit transaction created successfully', [
                'transaction_id' => $transaction->id,
                'user_id' => $user->id,
                'amount' => $amount
            ]);

            DB::commit();
            Log::info('Transaction committed successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create credit transaction', [
                'user_id' => $user->id,
                'amount' => $amount,
                'description' => $description,
                'reference' => $reference,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function deductCredits(User $user, int $amount, ?string $description = null, ?string $reference = null, ?int $pack_id = null): bool
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Debit amount must be positive');
        }

        try {
            DB::beginTransaction();

            // Lock the user's transactions for balance calculation
            $credits = CreditTransaction::where('user_id', $user->id)
                ->where('type', 'credit')
                ->lockForUpdate()
                ->sum('amount');

            $debits = CreditTransaction::where('user_id', $user->id)
                ->where('type', 'debit')
                ->lockForUpdate()
                ->sum('amount');

            $currentBalance = $credits - $debits;

            if ($currentBalance < $amount) {
                DB::rollBack();
                return false;
            }

            Log::info('Creating debit transaction', [
                'user_id' => $user->id,
                'amount' => $amount,
                'type' => 'debit'
            ]);

            $transaction = CreditTransaction::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'type' => 'debit',
                'description' => $description,
                'reference' => $reference,
                'pack_id' => $pack_id
            ]);

            if (!$transaction) {
                Log::error('Debit transaction creation returned null');
                DB::rollBack();
                throw new \Exception('Failed to create debit transaction');
            }

            Log::info('Debit transaction created successfully', [
                'transaction_id' => $transaction->id,
                'user_id' => $user->id,
                'amount' => $amount
            ]);

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create debit transaction', [
                'user_id' => $user->id,
                'amount' => $amount,
                'description' => $description,
                'reference' => $reference,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function getTransactionHistory(User $user, int $limit = 10)
    {
        return CreditTransaction::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    private function calculateBalanceFromDb(User $user): int
    {
        return DB::transaction(function () use ($user) {
            $credits = CreditTransaction::where('user_id', $user->id)
                ->where('type', 'credit')
                ->lockForUpdate()
                ->sum('amount');

            $debits = CreditTransaction::where('user_id', $user->id)
                ->where('type', 'debit')
                ->lockForUpdate()
                ->sum('amount');

            return $credits - $debits;
        });
    }
}
