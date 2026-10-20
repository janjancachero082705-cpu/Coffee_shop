<?php
namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ConsignmentPayment;
use App\Models\PaymentSetting;
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
            'this_month' => ConsignmentPayment::where('store_id', $store->id)
                                ->whereMonth('payment_date', now()->month)
                                ->whereYear('payment_date', now()->year)
                                ->sum('amount'),
            'pending'    => ConsignmentPayment::where('store_id', $store->id)->where('verification_status', 'pending')->count(),
            'verified'   => ConsignmentPayment::where('store_id', $store->id)->where('verification_status', 'verified')->count(),
            'rejected'   => ConsignmentPayment::where('store_id', $store->id)->where('verification_status', 'rejected')->count(),
        ];

        $unpaidDRs = DeliveryReceipt::where('store_id', $store->id)
                        ->whereColumn('total_amount', '>', 'amount_paid')
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('portal.payments.index', compact('store', 'payments', 'stats', 'unpaidDRs'));
    }

    public function create(Request $request)
    {
        $store = Auth::guard('store')->user();
        $drId  = $request->input('delivery_receipt_id') ?? $request->input('dr');
        $paymentSetting = PaymentSetting::first();

        if ($drId) {
            $linkedDR = DeliveryReceipt::where('store_id', $store->id)->find($drId);
            if ($linkedDR) {
                $displayBalance = (float) max(0, $linkedDR->balance);
                $presetAmount   = $displayBalance;
                $totalBalance   = $displayBalance;
                return view('portal.payments.create', compact('store', 'linkedDR', 'displayBalance', 'presetAmount', 'totalBalance', 'paymentSetting'));
            }
        }

        $totalBalance = (float) DeliveryReceipt::where('store_id', $store->id)->sum('balance');
        return view('portal.payments.create', compact('store', 'totalBalance', 'paymentSetting'));
    }

    public function store(Request $request)
    {
        $store = Auth::guard('store')->user();
        $drId  = $request->input('delivery_receipt_id') ?? $request->input('dr');

        $request->validate([
            'amount'            => 'required|numeric|min:1',
            'method'            => 'required|string',
            'payment_date'      => 'required|date',
            'reference_number'  => 'required_if:method,gcash|nullable|string',
            'proof_of_payment'  => 'required_if:method,gcash|nullable|image|max:10240',
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
}