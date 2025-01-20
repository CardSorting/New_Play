<?php

namespace App\Models;

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
        'pack_id',
        'running_balance'
    ];

    protected $casts = [
        'amount' => 'integer',
        'running_balance' => 'integer',
        'pack_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public const TYPE_CREDIT = 'credit';
    public const TYPE_DEBIT = 'debit';

    protected static function booted()
    {
        static::creating(function (CreditTransaction $transaction) {
            if ($transaction->amount <= 0) {
                throw new \InvalidArgumentException('Transaction amount must be positive');
            }
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

    public function scopeLatestBalance($query, int $userId)
    {
        return $query->where('user_id', $userId)
                    ->latest()
                    ->select('running_balance')
                    ->first();
    }

    public function scopeWithBalanceChanges($query, int $userId, int $limit = 10)
    {
        return $query->where('user_id', $userId)
                    ->select([
                        '*',
                        \DB::raw('LAG(running_balance) OVER (ORDER BY created_at DESC, id DESC) as previous_balance')
                    ])
                    ->latest()
                    ->limit($limit)
                    ->get()
                    ->map(function ($transaction) {
                        $transaction->balance_change = $transaction->previous_balance
                            ? $transaction->running_balance - $transaction->previous_balance
                            : ($transaction->type === self::TYPE_CREDIT ? $transaction->amount : -$transaction->amount);
                        unset($transaction->previous_balance);
                        return $transaction;
                    });
    }
}
