<?php
namespace App\Http\Controllers;
use App\Models\Payment;
use App\Models\Store;
use App\Models\Delivery;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $payments = Payment::with(['store', 'delivery'])->latest()->paginate(10);
        $stores = Store::all();
        
        // Kunin ang mga deliveries na may utang pa (pending/partial)
        $query = Delivery::with('store')->whereIn('status', ['pending', 'partial']);
        
        if ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        }
        
        $unpaidDeliveries = $query->orderBy('delivery_date', 'asc')->get();

        $totalCollected = Payment::sum('amount');
        $totalDelivered = Delivery::sum('total_amount');
        $totalOutstanding = $totalDelivered - $totalCollected;
        
        // Preselect if redirected from Store Profile
        $selectedStoreId = $request->query('store_id');
        $selectedDeliveryId = $request->query('delivery_id');

        return view('payments.index', compact(
            'payments', 'stores', 'unpaidDeliveries', 'totalCollected', 'totalOutstanding', 'selectedStoreId', 'selectedDeliveryId'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'store_id' => 'required|exists:stores,id',
            'delivery_id' => 'required|exists:deliveries,id',
            'amount' => 'required|numeric|min:1',
            'payment_date' => 'required|date',
        ]);

        Payment::create($request->all());
        
        // Sync specific delivery status
        \App\Http\Controllers\DeliveryController::syncDeliveryStatus($request->delivery_id);

        return redirect()->route('payments.index')->with('success', 'Payment recorded for Order!');
    }
}