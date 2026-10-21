<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConsignmentPayment;
use App\Models\Store;
use App\Models\DeliveryReceipt;
use App\Models\SalesReport;
use App\Models\StoreNotification;
use Illuminate\Support\Facades\Auth;

class ConsignmentPaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = ConsignmentPayment::with('store')->latest();

        if ($request->filled('search')) {
            $query->where('payment_number', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        }

        $tab = $request->input('tab', 'all');
        if ($tab === 'pending') { $query->where('verification_status', 'pending'); }
        elseif ($tab === 'verified') { $query->where('verification_status', 'verified'); }
        elseif ($tab === 'rejected') { $query->where('verification_status', 'rejected'); }

        $payments = $query->paginate(15);
        $stores = Store::orderBy('store_name')->get();

        $stats = [
            'total' => ConsignmentPayment::count(),
            'verified_amount' => ConsignmentPayment::where('verification_status', 'verified')->sum('amount'),
            'pending' => ConsignmentPayment::where('verification_status', 'pending')->count(),
            'verified' => ConsignmentPayment::where('verification_status', 'verified')->count(),
            'rejected' => ConsignmentPayment::where('verification_status', 'rejected')->count(),
        ];

        $tabCounts = [
            'all' => ConsignmentPayment::count(),
            'paid' => ConsignmentPayment::whereNotNull('sales_report_id')->count(),
            'partial' => ConsignmentPayment::whereNotNull('sales_report_id')->whereHas('salesReport', function ($q) { $q->where('balance', '>', 0); })->count(),
            'unlinked' => ConsignmentPayment::whereNull('sales_report_id')->whereNull('delivery_receipt_id')->count(),
        ];

        return view('payments.consignment.index', compact('payments', 'stores', 'stats', 'tabCounts'));
    }

    public function show($id)
    {
        $payment = ConsignmentPayment::with(['store', 'deliveryReceipt', 'salesReport'])->findOrFail($id);
        return view('payments.consignment.show', compact('payment'));
    }

    public function approve(Request $request, $id)
    {
        $payment = ConsignmentPayment::findOrFail($id);
        
        $payment->update([
            'verification_status' => 'verified',
            'verified_by' => Auth::id(),
            'verified_at' => now(),
        ]);

        // Send Notification to Store (Mobile App)
        StoreNotification::create([
            'store_id' => $payment->store_id,
            'type' => 'payment',
            'title' => 'Payment Approved ✅',
            'message' => 'Your payment of ₱' . number_format($payment->amount, 2) . ' has been verified successfully.',
            'link' => route('portal.payments.show', $payment->id),
        ]);

        $this->recalculateBalances($payment);

        return redirect()->back()->with('success', 'Payment approved successfully.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $payment = ConsignmentPayment::findOrFail($id);
        
        $payment->update([
            'verification_status' => 'rejected',
            'rejection_reason' => $request->reason,
            'verified_by' => Auth::id(),
            'verified_at' => now(),
        ]);

        // Revert Delivery Receipt (para mawala ang "Confirmed" sa mobile)
        if ($payment->delivery_receipt_id) {
            $dr = DeliveryReceipt::find($payment->delivery_receipt_id);
            if ($dr) {
                $dr->update([
                    'customer_confirmed' => false,
                    'out_for_delivery_at' => null,
                ]);
            }
        }

        // Send Notification to Store (Mobile App)
        StoreNotification::create([
            'store_id' => $payment->store_id,
            'type' => 'payment',
            'title' => 'Payment Rejected ❌',
            'message' => 'Your payment of ₱' . number_format($payment->amount, 2) . ' was rejected. Reason: ' . $request->reason,
            'link' => route('portal.payments.show', $payment->id),
        ]);

        $this->recalculateBalances($payment);

        return redirect()->back()->with('success', 'Payment rejected successfully.');
    }

    protected function recalculateBalances($payment)
    {
        // Recalculate Delivery Receipt
        if ($payment->delivery_receipt_id) {
            $dr = DeliveryReceipt::find($payment->delivery_receipt_id);
            if ($dr) {
                $verifiedPaid = (float) ConsignmentPayment::where('delivery_receipt_id', $dr->id)->where('verification_status', 'verified')->sum('amount');
                $dr->update([
                    'amount_paid' => $verifiedPaid,
                    'balance' => max(0, $dr->total_amount - $verifiedPaid),
                ]);
            }
        }

        // Recalculate Sales Report
        if ($payment->sales_report_id) {
            $sr = SalesReport::find($payment->sales_report_id);
            if ($sr) {
                $verifiedPaid = (float) ConsignmentPayment::where('sales_report_id', $sr->id)->where('verification_status', 'verified')->sum('amount');
                $sr->update([
                    'amount_paid' => $verifiedPaid,
                    'balance' => max(0, $sr->total_amount - $verifiedPaid),
                ]);
            }
        }
    }
}