<?php

namespace App\View\Components;

use Illuminate\View\Component;

class GalleryHeader extends Component
{
    public $title;
    public $createRoute;

    public function __construct($title = 'My Gallery', $createRoute = 'images.create')
    {
        $this->title = $title;
        $this->createRoute = $createRoute;
    }

    public function render()
    {
        return view('components.gallery-header');
    }
}
