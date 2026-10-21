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
        $store = Store::find(Auth::guard('store')->id());

        $request->validate([
            'store_name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'contact_number' => 'required|string|max:20',
            'address' => 'required|string',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        $store->store_name = $request->store_name;
        $store->owner_name = $request->owner_name;
        $store->email = $request->email;
        $store->contact_number = $request->contact_number;
        $store->address = $request->address;
        $store->barangay = $request->barangay;
        $store->city = $request->city;

        if ($request->hasFile('profile_image')) {
            if ($store->profile_image && Storage::disk('public')->exists($store->profile_image)) {
                Storage::disk('public')->delete($store->profile_image);
            }
            $path = $request->file('profile_image')->store('profiles', 'public');
            $store->profile_image = $path;
        }

        $store->save();
        $store->refresh();
        Auth::guard('store')->setUser($store);

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