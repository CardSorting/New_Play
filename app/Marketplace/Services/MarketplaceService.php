<?php

namespace App\Marketplace\Services;

use App\Models\Pack;
use App\Models\User;
use App\Services\PulseService;
use Illuminate\Support\Facades\DB;

class MarketplaceService
{
    protected $pulseService;

    public function __construct(PulseService $pulseService)
    {
        $this->pulseService = $pulseService;
    }

    public function getAvailablePacks()
    {
        return Pack::availableOnMarketplace()
            ->withCount('cards')
            ->with(['cards' => function($query) {
                $query->inRandomOrder()->limit(1);
            }])
            ->with('user')
            ->latest('listed_at')
            ->get();
    }

    public function getListedPacks(User $user)
    {
        return Pack::where('user_id', $user->id)
            ->where('is_listed', true)
            ->withCount('cards')
            ->with(['cards' => function($query) {
                $query->inRandomOrder()->limit(1);
            }])
            ->latest('listed_at')
            ->get();
    }

    public function getSoldPacks(User $user)
    {
        return Pack::whereIn('id', function($query) use ($user) {
                $query->select('pack_id')
                    ->from('credit_transactions')
                    ->where('user_id', $user->id)
                    ->where('description', 'like', 'Sold pack #%');
            })
            ->with('user')
            ->latest()
            ->get();
    }

    public function getPurchasedPacks(User $user)
    {
        return Pack::where('user_id', $user->id)
            ->whereIn('id', function($query) {
                $query->select('pack_id')
                    ->from('credit_transactions')
                    ->where('description', 'like', 'Purchase pack #%');
            })
            ->withCount('cards')
            ->with(['cards' => function($query) {
                $query->inRandomOrder()->limit(1);
            }])
            ->with('user')
            ->latest()
            ->get();
    }

    public function listPack(Pack $pack, int $price): bool
    {
        if (!$pack->is_sealed) {
            return false;
        }

        $pack->listOnMarketplace($price);
        return true;
    }

    public function unlistPack(Pack $pack): bool
    {
        if (!$pack->is_listed) {
            return false;
        }

        $pack->removeFromMarketplace();
        return true;
    }

    public function purchasePack(Pack $pack, User $buyer): array
    {
        if (!$pack->canBePurchased() || $pack->user_id === $buyer->id) {
            return [
                'success' => false,
                'message' => 'Pack is not available for purchase'
            ];
        }

        if (!$this->pulseService->deductCredits($buyer, $pack->price, 'Purchase pack #' . $pack->id)) {
            return [
                'success' => false,
                'message' => 'Insufficient credits'
            ];
        }

        try {
            DB::beginTransaction();

            // Credit the seller
            $this->pulseService->addCredits(
                $pack->user, 
                $pack->price, 
                'Sold pack #' . $pack->id
            );

            // Transfer ownership
            $pack->update([
                'user_id' => $buyer->id,
                'is_listed' => false,
                'listed_at' => null
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Pack purchased successfully'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Refund the buyer
            $this->pulseService->addCredits(
                $buyer, 
                $pack->price, 
                'Refund for failed purchase of pack #' . $pack->id
            );

            return [
                'success' => false,
                'message' => 'Failed to process purchase'
            ];
        }
    }
}
