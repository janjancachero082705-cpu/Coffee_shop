<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        return view('admin.profile');
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'profile_image' => 'required|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        $user = auth()->user();

        if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
            Storage::disk('public')->delete($user->profile_image);
        }

        $path = $request->file('profile_image')->store('profiles', 'public');
        $user->profile_image = $path;
        $user->save();

        return back()->with('success', 'Profile picture updated!');
    }

    public function removeAvatar()
    {
        $user = auth()->user();
        if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
            Storage::disk('public')->delete($user->profile_image);
        }
        $user->profile_image = null;
        $user->save();

        return back()->with('success', 'Profile picture removed.');
    }
}