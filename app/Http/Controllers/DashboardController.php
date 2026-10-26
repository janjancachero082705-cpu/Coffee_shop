<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Store;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. DISTRIBUTED (Total Deliveries)
        $distributed = Delivery::sum('total_amount') ?? 0;
        $distributedCount = Delivery::count();

        // 2. SOLD (Sales Reports)
        $sold = 0;
        $recentReports = collect();
        if (class_exists('App\Models\SalesReport') && Schema::hasTable('sales_reports')) {
            $sold = \App\Models\SalesReport::sum('total_sales') ?? 0;
            $recentReports = \App\Models\SalesReport::with('store')->latest()->take(5)->get();
        } else {
            $sold = $distributed; // Fallback
        }

        // 3. COLLECTED (Payments)
        $collected = 0;
        if (class_exists('App\Models\Payment') && Schema::hasTable('payments')) {
            $collected = \App\Models\Payment::sum('amount') ?? 0;
        }

        // 4. OUTSTANDING
        $outstanding = max(0, $sold - $collected);

        // 5. STORE BALANCES (Top 5) - FIXED SQL ERROR
        $storeBalances = Store::withSum('deliveries', 'total_amount')
            ->orderByDesc('deliveries_sum_total_amount')
            ->take(5)
            ->get()
            ->map(function($store) {
                $obj = new \stdClass();
                $obj->store = $store;
                $obj->total_balance = $store->deliveries_sum_total_amount ?? 0;
                return $obj;
            })
            ->filter(fn($sb) => $sb->total_balance > 0) // Filter > 0 sa memorya
            ->values(); // Reset keys

        // 6. RECENT DELIVERIES
        $recentDeliveries = Delivery::with('store')->latest()->take(5)->get();

        // 7. TREND (Last 6 Months)
        $trend = collect();
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthSold = 0;
            
            if (class_exists('App\Models\SalesReport') && Schema::hasTable('sales_reports')) {
                $monthSold = \App\Models\SalesReport::whereYear('created_at', $month->year)->whereMonth('created_at', $month->month)->sum('total_sales') ?? 0;
            } else {
                $monthSold = Delivery::whereYear('created_at', $month->year)->whereMonth('created_at', $month->month)->sum('total_amount') ?? 0;
            }
            
            $trend->push(['label' => $month->format('M'), 'sold' => $monthSold]);
        }

        return view('dashboard.index', compact(
            'distributed', 'distributedCount', 'sold', 'collected', 'outstanding',
            'storeBalances', 'recentReports', 'recentDeliveries', 'trend'
        ));
    }
}