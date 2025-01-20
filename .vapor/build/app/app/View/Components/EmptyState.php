<?php

namespace App\View\Components;

use Illuminate\View\Component;

class EmptyState extends Component
{
    public $type;
    public $title;
    public $message;
    public $actionRoute;
    public $actionText;
    public $icon;

    public function __construct($type = 'images')
    {
        $this->type = $type;
        
        if ($type === 'images') {
            $this->title = 'No Images Yet';
            $this->message = 'Start your creative journey by generating your first AI image!';
            $this->actionRoute = 'images.create';
            $this->actionText = 'Generate First Image';
            $this->icon = 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z';
        } else {
            $this->title = 'No Cards Created';
            $this->message = 'Transform your AI images into unique cards by clicking the "Create Card" button on any image!';
            $this->actionRoute = '';
            $this->actionText = '';
            $this->icon = 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10';
        }
    }

    public function render()
    {
        return view('components.empty-state');
    }

    public function getBackgroundColor()
    {
        return $this->type === 'images' ? 'blue' : 'purple';
    }
}
