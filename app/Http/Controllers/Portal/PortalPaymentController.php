<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PortalPaymentController extends Controller
{
    public function index(Request $request)
    {
        $store = Auth::guard('store')->user();
        $payments = $store->consignmentPayments()->latest()->paginate(15);

        $stats = [
            'total'      => $store->consignmentPayments()->count(),
            'all_amount' => (float) $store->consignmentPayments()->sum('amount'),
            'this_month' => (float) $store->consignmentPayments()->where('payment_date', '>=', now()->startOfMonth())->sum('amount'),
        ];

        return view('portal.payments.index', compact('store', 'payments', 'stats'));
    }

    public function show($id)
    {
        $store = Auth::guard('store')->user();
        $payment = $store->consignmentPayments()->findOrFail($id);
        return view('portal.payments.show', compact('store', 'payment'));
    }
}