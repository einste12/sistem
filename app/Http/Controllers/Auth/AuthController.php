<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }
    public function login_post(LoginRequest $request)
    {
        if(Auth::attempt(['email' => $request->email , 'password' => $request->password]))
        {
            return redirect()->route('dashboard');
        }
        
        return redirect()->back()->withErrors(['password' => 'Hatalı şifre! lütfen tekrar deneyiniz.'])->withInput();

    }
    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}
