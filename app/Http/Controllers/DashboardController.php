<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\Product;
use App\Models\DeliveryReceipt;
use App\Models\SalesReport;
use App\Models\ConsignmentPayment;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'stores_total'      => Store::count(),
            'stores_active'     => Store::where('status', 'active')->count(),
            'products_total'    => Product::count(),
            'products_active'   => Product::where('is_active', true)->count(),
            'low_stock'         => Product::where('is_active', true)
                                          ->whereColumn('stock', '<=', 'reorder_level')
                                          ->count(),
            'deliveries_total'  => DeliveryReceipt::count(),
            'deliveries_today'  => DeliveryReceipt::whereDate('delivery_date', today())->count(),
            'receivables'       => (float) DeliveryReceipt::sum('balance'),
            'overdue'           => (float) DeliveryReceipt::where('status', 'overdue')->sum('balance'),
            'pending_reports'   => SalesReport::where('status', 'pending')->count(),
            'month_sales'       => (float) SalesReport::where('created_at', '>=', now()->startOfMonth())->sum('total_sales'),
            'month_payments'    => (float) ConsignmentPayment::where('payment_date', '>=', now()->startOfMonth())->sum('amount'),

            // NEW - DR-based totals
            'total_delivered'   => (float) DeliveryReceipt::sum('total_amount'),
            'total_paid'        => (float) ConsignmentPayment::sum('amount'),
            'total_outstanding' => (float) DeliveryReceipt::sum('balance'),
        ];

        $recentDeliveries = DeliveryReceipt::with('store')
            ->latest()
            ->take(5)
            ->get();

        $recentPayments = ConsignmentPayment::with('store')
            ->latest()
            ->take(5)
            ->get();

        $topStores = Store::withSum('deliveryReceipts as total_delivered', 'total_amount')
            ->orderByDesc('total_delivered')
            ->take(5)
            ->get();

        return view('dashboard.index', compact(
            'stats',
            'recentDeliveries',
            'recentPayments',
            'topStores'
        ));
    }
}