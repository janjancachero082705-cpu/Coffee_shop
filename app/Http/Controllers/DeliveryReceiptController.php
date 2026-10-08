<?php

namespace App\Http\Controllers;

use App\Models\DeliveryReceipt;
use App\Models\DeliveryReceiptItem;
use App\Models\Store;
use App\Models\Product;
use App\Models\StoreInventory;
use App\Models\InventoryTransaction;
use App\Models\SalesReport;
use App\Events\DeliveryCreated;
use App\Models\SalesReportItem;
use App\Traits\NotifiesStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeliveryReceiptController extends Controller
{
    use NotifiesStore;

    public function index(Request $request)
    {
        $query = DeliveryReceipt::with('store', 'items');

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

        $tab = $request->get('tab', 'all');
        if ($tab === 'pending') {
            $query->where('status', 'pending');
        } elseif ($tab === 'partial') {
            $query->where('status', 'partial');
        } elseif ($tab === 'paid') {
            $query->where('status', 'paid');
        } elseif ($tab === 'out_for_delivery') {
            $query->whereNotNull('out_for_delivery_at')
                  ->where('customer_confirmed', false);
        } elseif ($tab === 'confirmed') {
            $query->where('customer_confirmed', true);
        }

        $deliveries = $query->latest()->paginate(15)->withQueryString();
        $stores = Store::orderBy('store_name')->get();

        $stats = [
            'total'        => DeliveryReceipt::count(),
            'pending'      => DeliveryReceipt::where('status', 'pending')->count(),
            'partial'      => DeliveryReceipt::where('status', 'partial')->count(),
            'paid'         => DeliveryReceipt::where('status', 'paid')->count(),
            'out_delivery' => DeliveryReceipt::whereNotNull('out_for_delivery_at')
                                ->where('customer_confirmed', false)->count(),
            'confirmed'    => DeliveryReceipt::where('customer_confirmed', true)->count(),
            'outstanding'  => (float) DeliveryReceipt::sum('balance'),
        ];

        $tabCounts = [
            'all'              => $stats['total'],
            'pending'          => $stats['pending'],
            'partial'          => $stats['partial'],
            'paid'             => $stats['paid'],
            'out_for_delivery' => $stats['out_delivery'],
            'confirmed'        => $stats['confirmed'],
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

            // Auto-create Sales Report from DR
            $reportNumber = 'SR-' . $dr->delivery_date->format('Ymd') . '-' . str_pad(
                SalesReport::whereDate('created_at', today())->count() + 1,
                4, '0', STR_PAD_LEFT
            );

            $sr = SalesReport::create([
                'report_number'  => $reportNumber,
                'store_id'       => $dr->store_id,
                'user_id'        => auth()->id(),
                'period_from'    => $dr->delivery_date,
                'period_to'      => $dr->delivery_date,
                'total_sales'    => $total,
                'total_quantity' => collect($data['items'])->sum('quantity_delivered'),
                'amount_due'     => $total,
                'amount_paid'    => 0,
                'balance'        => $total,
                'status'         => 'pending',
                'notes'          => 'Auto-created from ' . $dr->dr_number,
            ]);

            foreach ($data['items'] as $item) {
                SalesReportItem::create([
                    'sales_report_id' => $sr->id,
                    'product_id'      => $item['product_id'],
                    'quantity_sold'   => $item['quantity_delivered'],
                    'unit_price'      => $item['unit_price'],
                    'subtotal'        => $item['quantity_delivered'] * $item['unit_price'],
                ]);
            }
        });

        DeliveryCreated::dispatch($dr);

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
            'status'        => 'required|in:pending,partial,paid',
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

    /**
     * Mark delivery as "out for delivery"
     */
    public function markOutForDelivery($id, \Illuminate\Http\Request $request)
    {
        $delivery = \App\Models\DeliveryReceipt::findOrFail($id);

        if ($delivery->customer_confirmed) {
            return back()->with('error', 'Cannot mark out for delivery - already confirmed.');
        }

        // Save optional ship note to delivery notes
        $shipNote = trim((string) $request->input('ship_note', ''));
        if ($shipNote !== '') {
            $existingNotes = trim((string) ($delivery->notes ?? ''));
            $delivery->notes = $existingNotes === ''
                ? "Ship note: {$shipNote}"
                : $existingNotes . "\n\nShip note: {$shipNote}";
            $delivery->save();
        }

        $delivery->markOutForDelivery();

        // Notification message — apil ang ship note kung naa
        $message = "Ang imong delivery {$delivery->dr_number} gi-ship na. Click para i-confirm kung nadawat na.";
        if ($shipNote !== '') {
            $message .= " Note: {$shipNote}";
        }

        // Notify customer via portal bell + popup
        $this->notifyStore(
            $delivery->store_id,
            'delivery_out',
            'Out for Delivery',
            $message,
            [
                'delivery_id' => $delivery->id,
                'dr_number' => $delivery->dr_number,
                'store_id' => $delivery->store_id,
                'ship_note' => $shipNote,
            ]
        );

        // Fire real-time event
        try {
            event(new \App\Events\DeliveryOutForDelivery($delivery));
        } catch (\Throwable $e) {
            \Log::warning('Delivery event failed: ' . $e->getMessage());
        }

        return back()->with('success', 'Delivery marked as out for delivery. Customer notified.');
    }
}
