<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\ReorderRequest;
use App\Models\ReorderRequestItem;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Events\OrderPlaced;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PortalOrderController extends Controller
{
    public function index(Request $request)
    {
        $store = Auth::guard('store')->user();
        $query = $store->reorderRequests();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total'    => $store->reorderRequests()->count(),
            'pending'  => $store->reorderRequests()->where('status', 'pending')->count(),
            'approved' => $store->reorderRequests()->where('status', 'approved')->count(),
            'rejected' => $store->reorderRequests()->where('status', 'rejected')->count(),
        ];

        return view('portal.orders.index', compact('store', 'orders', 'stats'));
    }

    public function create()
    {
        $store = Auth::guard('store')->user();
        $products = Product::where('is_active', true)->orderBy('name')->get();
        return view('portal.orders.create', compact('store', 'products'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'notes' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
        ]);

        $store = Auth::guard('store')->user();
        $items = [];
        $totalAmount = 0;
        $totalQty = 0;

        foreach ($data['items'] as $item) {
            $product = Product::findOrFail($item['product_id']);
            $subtotal = $product->price * $item['quantity'];
            $items[] = [
                'product_id'         => $product->id,
                'quantity_requested' => $item['quantity'],
                'unit_price'         => $product->price,
                'subtotal'           => $subtotal,
            ];
            $totalAmount += $subtotal;
            $totalQty += $item['quantity'];
        }

        // Capture ang created request para magamit after transaction
        $reorderRequest = null;

        DB::transaction(function () use ($store, $items, $totalAmount, $totalQty, $data, &$reorderRequest) {
            $reorderRequest = ReorderRequest::create([
                'request_number' => ReorderRequest::generateNumber(),
                'store_id'       => $store->id,
                'status'         => 'pending',
                'total_amount'   => $totalAmount,
                'total_quantity' => $totalQty,
                'requested_date' => now()->toDateString(),
                'notes'          => $data['notes'] ?? null,
            ]);

            foreach ($items as $item) {
                ReorderRequestItem::create([
                    'reorder_request_id' => $reorderRequest->id,
                    'product_id'         => $item['product_id'],
                    'quantity_requested' => $item['quantity_requested'],
                    'unit_price'         => $item['unit_price'],
                    'subtotal'           => $item['subtotal'],
                ]);
            }
        });

        // ===== FIRE REAL-TIME EVENT =====
        if ($reorderRequest) {
            try {
                OrderPlaced::dispatch($reorderRequest);
                \Log::info('[Reverb] OrderPlaced dispatched for #' . $reorderRequest->request_number);
            } catch (\Throwable $e) {
                \Log::warning('[Reverb] Failed to dispatch OrderPlaced: ' . $e->getMessage());
            }
        }

        return redirect()->route('portal.orders.index')->with('success', 'Order submitted!');
    }

    public function show($id)
    {
        $store = Auth::guard('store')->user();
        $order = $store->reorderRequests()->with('items.product', 'deliveryReceipt')->findOrFail($id);
        return view('portal.orders.show', compact('store', 'order'));
    }

    public function cancel($id)
    {
        $store = Auth::guard('store')->user();
        $order = $store->reorderRequests()->findOrFail($id);

        if (!$order->isPending()) {
            return back()->with('error', 'Cannot cancel.');
        }

        $order->update(['status' => 'cancelled']);
        return back()->with('success', 'Order cancelled.');
    }
}