<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'all');
        $search = $request->get('search', '');
        $status = $request->get('status', '');

        // ═══ REGISTERED STORES ═══
        $query = Store::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('store_name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('owner_name', 'like', "%{$search}%")
                  ->orWhere('contact_number', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        $stores = $query->latest()->paginate(12, ['*'], 'stores_page');

        // ═══ PENDING REGISTRATIONS ═══
        $pendingQuery = class_exists('\App\Models\StoreRegistration')
            ? \App\Models\StoreRegistration::query()
            : \App\Models\Store::where('status', 'pending');

        if ($search) {
            $pendingQuery->where(function ($q) use ($search) {
                $q->where('store_name', 'like', "%{$search}%")
                  ->orWhere('owner_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('contact_number', 'like', "%{$search}%");
            });
        }

        $pending = $pendingQuery->latest()->paginate(12, ['*'], 'pending_page');

        // ═══ COUNTS ═══
        $totalCount = Store::count();
        $activeCount = Store::where('status', 'active')->count();
        $pendingCount = class_exists('\App\Models\StoreRegistration')
            ? \App\Models\StoreRegistration::count()
            : Store::where('status', 'pending')->count();

        return view('stores.index', compact(
            'stores',
            'pending',
            'tab',
            'search',
            'status',
            'totalCount',
            'activeCount',
            'pendingCount'
        ));
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
              'customer_type'  => 'nullable|in:regular,suki,friend',
              'default_discount_percent' => 'nullable|numeric|min:0|max:100',
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
              'customer_type'  => 'nullable|in:regular,suki,friend',
              'default_discount_percent' => 'nullable|numeric|min:0|max:100',
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