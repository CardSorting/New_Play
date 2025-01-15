<?php

namespace App\Marketplace\Components\Browse;

use App\Models\Pack;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Auth;

class AvailablePackCard extends Component
{
    public $pack;
    public $canPurchase;

    public function __construct(Pack $pack)
    {
        $this->pack = $pack;
        $this->canPurchase = $pack->canBePurchased() && $pack->user_id !== Auth::id();
    }

    public function render()
    {
        return view('marketplace.browse.components.available-pack-card');
    }
}
