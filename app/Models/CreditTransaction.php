<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class CreditTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'type',
        'description',
        'reference',
        'pack_id',
        'running_balance'
    ];

    public const TYPE_CREDIT = 'credit';
    public const TYPE_DEBIT = 'debit';

    protected static function booted()
    {
        static::creating(function ($transaction) {
            if ($transaction->amount <= 0) {
                throw new \InvalidArgumentException('Transaction amount must be positive');
            }

            // Calculate running balance
            $previousBalance = self::where('user_id', $transaction->user_id)
                ->orderByDesc('created_at')
                ->value('running_balance') ?? 0;

            $transaction->running_balance = $previousBalance + 
                ($transaction->type === self::TYPE_CREDIT 
                    ? $transaction->amount 
                    : -$transaction->amount);
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pack(): BelongsTo
    {
        return $this->belongsTo(Pack::class);
    }

    public static function latestBalance(int $userId): int
    {
        return self::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->value('running_balance') ?? 0;
    }

    public static function withBalanceChanges(int $userId, int $limit = 10): array
    {
        return self::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'amount' => $transaction->amount,
                    'type' => $transaction->type,
                    'description' => $transaction->description,
                    'created_at' => $transaction->created_at,
                    'running_balance' => $transaction->running_balance
                ];
            })
            ->toArray();
    }

    public static function getBalanceHistory(int $userId, int $limit = 10): array
    {
        return self::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'amount' => $transaction->amount,
                    'type' => $transaction->type,
                    'description' => $transaction->description,
                    'created_at' => $transaction->created_at,
                    'running_balance' => $transaction->running_balance
                ];
            })
            ->toArray();
    }

    public static function createTransaction(array $attributes): self
    {
        return DB::transaction(function () use ($attributes) {
            $transaction = new self($attributes);
            $transaction->save();
            return $transaction;
        });
    }
}
