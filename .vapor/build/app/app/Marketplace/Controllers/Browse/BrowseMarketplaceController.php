<?php

namespace App\Marketplace\Controllers\Browse;

use App\Http\Controllers\Controller;
use App\Marketplace\Services\Browse\BrowseMarketplaceService;
use App\Models\Pack;
use Illuminate\Support\Facades\Auth;

class BrowseMarketplaceController extends Controller
{
    protected $browseService;

    public function __construct(BrowseMarketplaceService $browseService)
    {
        $this->browseService = $browseService;
    }

    public function index()
    {
        $availablePacks = $this->browseService->getAvailablePacks();

        return view('marketplace.browse.index', [
            'availablePacks' => $availablePacks
        ]);
    }

    public function purchasePack(Pack $pack)
    {
        $this->authorize('purchase', $pack);

        $result = $this->browseService->purchasePack($pack, Auth::user());

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return redirect()->route('packs.show', $pack)
            ->with('success', $result['message']);
    }
}
