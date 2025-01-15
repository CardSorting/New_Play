<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ImageGridItem extends Component
{
    public $image;

    public function __construct($image)
    {
        $this->image = $image;
    }

    public function render()
    {
        return view('components.image-grid-item');
    }
}
