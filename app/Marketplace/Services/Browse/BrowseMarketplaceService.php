<?php

namespace App\Marketplace\Services\Browse;

use App\Models\Pack;
use App\Models\User;
use App\Models\CreditTransaction;
use App\Services\PulseService;
use Illuminate\Support\Facades\DB;

class BrowseMarketplaceService
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
            ->with([
                'cards' => function($query) {
                    $query->select('id', 'pack_id', 'image_url')
                        ->inRandomOrder()
                        ->limit(1);
                },
                'user:id,name'
            ])
            ->latest('listed_at')
            ->paginate(12)
            ->withQueryString();
    }

    public function purchasePack(Pack $pack, User $buyer): array
    {
        if (!$pack->canBePurchased() || $pack->user_id === $buyer->id) {
            return [
                'success' => false,
                'message' => 'Pack is not available for purchase'
            ];
        }

        return DB::transaction(function () use ($pack, $buyer) {
            // Set a shorter timeout for the transaction
            DB::statement('SET LOCAL statement_timeout = 10000'); // 10 seconds
            
            // Lock the pack for update
            $pack = Pack::where('id', $pack->id)->lockForUpdate()->first();
            if (!$pack || !$pack->canBePurchased()) {
                return [
                    'success' => false,
                    'message' => 'Pack is no longer available'
                ];
            }

            try {
                // Check buyer's balance using PostgreSQL running balance
                $buyerBalance = CreditTransaction::where('user_id', $buyer->id)
                    ->latest('created_at')
                    ->value('running_balance');
                    
                if (!$buyerBalance || $buyerBalance < $pack->price) {
                    return [
                        'success' => false,
                        'message' => 'Insufficient credits'
                    ];
                }

                // Create debit transaction for buyer
                CreditTransaction::create([
                    'user_id' => $buyer->id,
                    'amount' => $pack->price,
                    'type' => CreditTransaction::TYPE_DEBIT,
                    'description' => 'Purchase pack',
                    'pack_id' => $pack->id
                ]);

                // Create credit transaction for seller
                CreditTransaction::create([
                    'user_id' => $pack->user_id,
                    'amount' => $pack->price,
                    'type' => CreditTransaction::TYPE_CREDIT,
                    'description' => 'Sold pack',
                    'pack_id' => $pack->id
                ]);

                // Transfer ownership
                $pack->update([
                    'user_id' => $buyer->id,
                    'is_listed' => false,
                    'listed_at' => null,
                    'price' => null
                ]);

                return [
                    'success' => true,
                    'message' => 'Pack purchased successfully'
                ];
            } catch (\Exception $e) {
                \Log::error('Pack purchase failed', [
                    'pack_id' => $pack->id,
                    'buyer_id' => $buyer->id,
                    'error' => $e->getMessage()
                ]);
                return [
                    'success' => false,
                    'message' => 'An error occurred while processing your purchase. Please try again.'
                ];
            }
        });
    }
}
