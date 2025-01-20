<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    public function index()
    {
        return view('pages.home');
    }

    public function pricing()
    {
        return view('pages.pricing');
    }

    public function features()
    {
        return view('pages.features');
    }
}
