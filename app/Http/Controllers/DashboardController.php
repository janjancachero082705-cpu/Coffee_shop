<?php
namespace App\Http\Controllers;
use App\Models\Delivery;
use App\Models\Store;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Calculate Start and End of Current Month
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // 1. Metrics (Base sa atong bag-ong tables)
        $distributed = Delivery::whereBetween('delivery_date', [$startOfMonth, $endOfMonth])->sum('total_amount');
        $distributedCount = Delivery::whereBetween('delivery_date', [$startOfMonth, $endOfMonth])->count();
        
        // Dummy data para sa mga module nga wala pa na-build
        $sold = 0; 
        $collected = 0;
        $outstanding = 0;

        $totalStores = Store::count();
        $totalProducts = Product::count();

        // 2. Recent Deliveries
        $recentDeliveries = Delivery::with('store')->latest()->take(5)->get();

        // 3. Store Balances (Dummy data kay wala pa tay sales module)
        $storeBalances = collect([]); 

        // 4. Trend Data (Sample data para sa Chart)
        $trend = collect([
            ['label' => 'Week 1', 'sold' => 5000],
            ['label' => 'Week 2', 'sold' => 8000],
            ['label' => 'Week 3', 'sold' => 12000],
            ['label' => 'Week 4', 'sold' => 15000],
        ]);

        // 5. Low Stock Variants
        $lowStockVariants = ProductVariant::with('product')->where('stock', '<=', 5)->take(5)->get();

        return view('dashboard', compact(
            'distributed', 'distributedCount', 'sold', 'collected', 'outstanding',
            'totalStores', 'totalProducts', 'recentDeliveries', 'storeBalances', 
            'trend', 'lowStockVariants'
        ));
    }
}