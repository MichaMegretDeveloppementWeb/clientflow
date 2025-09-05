<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class SupportController extends Controller
{
    public function helpCenter(): View
    {
        return view('support.help-center');
    }

    public function documentation(): View
    {
        return view('support.documentation');
    }

    public function contact(): View
    {
        return view('support.contact');
    }

    public function community(): View
    {
        return view('support.community');
    }
}
