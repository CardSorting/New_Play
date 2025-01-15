<?php

namespace App\Marketplace\Components\Seller;

use App\Models\Pack;
use Illuminate\View\Component;

class ListedPackCard extends Component
{
    public $pack;
    public $showListingDate;

    public function __construct(Pack $pack, bool $showListingDate = true)
    {
        $this->pack = $pack;
        $this->showListingDate = $showListingDate;
    }

    public function render()
    {
        return view('marketplace.seller.components.listed-pack-card');
    }
}
