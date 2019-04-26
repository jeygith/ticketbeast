<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    public function showLoginForm()
    {
        return view('auth.login');
    }


    public function login()
    {
        // dd(request(['email', 'password']));

        if (!Auth::attempt(request(['email', 'password']))) {
            return redirect('/login')->withErrors([
                'email' => ['These credentials do not match our records']
            ])->withInput(request(['email']));
        }
        return redirect('/backstage/concerts');

    }


    public function logout()
    {
        Auth::logout();

        return redirect('/login');
    }
}
