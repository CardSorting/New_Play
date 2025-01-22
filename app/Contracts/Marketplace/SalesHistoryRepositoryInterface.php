<?php

namespace App\Contracts\Marketplace;

use App\Models\SalesHistory;

interface SalesHistoryRepositoryInterface
{
    public function create(array $data): SalesHistory;
    
    public function findByTransactionId(string $transactionId): ?SalesHistory;
    
    public function getSalesHistoryForUser(int $userId, array $filters = []): array;
    
    public function getSalesHistoryForPack(int $packId, array $filters = []): array;
    
    public function getTotalSalesForUser(int $userId): int;
    
    public function getRecentSalesForUser(int $userId, int $limit = 5): \Illuminate\Database\Eloquent\Collection;
    
    public function getSoldPacksForUser(int $userId): array;
}
