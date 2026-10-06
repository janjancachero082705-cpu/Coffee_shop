<?php

namespace App\Http\Controllers;

use App\Models\ConsignmentPayment;
use App\Models\Store;
use App\Models\DeliveryReceipt;
use App\Models\SalesReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConsignmentPaymentController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('payment_status', 'all');
        $stores = Store::orderBy('store_name')->get();

        // ═══ PENDING TAB — show Delivery Receipts with pending status ═══
        if ($tab === 'pending') {
            $query = DeliveryReceipt::with('store')
                ->where('status', 'pending')
                ->where('balance', '>', 0);

            if ($request->filled('search')) {
                $s = $request->search;
                $query->where(function ($q) use ($s) {
                    $q->where('dr_number', 'like', "%{$s}%")
                      ->orWhereHas('store', fn($sq) => $sq->where('store_name', 'like', "%{$s}%"));
                });
            }
            if ($request->filled('store')) {
                $query->where('store_id', $request->store);
            }

            $pendings = $query->latest()->paginate(15)->withQueryString();

            // Stats
            $stats = [
                'total'      => ConsignmentPayment::count(),
                'all_amount' => (float) ConsignmentPayment::sum('amount'),
                'this_month' => (float) ConsignmentPayment::where('payment_date', '>=', now()->startOfMonth())->sum('amount'),
                'today'      => (float) ConsignmentPayment::whereDate('payment_date', today())->sum('amount'),
            ];

            $tabCounts = [
                'all'      => ConsignmentPayment::count(),
                'pending'  => DeliveryReceipt::where('status', 'pending')->where('balance', '>', 0)->count(),
                'partial'  => DeliveryReceipt::where('status', 'partial')->count(),
                'full'     => DeliveryReceipt::where('status', 'paid')->count(),
                'unlinked' => ConsignmentPayment::whereNull('delivery_receipt_id')->whereNull('sales_report_id')->count(),
            ];

            $payments = collect(); // empty
            return view('payments.consignment.index', compact('payments', 'pendings', 'stores', 'stats', 'tabCounts'))
                ->with('paymentStatus', $tab);
        }

        // ═══ OTHER TABS — show payments ═══
        $query = ConsignmentPayment::with('store', 'deliveryReceipt');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('payment_number', 'like', "%{$s}%")
                  ->orWhere('reference_number', 'like', "%{$s}%")
                  ->orWhereHas('store', fn($sq) => $sq->where('store_name', 'like', "%{$s}%"));
            });
        }

        if ($request->filled('store')) {
            $query->where('store_id', $request->store);
        }

        if ($tab === 'partial') {
            $query->whereHas('deliveryReceipt', fn($q) => $q->where('status', 'partial'));
        } elseif ($tab === 'full') {
            $query->where(function ($q) {
                $q->whereHas('deliveryReceipt', fn($sq) => $sq->where('status', 'paid'))
                  ->orWhereHas('salesReport', fn($sq) => $sq->where('status', 'paid'));
            });
        } elseif ($tab === 'unlinked') {
            $query->whereNull('delivery_receipt_id')->whereNull('sales_report_id');
        }

        $payments = $query->latest()->paginate(15)->withQueryString();
        $pendings = collect();

        $stats = [
            'total'      => ConsignmentPayment::count(),
            'all_amount' => (float) ConsignmentPayment::sum('amount'),
            'this_month' => (float) ConsignmentPayment::where('payment_date', '>=', now()->startOfMonth())->sum('amount'),
            'today'      => (float) ConsignmentPayment::whereDate('payment_date', today())->sum('amount'),
        ];

        $tabCounts = [
            'all'      => ConsignmentPayment::count(),
            'pending'  => DeliveryReceipt::where('status', 'pending')->where('balance', '>', 0)->count(),
            'partial'  => DeliveryReceipt::where('status', 'partial')->count(),
            'full'     => DeliveryReceipt::where('status', 'paid')->count(),
            'unlinked' => ConsignmentPayment::whereNull('delivery_receipt_id')->whereNull('sales_report_id')->count(),
        ];

        $paymentStatus = $tab;

        return view('payments.consignment.index', compact('payments', 'pendings', 'stores', 'stats', 'tabCounts', 'paymentStatus'));
    }

    public function create(Request $request)
    {
        $stores = Store::where('status', 'active')->orderBy('store_name')->get();
        $selectedStore = $request->get('store_id');
        $selectedDr = $request->get('delivery_receipt_id');

        $outstandingDRs = collect();
        if ($selectedStore) {
            $outstandingDRs = DeliveryReceipt::where('store_id', $selectedStore)
                ->whereIn('status', ['pending', 'partial', 'overdue'])
                ->orderBy('delivery_date')
                ->get();
        }

        $outstandingReports = collect();
        if ($selectedStore) {
            $outstandingReports = SalesReport::where('store_id', $selectedStore)
                ->whereIn('status', ['pending', 'verified'])
                ->orderBy('period_from')
                ->get();
        }

        return view('payments.consignment.create', compact(
            'stores', 'selectedStore', 'selectedDr', 'outstandingDRs', 'outstandingReports'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'store_id'            => 'required|exists:stores,id',
            'delivery_receipt_id' => 'nullable|exists:delivery_receipts,id',
            'sales_report_id'     => 'nullable|exists:sales_reports,id',
            'amount'              => 'required|numeric|min:0.01',
            'method'              => 'required|in:cash,gcash,bank_transfer,check,maya',
            'reference_number'    => 'nullable|string|max:100',
            'payment_date'        => 'required|date',
            'notes'               => 'nullable|string',
        ]);

        DB::transaction(function () use ($data) {
            $paymentNumber = 'PMT-' . now()->format('Ymd') . '-' . str_pad(
                ConsignmentPayment::whereDate('created_at', today())->count() + 1,
                4, '0', STR_PAD_LEFT
            );

            ConsignmentPayment::create([
                'payment_number'      => $paymentNumber,
                'store_id'            => $data['store_id'],
                'delivery_receipt_id' => $data['delivery_receipt_id'] ?? null,
                'sales_report_id'     => $data['sales_report_id'] ?? null,
                'user_id'             => auth()->id(),
                'amount'              => $data['amount'],
                'method'              => $data['method'],
                'reference_number'    => $data['reference_number'] ?? null,
                'payment_date'        => $data['payment_date'],
                'notes'               => $data['notes'] ?? null,
            ]);

            if (!empty($data['delivery_receipt_id'])) {
                $dr = DeliveryReceipt::find($data['delivery_receipt_id']);
                if ($dr) {
                    $dr->amount_paid += $data['amount'];
                    $dr->balance = max(0, $dr->total_amount - $dr->amount_paid);

                    if ($dr->balance <= 0.01) {
                        $dr->status = 'paid';
                        $dr->balance = 0;
                    } elseif ($dr->amount_paid > 0) {
                        $dr->status = 'partial';
                    }
                    $dr->save();
                }
            }

            if (!empty($data['sales_report_id'])) {
                $sr = SalesReport::find($data['sales_report_id']);
                if ($sr && $sr->amount_due <= $data['amount']) {
                    $sr->status = 'paid';
                    $sr->save();
                }
            }
        });

        return redirect()
            ->route('consignment.payments.index')
            ->with('success', 'Payment recorded successfully.');
    }

    public function show(ConsignmentPayment $payment)
    {
        $payment->load('store', 'user', 'deliveryReceipt', 'salesReport');
        return view('payments.consignment.show', compact('payment'));
    }
}