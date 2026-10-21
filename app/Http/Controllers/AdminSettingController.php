<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaymentSetting;
use App\Models\EWallet;
use Illuminate\Support\Facades\Storage;

class AdminSettingController extends Controller
{
    public function index()
    {
        $wallets = \App\Models\EWallet::latest()->get();
        $setting = PaymentSetting::first() ?? new PaymentSetting();
        return view('admin.settings.payment', compact('setting', 'wallets'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'gcash_name' => 'required|string',
            'gcash_number' => 'required|string',
            'gcash_qr' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $setting = PaymentSetting::first() ?? new PaymentSetting();
        $setting->gcash_name = $request->gcash_name;
        $setting->gcash_number = $request->gcash_number;

        if ($request->hasFile('gcash_qr')) {
            // Delete old QR if exists
            if ($setting->gcash_qr_path) {
                Storage::disk('public')->delete($setting->gcash_qr_path);
            }
            // Save new QR
            $path = $request->file('gcash_qr')->store('payment_qr', 'public');
            $setting->gcash_qr_path = $path;
        }

        $setting->save();

        return back()->with('success', 'GCash Settings updated successfully!');
    }
}