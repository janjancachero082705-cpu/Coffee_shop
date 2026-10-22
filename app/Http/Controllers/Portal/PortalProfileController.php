<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\Store;

class PortalProfileController extends Controller
{
    public function index()
    {
        $store = Store::find(Auth::guard('store')->id());
        return view('portal.profile.index', compact('store'));
    }

    public function edit()
    {
        $store = Store::find(Auth::guard('store')->id());
        return view('portal.profile.edit', compact('store'));
    }

    public function update(Request $request)
    {
        // Robust store lookup
        $store = null;
        if (auth('store')->check()) {
            $store = auth('store')->user();
        } elseif (auth()->check() && auth()->user()->store_id) {
            $store = \App\Models\Store::find(auth()->user()->store_id);
        }

        if (!$store) {
            return back()->withErrors(['error' => 'Store not found. Please login again.']);
        }

        // Validation — nullable fields (para mo-work sa bag-ong accounts)
        $request->validate([
            'store_name'    => 'nullable|string|max:255',
            'owner_name'    => 'nullable|string|max:255',
            'email'         => 'nullable|email|max:255',
            'contact_number'=> 'nullable|string|max:20',
            'phone'         => 'nullable|string|max:20',
            'address'       => 'nullable|string|max:500',
            'barangay'      => 'nullable|string|max:255',
            'city'          => 'nullable|string|max:255',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        // Update text fields — support both 'contact_number' and 'phone'
        $store->store_name = $request->input('store_name', $store->store_name);
        $store->owner_name = $request->input('owner_name', $store->owner_name);
        $store->email = $request->input('email', $store->email);
        
        // Support both field names
        if ($request->filled('contact_number')) {
            $store->contact_number = $request->contact_number;
        } elseif ($request->filled('phone')) {
            $store->contact_number = $request->phone;
        }
        
        $store->address = $request->input('address', $store->address);
        $store->barangay = $request->input('barangay', $store->barangay);
        $store->city = $request->input('city', $store->city);

        // Handle profile picture upload
        if ($request->hasFile('profile_image')) {
            // Delete old
            if ($store->profile_image && \Storage::disk('public')->exists($store->profile_image)) {
                \Storage::disk('public')->delete($store->profile_image);
            }

            // Save new — with 'profiles/' prefix
            $path = $request->file('profile_image')->store('profiles', 'public');
            $store->profile_image = $path;
        }

        // IMPORTANTE — explicit save + touch for cache-bust
        $store->save();
        $store->refresh();
        $store->touch();  // force updated_at change

        return redirect()->route('portal.profile')->with('success', 'Profile updated successfully!');
    }

    public function password()
    {
        $store = Store::find(Auth::guard('store')->id());
        return view('portal.profile.password', compact('store'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        $store = \App\Models\Store::find(Auth::guard('store')->id());

        if (!\Illuminate\Support\Facades\Hash::check($request->current_password, $store->password)) {
            return back()->withErrors(['current_password' => 'Current password does not match.']);
        }

        $store->password = \Illuminate\Support\Facades\Hash::make($request->password);
        $store->save();

        // REDIRECT PABALIK SA PROFILE PAGE
        return redirect()->route('portal.profile')->with('success', 'Password updated successfully!');
    }

    public function passwordForm()
    {
        $store = \App\Models\Store::find(Auth::guard('store')->id());
        return view('portal.profile.password', compact('store'));
    }
}