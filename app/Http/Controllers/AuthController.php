<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin(Request $request)
    {
        // Kung already logged in as admin, redirect to dashboard
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }
        // Kung already logged in as store, redirect to portal
        if (auth('store')->check()) {
            return redirect()->route('portal.dashboard');
        }

        // Admin-only login (walang tabs)
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return redirect()->route('login')->withErrors([
            'email' => 'Invalid email or password.',
        ])->withInput(['email' => $request->email]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}