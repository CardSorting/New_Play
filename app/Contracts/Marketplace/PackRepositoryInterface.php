<?php

namespace App\Contracts\Marketplace;

use App\Models\Pack;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface PackRepositoryInterface
{
    public function findAvailablePacks(): Collection;
    public function findListedPacksByUser(User $user): Collection;
    public function findSoldPacksByUser(User $user): Collection;
    public function findPurchasedPacksByUser(User $user): Collection;
    public function updatePackOwnership(Pack $pack, User $buyer): bool;
    public function listPackForSale(Pack $pack, int $price): bool;
    public function unlistPack(Pack $pack): bool;
}
