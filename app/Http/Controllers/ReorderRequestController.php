<?php

namespace App\Http\Controllers;

use App\Traits\NotifiesStore;

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
    use NotifiesStore;

    public function index(Request $request)
    {
        $tab = $request->get('status', 'all');
        $viewMode = $request->get('view', 'store');
        $storeFilter = $request->get('store', 'all');

        // ─── DATE FILTER ───
        $dateFilter = $request->get('date', 'today');
        $dateFrom = null;
        $dateTo = null;
        $dateLabel = 'All Dates';

        if ($dateFilter === 'today') {
            $dateFrom = today()->toDateString();
            $dateTo = $dateFrom;
            $dateLabel = 'Today · ' . today()->format('M d, Y');
        } elseif ($dateFilter === 'tomorrow') {
            $dateFrom = today()->addDay()->toDateString();
            $dateTo = $dateFrom;
            $dateLabel = 'Tomorrow · ' . today()->addDay()->format('M d, Y');
        } elseif ($dateFilter === 'week') {
            $dateFrom = today()->startOfWeek()->toDateString();
            $dateTo = today()->endOfWeek()->toDateString();
            $dateLabel = 'This Week';
        } elseif ($dateFilter && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateFilter)) {
            $dateFrom = $dateFilter;
            $dateTo = $dateFilter;
            try {
                $dateLabel = \Carbon\Carbon::parse($dateFilter)->format('l, M d, Y');
            } catch (\Exception $e) {
                $dateFrom = today()->toDateString();
                $dateTo = $dateFrom;
                $dateLabel = 'Today · ' . today()->format('M d, Y');
            }
        }

        // ─── QUERY ───
        $query = ReorderRequest::with(['store', 'items.product']);

        if ($tab === 'pending') {
            $query->whereRaw('LOWER(status) = ?', ['pending']);
        } elseif ($tab === 'approved') {
            $query->whereRaw('LOWER(status) = ?', ['approved']);
        } elseif ($tab === 'rejected') {
            $query->whereRaw('LOWER(status) = ?', ['rejected']);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('request_number', 'like', "%{$s}%")
                  ->orWhereHas('store', fn($sq) => $sq->where('store_name', 'like', "%{$s}%"));
            });
        }

        if ($dateFrom) {
            $query->where(function ($q) use ($dateFrom, $dateTo) {
                $q->whereDate('requested_date', '>=', $dateFrom)
                  ->whereDate('requested_date', '<=', $dateTo)
                  ->orWhereHas('deliveryReceipt', function ($dq) use ($dateFrom, $dateTo) {
                      $dq->whereDate('delivery_date', '>=', $dateFrom)
                         ->whereDate('delivery_date', '<=', $dateTo);
                  });
            });
        }

        // Store filter
        if ($storeFilter && $storeFilter !== 'all') {
            $query->where('store_id', $storeFilter);
        }

        $allRequests = $query->latest()->get();
        $requests = $allRequests;

        // ─── STORE GROUPS ───
        $storeGroups = $allRequests->groupBy('store_id')->map(function ($group) {
            $store = $group->first()->store;
            return [
                'store' => $store,
                'requests' => $group,
                'count' => $group->count(),
                'pending' => $group->where('status', 'pending')->count(),
                'approved' => $group->where('status', 'approved')->count(),
                'rejected' => $group->where('status', 'rejected')->count(),
                'total_amount' => $group->sum('total_amount'),
                'latest' => $group->max('created_at'),
            ];
        })->sortByDesc('latest')->values();

        // ─── AGGREGATED PRODUCTS (with store breakdown) ───
        $flatItems = collect();

        foreach ($allRequests as $order) {
            foreach ($order->items as $item) {
                $flatItems->push([
                    'product_id' => $item->product_id,
                    'name'       => $item->product->name ?? ('Product #' . $item->product_id),
                    'sku'        => $item->product->sku ?? null,
                    'unit'       => $item->product->unit ?? null,
                    'qty'        => $item->quantity_requested ?? $item->quantity ?? 0,
                    'store_id'   => $order->store_id,
                    'store_name' => $order->store->store_name ?? 'Unknown Store',
                    'store_code' => $order->store->code ?? null,
                ]);
            }
        }

        $aggregatedProducts = $flatItems
            ->groupBy('product_id')
            ->map(function ($items) {
                $first = $items->first();
                $stores = $items->groupBy('store_id')->map(function ($storeItems) {
                    $s = $storeItems->first();
                    return [
                        'store_id'   => $s['store_id'],
                        'store_name' => $s['store_name'],
                        'store_code' => $s['store_code'],
                        'qty'        => $storeItems->sum('qty'),
                    ];
                })->sortByDesc('qty')->values();

                return [
                    'product_id' => $first['product_id'],
                    'name'       => $first['name'],
                    'sku'        => $first['sku'],
                    'unit'       => $first['unit'],
                    'total_qty'  => $items->sum('qty'),
                    'stores'     => $stores,
                ];
            })
            ->sortByDesc('total_qty')
            ->values();

        // ─── STATS ───
        $stats = [
            'total'    => ReorderRequest::count(),
            'pending'  => ReorderRequest::whereRaw('LOWER(status) = ?', ['pending'])->count(),
            'approved' => ReorderRequest::whereRaw('LOWER(status) = ?', ['approved'])->count(),
            'rejected' => ReorderRequest::whereRaw('LOWER(status) = ?', ['rejected'])->count(),
        ];

        $tabCounts = [
            'all'      => $stats['total'],
            'pending'  => $stats['pending'],
            'approved' => $stats['approved'],
            'rejected' => $stats['rejected'],
        ];

        return view('reorder-requests.index', compact(
            'requests',
            'allRequests',
            'storeGroups',
            'stats',
            'tabCounts',
            'tab',
            'viewMode',
            'dateFilter',
            'dateFrom',
            'dateTo',
            'dateLabel',
            'aggregatedProducts'
        ));
    }
    public function show(ReorderRequest $reorderRequest)
    {
        $reorderRequest->load(['store', 'items.product', 'deliveryReceipt', 'approver']);
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

            $srNumber = SalesReport::generateNumber();

            $sr = SalesReport::create([
                'report_number'       => $srNumber,
                'delivery_receipt_id' => $dr->id,
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

            // === NOTIFY CUSTOMER (bell + popup) ===
            $this->notifyStore(
            $reorderRequest->store_id,
            'reorder_approved',
            'Order Approved!',
            "Ang imong order {$reorderRequest->request_number} gi-approve na sa admin. Click para sa details.",
            [
                'reorder_id' => $reorderRequest->id,
                'request_number' => $reorderRequest->request_number,
                'dr_number' => $dr->dr_number ?? null,
                'delivery_id' => $dr->id ?? null,
            ]
        );
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

        // Notify customer
        $this->notifyStore(
            $reorderRequest->store_id,
            'reorder_rejected',
            'Order Rejected',
            "Ang imong order {$reorderRequest->request_number} gi-reject sa admin. Click para sa details.",
            [
                'reorder_id' => $reorderRequest->id,
                'request_number' => $reorderRequest->request_number,
            ]
        );

        return redirect()->route('reorder-requests.show', $reorderRequest)->with('success', 'Rejected.');
    }
}
