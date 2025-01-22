<?php

namespace App\Contracts\Marketplace;

use App\Models\PurchaseHistory;

interface PurchaseHistoryRepositoryInterface
{
    public function create(array $data): PurchaseHistory;
    
    public function getPurchaseHistoryForUser(int $userId, int $limit = 10): array;
}
