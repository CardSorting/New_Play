<?php

namespace App\Services\Credits;

use App\Models\User;
use App\Contracts\Credits\TimeBasedValidation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PulseClaimValidationService implements TimeBasedValidation
{
    /**
     * The cooldown period in hours between claims
     */
    private const COOLDOWN_HOURS = 24;

    /**
     * {@inheritdoc}
     */
    public function hasEnoughTimePassed(User $user): bool
    {
        if (!$user->last_pulse_claim) {
            return true;
        }

        $lastClaim = Carbon::parse($user->last_pulse_claim);
        $hoursSinceLastClaim = $lastClaim->diffInHours(now());

        Log::info('Checking pulse claim cooldown', [
            'user_id' => $user->id,
            'hours_since_last_claim' => $hoursSinceLastClaim,
            'cooldown_hours' => self::COOLDOWN_HOURS,
        ]);

        return $hoursSinceLastClaim >= self::COOLDOWN_HOURS;
    }

    /**
     * {@inheritdoc}
     */
    public function getNextClaimTime(User $user): ?Carbon
    {
        if (!$user->last_pulse_claim) {
            return null;
        }

        return Carbon::parse($user->last_pulse_claim)->addHours(self::COOLDOWN_HOURS);
    }

    /**
     * {@inheritdoc}
     */
    public function getCooldownHours(): int
    {
        return self::COOLDOWN_HOURS;
    }
}
