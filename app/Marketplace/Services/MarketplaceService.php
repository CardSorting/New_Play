<?php

namespace App\Marketplace\Services;

use App\Contracts\Marketplace\MarketplaceServiceInterface;
use App\Contracts\Marketplace\PackRepositoryInterface;
use App\Contracts\Marketplace\PurchaseHistoryRepositoryInterface;
use App\Contracts\Marketplace\SalesHistoryRepositoryInterface;
use App\DTOs\Marketplace\PackTransactionDTO;
use App\Exceptions\InsufficientCreditsException;
use App\Models\Pack;
use App\Models\User;
use App\Services\PulseService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MarketplaceService implements MarketplaceServiceInterface
{
    public function __construct(
        private readonly PackRepositoryInterface $packRepository,
        private readonly PulseService $pulseService,
        private readonly SalesHistoryRepositoryInterface $salesHistoryRepository,
        private readonly PurchaseHistoryRepositoryInterface $purchaseHistoryRepository
    ) {}

    public function getAvailablePacks(): Collection
    {
        return $this->packRepository->findAvailablePacks();
    }

    public function getListedPacks(User $user): Collection
    {
        return $this->packRepository->findListedPacksByUser($user);
    }

    public function getSoldPacks(User $user): Collection
    {
        return $this->packRepository->findSoldPacksByUser($user);
    }

    public function getPurchasedPacks(User $user): Collection
    {
        return $this->packRepository->findPurchasedPacksByUser($user);
    }

    public function listPack(Pack $pack, int $price): bool
    {
        if (!$pack->is_sealed) {
            Log::warning('Attempted to list unsealed pack', [
                'pack_id' => $pack->id,
                'user_id' => $pack->user_id
            ]);
            return false;
        }

        return $this->packRepository->listPackForSale($pack, $price);
    }

    public function unlistPack(Pack $pack): bool
    {
        if (!$pack->is_listed) {
            Log::warning('Attempted to unlist non-listed pack', [
                'pack_id' => $pack->id,
                'user_id' => $pack->user_id
            ]);
            return false;
        }

        return $this->packRepository->unlistPack($pack);
    }

    public function purchasePack(Pack $pack, User $buyer): array
    {
        if (!$pack->canBePurchased() || $pack->user_id === $buyer->id) {
            Log::warning('Invalid pack purchase attempt', [
                'pack_id' => $pack->id,
                'buyer_id' => $buyer->id,
                'seller_id' => $pack->user_id
            ]);
            return [
                'success' => false,
                'message' => 'Pack is not available for purchase'
            ];
        }

        try {
            DB::beginTransaction();

            // Create transaction DTOs
            $purchaseTransaction = PackTransactionDTO::fromPurchase($pack, $buyer);
            $saleTransaction = PackTransactionDTO::fromSale($pack, $buyer);

            // Process buyer's payment
            if (!$this->pulseService->deductCredits(
                $buyer, 
                $purchaseTransaction->price, 
                $purchaseTransaction->description,
                ['pack_id' => $pack->id]
            )) {
                throw new InsufficientCreditsException();
            }

            // Credit the seller
            $this->pulseService->addCredits(
                User::findOrFail($saleTransaction->sellerId),
                $saleTransaction->price,
                $saleTransaction->description,
                ['pack_id' => $pack->id]
            );

            // Transfer ownership
            $this->packRepository->updatePackOwnership($pack, $buyer);

            // Record sales history
            $this->salesHistoryRepository->create([
                'pack_id' => $pack->id,
                'seller_id' => $pack->user_id,
                'buyer_id' => $buyer->id,
                'price' => $pack->price,
                'sold_at' => now()
            ]);

            // Record purchase history
            $this->purchaseHistoryRepository->create([
                'pack_id' => $pack->id,
                'buyer_id' => $buyer->id,
                'price' => $pack->price,
                'purchased_at' => now()
            ]);

            DB::commit();

            Log::info('Pack purchase successful', [
                'pack_id' => $pack->id,
                'buyer_id' => $buyer->id,
                'seller_id' => $pack->user_id,
                'price' => $pack->price
            ]);

            return [
                'success' => true,
                'message' => 'Pack purchased successfully'
            ];

        } catch (InsufficientCreditsException $e) {
            DB::rollBack();
            Log::warning('Insufficient credits for pack purchase', [
                'pack_id' => $pack->id,
                'buyer_id' => $buyer->id,
                'price' => $pack->price
            ]);
            return [
                'success' => false,
                'message' => 'Insufficient credits'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Pack purchase failed', [
                'error' => $e->getMessage(),
                'pack_id' => $pack->id,
                'buyer_id' => $buyer->id
            ]);

            // Refund the buyer if needed
            try {
                $this->pulseService->addCredits(
                    $buyer,
                    $pack->price,
                    'Refund for failed purchase of pack #' . $pack->id,
                    ['pack_id' => $pack->id, 'refund' => true]
                );
            } catch (\Exception $refundError) {
                Log::error('Refund failed after purchase failure', [
                    'error' => $refundError->getMessage(),
                    'original_error' => $e->getMessage(),
                    'pack_id' => $pack->id,
                    'buyer_id' => $buyer->id
                ]);
            }

            return [
                'success' => false,
                'message' => 'Failed to process purchase'
            ];
        }
    }
}
