<?php

namespace App\View\Components;

use Illuminate\View\Component;

class GalleryTabs extends Component
{
    public $imagesCount;
    public $cardsCount;
    public $activeTab;

    public function __construct($imagesCount = 0, $cardsCount = 0, $activeTab = 'images')
    {
        $this->imagesCount = $imagesCount;
        $this->cardsCount = $cardsCount;
        $this->activeTab = $activeTab;
    }

    public function render()
    {
        return view('components.gallery-tabs');
    }

    public function isActive($tab)
    {
        return $this->activeTab === $tab;
    }

    public function getTabClasses($tab)
    {
        return $this->isActive($tab)
            ? 'border-blue-500 text-blue-600'
            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300';
    }
}
