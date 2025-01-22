<?php

namespace App\Contracts\Marketplace;

use App\Models\Pack;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface MarketplaceServiceInterface
{
    public function getAvailablePacks(): Collection;
    public function getListedPacks(User $user): Collection;
    public function getSoldPacks(User $user): Collection;
    public function getPurchasedPacks(User $user): Collection;
    public function listPack(Pack $pack, int $price): bool;
    public function unlistPack(Pack $pack): bool;
    public function purchasePack(Pack $pack, User $buyer): array;
}
