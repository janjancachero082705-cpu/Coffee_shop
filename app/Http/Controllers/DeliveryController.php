<?php
namespace App\Http\Controllers;
use App\Models\Delivery;
use App\Models\DeliveryItem;
use App\Models\Store;
use App\Models\ProductVariant;
use App\Models\Payment;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DeliveryController extends Controller
{
    public static function syncDeliveryStatus($delivery_id)
    {
        $delivery = Delivery::find($delivery_id);
        if (!$delivery) return;

        $totalPaid = Payment::where('delivery_id', $delivery_id)->sum('amount');

        if ($totalPaid >= $delivery->total_amount) {
            $delivery->update(['status' => 'completed']);
        } elseif ($totalPaid > 0) {
            $delivery->update(['status' => 'partial']);
        } else {
            $delivery->update(['status' => 'pending']);
        }
    }

    public function index(Request $request)
    {
        $query = Delivery::with('store');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('dr_number', 'like', "%$search%")
                  ->orWhereHas('store', function($sq) use ($search) {
                      $sq->where('store_name', 'like', "%$search%");
                  });
            });
        }

        $deliveries = $query->latest('delivery_date')->paginate(15)->withQueryString();

        // Stats for Cards
        $stats = [
            'total' => Delivery::count(),
            // GIKAN SA 'pending' lang, gidugangan natog 'partial' para mabilang sila
            'pending' => Delivery::whereIn('status', ['pending', 'partial'])->count(), 
            'completed' => Delivery::where('status', 'completed')->count(),
            'total_value' => Delivery::sum('total_amount'),
        ];

        return view('deliveries.index', compact('deliveries', 'stats'));
    }

    public function showStore(Store $store)
    {
        $deliveries = $store->deliveries()->with(['items.variant.product', 'payments'])
            ->withSum('payments', 'amount')
            ->latest()
            ->paginate(10);
            
        $totalAmount = $store->deliveries()->sum('total_amount');
        $totalPaid = $store->payments()->sum('amount');
        $totalItems = $store->deliveries()->count();
        
        return view('deliveries.store', compact('store', 'deliveries', 'totalAmount', 'totalPaid', 'totalItems'));
    }

    public function create()
    {
        $stores = Store::all();
        $variants = ProductVariant::with('product')->whereHas('product', function($q) {
            $q->where('is_active', 1);
        })->get();
        
        return view('deliveries.create', compact('stores', 'variants'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'store_id' => 'required|exists:stores,id',
            'delivery_date' => 'required|date',
            'products' => 'required|array|min:1',
            'products.*.variant_id' => 'required|exists:product_variants,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.price' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $drNumber = 'DR-' . Carbon::now()->format('Ymd') . '-' . str_pad(Delivery::whereDate('created_at', Carbon::today())->count() + 1, 3, '0', STR_PAD_LEFT);
            $totalAmount = 0;

            $delivery = Delivery::create([
                'dr_number' => $drNumber,
                'store_id' => $request->store_id,
                'delivery_date' => $request->delivery_date,
                'total_amount' => 0,
                'status' => 'pending'
            ]);

            foreach ($request->products as $item) {
                $variant = ProductVariant::lockForUpdate()->findOrFail($item['variant_id']);
                
                if ($variant->stock < $item['quantity']) {
                    throw new \Exception("Kulang ang stock para sa {$variant->product->name} ({$variant->name}). Available: {$variant->stock}");
                }

                $subtotal = $item['quantity'] * $item['price']; 
                $totalAmount += $subtotal;
                $totalWeight = $item['quantity'] * $variant->weight;

                DeliveryItem::create([
                    'delivery_id' => $delivery->id,
                    'product_id' => $variant->product_id,
                    'product_variant_id' => $variant->id,
                    'quantity' => $item['quantity'],
                    'weight' => $totalWeight,
                    'unit_price' => $item['price'],
                    'subtotal' => $subtotal,
                ]);

                $variant->decrement('stock', $item['quantity']);
            }

            $delivery->update(['total_amount' => $totalAmount]);
            DB::commit();

            return redirect()->route('deliveries.store-profile', $request->store_id)->with('success', 'Delivery created successfully!');
        } catch (\Exception $e) {
            DB::RollBack();
            return back()->with('error', $e->getMessage())->withInput();
        }
    }
}