<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function handleLogin(Request $request)
    {
        $validate = $request->validate([
            'name' => 'required|string',
            'password' => 'required'
        ]);

        if(Auth::attempt($validate)) {
            $request->session()->regenerate();

            return redirect()->intended('/dashboard');
        }
    }
}
