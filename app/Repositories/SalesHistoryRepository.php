<?php

namespace App\Repositories;

use App\Contracts\Marketplace\SalesHistoryRepositoryInterface;
use App\Models\SalesHistory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class SalesHistoryRepository implements SalesHistoryRepositoryInterface
{
    public function create(array $data): SalesHistory
    {
        return DB::transaction(function() use ($data) {
            return SalesHistory::create($data);
        });
    }

    public function findByTransactionId(string $transactionId): ?SalesHistory
    {
        return SalesHistory::where('transaction_id', $transactionId)->first();
    }

    public function getSalesHistoryForUser(int $userId, array $filters = []): array
    {
        return $this->applyFilters(SalesHistory::where('user_id', $userId), $filters)
            ->orderBy('sale_date', 'desc')
            ->get()
            ->toArray();
    }

    public function getSalesHistoryForPack(int $packId, array $filters = []): array
    {
        return $this->applyFilters(SalesHistory::where('pack_id', $packId), $filters)
            ->orderBy('sale_date', 'desc')
            ->get()
            ->toArray();
    }

    private function applyFilters(Builder $query, array $filters): Builder
    {
        if (isset($filters['start_date'])) {
            $query->where('sale_date', '>=', $filters['start_date']);
        }

        if (isset($filters['end_date'])) {
            $query->where('sale_date', '<=', $filters['end_date']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query;
    }
}
