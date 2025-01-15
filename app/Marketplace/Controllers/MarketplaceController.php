<?php

namespace App\Marketplace\Controllers;

use App\Http\Controllers\Controller;
use App\Marketplace\Services\MarketplaceService;
use App\Models\Pack;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MarketplaceController extends Controller
{
    protected $marketplaceService;

    public function __construct(MarketplaceService $marketplaceService)
    {
        $this->marketplaceService = $marketplaceService;
    }

    public function index(Request $request)
    {
        $tab = $request->get('tab', 'browse');
        
        $availablePacks = $this->marketplaceService->getAvailablePacks();
        $listedPacks = $this->marketplaceService->getListedPacks(Auth::user());
        $soldPacks = $this->marketplaceService->getSoldPacks(Auth::user());
        $purchasedPacks = $this->marketplaceService->getPurchasedPacks(Auth::user());
        
        return view('marketplace.index', compact(
            'tab',
            'availablePacks',
            'listedPacks',
            'soldPacks',
            'purchasedPacks'
        ));
    }

    public function listPack(Request $request, Pack $pack)
    {
        $this->authorize('listOnMarketplace', $pack);

        $validated = $request->validate([
            'price' => 'required|integer|min:1'
        ]);

        if (!$this->marketplaceService->listPack($pack, $validated['price'])) {
            return back()->with('error', 'Pack must be sealed before listing');
        }

        return back()->with('success', 'Pack listed on marketplace');
    }

    public function unlistPack(Pack $pack)
    {
        $this->authorize('removeFromMarketplace', $pack);

        if (!$this->marketplaceService->unlistPack($pack)) {
            return back()->with('error', 'Failed to remove pack from marketplace');
        }

        return back()->with('success', 'Pack removed from marketplace');
    }

    public function purchasePack(Pack $pack)
    {
        $this->authorize('purchase', $pack);

        $result = $this->marketplaceService->purchasePack($pack, Auth::user());

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return redirect()->route('packs.show', $pack)
            ->with('success', $result['message']);
    }
}
