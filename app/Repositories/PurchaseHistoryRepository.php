<?php

namespace App\Repositories;

use App\Contracts\Marketplace\PurchaseHistoryRepositoryInterface;
use App\Models\PurchaseHistory;
use Illuminate\Support\Facades\DB;

class PurchaseHistoryRepository implements PurchaseHistoryRepositoryInterface
{
    public function create(array $data): PurchaseHistory
    {
        return PurchaseHistory::create($data);
    }

    public function getPurchaseHistoryForUser(int $userId, int $limit = 10): array
    {
        return PurchaseHistory::with('pack')
            ->where('buyer_id', $userId)
            ->orderBy('purchased_at', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }
}
