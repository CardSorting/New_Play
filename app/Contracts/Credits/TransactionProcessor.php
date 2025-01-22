<?php

namespace App\Contracts\Credits;

use App\Models\User;

interface TransactionProcessor
{
    /**
     * Process a credit transaction
     *
     * @param User $user
     * @param int $amount
     * @param string $description
     * @param string|null $reference
     * @return bool
     */
    public function processTransaction(User $user, int $amount, string $description, ?string $reference = null): bool;

    /**
     * Get current credit balance for a user
     *
     * @param User $user
     * @return int
     */
    public function getBalance(User $user): int;

    /**
     * Get transaction history for a user
     *
     * @param User $user
     * @param int $limit
     * @return array
     */
    public function getTransactionHistory(User $user, int $limit = 10): array;
}
