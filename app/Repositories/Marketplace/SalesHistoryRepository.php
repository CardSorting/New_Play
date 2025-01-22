<?php

namespace App\Repositories\Marketplace;

use App\Contracts\Marketplace\SalesHistoryRepositoryInterface;
use App\Models\SalesHistory;
use Illuminate\Support\Facades\DB;

class SalesHistoryRepository implements SalesHistoryRepositoryInterface
{
    public function create(array $data): SalesHistory
    {
        return SalesHistory::create($data);
    }

    public function findByTransactionId(string $transactionId): ?SalesHistory
    {
        return SalesHistory::where('transaction_id', $transactionId)->first();
    }

    public function getSalesHistoryForUser(int $userId, array $filters = []): array
    {
        $query = SalesHistory::where('user_id', $userId);
        
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        return $query->orderBy('created_at', 'desc')->get()->toArray();
    }

    public function getSalesHistoryForPack(int $packId, array $filters = []): array
    {
        $query = SalesHistory::where('pack_id', $packId);
        
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        return $query->orderBy('created_at', 'desc')->get()->toArray();
    }

    public function getTotalSalesForUser(int $userId): int
    {
        return SalesHistory::where('user_id', $userId)
            ->where('status', 'completed')
            ->count();
    }

    public function getRecentSalesForUser(int $userId, int $limit = 5): \Illuminate\Database\Eloquent\Collection
    {
        return SalesHistory::where('user_id', $userId)
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getSoldPacksForUser(int $userId): array
    {
        return SalesHistory::where('user_id', $userId)
            ->where('status', 'completed')
            ->with('pack')
            ->get()
            ->pluck('pack')
            ->unique()
            ->values()
            ->toArray();
    }
}
