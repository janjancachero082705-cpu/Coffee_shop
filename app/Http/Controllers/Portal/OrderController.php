<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ReorderRequest;
use App\Models\Product;
use App\Models\Category;
use App\Models\DeliveryReceipt;

class OrderController extends Controller
{
    public function index()
    {
        $store = Auth::guard('store')->user();
        $status = request('status', 'all');

        $query = ReorderRequest::where('store_id', $store->id);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $orders = $query->with('items')->orderByDesc('created_at')->paginate(10)->withQueryString();

        return view('portal.orders.index', compact('orders'));
    }

    public function tracking()
    {
        $store = Auth::guard('store')->user();

        $filter = request('filter', 'active');
        $query = ReorderRequest::where('store_id', $store->id)->with('deliveryReceipt');

        if ($filter === 'active') {
            $query->whereIn('status', ['pending', 'approved']);
        } elseif ($filter === 'rejected') {
            $query->where('status', 'rejected');
        }

        $orders = $query->orderByDesc('created_at')->paginate(10)->withQueryString();

        $allCount = ReorderRequest::where('store_id', $store->id)->count();
        $activeCount = ReorderRequest::where('store_id', $store->id)
            ->whereIn('status', ['pending', 'approved'])->count();
        $rejectedCount = ReorderRequest::where('store_id', $store->id)
            ->where('status', 'rejected')->count();

        return view('portal.orders.tracking', compact('orders', 'allCount', 'activeCount', 'rejectedCount'));
    }

    public function create()
    {
        $store = Auth::guard('store')->user();
        $products = Product::where('is_active', true)
            ->orderByDesc('is_featured')
            ->orderBy('name')
            ->get();
        $categories = Category::orderBy('name')->get();

        return view('portal.orders.create', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:1000',
        ]);

        $store = Auth::guard('store')->user();

        $order = ReorderRequest::create([
            'store_id' => $store->id,
            'request_number' => 'REQ-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4)),
            'status' => 'pending',
            'total_amount' => 0,
            'notes' => $request->notes,
        ]);

        $totalAmount = 0;

        foreach ($request->items as $item) {
            $product = Product::find($item['product_id']);
            $subtotal = $product->price * $item['quantity'];

            $order->items()->create([
                'product_id' => $product->id,
                'quantity_requested' => $item['quantity'],
                'unit_price' => $product->price,
                'subtotal' => $subtotal,
            ]);

            $totalAmount += $subtotal;
        }

        $order->update(['total_amount' => $totalAmount]);

        return redirect()->route('portal.orders.show', $order->id)
            ->with('success', 'Order submitted successfully!');
    }

    public function show($id)
    {
        $store = Auth::guard('store')->user();

        $order = ReorderRequest::where('store_id', $store->id)
            ->with(['items.product', 'deliveryReceipt'])
            ->findOrFail($id);

        return view('portal.orders.show', compact('order'));
    }

    public function cancel(Request $request, $id)
    {
        $store = Auth::guard('store')->user();

        $order = ReorderRequest::where('store_id', $store->id)->findOrFail($id);

        if ($order->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending orders can be cancelled.');
        }

        $order->update([
            'status' => 'cancelled',
            'rejection_reason' => $request->cancel_reason ?? 'Cancelled by store',
        ]);

        return redirect()->route('portal.orders.index')
            ->with('success', 'Order cancelled successfully.');
    }
}
