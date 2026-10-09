<?php

namespace App\Http\Controllers;

use App\Models\SalesReport;
use App\Models\SalesReportItem;
use App\Models\Store;
use App\Models\StoreInventory;
use App\Models\Product;
use App\Models\ConsignmentPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Events\SalesReportCreated;

class SalesReportController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'all');
        $stores = Store::orderBy('store_name')->get();

        $query = SalesReport::with('store', 'items');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('report_number', 'like', "%{$s}%")
                  ->orWhereHas('store', fn($sq) => $sq->where('store_name', 'like', "%{$s}%"));
            });
        }

        if ($request->filled('store')) {
            $query->where('store_id', $request->store);
        }

        // IMPORTANT: Sales report only shows when there's a payment
        if ($tab === 'all') {
            // All = with payments only
            $query->where('amount_paid', '>', 0);
        } elseif ($tab === 'pending') {
            // Pending = walay bayad pa
            $query->where('amount_paid', 0);
        } elseif ($tab === 'partial') {
            $query->where('status', 'partial');
        } elseif ($tab === 'paid') {
            $query->where('status', 'paid');
        }

        $reports = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total'        => SalesReport::where('amount_paid', '>', 0)->count(),
            'pending'      => SalesReport::where('amount_paid', 0)->count(),
            'partial'      => SalesReport::where('status', 'partial')->count(),
            'paid'         => SalesReport::where('status', 'paid')->count(),
            'total_sales'  => (float) SalesReport::where('amount_paid', '>', 0)->sum('total_sales'),
            'total_paid'   => (float) SalesReport::sum('amount_paid'),
            'total_balance'=> (float) SalesReport::sum('balance'),
            'total_items'  => (int) SalesReport::sum('total_quantity'),
            'this_month'   => (float) SalesReport::where('amount_paid', '>', 0)->where('created_at', '>=', now()->startOfMonth())->sum('total_sales'),
            'today'        => (float) SalesReport::where('amount_paid', '>', 0)->whereDate('created_at', today())->sum('total_sales'),
        ];

        $tabCounts = [
            'all'      => $stats['total'],
            'pending'  => $stats['pending'],
            'partial'  => $stats['partial'],
            'paid'     => $stats['paid'],
        ];

        return view('reports.consignment.index', compact('reports', 'stores', 'stats', 'tabCounts', 'tab'));
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

        $products = Product::where('is_active', true)->orderBy('name')->get();

        return view('reports.consignment.create', compact('stores', 'selectedStore', 'inventory', 'products'));
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
            'items.*.quantity_sold' => 'required|integer|min:1',
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
                'amount_paid'    => 0,
                'balance'        => $totalSales,
                'status'         => 'pending',
                'notes'          => $data['notes'] ?? null,
            ]);

            foreach ($data['items'] as $item) {
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

        SalesReportCreated::dispatch($report);

        return redirect()->route('consignment.reports.index')->with('success', 'Sales report created successfully.');
    }

    public function show(SalesReport $report)
    {
        $report->load('store', 'items.product', 'user');

        $storePayments = ConsignmentPayment::where('store_id', $report->store_id)
            ->orderBy('payment_date', 'desc')
            ->take(10)
            ->get();

        return view('reports.consignment.show', compact('report', 'storePayments'));
    }

    /**
     * AJAX partial for the Sales Report modal.
     */
    public function modal(SalesReport $report)
    {
        $report->load(['store', 'user', 'items.product', 'payments']);
        return view('consignment.sales-reports._modal', compact('report'));
    }

    /**
     * AJAX partial — store report list inside modal.
     */
    public function storeModal(Store $store)
    {
        $reports = $store->salesReports()->latest()->get();
        return view('consignment.sales-reports._store-modal', compact('store', 'reports'));
    }

    /**
     * Show all reports for a specific store — dedicated page.
     */
    public function storeReports(Store $store)
    {
        $reports = $store->salesReports()->latest()->get();

        $totalSales = (float) $reports->sum('total_sales');
        $totalPaid  = (float) $reports->sum('amount_paid');
        $totalBal   = (float) $reports->sum('balance');
        $paidPct    = $totalSales > 0 ? min(100, ($totalPaid / $totalSales) * 100) : 0;

        return view('reports.consignment.store', compact(
            'store', 'reports', 'totalSales', 'totalPaid', 'totalBal', 'paidPct'
        ));
    }
}
