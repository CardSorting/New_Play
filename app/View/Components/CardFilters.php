<?php

namespace App\View\Components;

use Illuminate\View\Component;

class CardFilters extends Component
{
    public $rarityFilters;
    public $typeFilters;

    public function __construct()
    {
        $this->rarityFilters = $this->getRarityFilters();
        $this->typeFilters = $this->getTypeFilters();
    }

    protected function getRarityFilters()
    {
        return [
            'all' => [
                'label' => 'All',
                'bg' => 'bg-blue-100',
                'text' => 'text-blue-800',
                'hover' => 'hover:bg-blue-200'
            ],
            'mythic' => [
                'label' => 'Mythic Rare',
                'bg' => 'bg-orange-100',
                'text' => 'text-orange-800',
                'hover' => 'hover:bg-orange-200'
            ],
            'rare' => [
                'label' => 'Rare',
                'bg' => 'bg-yellow-100',
                'text' => 'text-yellow-800',
                'hover' => 'hover:bg-yellow-200'
            ],
            'uncommon' => [
                'label' => 'Uncommon',
                'bg' => 'bg-gray-100',
                'text' => 'text-gray-800',
                'hover' => 'hover:bg-gray-200'
            ],
            'common' => [
                'label' => 'Common',
                'bg' => 'bg-gray-100',
                'text' => 'text-gray-700',
                'hover' => 'hover:bg-gray-200'
            ]
        ];
    }

    protected function getTypeFilters()
    {
        return [
            'all' => [
                'label' => 'All Types',
                'bg' => 'bg-purple-100',
                'text' => 'text-purple-800',
                'hover' => 'hover:bg-purple-200'
            ],
            'creature' => [
                'label' => 'Creatures',
                'bg' => 'bg-green-100',
                'text' => 'text-green-800',
                'hover' => 'hover:bg-green-200'
            ],
            'spell' => [
                'label' => 'Spells',
                'bg' => 'bg-blue-100',
                'text' => 'text-blue-800',
                'hover' => 'hover:bg-blue-200'
            ],
            'artifact' => [
                'label' => 'Artifacts',
                'bg' => 'bg-gray-100',
                'text' => 'text-gray-800',
                'hover' => 'hover:bg-gray-200'
            ],
            'enchantment' => [
                'label' => 'Enchantments',
                'bg' => 'bg-pink-100',
                'text' => 'text-pink-800',
                'hover' => 'hover:bg-pink-200'
            ]
        ];
    }

    public function render()
    {
        return view('components.card-filters');
    }
}
