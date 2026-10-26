<?php
namespace App\Http\Controllers;
use App\Models\Delivery;
use App\Models\Store;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Purchase;
use App\Models\Expense;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. TOTAL SALES (Pera na nisulod)
        $totalSales = Delivery::sum('total_amount');
        $totalDeliveries = Delivery::count();

        // 2. TOTAL PURCHASES (Gasto sa Raw Materials)
        $totalPurchases = Purchase::sum('total_cost');

        // 3. TOTAL EXPENSES (Ibang gasto: gas, labor, etc.)
        $totalExpenses = Expense::sum('amount');

        // 4. TOTAL OUTFLOW (Kombinasyon sa Gasto)
        $totalOutflow = $totalPurchases + $totalExpenses;

        // 5. NET PROFIT (Sales - Outflow)
        $netProfit = $totalSales - $totalOutflow;

        // 6. Iba pang Stats
        $totalStores = Store::count();
        $totalProducts = Product::count();
        
        $recentDeliveries = Delivery::with('store')->latest()->take(5)->get();
        $recentExpenses = Expense::latest()->take(5)->get();
        
        // Chart Data (Sample Trend - Pwede nimo ni i-update puhon para dynamic per month)
        $trend = collect([
            ['label' => 'Week 1', 'sold' => $totalSales * 0.2],
            ['label' => 'Week 2', 'sold' => $totalSales * 0.3],
            ['label' => 'Week 3', 'sold' => $totalSales * 0.1],
            ['label' => 'Week 4', 'sold' => $totalSales * 0.4],
        ]);

        $lowStockVariants = ProductVariant::with('product')->where('stock', '<=', 5)->take(5)->get();

        return view('dashboard', compact(
            'totalSales', 'totalPurchases', 'totalExpenses', 'totalOutflow', 'netProfit',
            'totalDeliveries', 'totalStores', 'totalProducts', 
            'recentDeliveries', 'recentExpenses', 'trend', 'lowStockVariants'
        ));
    }
}