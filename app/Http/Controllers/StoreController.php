<?php
namespace App\Http\Controllers;
use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index(Request $request) {
        $query = Store::query();
        
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('store_name', 'like', "%$search%");
        }
        
        if ($request->filled('type')) {
            $query->where('customer_type', $request->input('type'));
        }

        $stores = $query->latest()->paginate(10)->withQueryString();
        return view('stores.index', compact('stores'));
    }

    public function create() {
        return view('stores.create');
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'store_name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'customer_type' => 'required|in:suki,regular,friend',
        ]);

        Store::create($validated);
        return redirect()->route('stores.index')->with('success', 'Store created successfully!');
    }

    public function edit(Store $store) {
        return view('stores.edit', compact('store'));
    }

    public function update(Request $request, Store $store) {
        $validated = $request->validate([
            'store_name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'customer_type' => 'required|in:suki,regular,friend',
        ]);

        $store->update($validated);
        return redirect()->route('stores.index')->with('success', 'Store updated successfully!');
    }

    public function destroy(Store $store) {
        $store->delete();
        return redirect()->route('stores.index')->with('success', 'Store deleted successfully!');
    }
}