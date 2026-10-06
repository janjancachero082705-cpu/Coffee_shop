<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PortalDashboardController extends Controller
{
    public function index()
    {
        $store = Auth::guard('store')->user();

        $totalDelivered = (float) $store->deliveryReceipts()->sum('total_amount');
        $totalPaid = (float) $store->consignmentPayments()->sum('amount');
        $balance = (float) $store->salesReports()->sum('balance');

        $drCount = $store->deliveryReceipts()->count();
        $reportCount = $store->salesReports()->count();
        $paymentCount = $store->consignmentPayments()->count();

        $recentDrs = $store->deliveryReceipts()->latest()->take(5)->get();
        $recentPayments = $store->consignmentPayments()->latest()->take(5)->get();
        $recentReports = $store->salesReports()->latest()->take(5)->get();

        $monthDelivered = (float) $store->deliveryReceipts()->where('created_at', '>=', now()->startOfMonth())->sum('total_amount');
        $monthPaid = (float) $store->consignmentPayments()->where('payment_date', '>=', now()->startOfMonth())->sum('amount');

        return view('portal.dashboard', compact(
            'store', 'totalDelivered', 'totalPaid', 'balance',
            'drCount', 'reportCount', 'paymentCount',
            'recentDrs', 'recentPayments', 'recentReports',
            'monthDelivered', 'monthPaid'
        ));
    }
}