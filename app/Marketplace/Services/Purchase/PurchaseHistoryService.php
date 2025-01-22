<?php

namespace App\Marketplace\Services\Purchase;

use App\Contracts\Marketplace\PurchaseHistoryRepositoryInterface;
use App\Models\Pack;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PurchaseHistoryService
{
    public function getPurchasedPacks(User $user)
    {
        return app(PurchaseHistoryRepositoryInterface::class)
            ->getPurchasedPacksForUser($user);
    }

    public function getTotalSpent(User $user): int
    {
        return app(PurchaseHistoryRepositoryInterface::class)
            ->getTotalSpentForUser($user);
    }

    public function getRecentPurchases(User $user, int $limit = 5)
    {
        return app(PurchaseHistoryRepositoryInterface::class)
            ->getRecentPurchasesForUser($user, $limit);
    }

    public function getPurchaseDetails(Pack $pack, User $user)
    {
        return app(PurchaseHistoryRepositoryInterface::class)
            ->getPurchaseDetailsForPack($pack, $user);
    }
}
