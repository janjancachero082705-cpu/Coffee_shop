<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Store;
use App\Models\Purchase;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\SalesReport;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. DISTRIBUTED & SOLD
        $distributed = Delivery::sum('total_amount') ?? 0;
        $distributedCount = Delivery::count();
        
        $sold = 0;
        if (class_exists('App\Models\SalesReport') && Schema::hasTable('sales_reports')) {
            $sold = SalesReport::sum('total_sales') ?? 0;
        } else {
            $sold = $distributed;
        }

        // 2. COLLECTED (Kwarta nga nakuha)
        $collected = Payment::sum('amount') ?? 0;
        
        // 3. OUTSTANDING (Utang)
        $outstanding = max(0, $sold - $collected);

        // 4. EXPENSES
        $totalExpenses = Expense::sum('amount') ?? 0;

        // 5. PURCHASES (Auto-detect column)
        $purchaseColumn = 'total_cost';
        if (!Schema::hasColumn('purchases', 'total_cost')) {
            if (Schema::hasColumn('purchases', 'total_amount')) $purchaseColumn = 'total_amount';
            elseif (Schema::hasColumn('purchases', 'amount')) $purchaseColumn = 'amount';
            elseif (Schema::hasColumn('purchases', 'total')) $purchaseColumn = 'total';
            else $purchaseColumn = 'id'; 
        }
        $totalPurchases = Purchase::sum($purchaseColumn) ?? 0;

        // 6. NET PROFIT (Collected - Expenses - Purchases)
        $netProfit = $collected - $totalExpenses - $totalPurchases;

        // 7. RECENT ACTIVITIES
        $recentDeliveries = Delivery::with('store')->latest()->take(5)->get();
        $recentExpenses = Expense::latest()->take(5)->get();

        // 8. TREND (Last 6 Months)
        $trend = collect();
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthSold = Delivery::whereYear('created_at', $month->year)->whereMonth('created_at', $month->month)->sum('total_amount') ?? 0;
            $trend->push(['label' => $month->format('M'), 'sold' => $monthSold]);
        }

        // 9. BREAKDOWNS (para sa Cash Flow chips)
        $top = function ($rows, $max = 5) {
            $rows = collect($rows)->filter(fn($r) => $r['value'] > 0)->sortByDesc('value')->values();
            if ($rows->count() > $max) {
                $rest = $rows->slice($max)->sum('value');
                $rows = $rows->take($max)->push(['label' => 'Others', 'value' => $rest]);
            }
            return $rows->values()->all();
        };
        $expItems = $top(Expense::selectRaw('category, SUM(amount) as total')->groupBy('category')->get()
            ->map(fn($r) => ['label' => $r->category ?: 'Uncategorized', 'value' => (float) $r->total])->all());
        $purItems = $top(Purchase::selectRaw('supplier_name, SUM(' . $purchaseColumn . ') as total')->groupBy('supplier_name')->get()
            ->map(fn($r) => ['label' => $r->supplier_name ?: 'Unknown', 'value' => (float) $r->total])->all());
        $payRows = Payment::selectRaw('store_id, SUM(amount) as total')->groupBy('store_id')->get();
        $storeNames = Store::whereIn('id', $payRows->pluck('store_id'))->pluck('store_name', 'id');
        $cashItems = $top($payRows->map(fn($r) => ['label' => $storeNames[$r->store_id] ?? 'Unknown', 'value' => (float) $r->total])->all());
        
        $breakdown = ['exp' => $expItems, 'pur' => $purItems, 'cash' => $cashItems];

        return view('dashboard.index', compact(
            'distributed', 'distributedCount', 'sold', 'collected', 'outstanding', 
            'totalExpenses', 'totalPurchases', 'netProfit', 'recentDeliveries', 
            'recentExpenses', 'trend', 'breakdown'
        ));
    }

    public function checkUpdates()
    {
        $lastUpdate = max(
            Purchase::max('updated_at') ?? '0',
            Expense::max('updated_at') ?? '0',
            Payment::max('updated_at') ?? '0',
            Delivery::max('updated_at') ?? '0'
        );
        return response()->json(['last_update' => $lastUpdate]);
    }
    
    public function checkGlobalUpdates()
    {
        $tables = ['deliveries', 'purchases', 'expenses', 'payments', 'products', 'product_variants', 'stores'];
        $latest = '0';
        
        foreach ($tables as $table) {
            if (\Illuminate\Support\Facades\Schema::hasTable($table)) {
                $max = \Illuminate\Support\Facades\DB::table($table)->max('updated_at');
                if ($max > $latest) $latest = $max;
            }
        }
        
        return response()->json(['latest' => $latest]);
    }
}