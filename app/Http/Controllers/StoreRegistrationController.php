<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StoreRegistrationController extends Controller
{
    /**
     * List all stores (with optional status filter)
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $search = $request->get('search');

        $query = Store::query();

        if ($status === 'pending') {
            $query->where('registration_status', 'pending');
        } elseif ($status === 'approved') {
            $query->where('registration_status', 'approved');
        } elseif ($status === 'rejected') {
            $query->where('registration_status', 'rejected');
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('store_name', 'like', "%{$search}%")
                  ->orWhere('owner_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $stores = $query->orderByDesc('created_at')->paginate(15);

        $stats = [
            'total'    => Store::count(),
            'pending'  => Store::where('registration_status', 'pending')->count(),
            'approved' => Store::where('registration_status', 'approved')->count(),
            'rejected' => Store::where('registration_status', 'rejected')->count(),
            'recent'   => Store::where('created_at', '>=', now()->subDays(7))->count(),
        ];

        return view('store-registrations.index', compact('stores', 'stats', 'status', 'search'));
    }

    /**
     * Show a single store
     */
    public function show($id)
    {
        $store = Store::findOrFail($id);

        // Auto-mark as read by admin
        if (!$store->is_read_by_admin) {
            $store->update([
                'is_read_by_admin' => true,
                'is_read_at' => now(),
            ]);
        }

        return view('store-registrations.show', compact('store'));
    }

    /**
     * Approve a store registration
     */
    public function approve($id)
    {
        $store = Store::findOrFail($id);

        $store->update([
            'registration_status' => 'approved',
            'status' => 'active',
            'portal_enabled' => true,
            'approved_at' => now(),
        ]);

        return redirect()->route('store-registrations.index')
            ->with('success', "Store '{$store->store_name}' approved successfully.");
    }

    /**
     * Reject a store registration
     */
    public function reject(Request $request, $id)
    {
        $store = Store::findOrFail($id);

        $store->update([
            'registration_status' => 'rejected',
            'status' => 'inactive',
            'portal_enabled' => false,
            'rejection_reason' => $request->input('reason'),
        ]);

        return redirect()->route('store-registrations.index')
            ->with('success', "Store '{$store->store_name}' rejected.");
    }

    /**
     * Delete a store registration
     */
    public function destroy($id)
    {
        $store = Store::findOrFail($id);
        $name = $store->store_name;
        $store->delete();

        return redirect()->route('store-registrations.index')
            ->with('success', "Store '{$name}' deleted.");
    }
}