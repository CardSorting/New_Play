<?php

namespace App\Contracts\Credits;

use App\Models\User;

interface CreditClaimable
{
    /**
     * Attempt to claim credits for a user
     *
     * @param User $user
     * @return bool
     */
    public function claim(User $user): bool;

    /**
     * Check if credits can be claimed
     *
     * @param User $user
     * @return bool
     */
    public function canClaim(User $user): bool;

    /**
     * Get the amount of credits that can be claimed
     *
     * @return int
     */
    public function getClaimAmount(): int;
}
