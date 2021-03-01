<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class HomeController extends Controller
{    
    public function landingPage()
    {
        return view('forecourt.landing_page');
    }
}
