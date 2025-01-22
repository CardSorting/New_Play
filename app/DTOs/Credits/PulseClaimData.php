<?php

namespace App\DTOs\Credits;

use Carbon\Carbon;

class PulseClaimData
{
    /**
     * @param int $amount The amount of credits to claim
     * @param Carbon|null $lastClaimTime The last time credits were claimed
     * @param bool $canClaim Whether credits can be claimed
     * @param Carbon|null $nextClaimTime The next time credits can be claimed
     * @param string|null $reference Optional reference for the transaction
     */
    public function __construct(
        public readonly int $amount,
        public readonly ?Carbon $lastClaimTime,
        public readonly bool $canClaim,
        public readonly ?Carbon $nextClaimTime,
        public readonly ?string $reference = null,
    ) {}

    /**
     * Create a new instance with updated claim time
     *
     * @return self
     */
    public function withUpdatedClaimTime(): self
    {
        return new self(
            amount: $this->amount,
            lastClaimTime: now(),
            canClaim: false,
            nextClaimTime: now()->addHours(24),
            reference: $this->reference
        );
    }

    /**
     * Format the next claim time as a string
     *
     * @return string|null
     */
    public function getNextClaimTimeString(): ?string
    {
        return $this->nextClaimTime?->format('Y-m-d H:i:s');
    }
}
