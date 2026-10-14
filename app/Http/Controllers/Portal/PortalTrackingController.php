<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ReorderRequest;
use App\Models\DeliveryReceipt;

class PortalTrackingController extends Controller
{
    /**
     * Tracking index — overview of all transactions
     */
    public function index()
    {
        $store = Auth::guard('store')->user();

        $filter = request('filter', 'active');
        $query = ReorderRequest::where('store_id', $store->id)->with(['deliveryReceipt', 'items']);

        if ($filter === 'active') {
            $query->whereIn('status', ['pending', 'approved']);
        } elseif ($filter === 'rejected') {
            $query->where('status', 'rejected');
        } elseif ($filter === 'in_transit') {
            $query->whereHas('deliveryReceipt', function ($q) {
                $q->whereNotNull('out_for_delivery_at')
                  ->whereNull('customer_confirmed');
            });
        } elseif ($filter === 'awaiting_payment') {
            $query->whereHas('deliveryReceipt', function ($q) {
                $q->whereNotNull('customer_confirmed')
                  ->whereRaw('amount_paid < total_amount');
            });
        } elseif ($filter === 'completed') {
            $query->whereHas('deliveryReceipt', function ($q) {
                $q->whereNotNull('customer_confirmed')
                  ->whereRaw('amount_paid >= total_amount');
            });
        }

        $orders = $query->orderByDesc('created_at')->paginate(10)->withQueryString();

        // Counts
        $allCount = ReorderRequest::where('store_id', $store->id)->count();
        $activeCount = ReorderRequest::where('store_id', $store->id)
            ->whereIn('status', ['pending', 'approved'])->count();
        $rejectedCount = ReorderRequest::where('store_id', $store->id)
            ->where('status', 'rejected')->count();
        $inTransitCount = ReorderRequest::where('store_id', $store->id)
            ->whereHas('deliveryReceipt', function ($q) {
                $q->whereNotNull('out_for_delivery_at')->whereNull('customer_confirmed');
            })->count();
        $awaitingPaymentCount = ReorderRequest::where('store_id', $store->id)
            ->whereHas('deliveryReceipt', function ($q) {
                $q->whereNotNull('customer_confirmed')->whereRaw('amount_paid < total_amount');
            })->count();
        $completedCount = ReorderRequest::where('store_id', $store->id)
            ->whereHas('deliveryReceipt', function ($q) {
                $q->whereNotNull('customer_confirmed')->whereRaw('amount_paid >= total_amount');
            })->count();

        return view('portal.tracking.index', compact(
            'orders',
            'allCount',
            'activeCount',
            'rejectedCount',
            'inTransitCount',
            'awaitingPaymentCount',
            'completedCount'
        ));
    }

    /**
     * Tracking detail — timeline for one transaction
     */
    public function show($id)
    {
        $store = Auth::guard('store')->user();

        $order = ReorderRequest::where('store_id', $store->id)
            ->with(['items.product', 'deliveryReceipt'])
            ->findOrFail($id);

        return view('portal.tracking.show', compact('order'));
    }
}