<?php

namespace App\Services\Credits;

use App\Contracts\Credits\{CreditClaimable, TimeBasedValidation, TransactionProcessor};
use App\DTOs\Credits\PulseClaimData;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\{DB, Log};

class DailyPulseService implements CreditClaimable
{
    /**
     * The amount of credits to award for daily pulse
     */
    private const DAILY_PULSE_AMOUNT = 500;

    public function __construct(
        private readonly TimeBasedValidation $validator,
        private readonly TransactionProcessor $transactionProcessor
    ) {}

    /**
     * {@inheritdoc}
     */
    public function claim(User $user): bool
    {
        try {
            return DB::transaction(function () use ($user) {
                // Check if user is eligible for claim
                if (!$this->canClaim($user)) {
                    Log::info('Daily pulse claim rejected - too soon', [
                        'user_id' => $user->id,
                    ]);
                    return false;
                }

                // Update last claim time atomically
                $updated = User::where('id', $user->id)
                    ->where(function ($query) {
                        $query->whereNull('last_pulse_claim')
                            ->orWhere('last_pulse_claim', '<=', now()->subHours($this->validator->getCooldownHours()));
                    })
                    ->update(['last_pulse_claim' => now()]);

                if (!$updated) {
                    return false;
                }

                // Process the credit transaction
                $this->transactionProcessor->processTransaction(
                    user: $user,
                    amount: self::DAILY_PULSE_AMOUNT,
                    description: 'Daily Pulse Claim',
                    reference: 'pulse_' . now()->format('Y-m-d')
                );

                Log::info('Daily pulse claimed successfully', [
                    'user_id' => $user->id,
                    'amount' => self::DAILY_PULSE_AMOUNT
                ]);

                return true;
            });
        } catch (\Exception $e) {
            Log::error('Failed to claim daily pulse', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function canClaim(User $user): bool
    {
        return $this->validator->hasEnoughTimePassed($user);
    }

    /**
     * {@inheritdoc}
     */
    public function getClaimAmount(): int
    {
        return self::DAILY_PULSE_AMOUNT;
    }

    /**
     * Get pulse claim data for a user
     *
     * @param User $user
     * @return PulseClaimData
     */
    public function getPulseClaimData(User $user): PulseClaimData
    {
        return new PulseClaimData(
            amount: self::DAILY_PULSE_AMOUNT,
            lastClaimTime: $user->last_pulse_claim ? Carbon::parse($user->last_pulse_claim) : null,
            canClaim: $this->canClaim($user),
            nextClaimTime: $this->validator->getNextClaimTime($user),
            reference: 'pulse_' . now()->format('Y-m-d')
        );
    }

    /**
     * Get user's current credit balance
     *
     * @param User $user
     * @return int
     */
    public function getCreditBalance(User $user): int
    {
        return $this->transactionProcessor->getBalance($user);
    }
}
