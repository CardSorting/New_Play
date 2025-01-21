<?php

namespace App\Services;

use App\Models\CreditTransaction;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;

class CreditTransactionRedisService
{
    public const TYPE_CREDIT = 'credit';
    public const TYPE_DEBIT = 'debit';

    private static function getBalanceKey(int $userId): string
    {
        return "user:{$userId}:credit_balance";
    }

    private static function getTransactionsKey(int $userId): string
    {
        return "user:{$userId}:credit_transactions";
    }

    private static function getBalanceFromRedis(int $userId)
    {
        try {
            $balance = Redis::get(self::getBalanceKey($userId));
            return $balance !== false ? (int) $balance : null;
        } catch (\Exception $e) {
            \Log::warning('Redis get balance failed', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    public static function latestBalance(int $userId): int
    {
        // Try Redis balance first (fastest)
        $balance = self::getBalanceFromRedis($userId);
        if ($balance !== null) {
            return $balance;
        }

        // Fallback to DB without locking
        $credits = CreditTransaction::where('user_id', $userId)
            ->where('type', self::TYPE_CREDIT)
            ->sum('amount');

        $debits = CreditTransaction::where('user_id', $userId)
            ->where('type', self::TYPE_DEBIT)
            ->sum('amount');

        $balance = $credits - $debits;
        
        // Cache the balance for future lookups
        try {
            Redis::set(self::getBalanceKey($userId), $balance);
        } catch (\Exception $e) {
            \Log::warning('Failed to cache balance in Redis', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
        }

        return $balance;
    }

    public static function updateBalance(int $userId, int $amount): void
    {
        try {
            Redis::incrby(self::getBalanceKey($userId), $amount);
        } catch (\Exception $e) {
            \Log::error('Failed to update Redis balance', [
                'user_id' => $userId,
                'amount' => $amount,
                'error' => $e->getMessage()
            ]);
        }
    }

    public static function syncToRedis(CreditTransaction $transaction): bool
    {
        try {
            // Update balance
            $balanceChange = $transaction->type === self::TYPE_CREDIT 
                ? $transaction->amount 
                : -$transaction->amount;
            self::updateBalance($transaction->user_id, $balanceChange);

            // Store transaction details
            $key = self::getTransactionsKey($transaction->user_id);
            $transactionData = [
                'user_id' => $transaction->user_id,
                'amount' => $transaction->amount,
                'type' => $transaction->type,
                'description' => $transaction->description,
                'reference' => $transaction->reference,
                'pack_id' => $transaction->pack_id,
                'created_at' => $transaction->created_at->toIso8601String(),
            ];

            Redis::rpush($key, json_encode($transactionData));
            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to sync transaction to Redis', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public static function syncFromDatabase(int $userId): void
    {
        try {
            $transactions = CreditTransaction::where('user_id', $userId)
                ->orderBy('created_at')
                ->get();

            $balance = 0;
            $transactionData = [];

            foreach ($transactions as $transaction) {
                $balance += $transaction->type === self::TYPE_CREDIT 
                    ? $transaction->amount 
                    : -$transaction->amount;

                $transactionData[] = json_encode([
                    'user_id' => $transaction->user_id,
                    'amount' => $transaction->amount,
                    'type' => $transaction->type,
                    'description' => $transaction->description,
                    'reference' => $transaction->reference,
                    'pack_id' => $transaction->pack_id,
                    'created_at' => $transaction->created_at->toIso8601String(),
                ]);
            }

            // Update Redis in a pipeline for better performance
            Redis::pipeline(function ($pipe) use ($userId, $balance, $transactionData) {
                $pipe->del([
                    self::getBalanceKey($userId),
                    self::getTransactionsKey($userId)
                ]);
                
                $pipe->set(self::getBalanceKey($userId), $balance);
                
                if (!empty($transactionData)) {
                    $pipe->rpush(self::getTransactionsKey($userId), ...$transactionData);
                }
            });
        } catch (\Exception $e) {
            \Log::error('Failed to sync transactions from database to Redis', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
        }
    }
}
