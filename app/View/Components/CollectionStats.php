<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Collection;

class CollectionStats extends Component
{
    public $cards;
    public $stats;

    public function __construct($cards)
    {
        $this->cards = $cards;
        $this->stats = $this->calculateStats();
    }

    protected function calculateStats()
    {
        return [
            'mythicRare' => [
                'count' => $this->cards->where('rarity', 'Mythic Rare')->count(),
                'label' => 'Mythic Rare',
                'color' => 'orange',
                'gradient' => 'from-orange-50 to-orange-100',
                'dot' => 'bg-orange-400'
            ],
            'rare' => [
                'count' => $this->cards->where('rarity', 'Rare')->count(),
                'label' => 'Rare',
                'color' => 'yellow',
                'gradient' => 'from-yellow-50 to-yellow-100',
                'dot' => 'bg-yellow-400'
            ],
            'uncommon' => [
                'count' => $this->cards->where('rarity', 'Uncommon')->count(),
                'label' => 'Uncommon',
                'color' => 'gray',
                'gradient' => 'from-gray-50 to-gray-100',
                'dot' => 'bg-gray-400'
            ],
            'common' => [
                'count' => $this->cards->where('rarity', 'Common')->count(),
                'label' => 'Common',
                'color' => 'gray',
                'gradient' => 'from-gray-50 to-gray-100',
                'dot' => 'bg-gray-600'
            ]
        ];
    }

    public function render()
    {
        return view('components.collection-stats');
    }
}
