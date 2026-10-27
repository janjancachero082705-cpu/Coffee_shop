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
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        // I-cache ang tibuok dashboard data sulod sa 5 minutos (300 seconds)
        $dashboardData = Cache::remember('dashboard_metrics', 300, function () {
            
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

            return [
                'distributed' => $distributed,
                'distributedCount' => $distributedCount,
                'sold' => $sold,
                'collected' => $collected,
                'outstanding' => $outstanding,
                'totalExpenses' => $totalExpenses,
                'totalPurchases' => $totalPurchases,
                'netProfit' => $netProfit,
                'recentDeliveries' => $recentDeliveries,
                'recentExpenses' => $recentExpenses,
                'trend' => $trend,
            ];
        });

        return view('dashboard.index', $dashboardData);
    }
}