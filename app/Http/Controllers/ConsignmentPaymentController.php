<?php

namespace App\Http\Controllers;

use App\Models\ConsignmentPayment;
use App\Models\Store;
use App\Models\DeliveryReceipt;
use App\Models\SalesReport;
use Illuminate\Http\Request;
use App\Events\PaymentRecorded;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Events\PaymentCreated;

class ConsignmentPaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = ConsignmentPayment::with('store');

        // Search filter
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('payment_number', 'like', "%{$s}%")
                  ->orWhere('reference_number', 'like', "%{$s}%")
                  ->orWhereHas('store', fn($sq) => $sq->where('store_name', 'like', "%{$s}%"));
            });
        }

        // Store filter
        if ($request->filled('store')) {
            $query->where('store_id', $request->store);
        }

        // Method filter
        if ($request->filled('method')) {
            $query->where('method', $request->method);
        }

        // Status filter (paid / partial / unlinked)
        if ($request->filled('status')) {
            $status = $request->status;
            if ($status === 'paid') {
                $query->whereNotNull('sales_report_id');
            } elseif ($status === 'partial') {
                // Partial = linked pero naay remaining balance sa related sales report
                $query->whereNotNull('sales_report_id')
                      ->whereHas('salesReport', function ($q) {
                          $q->where('balance', '>', 0);
                      });
            } elseif ($status === 'unlinked') {
                $query->whereNull('sales_report_id')
                      ->whereNull('delivery_receipt_id');
            }
        }

        $payments = $query->latest()->paginate(15)->withQueryString();

        $stores = Store::orderBy('store_name')->get();

        // Stats
        $stats = [
            'total'      => ConsignmentPayment::count(),
            'all_amount' => (float) ConsignmentPayment::sum('amount'),
            'this_month' => (float) ConsignmentPayment::where('payment_date', '>=', now()->startOfMonth())->sum('amount'),
            'today'      => (float) ConsignmentPayment::whereDate('payment_date', today())->sum('amount'),
        ];

        // Tab counts
        $tabCounts = [
            'all'      => ConsignmentPayment::count(),
            'paid'     => ConsignmentPayment::whereNotNull('sales_report_id')->count(),
            'partial'  => ConsignmentPayment::whereNotNull('sales_report_id')
                            ->whereHas('salesReport', function ($q) {
                                $q->where('balance', '>', 0);
                            })->count(),
            'unlinked' => ConsignmentPayment::whereNull('sales_report_id')
                            ->whereNull('delivery_receipt_id')
                            ->count(),
        ];

        return view('payments.consignment.index', compact('payments', 'stores', 'stats', 'tabCounts'));
    }

    public function create(Request $request)
    {
        $stores = Store::where('status', 'active')->orderBy('store_name')->get();
        $selectedStore = $request->get('store_id');

        // Calculate store balance for validation
        $storeBalance = 0;
        if ($selectedStore) {
            $storeBalance = (float) SalesReport::where('store_id', $selectedStore)
                ->whereIn('status', ['pending', 'partial'])
                ->sum('balance');
        }

        return view('payments.consignment.create', compact('stores', 'selectedStore', 'storeBalance'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'store_id'         => 'required|exists:stores,id',
            'amount'           => 'required|numeric|min:0.01',
            'method'           => 'required|in:cash,gcash,bank_transfer,check,maya',
            'reference_number' => 'nullable|string|max:100',
            'payment_date'     => 'required|date',
            'notes'            => 'nullable|string',
        ]);

        // Calculate store's total outstanding
        $storeBalance = (float) SalesReport::where('store_id', $data['store_id'])
            ->whereIn('status', ['pending', 'partial'])
            ->sum('balance');

        if ($storeBalance <= 0) {
            return back()->withInput()->withErrors([
                'amount' => 'This store has no outstanding balance.'
            ]);
        }

        if ($data['amount'] > $storeBalance + 0.01) {
            return back()->withInput()->withErrors([
                'amount' => 'Payment exceeds store balance. Total outstanding: P' . number_format($storeBalance, 2)
            ]);
        }

        DB::transaction(function () use ($data) {
            $paymentNumber = 'PMT-' . now()->format('Ymd') . '-' . str_pad(
                ConsignmentPayment::whereDate('created_at', today())->count() + 1,
                4, '0', STR_PAD_LEFT
            );

            $payment = ConsignmentPayment::create([
                'payment_number'   => $paymentNumber,
                'store_id'         => $data['store_id'],
                'user_id'          => auth()->id(),
                'amount'           => $data['amount'],
                'method'           => $data['method'],
                'reference_number' => $data['reference_number'] ?? null,
                'payment_date'     => $data['payment_date'],
                'notes'            => $data['notes'] ?? null,
            ]);

            // FIFO allocation to Sales Reports (oldest first)
            $remaining = (float) $data['amount'];
            $reports = SalesReport::where('store_id', $data['store_id'])
                ->whereIn('status', ['pending', 'partial'])
                ->where('balance', '>', 0)
                ->orderBy('period_from', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            foreach ($reports as $sr) {
                if ($remaining <= 0) break;

                $applied = min($remaining, (float) $sr->balance);

                $sr->amount_paid = (float) $sr->amount_paid + $applied;
                $sr->balance = max(0, (float) $sr->total_sales - (float) $sr->amount_paid);

                if ($sr->balance <= 0.01) {
                    $sr->status = 'paid';
                    $sr->balance = 0;
                } else {
                    $sr->status = 'partial';
                }
                $sr->save();

                $remaining -= $applied;
            }

            // Broadcast the payment event (inside transaction)
            PaymentCreated::dispatch($payment);
        });

        return redirect()
            ->route('consignment.payments.index')
            ->with('success', 'Payment recorded successfully.');
    }

    public function show(ConsignmentPayment $payment)
    {
        $payment->load('store', 'user');
        return view('payments.consignment.show', compact('payment'));
    }
}