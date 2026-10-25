<?php
namespace App\Http\Controllers;
use App\Models\Delivery;
use App\Models\DeliveryItem;
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
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // 1. SALES (Pera na pumasok)
        $totalSales = Delivery::whereBetween('delivery_date', [$startOfMonth, $endOfMonth])->sum('total_amount');
        $distributedCount = Delivery::whereBetween('delivery_date', [$startOfMonth, $endOfMonth])->count();
        
        // 2. PURCHASES (Pera na ginastos sa Cherries / Raw Materials)
        $totalPurchases = Purchase::whereBetween('purchase_date', [$startOfMonth, $endOfMonth])->sum('total_cost');

        // 3. EXPENSES (Ibang gastos tulad ng gas, labor)
        $totalExpenses = Expense::whereBetween('expense_date', [$startOfMonth, $endOfMonth])->sum('amount');

        // 4. PROFIT CALCULATION
        // Profit = Sales - (Purchases + Expenses)
        $totalOutflow = $totalPurchases + $totalExpenses;
        $netProfit = $totalSales - $totalOutflow;

        $totalStores = Store::count();
        $totalProducts = Product::count();

        $recentDeliveries = Delivery::with('store')->latest()->take(5)->get();
        
        // Trend Data (Sample)
        $trend = collect([
            ['label' => 'Week 1', 'sold' => $totalSales * 0.2],
            ['label' => 'Week 2', 'sold' => $totalSales * 0.3],
            ['label' => 'Week 3', 'sold' => $totalSales * 0.1],
            ['label' => 'Week 4', 'sold' => $totalSales * 0.4],
        ]);

        $lowStockVariants = ProductVariant::with('product')->where('stock', '<=', 5)->take(5)->get();

        return view('dashboard', compact(
            'totalSales', 'totalPurchases', 'totalExpenses', 'netProfit',
            'distributedCount', 'totalStores', 'totalProducts', 
            'recentDeliveries', 'trend', 'lowStockVariants'
        ));
    }
}