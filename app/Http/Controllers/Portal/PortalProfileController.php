<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class PortalProfileController extends Controller
{
    public function show()
    {
        $store = Auth::guard('store')->user();
        return view('portal.profile.show', compact('store'));
    }

    public function edit()
    {
        $store = Auth::guard('store')->user();
        return view('portal.profile.edit', compact('store'));
    }

    public function update(Request $request)
    {
        $store = Auth::guard('store')->user();

        $data = $request->validate([
            'store_name'     => 'required|string|max:255',
            'owner_name'     => 'required|string|max:255',
            'contact_number' => 'required|string|max:50',
            'email'          => 'required|email|max:255|unique:stores,email,' . $store->id,
            'address'        => 'required|string|max:500',
            'barangay'       => 'nullable|string|max:100',
            'city'           => 'nullable|string|max:100',
            'logo'           => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            if ($store->logo && !str_starts_with($store->logo, 'http')) {
                Storage::disk('public')->delete($store->logo);
            }
            $data['logo'] = $request->file('logo')->store('store-logos', 'public');
        }

        $store->update($data);

        return redirect()->route('portal.dashboard')
            ->with('success', 'Profile updated successfully.');
    }

    public function removeLogo(Request $request)
    {
        $store = Auth::guard('store')->user();

        if ($store->logo && !str_starts_with($store->logo, 'http')) {
            Storage::disk('public')->delete($store->logo);
        }

        $store->update(['logo' => null]);

        return redirect()->route('portal.dashboard')
            ->with('success', 'Logo removed.');
    }

    public function passwordForm()
    {
        $store = Auth::guard('store')->user();
        return view('portal.profile.password', compact('store'));
    }

    public function updatePassword(Request $request)
    {
        $store = Auth::guard('store')->user();

        $data = $request->validate([
            'current_password' => 'required',
            'password'         => 'required|string|min:6|confirmed',
        ]);

        // Check current password
        if (!Hash::check($data['current_password'], $store->password)) {
            return back()->withErrors([
                'current_password' => 'The current password is incorrect.'
            ]);
        }

        $store->update([
            'password' => Hash::make($data['password']),
        ]);

        return redirect()->route('portal.dashboard')
            ->with('success', 'Password changed successfully.');
    }
}