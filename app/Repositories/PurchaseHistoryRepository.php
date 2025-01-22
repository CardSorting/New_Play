<?php

namespace App\Repositories;

use App\Contracts\Marketplace\PurchaseHistoryRepositoryInterface;
use App\Models\PurchaseHistory;
use App\Models\User;
use App\Models\Pack;
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

    public function getPurchasedPacksForUser(User $user): array
    {
        return PurchaseHistory::with('pack')
            ->where('buyer_id', $user->id)
            ->orderBy('purchased_at', 'desc')
            ->get()
            ->pluck('pack')
            ->toArray();
    }

    public function getTotalSpentForUser(User $user): int
    {
        return PurchaseHistory::where('buyer_id', $user->id)
            ->sum('price');
    }

    public function getRecentPurchasesForUser(User $user, int $limit = 5): array
    {
        return PurchaseHistory::with('pack')
            ->where('buyer_id', $user->id)
            ->orderBy('purchased_at', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    public function getPurchaseDetailsForPack(Pack $pack, User $user): ?PurchaseHistory
    {
        return PurchaseHistory::with('pack')
            ->where('buyer_id', $user->id)
            ->where('pack_id', $pack->id)
            ->first();
    }
}
