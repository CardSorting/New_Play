<?php

namespace App\Repositories;

use App\Contracts\Marketplace\PackRepositoryInterface;
use App\Models\Pack;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class PackRepository implements PackRepositoryInterface
{
    public function findAvailablePacks(): Collection
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

    public function findListedPacksByUser(User $user): Collection
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

    public function findSoldPacksByUser(User $user): Collection
    {
        return Pack::query()
            ->whereHas('creditTransactions', function($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->where('amount', '>', 0)
                    ->where('description', 'like', 'Sold pack #%');
            })
            ->with(['user', 'creditTransactions' => function($query) {
                $query->where('amount', '>', 0)
                    ->where('description', 'like', 'Sold pack #%');
            }])
            ->latest()
            ->get();
    }

    public function findPurchasedPacksByUser(User $user): Collection
    {
        return Pack::where('user_id', $user->id)
            ->whereHas('creditTransactions', function($query) use ($user) {
                $query->where('description', 'like', 'Purchase pack #%');
            })
            ->withCount('cards')
            ->with(['cards' => function($query) {
                $query->inRandomOrder()->limit(1);
            }])
            ->with(['user', 'creditTransactions' => function($query) {
                $query->where('description', 'like', 'Purchase pack #%');
            }])
            ->latest()
            ->get();
    }

    public function updatePackOwnership(Pack $pack, User $buyer): bool
    {
        return DB::transaction(function() use ($pack, $buyer) {
            return $pack->update([
                'user_id' => $buyer->id,
                'is_listed' => false,
                'listed_at' => null,
                'price' => null
            ]);
        });
    }

    public function listPackForSale(Pack $pack, int $price): bool
    {
        if (!$pack->canBeListed()) {
            return false;
        }

        return DB::transaction(function() use ($pack, $price) {
            return $pack->update([
                'is_listed' => true,
                'listed_at' => now(),
                'price' => $price
            ]);
        });
    }

    public function unlistPack(Pack $pack): bool
    {
        if (!$pack->is_listed) {
            return false;
        }

        return DB::transaction(function() use ($pack) {
            return $pack->update([
                'is_listed' => false,
                'listed_at' => null,
                'price' => null
            ]);
        });
    }
}
