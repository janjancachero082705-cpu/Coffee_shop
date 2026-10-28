<?php
namespace App\Http\Controllers;
use App\Models\Payment;
use App\Models\PaymentItem;
use App\Models\Store;
use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $payments = Payment::with(['store', 'delivery', 'items.deliveryItem.variant.product'])->latest()->paginate(10);
        $stores = Store::all();
        
        $query = Delivery::with(['store', 'items.variant.product', 'items.paymentItems'])
            ->whereIn('status', ['pending', 'partial']);
        
        if ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        }
        
        $unpaidDeliveries = $query->orderBy('delivery_date', 'asc')->get();

        $deliveriesData = [];
        foreach ($unpaidDeliveries as $d) {
            $items = [];
            foreach ($d->items as $item) {
                $paid = $item->paymentItems->sum('amount_paid');
                $balance = $item->subtotal - $paid;
                $items[] = [
                    'id' => $item->id,
                    'name' => $item->variant->product->name ?? 'Unknown',
                    'variant' => $item->variant->name ?? '',
                    'price' => (float) $item->unit_price,
                    'qty' => (float) $item->quantity,
                    'paid' => (float) $paid,
                    'balance' => (float) $balance
                ];
            }
            $deliveriesData[$d->id] = [
                'dr_number' => $d->dr_number,
                'items' => $items
            ];
        }

        $totalCollected = Payment::sum('amount');
        $totalDelivered = Delivery::sum('total_amount');
        $totalOutstanding = $totalDelivered - $totalCollected;
        
        $selectedStoreId = $request->query('store_id');
        $selectedDeliveryId = $request->query('delivery_id');

        $deliveriesData = (object) $deliveriesData;
        return view('payments.index', compact(
            'payments', 'stores', 'unpaidDeliveries', 'deliveriesData', 'totalCollected', 'totalOutstanding', 'selectedStoreId', 'selectedDeliveryId'
        ));
    }

    // BAG-ONG METHOD PARA SA REALTIME UPDATES
    public function liveData(Request $request)
    {
        $payments = Payment::with(['store', 'delivery', 'items.deliveryItem.variant.product'])
            ->latest()->paginate(10);
            
        $totalCollected = Payment::sum('amount');
        $totalDelivered = Delivery::sum('total_amount');
        $totalOutstanding = $totalDelivered - $totalCollected;

        return response()->json([
            'totalCollected' => '₱' . number_format($totalCollected, 0),
            'totalOutstanding' => '₱' . number_format($totalOutstanding, 0),
            'totalTransactions' => $payments->total(),
            'html' => view('payments._table', compact('payments'))->render()
        ]);
    }

        public function store(Request $request)
    {
        $request->validate([
            'delivery_id' => 'required|exists:deliveries,id',
            'payment_date' => 'required|date',
            'items' => 'required|array',
            'items.*' => 'numeric|min:0|max:999999',
        ]);

        try {
            DB::beginTransaction();

            $totalPayment = 0;
            $itemsToSave = [];

            foreach ($request->items as $itemId => $amount) {
                $amount = (float) $amount;
                if ($amount > 0) {
                    $totalPayment += $amount;
                    $itemsToSave[] = [
                        'delivery_item_id' => $itemId,
                        'amount_paid' => $amount
                    ];
                }
            }

            if ($totalPayment <= 0) {
                if ($request->ajax()) return response()->json(['success' => false, 'message' => 'Please enter a payment amount.'], 422);
                return back()->with('error', 'Please enter a payment amount for at least one item.')->withInput();
            }

            $delivery = Delivery::findOrFail($request->delivery_id);

            $payment = Payment::create([
                'store_id' => $delivery->store_id,
                'delivery_id' => $delivery->id,
                'amount' => $totalPayment,
                'payment_date' => $request->payment_date,
            ]);

            foreach ($itemsToSave as $item) {
                PaymentItem::create([
                    'payment_id' => $payment->id,
                    'delivery_item_id' => $item['delivery_item_id'],
                    'amount_paid' => $item['amount_paid'],
                ]);
            }

            DeliveryController::syncDeliveryStatus($delivery->id);
            DB::commit();

            // KUNG AJAX (Realtime), I-return ang updated data
            if ($request->ajax() || $request->wantsJson()) {
                $payments = Payment::with(['store', 'delivery', 'items.deliveryItem.variant.product'])->latest()->paginate(10);
                
                $query = Delivery::with(['store', 'items.variant.product', 'items.paymentItems'])->whereIn('status', ['pending', 'partial']);
                $unpaidDeliveries = $query->orderBy('delivery_date', 'asc')->get();

                $deliveriesData = [];
                foreach ($unpaidDeliveries as $d) {
                    $items = [];
                    foreach ($d->items as $item) {
                        $paid = $item->paymentItems->sum('amount_paid');
                        $balance = $item->subtotal - $paid;
                        $items[] = [
                            'id' => $item->id, 'name' => $item->variant->product->name ?? 'Unknown',
                            'variant' => $item->variant->name ?? '', 'price' => (float) $item->unit_price,
                            'qty' => (float) $item->quantity, 'paid' => (float) $paid, 'balance' => (float) $balance
                        ];
                    }
                    $deliveriesData[$d->id] = ['dr_number' => $d->dr_number, 'items' => $items];
                }

                $totalCollected = Payment::sum('amount');
                $totalDelivered = Delivery::sum('total_amount');
                $totalOutstanding = max(0, $totalDelivered - $totalCollected);

                return response()->json([
                    'success' => true,
                    'message' => 'Payment recorded successfully!',
                    'html' => view('payments._table', compact('payments'))->render(),
                    'totalCollected' => '₱' . number_format($totalCollected, 0),
                    'totalOutstanding' => '₱' . number_format($totalOutstanding, 0),
                    'totalTransactions' => $payments->total(),
                    'deliveriesData' => $deliveriesData,
                    'unpaidOptions' => $unpaidDeliveries->map(function($d) { return ['id' => $d->id, 'text' => $d->dr_number . ' - ' . ($d->store->store_name ?? '')]; })->toArray()
                ]);
            }

            \App\Models\ActivityLog::record('Payments', 'Recorded', 'Received payment of ₱' . number_format($totalPayment, 2));
            return redirect()->route('payments.index')->with('success', 'Payment recorded successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax()) return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            return back()->with('error', $e->getMessage())->withInput();
        }
    }
}