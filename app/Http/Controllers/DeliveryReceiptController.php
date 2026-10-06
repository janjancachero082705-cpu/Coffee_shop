<?php

namespace App\Http\Controllers;

use App\Models\DeliveryReceipt;
use App\Models\DeliveryReceiptItem;
use App\Models\Store;
use App\Models\Product;
use App\Models\StoreInventory;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeliveryReceiptController extends Controller
{
    public function index(Request $request)
    {
        $query = DeliveryReceipt::with('store');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('dr_number', 'like', "%{$s}%")
                  ->orWhereHas('store', fn($sq) => $sq->where('store_name', 'like', "%{$s}%"));
            });
        }

        if ($request->filled('store')) {
            $query->where('store_id', $request->store);
        }

        // Tab status filter
        $tab = $request->get('tab', 'all');
        if ($tab === 'pending') {
            $query->where('status', 'pending');
        } elseif ($tab === 'partial') {
            $query->where('status', 'partial');
        } elseif ($tab === 'paid') {
            $query->where('status', 'paid');
        } elseif ($tab === 'overdue') {
            $query->where('status', 'overdue');
        }

        $deliveries = $query->latest()->paginate(15)->withQueryString();
        $stores = Store::orderBy('store_name')->get();

        // Stats
        $stats = [
            'total'        => DeliveryReceipt::count(),
            'pending'      => DeliveryReceipt::where('status', 'pending')->count(),
            'partial'      => DeliveryReceipt::where('status', 'partial')->count(),
            'paid'         => DeliveryReceipt::where('status', 'paid')->count(),
            'overdue'      => DeliveryReceipt::where('status', 'overdue')->count(),
            'outstanding'  => (float) DeliveryReceipt::sum('balance'),
        ];

        // Tab counts
        $tabCounts = [
            'all'     => $stats['total'],
            'pending' => $stats['pending'],
            'partial' => $stats['partial'],
            'paid'    => $stats['paid'],
            'overdue' => $stats['overdue'],
        ];

        return view('deliveries.index', compact('deliveries', 'stores', 'stats', 'tabCounts', 'tab'));
    }

    public function create(Request $request)
    {
        $stores = Store::where('status', 'active')->orderBy('store_name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $selectedStore = $request->get('store_id');

        return view('deliveries.create', compact('stores', 'products', 'selectedStore'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'store_id'      => 'required|exists:stores,id',
            'delivery_date' => 'required|date',
            'due_date'      => 'nullable|date',
            'notes'         => 'nullable|string',
            'items'         => 'required|array|min:1',
            'items.*.product_id'         => 'required|exists:products,id',
            'items.*.quantity_delivered' => 'required|integer|min:1',
            'items.*.unit_price'         => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($data) {
            $drNumber = 'DR-' . now()->format('Ymd') . '-' . str_pad(
                DeliveryReceipt::whereDate('created_at', today())->count() + 1,
                4, '0', STR_PAD_LEFT
            );

            $total = 0;
            foreach ($data['items'] as $item) {
                $total += $item['quantity_delivered'] * $item['unit_price'];
            }

            $dr = DeliveryReceipt::create([
                'dr_number'     => $drNumber,
                'store_id'      => $data['store_id'],
                'user_id'       => auth()->id(),
                'delivery_date' => $data['delivery_date'],
                'due_date'      => $data['due_date'] ?? null,
                'total_amount'  => $total,
                'amount_paid'   => 0,
                'balance'       => $total,
                'status'        => 'pending',
                'notes'         => $data['notes'] ?? null,
            ]);

            foreach ($data['items'] as $item) {
                DeliveryReceiptItem::create([
                    'delivery_receipt_id' => $dr->id,
                    'product_id'          => $item['product_id'],
                    'quantity_delivered'  => $item['quantity_delivered'],
                    'quantity_sold'       => 0,
                    'quantity_returned'   => 0,
                    'unit_price'          => $item['unit_price'],
                    'subtotal'            => $item['quantity_delivered'] * $item['unit_price'],
                ]);

                // Update store inventory
                $inv = StoreInventory::firstOrCreate(
                    ['store_id' => $dr->store_id, 'product_id' => $item['product_id']],
                    ['quantity_delivered' => 0, 'quantity_sold' => 0, 'quantity_returned' => 0, 'quantity_on_hand' => 0]
                );
                $inv->increment('quantity_delivered', $item['quantity_delivered']);
                $inv->increment('quantity_on_hand', $item['quantity_delivered']);

                // Warehouse inventory transaction
                InventoryTransaction::create([
                    'product_id'    => $item['product_id'],
                    'user_id'       => auth()->id(),
                    'type'          => 'out',
                    'quantity'      => $item['quantity_delivered'],
                    'reference'     => $dr->dr_number,
                    'notes'         => 'Delivered to store: ' . ($dr->store->store_name ?? ''),
                ]);
            }
        });

        return redirect()->route('deliveries.index')->with('success', 'Delivery receipt created.');
    }

    public function show(DeliveryReceipt $delivery)
    {
        $delivery->load('store', 'items.product', 'user');
        return view('deliveries.show', compact('delivery'));
    }

    public function edit(DeliveryReceipt $delivery)
    {
        $stores = Store::orderBy('store_name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();
        return view('deliveries.edit', compact('delivery', 'stores', 'products'));
    }

    public function update(Request $request, DeliveryReceipt $delivery)
    {
        $data = $request->validate([
            'delivery_date' => 'required|date',
            'due_date'      => 'nullable|date',
            'status'        => 'required|in:pending,partial,paid,overdue',
            'notes'         => 'nullable|string',
        ]);

        $delivery->update($data);

        return redirect()->route('deliveries.show', $delivery)->with('success', 'Delivery updated.');
    }

    public function destroy(DeliveryReceipt $delivery)
    {
        DB::transaction(function () use ($delivery) {
            foreach ($delivery->items as $item) {
                $inv = StoreInventory::where('store_id', $delivery->store_id)
                    ->where('product_id', $item->product_id)->first();
                if ($inv) {
                    $inv->decrement('quantity_delivered', $item->quantity_delivered);
                    $inv->decrement('quantity_on_hand', $item->quantity_delivered);
                }
            }
            $delivery->delete();
        });

        return redirect()->route('deliveries.index')->with('success', 'Delivery deleted.');
    }
}