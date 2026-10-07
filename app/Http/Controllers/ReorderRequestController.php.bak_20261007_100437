<?php

namespace App\Http\Controllers;

use App\Models\ReorderRequest;
use App\Models\DeliveryReceipt;
use App\Models\DeliveryReceiptItem;
use App\Models\SalesReport;
use App\Models\SalesReportItem;
use App\Models\StoreInventory;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use App\Events\OrderStatusUpdated;
use Illuminate\Support\Facades\DB;

class ReorderRequestController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'pending');
        $query = ReorderRequest::with('store', 'items');

        if ($tab !== 'all') {
            $query->where('status', $tab);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('request_number', 'like', "%{$s}%")
                  ->orWhereHas('store', fn($sq) => $sq->where('store_name', 'like', "%{$s}%"));
            });
        }

        $requests = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total'    => ReorderRequest::count(),
            'pending'  => ReorderRequest::where('status', 'pending')->count(),
            'approved' => ReorderRequest::where('status', 'approved')->count(),
            'rejected' => ReorderRequest::where('status', 'rejected')->count(),
        ];

        $tabCounts = [
            'pending'  => $stats['pending'],
            'approved' => $stats['approved'],
            'rejected' => $stats['rejected'],
            'all'      => $stats['total'],
        ];

        return view('reorder-requests.index', compact('requests', 'stats', 'tab', 'tabCounts'));
    }

    public function show(ReorderRequest $reorderRequest)
    {
        $reorderRequest->load('store', 'items.product', 'approver', 'deliveryReceipt');
        return view('reorder-requests.show', compact('reorderRequest'));
    }

    public function approve(Request $request, ReorderRequest $reorderRequest)
    {
        if (!$reorderRequest->isPending()) {
            return back()->with('error', 'Already processed.');
        }

        $data = $request->validate([
            'delivery_date' => 'required|date',
            'admin_notes'   => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($reorderRequest, $data) {
            $drNumber = 'DR-' . now()->format('Ymd') . '-' . str_pad(
                DeliveryReceipt::whereDate('created_at', today())->count() + 1,
                4, '0', STR_PAD_LEFT
            );

            $dr = DeliveryReceipt::create([
                'dr_number'     => $drNumber,
                'store_id'      => $reorderRequest->store_id,
                'user_id'       => auth()->id(),
                'delivery_date' => $data['delivery_date'],
                'total_amount'  => $reorderRequest->total_amount,
                'amount_paid'   => 0,
                'balance'       => $reorderRequest->total_amount,
                'status'        => 'pending',
                'notes'         => 'Auto-created from ' . $reorderRequest->request_number,
            ]);

            foreach ($reorderRequest->items as $item) {
                DeliveryReceiptItem::create([
                    'delivery_receipt_id' => $dr->id,
                    'product_id'          => $item->product_id,
                    'quantity_delivered'  => $item->quantity_requested,
                    'quantity_sold'       => 0,
                    'quantity_returned'   => 0,
                    'unit_price'          => $item->unit_price,
                    'subtotal'            => $item->subtotal,
                ]);

                $inv = StoreInventory::firstOrCreate(
                    ['store_id' => $reorderRequest->store_id, 'product_id' => $item->product_id],
                    ['quantity_delivered' => 0, 'quantity_sold' => 0, 'quantity_returned' => 0, 'quantity_on_hand' => 0]
                );
                $inv->increment('quantity_delivered', $item->quantity_requested);
                $inv->increment('quantity_on_hand', $item->quantity_requested);

                InventoryTransaction::create([
                    'product_id' => $item->product_id,
                    'user_id'    => auth()->id(),
                    'type'       => 'out',
                    'quantity'   => $item->quantity_requested,
                    'reference'  => $dr->dr_number,
                    'notes'      => 'Reorder: ' . $reorderRequest->request_number,
                ]);

                if ($item->product) {
                    $item->product->decrement('stock', $item->quantity_requested);
                }
            }

            $srNumber = 'SR-' . now()->format('Ymd') . '-' . str_pad(
                SalesReport::whereDate('created_at', today())->count() + 1,
                4, '0', STR_PAD_LEFT
            );

            $sr = SalesReport::create([
                'report_number'  => $srNumber,
                'store_id'       => $reorderRequest->store_id,
                'user_id'        => auth()->id(),
                'period_from'    => $data['delivery_date'],
                'period_to'      => $data['delivery_date'],
                'total_sales'    => $reorderRequest->total_amount,
                'total_quantity' => $reorderRequest->total_quantity,
                'amount_due'     => $reorderRequest->total_amount,
                'amount_paid'    => 0,
                'balance'        => $reorderRequest->total_amount,
                'status'         => 'pending',
                'notes'          => 'Auto-created from ' . $dr->dr_number,
            ]);

            foreach ($reorderRequest->items as $item) {
                SalesReportItem::create([
                    'sales_report_id' => $sr->id,
                    'product_id'      => $item->product_id,
                    'quantity_sold'   => $item->quantity_requested,
                    'unit_price'      => $item->unit_price,
                    'subtotal'        => $item->subtotal,
                ]);
            }

            $reorderRequest->update([
                'status'              => 'approved',
                'approved_at'         => now(),
                'approved_by'         => auth()->id(),
                'delivery_receipt_id' => $dr->id,
            ]);
        });

        return redirect()->route('reorder-requests.show', $reorderRequest)->with('success', 'Approved!');
    }

    public function reject(Request $request, ReorderRequest $reorderRequest)
    {
        if (!$reorderRequest->isPending()) {
            return back()->with('error', 'Already processed.');
        }

        $data = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $reorderRequest->update([
            'status'           => 'rejected',
            'rejected_at'      => now(),
            'rejection_reason' => $data['rejection_reason'],
        ]);

        return redirect()->route('reorder-requests.show', $reorderRequest)->with('success', 'Rejected.');
    }
}