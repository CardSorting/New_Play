<?php

namespace App\Marketplace\Controllers\Purchase;

use App\Http\Controllers\Controller;
use App\Marketplace\Services\Purchase\PurchaseHistoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseHistoryController extends Controller
{
    protected $purchaseService;

    public function __construct(PurchaseHistoryService $purchaseService)
    {
        $this->purchaseService = $purchaseService;
    }

    public function index()
    {
        $purchasedPacks = $this->purchaseService->getPurchasedPacks(Auth::user());
        $totalSpent = $this->purchaseService->getTotalSpent(Auth::user());
        $recentPurchases = $this->purchaseService->getRecentPurchases(Auth::user());

        return view('marketplace.purchase.history', compact(
            'purchasedPacks',
            'totalSpent',
            'recentPurchases'
        ));
    }
}
