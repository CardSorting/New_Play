<?php

namespace App\Marketplace\Services\Purchase;

use App\Models\Pack;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PurchaseHistoryService
{
    public function getPurchasedPacks(User $user)
    {
        return Pack::where('user_id', $user->id)
            ->whereIn('id', function($query) use ($user) {
                $query->select('pack_id')
                    ->from('credit_transactions')
                    ->where('user_id', $user->id)
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

    public function getTotalSpent(User $user): int
    {
        return DB::table('credit_transactions')
            ->where('user_id', $user->id)
            ->where('description', 'like', 'Purchase pack #%')
            ->sum('amount');
    }

    public function getRecentPurchases(User $user, int $limit = 5)
    {
        return DB::table('credit_transactions')
            ->where('user_id', $user->id)
            ->where('description', 'like', 'Purchase pack #%')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getPurchaseDetails(Pack $pack, User $user)
    {
        return DB::table('credit_transactions')
            ->where('user_id', $user->id)
            ->where('description', 'like', 'Purchase pack #' . $pack->id)
            ->first();
    }
}
