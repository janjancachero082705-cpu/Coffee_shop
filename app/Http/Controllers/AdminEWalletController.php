<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EWallet;
use Illuminate\Support\Facades\Storage;

class AdminEWalletController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'code' => 'required|string|unique:e_wallets,code',
            'account_name' => 'nullable|string',
            'account_number' => 'nullable|string',
            'qr_code' => 'nullable|image|max:5120',
            'logo_path' => 'nullable|image|max:5120',
        ], [
            'code.unique' => 'This e-wallet is already added to the system.',
        ]);

        $data = $request->except(['qr_code', 'logo_path']);
        
        if ($request->hasFile('qr_code')) {
            $data['qr_code'] = $request->file('qr_code')->store('wallets', 'public');
        }
        if ($request->hasFile('logo_path')) {
            $data['logo_path'] = $request->file('logo_path')->store('wallets', 'public');
        }
        $data['is_active'] = $request->has('is_active') ? 1 : 1;

        EWallet::create($data);
        return back()->with('success', 'E-Wallet added successfully!');
    }

    public function update(Request $request, $id)
    {
        $wallet = EWallet::findOrFail($id);

        $request->validate([
            'name' => 'required|string',
            'code' => 'required|string|unique:e_wallets,code,'.$id,
            'account_name' => 'nullable|string',
            'account_number' => 'nullable|string',
            'qr_code' => 'nullable|image|max:5120',
            'logo_path' => 'nullable|image|max:5120',
        ]);

        $data = $request->except(['qr_code', 'logo_path']);
        
        if ($request->hasFile('qr_code')) {
            if ($wallet->qr_code) Storage::disk('public')->delete($wallet->qr_code);
            $data['qr_code'] = $request->file('qr_code')->store('wallets', 'public');
        }
        if ($request->hasFile('logo_path')) {
            if ($wallet->logo_path) Storage::disk('public')->delete($wallet->logo_path);
            $data['logo_path'] = $request->file('logo_path')->store('wallets', 'public');
        }

        $wallet->update($data);
        return back()->with('success', 'E-Wallet updated successfully!');
    }

    public function destroy($id)
    {
        $wallet = EWallet::findOrFail($id);
        if ($wallet->qr_code) Storage::disk('public')->delete($wallet->qr_code);
        if ($wallet->logo_path) Storage::disk('public')->delete($wallet->logo_path);
        $wallet->delete();
        return back()->with('success', 'E-Wallet removed.');
    }

    public function toggle($id)
    {
        $wallet = EWallet::findOrFail($id);
        $wallet->update(['is_active' => !$wallet->is_active]);
        return back()->with('success', 'Status updated.');
    }
}