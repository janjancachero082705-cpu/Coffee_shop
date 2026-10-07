<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\ConsignmentPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PortalSalesReportController extends Controller
{
    public function index(Request $request)
    {
        $store = Auth::guard('store')->user();
        $query = $store->salesReports()->with(['items', 'deliveryReceipt']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $reports = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total'   => $store->salesReports()->count(),
            'pending' => $store->salesReports()->where('status', 'pending')->count(),
            'partial' => $store->salesReports()->where('status', 'partial')->count(),
            'paid'    => $store->salesReports()->where('status', 'paid')->count(),
        ];

        return view('portal.reports.index', compact('store', 'reports', 'stats'));
    }

    public function show($id)
    {
        $store = Auth::guard('store')->user();
        $report = $store->salesReports()
            ->with(['items.product', 'deliveryReceipt'])
            ->findOrFail($id);

        // Kun linked sa DR, kuhaon tanan payments sa maong DR
        $linkedPayments = collect();
        if ($report->delivery_receipt_id) {
            $linkedPayments = ConsignmentPayment::where('delivery_receipt_id', $report->delivery_receipt_id)
                ->orderBy('payment_date', 'desc')
                ->get();
        }

        return view('portal.reports.show', compact('store', 'report', 'linkedPayments'));
    }
}