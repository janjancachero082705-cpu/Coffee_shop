<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ConsignmentPayment;

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

    public function create(Request $request)
    {
        $store = Auth::guard('store')->user();

        // Handle linked DR (from Sales report "Pay" button)
        $linkedDR = null;
        if ($request->filled('delivery_receipt_id')) {
            // Remove `customer_confirmed` filter — find by ID regardless
            $linkedDR = $store->deliveryReceipts()
                ->find($request->input('delivery_receipt_id'));
        }

        // === COMPUTE BALANCE ===
        if ($linkedDR) {
            // Specific DR balance (para sa "Bayad Full ₱145" gikan reports)
            $displayBalance = (float) max(0, $linkedDR->balance);
        } else {
            // Total unpaid across ALL DRs
            $displayBalance = (float) $store->deliveryReceipts()
                ->where('customer_confirmed', true)
                ->get()
                ->sum(fn($dr) => max(0, $dr->balance));
        }

        // Pre-fill amount — PRIORITY: URL amount > displayBalance
        $presetAmount = $request->input('amount');
        if ($presetAmount === null || $presetAmount === '') {
            $presetAmount = number_format($displayBalance, 2, '.', '');
        }

        // Legacy support
        $totalBalance = $displayBalance;

        return view('portal.payments.create', compact('store', 'totalBalance', 'displayBalance', 'linkedDR', 'presetAmount'));
    }

    public function store(Request $request)
    {
        $store = Auth::guard('store')->user();

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'method' => 'required|in:cash,gcash,maya,bank_transfer,check,online',
            'reference_number' => 'nullable|string|max:100',
            'payment_date' => 'required|date',
            'delivery_receipt_id' => 'nullable|integer|exists:delivery_receipts,id',
            'notes' => 'nullable|string|max:500',
        ]);

        // === OVERPAY CHECK ===
        $linkedDR = null;
        if (!empty($validated['delivery_receipt_id'])) {
            $linkedDR = $store->deliveryReceipts()->find($validated['delivery_receipt_id']);
        }

        if ($linkedDR) {
            $allowedMax = (float) max(0, $linkedDR->balance);
        } else {
            $allowedMax = (float) $store->deliveryReceipts()
                ->where('customer_confirmed', true)
                ->get()
                ->sum(fn($dr) => max(0, $dr->balance));
        }

        if ($validated['amount'] > $allowedMax + 0.01) {
            return back()
                ->withErrors(['amount' => 'Sobra ang gibayad. Maximum: ₱' . number_format($allowedMax, 2)])
                ->withInput();
        }

        if (in_array($validated['method'], ['gcash', 'maya', 'bank_transfer', 'online'])
            && empty($validated['reference_number'])) {
            return back()
                ->withErrors(['reference_number' => 'Reference number is required for online payments.'])
                ->withInput();
        }

        // === AUTO-LINK TO OLDEST UNPAID DR ===
        // Priority 1: Use linked DR (from Sales "Pay" button)
        $targetDR = null;
        if (!empty($validated['delivery_receipt_id'])) {
            $targetDR = $store->deliveryReceipts()
                ->where('customer_confirmed', true)
                ->find($validated['delivery_receipt_id']);
        }

        // Priority 2: Auto-pick oldest unpaid DR
        if (!$targetDR) {
            $targetDR = $store->deliveryReceipts()
                ->where('customer_confirmed', true)
                ->whereRaw('total_amount > (SELECT COALESCE(SUM(amount),0) FROM consignment_payments WHERE delivery_receipt_id = delivery_receipts.id)')
                ->orderBy('delivery_date', 'asc')
                ->first();
        }

        $payment = ConsignmentPayment::create([
            'payment_number' => ConsignmentPayment::generateNumber(),
            'store_id' => $store->id,
            'delivery_receipt_id' => $targetDR?->id,
            'user_id' => null,
            'amount' => $validated['amount'],
            'method' => $validated['method'],
            'reference_number' => $validated['reference_number'] ?? null,
            'payment_date' => $validated['payment_date'],
            'notes' => $validated['notes'] ?? null,
            'is_read_by_admin' => false,
        ]);

        if ($targetDR) {
            $this->recalculateDR($targetDR);
            $this->syncSalesReport($targetDR);
        }

        return redirect()
            ->route('portal.payments.show', $payment->id)
            ->with('success', 'Payment recorded successfully!');
    }

    protected function recalculateDR($dr): void
    {
        $totalPaid = $dr->payments()->sum('amount');
        $balance = max(0, $dr->total_amount - $totalPaid);

        $newStatus = $dr->status;
        if (!in_array($dr->status, ['out_for_delivery', 'delivered'])) {
            if ($balance <= 0.01) {
                $newStatus = 'paid';
            } elseif ($totalPaid > 0) {
                $newStatus = 'partial';
            } else {
                $newStatus = 'pending';
            }
        }

        $dr->update([
            'amount_paid' => $totalPaid,
            'balance' => $balance,
            'status' => $newStatus,
        ]);
    }

    protected function syncSalesReport($dr): void
    {
        try {
            $sr = \App\Models\SalesReport::where('delivery_receipt_id', $dr->id)->first();
            if (!$sr) {
                \Log::info('syncSalesReport: walay SalesReport para sa DR ' . $dr->id);
                return;
            }

            // Sync sa DR state (total paid na across all payments)
            $sr->amount_paid = $dr->amount_paid;
            $sr->recalculate();

            \Log::info('syncSalesReport OK: SR ' . $sr->report_number . ' | Paid: ' . $sr->amount_paid . ' | Status: ' . $sr->status);
        } catch (\Exception $e) {
            \Log::warning('syncSalesReport failed: ' . $e->getMessage());
        }
    }
}
