<?php

namespace App\Services;

use App\Models\CreditTransaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreditTransactionService
{
    public function createTransaction(array $attributes): CreditTransaction
    {
        return DB::transaction(function () use ($attributes) {
            return CreditTransaction::createTransaction($attributes);
        });
    }

    public function getCurrentBalance(int $userId): int
    {
        return CreditTransaction::latestBalance($userId);
    }

    public function getBalanceHistory(int $userId, int $limit = 10): array
    {
        return CreditTransaction::getBalanceHistory($userId, $limit);
    }

    public function getRecentTransactions(int $userId, int $limit = 10): array
    {
        return CreditTransaction::withBalanceChanges($userId, $limit);
    }

    public function validateTransactionAmount(int $amount): void
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Transaction amount must be positive');
        }
    }

    public function ensureSufficientBalance(User $user, int $amount): void
    {
        $balance = $this->getCurrentBalance($user->id);
        if ($balance < $amount) {
            throw new \RuntimeException('Insufficient balance');
        }
    }
}
