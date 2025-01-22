<?php

namespace App\Contracts\Credits;

use App\Models\User;
use Carbon\Carbon;

interface TimeBasedValidation
{
    /**
     * Check if enough time has passed since last claim
     *
     * @param User $user
     * @return bool
     */
    public function hasEnoughTimePassed(User $user): bool;

    /**
     * Get the next available claim time
     *
     * @param User $user
     * @return Carbon|null
     */
    public function getNextClaimTime(User $user): ?Carbon;

    /**
     * Get the cooldown duration in hours
     *
     * @return int
     */
    public function getCooldownHours(): int;
}
