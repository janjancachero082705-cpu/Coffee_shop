<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\StoreInventory;
use Illuminate\Support\Facades\Auth;

class PortalInventoryController extends Controller
{
    public function index()
    {
        $store = Auth::guard('store')->user();

        $inventories = StoreInventory::with('product')
            ->where('store_id', $store->id)
            ->orderByDesc('updated_at')
            ->get();

        $totalDelivered = (int) $inventories->sum('quantity_delivered');
        $totalOnHand    = (int) $inventories->sum('quantity_on_hand');
        $totalSold      = (int) $inventories->sum('quantity_sold');

        return view('portal.inventory.index', compact(
            'store', 'inventories',
            'totalDelivered', 'totalOnHand', 'totalSold'
        ));
    }
}