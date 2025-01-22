<?php

namespace App\Contracts\Marketplace;

use App\Models\PurchaseHistory;
use App\Models\User;
use App\Models\Pack;

interface PurchaseHistoryRepositoryInterface
{
    public function create(array $data): PurchaseHistory;
    
    public function getPurchaseHistoryForUser(int $userId, int $limit = 10): array;
    
    public function getPurchasedPacksForUser(User $user): array;
    
    public function getTotalSpentForUser(User $user): int;
    
    public function getRecentPurchasesForUser(User $user, int $limit = 5): array;
    
    public function getPurchaseDetailsForPack(Pack $pack, User $user): ?PurchaseHistory;
}
