<?php

namespace App\Marketplace\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Marketplace\Services\Seller\SellerDashboardService;
use App\Models\Pack;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SellerDashboardController extends Controller
{
    protected $sellerService;

    public function __construct(SellerDashboardService $sellerService)
    {
        $this->sellerService = $sellerService;
    }

    public function index()
    {
        $listedPacks = $this->sellerService->getListedPacks(Auth::user());
        $availablePacks = $this->sellerService->getAvailablePacks(Auth::user());
        $totalSales = $this->sellerService->getTotalSales(Auth::user());
        $recentSales = $this->sellerService->getRecentSales(Auth::user());

        return view('marketplace.seller.dashboard', compact(
            'listedPacks',
            'availablePacks',
            'totalSales',
            'recentSales'
        ));
    }

    public function listPack(Request $request, Pack $pack)
    {
        try {
            $this->authorize('listOnMarketplace', $pack);

            $validated = $request->validate([
                'price' => 'required|integer|min:1|max:1000000'
            ], [
                'price.required' => 'Please enter a price for your pack.',
                'price.integer' => 'The price must be a whole number.',
                'price.min' => 'The price must be at least 1 PULSE.',
                'price.max' => 'The price cannot exceed 1,000,000 PULSE.'
            ]);

            $result = $pack->list($validated['price']);

            if (!$result['success']) {
                return back()
                    ->withInput()
                    ->with('error', $result['message']);
            }

            // Pack was successfully listed
            return redirect()
                ->route('marketplace.seller.dashboard')
                ->with('success', 'Your pack has been listed on the marketplace for ' . number_format($validated['price']) . ' PULSE.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'An unexpected error occurred while listing your pack. Please try again.');
        }
    }

    public function unlistPack(Pack $pack)
    {
        try {
            $this->authorize('removeFromMarketplace', $pack);

            if (!$pack->is_listed) {
                return back()->with('error', 'This pack is not currently listed on the marketplace.');
            }

            if (!$this->sellerService->unlistPack($pack)) {
                return back()->with('error', 'Unable to remove pack from marketplace. Please try again.');
            }

            return back()->with('success', 'Your pack has been removed from the marketplace.');
        } catch (\Exception $e) {
            return back()->with('error', 'An unexpected error occurred while removing your pack. Please try again.');
        }
    }

    public function salesHistory()
    {
        $soldPacks = $this->sellerService->getSoldPacks(Auth::user());
        $totalSales = $this->sellerService->getTotalSales(Auth::user());

        return view('marketplace.seller.sales', compact('soldPacks', 'totalSales'));
    }
}
