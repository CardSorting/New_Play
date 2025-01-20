<?php

namespace App\Models;

use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;

class CreditTransaction
{
    protected $fillable = [
        'user_id',
        'amount',
        'type',
        'description',
        'reference',
        'pack_id',
        'running_balance',
        'created_at'
    ];

    public const TYPE_CREDIT = 'credit';
    public const TYPE_DEBIT = 'debit';

    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            $this->{$key} = $value;
        }
        if (!isset($this->created_at)) {
            $this->created_at = now();
        }
    }

    public static function create(array $attributes): CreditTransaction
    {
        if ($attributes['amount'] <= 0) {
            throw new \InvalidArgumentException('Transaction amount must be positive');
        }
        $transaction = new self($attributes);
        $transaction->save();
        return $transaction;
    }

    public function save(): bool
    {
        $key = $this->getRedisKey($this->user_id);
        $transactions = self::getTransactionsFromRedis($this->user_id);
        $transactions[] = $this->toArray();
        Redis::set($key, json_encode($transactions));
        return true;
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

    public function toArray(): array
    {
        return [
            'user_id' => $this->user_id,
            'amount' => $this->amount,
            'type' => $this->type,
            'description' => $this->description,
            'reference' => $this->reference,
            'pack_id' => $this->pack_id,
            'running_balance' => $this->running_balance,
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
