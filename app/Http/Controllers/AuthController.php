<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Auth;

class AuthController extends Controller
{
    use AuthenticatesUsers;

    public function showLogin()
    {
        return view('login');
    }
    
    public function logout(Request $request)
    {
        $this->guard()->logout();
        $request->session()->flush();
        $request->session()->invalidate();
        $request->session()->regenerate();
        return redirect('/')->with('success', 'Successfully logged out');
    }
}

