<?php

namespace App\Contracts\Marketplace;

use App\Models\SalesHistory;

interface SalesHistoryRepositoryInterface
{
    public function create(array $data): SalesHistory;
    
    public function findByTransactionId(string $transactionId): ?SalesHistory;
    
    public function getSalesHistoryForUser(int $userId, array $filters = []): array;
    
    public function getSalesHistoryForPack(int $packId, array $filters = []): array;
}
