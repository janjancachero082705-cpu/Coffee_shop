<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PortalDeliveryController extends Controller
{
    public function index(Request $request)
    {
        $store = Auth::guard('store')->user();
        $query = $store->deliveryReceipts()->with('items');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $deliveries = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total'   => $store->deliveryReceipts()->count(),
            'pending' => $store->deliveryReceipts()->where('status', 'pending')->count(),
            'partial' => $store->deliveryReceipts()->where('status', 'partial')->count(),
            'paid'    => $store->deliveryReceipts()->where('status', 'paid')->count(),
        ];

        return view('portal.deliveries.index', compact('store', 'deliveries', 'stats'));
    }

    public function show($id)
    {
        $store = Auth::guard('store')->user();
        $delivery = $store->deliveryReceipts()->with('items.product')->findOrFail($id);
        return view('portal.deliveries.show', compact('store', 'delivery'));
    }
}