<?php

namespace App\Marketplace\Services\Seller;

use App\Contracts\Marketplace\SalesHistoryRepositoryInterface;
use App\Models\Pack;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SellerDashboardService
{
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
        return app(SalesHistoryRepositoryInterface::class)
            ->getSoldPacksForUser($user->id);
    }

    public function getAvailablePacks(User $user)
    {
        return Pack::where('user_id', $user->id)
            ->where('is_sealed', true)
            ->where('is_listed', false)
            ->whereNotNull('sealed_at')
            ->whereRaw('(SELECT COUNT(*) FROM galleries WHERE pack_id = packs.id AND is_in_pack = true) >= packs.card_limit')
            ->withCount('cards')
            ->with(['cards' => function($query) {
                $query->inRandomOrder()->limit(1);
            }])
            ->latest()
            ->get();
    }

    public function listPack(Pack $pack, int $price): array
    {
        // Delegate listing to the Pack model
        return $pack->list($price);
    }

    public function unlistPack(Pack $pack): bool
    {
        // Delegate unlisting to the Pack model
        return $pack->unlist();
    }

    public function getTotalSales(User $user): int
    {
        return app(SalesHistoryRepositoryInterface::class)
            ->getTotalSalesForUser($user->id);
    }

    public function getRecentSales(User $user, int $limit = 5)
    {
        return app(SalesHistoryRepositoryInterface::class)
            ->getRecentSalesForUser($user->id, $limit);
    }
}
