<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PortalSalesReportController extends Controller
{
    public function index(Request $request)
    {
        $store = Auth::guard('store')->user();
        $query = $store->salesReports()->with('items');

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
        $report = $store->salesReports()->with('items.product')->findOrFail($id);
        return view('portal.reports.show', compact('store', 'report'));
    }
}