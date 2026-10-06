<?php

namespace App\Http\Controllers;

use App\Models\SalesReport;
use App\Models\SalesReportItem;
use App\Models\Store;
use App\Models\StoreInventory;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesReportController extends Controller
{
    public function index(Request $request)
    {
        $query = SalesReport::with('store');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('report_number', 'like', "%{$s}%")
                  ->orWhereHas('store', fn($sq) => $sq->where('store_name', 'like', "%{$s}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('store')) {
            $query->where('store_id', $request->store);
        }

        $reports = $query->latest()->paginate(15);
        $stores = Store::orderBy('store_name')->get();

        // ═══ Stats ═══
        $stats = [
            'total'         => SalesReport::count(),
            'pending'       => SalesReport::where('status', 'pending')->count(),
            'verified'      => SalesReport::where('status', 'verified')->count(),
            'paid'          => SalesReport::where('status', 'paid')->count(),
            'total_sales'   => (float) SalesReport::sum('total_sales'),
            'pending_due'   => (float) SalesReport::whereIn('status', ['pending', 'verified'])->sum('amount_due'),
            'this_month'    => (float) SalesReport::where('created_at', '>=', now()->startOfMonth())->sum('total_sales'),
        ];

        return view('reports.consignment.index', compact('reports', 'stores', 'stats'));
    }

    public function create(Request $request)
    {
        $stores = Store::where('status', 'active')->orderBy('store_name')->get();
        $selectedStore = $request->get('store_id');

        $inventory = collect();
        if ($selectedStore) {
            $inventory = StoreInventory::with('product')
                ->where('store_id', $selectedStore)
                ->where('quantity_on_hand', '>', 0)
                ->get();
        }

        return view('reports.consignment.create', compact('stores', 'selectedStore', 'inventory'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'store_id'    => 'required|exists:stores,id',
            'period_from' => 'required|date',
            'period_to'   => 'required|date|after_or_equal:period_from',
            'notes'       => 'nullable|string',
            'items'       => 'required|array|min:1',
            'items.*.product_id'    => 'required|exists:products,id',
            'items.*.quantity_sold' => 'required|integer|min:0',
            'items.*.unit_price'    => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($data) {
            $reportNumber = 'SR-' . now()->format('Ymd') . '-' . str_pad(
                SalesReport::whereDate('created_at', today())->count() + 1,
                4, '0', STR_PAD_LEFT
            );

            $totalSales = 0;
            $totalQty = 0;
            foreach ($data['items'] as $item) {
                $totalSales += $item['quantity_sold'] * $item['unit_price'];
                $totalQty += $item['quantity_sold'];
            }

            $report = SalesReport::create([
                'report_number'  => $reportNumber,
                'store_id'       => $data['store_id'],
                'user_id'        => auth()->id(),
                'period_from'    => $data['period_from'],
                'period_to'      => $data['period_to'],
                'total_sales'    => $totalSales,
                'total_quantity' => $totalQty,
                'amount_due'     => $totalSales,
                'status'         => 'pending',
                'notes'          => $data['notes'] ?? null,
            ]);

            foreach ($data['items'] as $item) {
                if ($item['quantity_sold'] <= 0) continue;

                SalesReportItem::create([
                    'sales_report_id' => $report->id,
                    'product_id'      => $item['product_id'],
                    'quantity_sold'   => $item['quantity_sold'],
                    'unit_price'      => $item['unit_price'],
                    'subtotal'        => $item['quantity_sold'] * $item['unit_price'],
                ]);

                $inv = StoreInventory::where('store_id', $data['store_id'])
                    ->where('product_id', $item['product_id'])->first();
                if ($inv) {
                    $inv->increment('quantity_sold', $item['quantity_sold']);
                    $inv->decrement('quantity_on_hand', $item['quantity_sold']);
                }
            }
        });

        return redirect()->route('consignment.reports.index')->with('success', 'Sales report created.');
    }

    public function show(SalesReport $report)
    {
        $report->load('store', 'items.product', 'user');
        return view('reports.consignment.show', compact('report'));
    }
}