<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ConsignmentPayment;
use App\Models\PaymentSetting;
use App\Models\EWallet;
use App\Models\DeliveryReceipt;

class PortalPaymentController extends Controller
{
    public function index(Request $request)
    {
        $store = Auth::guard('store')->user();
        $payments = ConsignmentPayment::where('store_id', $store->id)->latest()->paginate(15);
        $stats = [
            'all_amount' => ConsignmentPayment::where('store_id', $store->id)->sum('amount'),
            'total'      => ConsignmentPayment::where('store_id', $store->id)->count(),
            'this_month' => ConsignmentPayment::where('store_id', $store->id)->whereMonth('payment_date', now()->month)->whereYear('payment_date', now()->year)->sum('amount'),
        ];
        $salesReports = \App\Models\SalesReport::where('store_id', $store->id)->latest()->paginate(15, ['*'], 'sales_page');
        $reportCount = \App\Models\SalesReport::where('store_id', $store->id)->count();
        $reportTotal = \App\Models\SalesReport::where('store_id', $store->id)->sum('total_sales');
        
        $pendingDeliveries = DeliveryReceipt::where('store_id', $store->id)->whereRaw('total_amount > amount_paid')->orderByDesc('created_at')->get();
        $pendingCount = $pendingDeliveries->count();
        $pendingTotal = $pendingDeliveries->sum(fn($d) => max(0, (float)$d->total_amount - (float)$d->amount_paid));

        return view('portal.payments.index', compact('store', 'payments', 'stats', 'salesReports', 'reportCount', 'reportTotal', 'pendingDeliveries', 'pendingCount', 'pendingTotal'));
    }

    public function create(Request $request)
    {
        $store = Auth::guard('store')->user();
        $drId  = $request->input('delivery_receipt_id') ?? $request->input('dr');
        $paymentSetting = PaymentSetting::first();
        
        // KUNIN LAHAT NG ACTIVE E-WALLETS MULA SA ADMIN
        $eWallets = EWallet::where('is_active', true)->get();

        if ($drId) {
            $linkedDR = DeliveryReceipt::where('store_id', $store->id)->find($drId);
            if ($linkedDR) {
                $displayBalance = (float) max(0, $linkedDR->balance);
                $presetAmount   = $displayBalance;
                $totalBalance   = $displayBalance;
                return view('portal.payments.create', compact('store', 'linkedDR', 'displayBalance', 'presetAmount', 'totalBalance', 'paymentSetting', 'eWallets'));
            }
        }

        $totalBalance = (float) DeliveryReceipt::where('store_id', $store->id)->sum('balance');
        return view('portal.payments.create', compact('store', 'totalBalance', 'paymentSetting', 'eWallets'));
    }

    public function store(Request $request)
    {
        $store = Auth::guard('store')->user();
        $drId  = $request->input('delivery_receipt_id') ?? $request->input('dr');

        $request->validate([
            'amount'            => 'required|numeric|min:1',
            'method'            => 'required|string',
            'payment_date'      => 'required|date',
            'reference_number'  => 'required_if:method,gcash,maya,grabpay,shopeepay|nullable|string',
            'proof_of_payment'  => 'required_if:method,gcash,maya,grabpay,shopeepay|nullable|image|max:10240',
        ]);

        $proofPath = null;
        if ($request->hasFile('proof_of_payment')) {
            $proofPath = $request->file('proof_of_payment')->store('receipts', 'public');
        }

        ConsignmentPayment::create([
            'store_id'           => $store->id,
            'delivery_receipt_id'=> $drId,
            'payment_number'     => 'PMT-' . date('Ymd') . '-' . strtoupper(uniqid()),
            'amount'             => $request->amount,
            'method'             => $request->method,
            'reference_number'   => $request->reference_number,
            'proof_of_payment'   => $proofPath,
            'payment_date'       => $request->payment_date,
            'notes'              => $request->notes,
            'verification_status'=> 'pending',
        ]);

        return redirect()->route('portal.payments.index')->with('success', 'Payment submitted! Waiting for admin approval.');
    }

    public function show($id)
    {
        $store = Auth::guard('store')->user();
        $payment = ConsignmentPayment::where('store_id', $store->id)->findOrFail($id);
        return view('portal.payments.show', compact('payment'));
    }
}