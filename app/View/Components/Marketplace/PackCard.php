<?php

namespace App\View\Components\Marketplace;

use App\Models\Pack;
use Illuminate\View\Component;

class PackCard extends Component
{
    public $pack;
    public $showActions;

    public function __construct(Pack $pack, bool $showActions = true)
    {
        $this->pack = $pack;
        $this->showActions = $showActions;
    }

    public function render()
    {
        return view('components.marketplace.pack-card');
    }
}
