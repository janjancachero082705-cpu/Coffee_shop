<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index(Request $request)
    {
        $query = Store::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('store_name', 'like', "%{$s}%")
                  ->orWhere('code', 'like', "%{$s}%")
                  ->orWhere('owner_name', 'like', "%{$s}%")
                  ->orWhere('contact_number', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $stores = $query->latest()->paginate(12);
        return view('stores.index', compact('stores'));
    }

    public function create()
    {
        return view('stores.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code'           => 'required|string|max:50|unique:stores,code',
            'store_name'     => 'required|string|max:255',
            'owner_name'     => 'required|string|max:255',
            'contact_number' => 'required|string|max:50',
            'email'          => 'nullable|email|max:255',
            'address'        => 'required|string',
            'barangay'       => 'nullable|string|max:255',
            'city'           => 'nullable|string|max:255',
            'credit_limit'   => 'nullable|numeric|min:0',
            'payment_terms'  => 'required|in:weekly,semi_monthly,monthly,flexible',
            'status'         => 'required|in:active,inactive,suspended',
            'notes'          => 'nullable|string',
        ]);

        $data['credit_limit'] = $data['credit_limit'] ?? 0;

        Store::create($data);

        return redirect()->route('stores.index')->with('success', 'Store created successfully.');
    }

    public function show(Store $store)
    {
        return view('stores.show', compact('store'));
    }

    public function edit(Store $store)
    {
        return view('stores.edit', compact('store'));
    }

    public function update(Request $request, Store $store)
    {
        $data = $request->validate([
            'code'           => 'required|string|max:50|unique:stores,code,' . $store->id,
            'store_name'     => 'required|string|max:255',
            'owner_name'     => 'required|string|max:255',
            'contact_number' => 'required|string|max:50',
            'email'          => 'nullable|email|max:255',
            'address'        => 'required|string',
            'barangay'       => 'nullable|string|max:255',
            'city'           => 'nullable|string|max:255',
            'credit_limit'   => 'nullable|numeric|min:0',
            'payment_terms'  => 'required|in:weekly,semi_monthly,monthly,flexible',
            'status'         => 'required|in:active,inactive,suspended',
            'notes'          => 'nullable|string',
        ]);

        $data['credit_limit'] = $data['credit_limit'] ?? 0;

        $store->update($data);

        return redirect()->route('stores.show', $store)->with('success', 'Store updated successfully.');
    }

    public function destroy(Store $store)
    {
        $store->delete();
        return redirect()->route('stores.index')->with('success', 'Store deleted successfully.');
    }
}