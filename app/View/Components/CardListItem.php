<?php

namespace App\View\Components;

use Illuminate\View\Component;

class CardListItem extends Component
{
    public $card;

    public function __construct($card)
    {
        $this->card = $card;
    }

    public function render()
    {
        return view('components.card-list-item');
    }

    public function getRarityClasses()
    {
        return match ($this->card['rarity']) {
            'Mythic Rare' => 'bg-orange-100 text-orange-800',
            'Rare' => 'bg-yellow-100 text-yellow-800',
            'Uncommon' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-600',
        };
    }
}
