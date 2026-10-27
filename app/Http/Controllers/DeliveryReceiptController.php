<?php
namespace App\Http\Controllers;

use App\Models\DeliveryReceipt;
use App\Models\DeliveryReceiptItem;
use App\Models\Store;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\InventoryService;
use App\Services\StoreInventoryService;
use App\Http\Controllers\Concerns\Filterable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeliveryReceiptController extends Controller
{
    use Filterable;

    public function __construct(
        private InventoryService $inventory,
        private StoreInventoryService $storeInv
    ) {}

    public function index(Request $request)
    {
        $query = DeliveryReceipt::with('store');
        $query = $this->applySearch($query, $request, ['dr_number','notes']);
        if ($request->filled('store')) $query->where('store_id', $request->store);
        if ($request->filled('status')) $query->where('status', $request->status);
        $query = $this->applyDateRange($query, $request, 'delivery_date');

        $deliveries = $query->orderBy('delivery_date','desc')->paginate(20)->withQueryString();
        $stores = Store::orderBy('store_name')->get();
        return view('deliveries.index', compact('deliveries','stores'));
    }

    public function create(Request $request)
    {
        $stores = Store::where('status','active')->orderBy('store_name')->get();
        $products = Product::with(['variants' => fn($q) => $q->where('is_active', true)])
            ->where('is_active', true)
            ->where('is_raw_material', false)
            ->orderBy('name')->get();
        $selectedStoreId = $request->store_id;
        return view('deliveries.create', compact('stores','products','selectedStoreId'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'store_id'=>'required|exists:stores,id',
            'delivery_date'=>'required|date',
            'notes'=>'nullable|string',
            'discount_percent'=>'nullable|numeric|min:0|max:100',
            'items'=>'required|array|min:1',
            'items.*.product_id'=>'required|exists:products,id',
            'items.*.product_variant_id'=>'nullable|exists:product_variants,id',
            'items.*.quantity_delivered'=>'required|numeric|min:0.01',
            'items.*.unit_price'=>'required|numeric|min:0',
        ]);

        return DB::transaction(function () use ($data) {
            $subtotal = 0;
            foreach ($data['items'] as $item) {
                $subtotal += $item['quantity_delivered'] * $item['unit_price'];
            }
            $dp = $data['discount_percent'] ?? 0;
            $da = $subtotal * ($dp / 100);
            $total = $subtotal - $da;
            $drNum = 'DR-' . now()->format('Ymd') . '-' . now()->format('His') . rand(10, 99);

            $dr = DeliveryReceipt::create([
                'dr_number'=>$drNum,'store_id'=>$data['store_id'],
                'user_id'=>auth()->id(),'delivery_date'=>$data['delivery_date'],
                'subtotal'=>$subtotal,'discount_amount'=>$da,
                'discount_type'=>'percent','discount_percent'=>$dp,
                'total_amount'=>$total,'amount_paid'=>0,'amount_sold'=>0,
                'balance'=>$total,'status'=>'delivered','is_consignment'=>true,
                'customer_confirmed'=>1,'confirmed_at'=>now(),'delivered_at'=>now(),
                'notes'=>$data['notes'] ?? null,
            ]);

            $store = Store::find($data['store_id']);

            foreach ($data['items'] as $item) {
                $product = Product::find($item['product_id']);
                $variant = !empty($item['product_variant_id'])
                    ? ProductVariant::find($item['product_variant_id'])
                    : null;

                $cost = $variant ? $variant->cost_price : $product->cost_price;

                DeliveryReceiptItem::create([
                    'delivery_receipt_id'=>$dr->id,
                    'product_id'=>$product->id,
                    'product_variant_id'=>$variant?->id,
                    'quantity_delivered'=>$item['quantity_delivered'],
                    'quantity_sold'=>0,'quantity_returned'=>0,
                    'unit'=>$product->unit_type,
                    'unit_price'=>$item['unit_price'],
                    'cost_price'=>$cost,
                    'subtotal'=>$item['quantity_delivered'] * $item['unit_price'],
                ]);

                // Deduct from WAREHOUSE
                if ($variant) {
                    $variant->decrement('stock', $item['quantity_delivered']);
                } else {
                    $this->inventory->removeStock($product, $item['quantity_delivered'],
                        ['type'=>'delivery','id'=>$dr->id],
                        "Distributed via {$drNum}", $data['delivery_date']);
                }

                // ADD to STORE inventory
                $this->storeInv->distribute($store, $product, $variant, $item['quantity_delivered'], $data['delivery_date']);
            }

            return redirect()->route('deliveries.show', $dr)->with('success', "Distributed to {$store->store_name}!");
        });
    }

    public function show(DeliveryReceipt $delivery)
    {
        $delivery->load(['store','items.product','items.variant','user']);
        return view('deliveries.show', compact('delivery'));
    }

    public function edit(DeliveryReceipt $delivery) { return redirect()->route('deliveries.show', $delivery); }
    public function update(Request $request, DeliveryReceipt $delivery) { return back(); }

    public function destroy(DeliveryReceipt $delivery)
    {
        DB::transaction(function () use ($delivery) {
            $store = $delivery->store;
            foreach ($delivery->items as $item) {
                $product = $item->product;
                $variant = $item->variant;
                if ($variant) {
                    $variant->increment('stock', $item->quantity_delivered);
                } else if ($product) {
                    $this->inventory->addStock($product, $item->quantity_delivered, null,
                        ['type'=>'return','id'=>$delivery->id],
                        "Cancelled {$delivery->dr_number}", now()->toDateString());
                }
                // Reduce from store
                $this->storeInv->recordSale($store, $product, $variant, 0, $item->quantity_delivered, now()->toDateString());
            }
            $delivery->delete();
        });
        return redirect()->route('deliveries.index')->with('success', 'Distribution cancelled & stock restored.');
    }
}