<?php
namespace App\Http\Controllers;
use App\Models\Delivery;
use App\Models\Payment;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalesReportController extends Controller
{
    public function index(Request $request)
    {
        // Date Range Filter (Default: Current Month)
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        // Overall Stats (Filtered by Date)
        $totalSales = Delivery::whereBetween('delivery_date', [$startDate, $endDate])->sum('total_amount');
        
        // Total Collected (Payments linked to those deliveries)
        $totalCollected = Payment::whereHas('delivery', function($q) use ($startDate, $endDate) {
            $q->whereBetween('delivery_date', [$startDate, $endDate]);
        })->sum('amount');

        $totalOutstanding = $totalSales - $totalCollected;

        // Sales Ledger (List of all deliveries as sales)
        $query = Delivery::with(['store', 'payments'])
            ->withSum('payments', 'amount')
            ->whereBetween('delivery_date', [$startDate, $endDate]);

        // Search Filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('dr_number', 'like', "%$search%")
                  ->orWhereHas('store', function($sq) use ($search) {
                      $sq->where('store_name', 'like', "%$search%");
                  });
            });
        }

        $sales = $query->latest('delivery_date')->paginate(15)->withQueryString();

                
        // Data para sa Pie Chart (Sales by Store)
        $salesByStore = \App\Models\Delivery::select('store_id', \Illuminate\Support\Facades\DB::raw('SUM(total_amount) as total'))
            ->groupBy('store_id')
            ->with('store')
            ->orderByDesc('total')
            ->get();

        $chartLabels = $salesByStore->map(fn($item) => $item->store->store_name ?? 'Unknown')->toArray();
        $chartData = $salesByStore->pluck('total')->toArray();

        return view('sales-reports.index', compact('chartLabels', 'chartData', 'sales', 'totalSales', 'totalCollected', 'totalOutstanding', 'startDate', 'endDate'));
    }
}