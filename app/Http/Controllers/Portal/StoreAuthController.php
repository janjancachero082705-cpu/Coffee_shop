<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StoreAuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('store')->check()) {
            return redirect()->route('portal.dashboard');
        }
        // Redirect sa unified login with store tab pre-selected
        return redirect()->route('login', ['tab' => 'store']);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::guard('store')->attempt($credentials, $remember)) {
            $store = Auth::guard('store')->user();

            if (!$store->portal_enabled) {
                Auth::guard('store')->logout();
                return redirect()->route('login', ['tab' => 'store'])->withErrors(['email' => 'Portal access not enabled. Contact admin.'])->withInput(['_tab_store' => 1, 'email' => $request->email]);
            }

            if ($store->status !== 'active') {
                Auth::guard('store')->logout();
                return redirect()->route('login', ['tab' => 'store'])->withErrors(['email' => 'Account is ' . $store->status . '.'])->withInput(['_tab_store' => 1, 'email' => $request->email]);
            }

            $store->update(['last_login_at' => now()]);
            $request->session()->regenerate();

            return redirect()->intended(route('portal.dashboard'));
        }

        return redirect()->route('login', ['tab' => 'store'])->withErrors(['email' => 'Invalid credentials.'])->withInput(['_tab_store' => 1, 'email' => $request->email, 'remember' => $request->boolean('remember')]);
    }

    public function logout(Request $request)
    {
        Auth::guard('store')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('portal.login');
    }
}