<?php

namespace App\Services;

use App\Models\CreditTransaction;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;

class CreditTransactionRedisService
{
    public const TYPE_CREDIT = 'credit';
    public const TYPE_DEBIT = 'debit';

    private static function getRedisKey(int $userId): string
    {
        return "user:{$userId}:credit_transactions";
    }

    private static function getTransactionsFromRedis(int $userId): array
    {
        $key = self::getRedisKey($userId);
        $transactions = Redis::get($key);
        return $transactions ? json_decode($transactions, true) : [];
    }

    public static function latestBalance(int $userId): int
    {
        $transactions = self::getTransactionsFromRedis($userId);
        if (empty($transactions)) {
            return 0;
        }
        $lastTransaction = end($transactions);
        return $lastTransaction['running_balance'] ?? 0;
    }

    public static function withBalanceChanges(int $userId, int $limit = 10): array
    {
        $transactions = self::getTransactionsFromRedis($userId);
        $transactions = array_reverse($transactions);
        $limitedTransactions = array_slice($transactions, 0, $limit);
        $result = [];
        $previousBalance = null;
        foreach ($limitedTransactions as $transaction) {
            $transaction = (object) $transaction;
            $transaction->balance_change = $previousBalance !== null
                ? $transaction->running_balance - $previousBalance
                : ($transaction->type === self::TYPE_CREDIT ? $transaction->amount : -$transaction->amount);
            $previousBalance = $transaction->running_balance;
            $result[] = $transaction;
        }
        return $result;
    }

    public static function syncToRedis(CreditTransaction $transaction): bool
    {
        $key = self::getRedisKey($transaction->user_id);
        $transactions = self::getTransactionsFromRedis($transaction->user_id);
        
        // Calculate running balance
        $runningBalance = empty($transactions) ? 0 : end($transactions)['running_balance'];
        $balanceChange = $transaction->type === self::TYPE_CREDIT ? $transaction->amount : -$transaction->amount;
        $runningBalance += $balanceChange;

        $transactions[] = [
            'user_id' => $transaction->user_id,
            'amount' => $transaction->amount,
            'type' => $transaction->type,
            'description' => $transaction->description,
            'reference' => $transaction->reference,
            'pack_id' => $transaction->pack_id,
            'running_balance' => $runningBalance,
            'created_at' => $transaction->created_at->toIso8601String(),
        ];

        return Redis::set($key, json_encode($transactions));
    }

    public static function syncFromDatabase(int $userId): void
    {
        $transactions = CreditTransaction::where('user_id', $userId)
            ->orderBy('created_at')
            ->get();

        $redisTransactions = [];
        $runningBalance = 0;

        foreach ($transactions as $transaction) {
            $balanceChange = $transaction->type === self::TYPE_CREDIT ? $transaction->amount : -$transaction->amount;
            $runningBalance += $balanceChange;

            $redisTransactions[] = [
                'user_id' => $transaction->user_id,
                'amount' => $transaction->amount,
                'type' => $transaction->type,
                'description' => $transaction->description,
                'reference' => $transaction->reference,
                'pack_id' => $transaction->pack_id,
                'running_balance' => $runningBalance,
                'created_at' => $transaction->created_at->toIso8601String(),
            ];
        }

        $key = self::getRedisKey($userId);
        Redis::set($key, json_encode($redisTransactions));
    }
}
