<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class StoreRegisterController extends Controller
{
    public function showForm()
    {
        if (Auth::guard('store')->check()) {
            return redirect()->route('portal.dashboard');
        }

        return view('portal.auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'store_name'     => 'required|string|max:255',
            'owner_name'     => 'required|string|max:255',
            'email'          => 'required|email|unique:stores,email',
            'contact_number' => 'required|string|max:50',
            'address'        => 'required|string|max:500',
            'city'           => 'required|string|max:100',
            'barangay'       => 'nullable|string|max:100',
            'password'       => 'required|string|min:6|confirmed',
        ]);

        // Generate store code
        $lastId = Store::max('id') + 1;
        $code = 'STORE-' . str_pad($lastId, 4, '0', STR_PAD_LEFT);

        // Create store — auto-approved
        $store = Store::create([
            'code'                 => $code,
            'store_name'           => $data['store_name'],
            'owner_name'           => $data['owner_name'],
            'email'                => $data['email'],
            'contact_number'       => $data['contact_number'],
            'address'              => $data['address'],
            'city'                 => $data['city'],
            'barangay'             => $data['barangay'] ?? null,
            'password'             => Hash::make($data['password']),
            'status'               => 'active',
            'registration_status'  => 'approved',
            'is_read_by_admin' => false,
            'login_count' => 0,
            'portal_enabled'       => true,
            'credit_limit'         => 0,
            'payment_terms'        => 'flexible',
            'last_login_at'        => now(),
        ]);

        // Auto-login
        Auth::guard('store')->login($store);

        // Redirect to dashboard
        return redirect()->route('portal.dashboard')
            ->with('success', 'Welcome to Coffee Beans! Your account is ready.');
    }

    public function success()
    {
        return view('portal.auth.register-success');
    }
}