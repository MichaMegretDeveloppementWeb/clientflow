<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    /**
     * Display the home/welcome page.
     */
    public function index()
    {
        return view('home.index');
    }
}
