<?php
namespace App\Http\Controllers;
use App\Models\Delivery;
use App\Models\Payment;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesReportController extends Controller
{
    public function index()
    {
        // Overall Stats
        $totalDelivered = Delivery::sum('total_amount');
        $totalCollected = Payment::sum('amount');
        $totalOutstanding = $totalDelivered - $totalCollected;

        // Per Store Breakdown
        $stores = Store::withSum('deliveries', 'total_amount')
                       ->withSum('payments', 'amount')
                       ->get()
                       ->map(function($store) {
                           $delivered = $store->deliveries_sum_total_amount ?? 0;
                           $paid = $store->payments_sum_amount ?? 0;
                           $store->balance = $delivered - $paid;
                           return $store;
                       });

        return view('sales-reports.index', compact('totalDelivered', 'totalCollected', 'totalOutstanding', 'stores'));
    }
}