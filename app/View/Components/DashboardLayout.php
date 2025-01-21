<?php

namespace App\View\Components;

use Illuminate\View\Component;

class DashboardLayout extends Component
{
    public function __construct()
    {
        //
    }

    public function render()
    {
        return view('components.dashboard-layout');
    }
}