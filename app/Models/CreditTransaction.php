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
            try {
                CreditTransactionRedisService::syncToRedis($transaction);
            } catch (\Exception $e) {
                \Log::error('Failed to sync credit transaction to Redis', [
                    'transaction_id' => $transaction->id,
                    'user_id' => $transaction->user_id,
                    'error' => $e->getMessage()
                ]);
                // Still allow the transaction to be created even if Redis sync fails
                // The fallback in latestBalance() will handle this case
            }
        });
        
        // Ensure Redis is synced for the user when retrieving transactions
        static::retrieved(function ($transaction) {
            try {
                if (!\Cache::has("user:{$transaction->user_id}:credit_sync")) {
                    CreditTransactionRedisService::syncFromDatabase($transaction->user_id);
                    // Cache this sync for 5 minutes to prevent redundant syncs
                    \Cache::put("user:{$transaction->user_id}:credit_sync", true, now()->addMinutes(5));
                }
            } catch (\Exception $e) {
                \Log::warning('Failed to sync credit transactions from database to Redis', [
                    'user_id' => $transaction->user_id,
                    'error' => $e->getMessage()
                ]);
            }
        });
    }

    public static function create(array $attributes): CreditTransaction
    {
        if ($attributes['amount'] <= 0) {
            throw new \InvalidArgumentException('Transaction amount must be positive');
        }

        try {
            return \DB::transaction(function () use ($attributes) {
                $transaction = parent::create($attributes);
                return $transaction;
            });
        } catch (\Exception $e) {
            \Log::error('Failed to create credit transaction', [
                'attributes' => $attributes,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
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
