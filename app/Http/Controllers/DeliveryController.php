<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\DeliveryItem;
use App\Models\Store;
use App\Models\ProductVariant;
use App\Models\InventoryMovement;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DeliveryController extends Controller
{
    public function index(Request $request)
    {
        $query = Delivery::with('store');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('dr_number', 'like', "%{$search}%")
                  ->orWhereHas('store', function($sq) use ($search) {
                      $sq->where('store_name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('start_date')) {
            $query->whereDate('delivery_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('delivery_date', '<=', $request->end_date);
        }

        $totalCount = (clone $query)->count();
        $totalDistributed = (clone $query)->sum('total_amount') ?? 0;
        
        $deliveryIds = (clone $query)->pluck('id');
        $totalCollected = \App\Models\Payment::whereIn('delivery_id', $deliveryIds)->sum('amount') ?? 0;
        $totalBalance = max(0, $totalDistributed - $totalCollected);

        $deliveries = $query->orderByDesc('delivery_date')->orderByDesc('id')->paginate(15)->appends($request->query());

        return view('deliveries.index', compact('deliveries', 'totalCount', 'totalDistributed', 'totalCollected', 'totalBalance'));
    }

        
    public function create()
    {
        $stores = \App\Models\Store::orderBy('store_name')->get();
        $variants = \App\Models\ProductVariant::with('product')
            ->whereHas('product', function($q) {
                $q->where('is_active', 1);
            })
            ->where('stock', '>', 0)
            ->get();
            
        return view('deliveries.create', compact('stores', 'variants'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'store_id' => 'required|exists:stores,id',
            'delivery_date' => 'required|date',
            'products' => 'required|array|min:1',
            'products.*.variant_id' => 'required|exists:product_variants,id',
            'products.*.quantity' => 'required|numeric|min:0.01',
            'products.*.price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0'
        ]);

        try {
            DB::beginTransaction();

            $drNumber = 'DR-' . date('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(6));
            $totalAmount = 0;
            $itemsToSave = [];

            foreach ($request->products as $item) {
                $variant = ProductVariant::findOrFail($item['variant_id']);
                
                if ($variant->stock < $item['quantity']) {
                    throw new \Exception("Dili igo ang stock para sa " . ($variant->product->name ?? 'Item'));
                }

                $subtotal = $item['quantity'] * $item['price'];
                $totalAmount += $subtotal;

                $itemsToSave[] = [
                    'product_id' => $variant->product_id,
                    'product_variant_id' => $variant->id,
                    'quantity' => $item['quantity'],
                    'weight' => $variant->weight ?? 0,
                    'unit_price' => $item['price'],
                    'subtotal' => $subtotal
                ];

                // Deduct Stock
                $variant->decrement('stock', $item['quantity']);
                
                // Log Inventory Movement
                InventoryMovement::create([
                    'product_variant_id' => $variant->id,
                    'type' => 'out',
                    'quantity' => $item['quantity'],
                    'notes' => 'Delivered via ' . $drNumber
                ]);
            }

            $discount = $request->discount ?? 0;

            $delivery = Delivery::create([
                'dr_number' => $drNumber,
                'store_id' => $request->store_id,
                'delivery_date' => $request->delivery_date,
                'total_amount' => $totalAmount,
                'status' => 'pending'
            ]);

            foreach ($itemsToSave as $item) {
                $delivery->items()->create($item);
            }

            // Log System Transaction
            ActivityLog::record('Deliveries', 'Created', 'Created delivery receipt for ' . ($delivery->store->store_name ?? 'Store'), $delivery);

            DB::commit();
            return redirect()->route('deliveries.index')->with('success', 'Delivery created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(Delivery $delivery)
    {
        $delivery->load('items.variant.product', 'store', 'payments');
        return view('deliveries.show', compact('delivery'));
    }

    public function showStore(Store $store)
    {
        $deliveries = Delivery::with(['items.variant.product'])
            ->where('store_id', $store->id)
            ->withSum('payments', 'amount')
            ->orderByDesc('delivery_date')
            ->paginate(10);

        $totalAmount = $deliveries->sum('total_amount');
        $totalPaid = $deliveries->sum('payments_sum_amount');
        
        $stats = [
            'total_sales' => $totalAmount,
            'total_paid' => $totalPaid,
            'outstanding' => max(0, $totalAmount - $totalPaid)
        ];

        return view('deliveries.store-profile', compact('store', 'deliveries', 'stats', 'totalAmount', 'totalPaid'));
    }

    public function destroy(Delivery $delivery)
    {
        try {
            DB::beginTransaction();

            // Restore stock
            foreach ($delivery->items as $item) {
                if ($item->variant) {
                    $item->variant->increment('stock', $item->quantity);
                    
                    InventoryMovement::create([
                        'product_variant_id' => $item->variant->id,
                        'type' => 'in',
                        'quantity' => $item->quantity,
                        'notes' => 'Restored from deleted delivery ' . $delivery->dr_number
                    ]);
                }
            }

            ActivityLog::record('Deliveries', 'Deleted', 'Deleted delivery receipt: ' . $delivery->dr_number, $delivery);
            
            $delivery->delete();

            DB::commit();
            return redirect()->route('deliveries.index')->with('success', 'Delivery deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error deleting delivery: ' . $e->getMessage());
        }
    }
}