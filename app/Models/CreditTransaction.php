<?php

namespace App\Models;

use App\Services\CreditTransactionRedisService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreditTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'type',
        'description',
        'reference',
        'pack_id'
    ];

    public const TYPE_CREDIT = 'credit';
    public const TYPE_DEBIT = 'debit';

    protected static function booted()
    {
        static::created(function ($transaction) {
            CreditTransactionRedisService::syncToRedis($transaction);
        });
    }

    public static function create(array $attributes): CreditTransaction
    {
        if ($attributes['amount'] <= 0) {
            throw new \InvalidArgumentException('Transaction amount must be positive');
        }
        return parent::create($attributes);
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
        return CreditTransactionRedisService::latestBalance($userId);
    }

    public static function withBalanceChanges(int $userId, int $limit = 10): array
    {
        return CreditTransactionRedisService::withBalanceChanges($userId, $limit);
    }

    public static function syncRedisCache(int $userId): void
    {
        CreditTransactionRedisService::syncFromDatabase($userId);
    }
}
