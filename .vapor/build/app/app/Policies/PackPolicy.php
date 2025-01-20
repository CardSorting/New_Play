<?php

namespace App\Policies;

use App\Models\Pack;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PackPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Pack $pack): bool
    {
        // Allow viewing if:
        // 1. User owns the pack and it's not sealed
        // 2. Pack is listed on marketplace
        // 3. User owns the pack (even if sealed)
        return ($user->id === $pack->user_id && !$pack->is_sealed) ||
               $pack->is_listed ||
               $user->id === $pack->user_id;
    }

    /**
     * Determine whether the user can list the pack on marketplace.
     */
    public function listOnMarketplace(User $user, Pack $pack): bool
    {
        return $user->id === $pack->user_id && $pack->is_sealed && !$pack->is_listed;
    }

    /**
     * Determine whether the user can remove the pack from marketplace.
     */
    public function removeFromMarketplace(User $user, Pack $pack): bool
    {
        return $user->id === $pack->user_id && $pack->is_listed;
    }

    /**
     * Determine whether the user can purchase the pack.
     */
    public function purchase(User $user, Pack $pack): bool
    {
        return $pack->is_listed && $user->id !== $pack->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Pack $pack): bool
    {
        return $user->id === $pack->user_id && !$pack->is_sealed && !$pack->is_listed;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Pack $pack): bool
    {
        return $user->id === $pack->user_id && !$pack->is_sealed;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Pack $pack): bool
    {
        return $user->id === $pack->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Pack $pack): bool
    {
        return $user->id === $pack->user_id && !$pack->is_sealed;
    }
}
