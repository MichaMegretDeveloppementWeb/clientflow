<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function features(): View
    {
        return view('pages.features');
    }

    public function security(): View
    {
        return view('pages.security');
    }

    public function updates(): View
    {
        return view('pages.updates');
    }
}
