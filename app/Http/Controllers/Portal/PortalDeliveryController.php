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

    /**
     * Customer confirms delivery received
     */
    public function confirm(Request $request, $id)
    {
        $store = Auth::guard('store')->user();

        $delivery = \App\Models\DeliveryReceipt::where('store_id', $store->id)->findOrFail($id);

        if ($delivery->customer_confirmed) {
            return back()->with('error', 'Already confirmed.');
        }

        $notes = $request->input('notes');

        // Confirm received
        $delivery->confirmReceived($notes);

        // Auto-generate Sales Report
        try {
            $salesReport = \App\Models\SalesReport::create([
                'report_number'   => 'SR-' . now()->format('Ymd') . '-' . str_pad(
                    \App\Models\SalesReport::whereDate('created_at', today())->count() + 1,
                    4, '0', STR_PAD_LEFT
                ),
                'store_id'        => $store->id,
                'period_from'     => $delivery->delivery_date,
                'period_to'       => now()->toDateString(),
                'total_sales'     => $delivery->total_amount,
                'total_quantity'  => $delivery->items->sum('quantity_delivered'),
                'amount_paid'     => 0,
                'balance'         => $delivery->total_amount,
                'status'          => 'pending',
                'created_by'      => null,
            ]);

            // Link sa delivery
            $delivery->update(['sales_report_id' => $salesReport->id]);

            \Log::info('[Delivery] Sales Report auto-generated: ' . $salesReport->report_number);

        } catch (\Throwable $e) {
            \Log::warning('Sales report auto-generation failed: ' . $e->getMessage());
        }

        // Fire real-time event
        try {
            event(new \App\Events\DeliveryConfirmed($delivery));
        } catch (\Throwable $e) {
            \Log::warning('Event failed: ' . $e->getMessage());
        }

        return redirect()
            ->route('portal.deliveries.show', $delivery->id)
            ->with('success', 'Delivery confirmed! Sales report auto-generated.');
    }
}
