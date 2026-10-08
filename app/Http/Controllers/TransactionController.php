<?php

namespace App\Http\Controllers;

use App\Models\ConsignmentPayment;
use App\Models\DeliveryReceipt;
use App\Models\ReorderRequest;
use App\Models\SalesReport;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->input('type', 'all');
        $storeId = $request->input('store');
        $search = $request->input('search');
        $dateFrom = $request->input('from');
        $dateTo = $request->input('to');

        $activities = collect();

        // ==========================================
        // 1. REORDER REQUESTS (Orders)
        // ==========================================
        if (in_array($type, ['all', 'order'])) {
            $query = ReorderRequest::with(['store', 'deliveryReceipt'])
                ->when($storeId, fn($q) => $q->where('store_id', $storeId))
                ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
                ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
                ->when($search, function($q) use ($search) {
                    $q->where(function($sub) use ($search) {
                        $sub->where('request_number', 'like', "%{$search}%")
                            ->orWhereHas('store', fn($sq) => $sq->where('store_name', 'like', "%{$search}%"));
                    });
                });

            $query->get()->each(function($r) use (&$activities) {
                $activities->push([
                    'type' => 'order',
                    'icon' => '🛒',
                    'color' => '#f59e0b',
                    'title' => 'Order Placed',
                    'reference' => $r->request_number,
                    'store_id' => $r->store_id,
                    'store_name' => $r->store->store_name ?? '-',
                    'store_code' => $r->store->code ?? '',
                    'amount' => (float) $r->total_amount,
                    'quantity' => $r->total_quantity,
                    'status' => $r->status,
                    'description' => "Order sa {$r->items->count()} product(s) — {$r->total_quantity} unit(s)",
                    'url' => route('reorder-requests.show', $r->id),
                    'created_at' => $r->created_at,
                ]);

                // If approved, add activity
                if ($r->status === 'approved' && $r->approved_at) {
                    $activities->push([
                        'type' => 'order',
                        'icon' => '✓',
                        'color' => '#22c55e',
                        'title' => 'Order Approved',
                        'reference' => $r->request_number,
                        'store_id' => $r->store_id,
                        'store_name' => $r->store->store_name ?? '-',
                        'store_code' => $r->store->code ?? '',
                        'amount' => null,
                        'status' => 'approved',
                        'description' => "Gi-approve ni admin · DR: " . ($r->deliveryReceipt->dr_number ?? '—'),
                        'url' => route('reorder-requests.show', $r->id),
                        'created_at' => $r->approved_at,
                    ]);
                }

                if ($r->status === 'rejected' && $r->rejected_at) {
                    $activities->push([
                        'type' => 'order',
                        'icon' => '✕',
                        'color' => '#ef4444',
                        'title' => 'Order Rejected',
                        'reference' => $r->request_number,
                        'store_id' => $r->store_id,
                        'store_name' => $r->store->store_name ?? '-',
                        'store_code' => $r->store->code ?? '',
                        'amount' => null,
                        'status' => 'rejected',
                        'description' => "Gi-reject · " . ($r->rejection_reason ?? 'Walay reason'),
                        'url' => route('reorder-requests.show', $r->id),
                        'created_at' => $r->rejected_at,
                    ]);
                }
            });
        }

        // ==========================================
        // 2. DELIVERY RECEIPTS
        // ==========================================
        if (in_array($type, ['all', 'delivery'])) {
            $query = DeliveryReceipt::with(['store', 'items'])
                ->when($storeId, fn($q) => $q->where('store_id', $storeId))
                ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
                ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
                ->when($search, function($q) use ($search) {
                    $q->where(function($sub) use ($search) {
                        $sub->where('dr_number', 'like', "%{$search}%")
                            ->orWhereHas('store', fn($sq) => $sq->where('store_name', 'like', "%{$search}%"));
                    });
                });

            $query->get()->each(function($d) use (&$activities) {
                // Created
                $activities->push([
                    'type' => 'delivery',
                    'icon' => '📦',
                    'color' => '#c9a961',
                    'title' => 'Delivery Created',
                    'reference' => $d->dr_number,
                    'store_id' => $d->store_id,
                    'store_name' => $d->store->store_name ?? '-',
                    'store_code' => $d->store->code ?? '',
                    'amount' => (float) $d->total_amount,
                    'status' => 'pending',
                    'description' => "{$d->items->count()} item(s) · Total ₱" . number_format($d->total_amount, 2),
                    'url' => route('deliveries.show', $d->id),
                    'created_at' => $d->created_at,
                ]);

                // Shipped
                if ($d->out_for_delivery_at) {
                    $activities->push([
                        'type' => 'delivery',
                        'icon' => '🚚',
                        'color' => '#3b82f6',
                        'title' => 'Out for Delivery',
                        'reference' => $d->dr_number,
                        'store_id' => $d->store_id,
                        'store_name' => $d->store->store_name ?? '-',
                        'store_code' => $d->store->code ?? '',
                        'amount' => null,
                        'status' => 'out_for_delivery',
                        'description' => 'Gi-ship na sa customer',
                        'url' => route('deliveries.show', $d->id),
                        'created_at' => $d->out_for_delivery_at,
                    ]);
                }

                // Confirmed
                if ($d->customer_confirmed && $d->confirmed_at) {
                    $activities->push([
                        'type' => 'delivery',
                        'icon' => '✓',
                        'color' => '#22c55e',
                        'title' => 'Delivery Confirmed',
                        'reference' => $d->dr_number,
                        'store_id' => $d->store_id,
                        'store_name' => $d->store->store_name ?? '-',
                        'store_code' => $d->store->code ?? '',
                        'amount' => null,
                        'status' => 'delivered',
                        'description' => 'Gi-confirm sa customer nga nadawat',
                        'url' => route('deliveries.show', $d->id),
                        'created_at' => $d->confirmed_at,
                    ]);
                }
            });
        }

        // ==========================================
        // 3. PAYMENTS
        // ==========================================
        if (in_array($type, ['all', 'payment'])) {
            $query = ConsignmentPayment::with(['store', 'deliveryReceipt'])
                ->when($storeId, fn($q) => $q->where('store_id', $storeId))
                ->when($dateFrom, fn($q) => $q->whereDate('payment_date', '>=', $dateFrom))
                ->when($dateTo, fn($q) => $q->whereDate('payment_date', '<=', $dateTo))
                ->when($search, function($q) use ($search) {
                    $q->where(function($sub) use ($search) {
                        $sub->where('payment_number', 'like', "%{$search}%")
                            ->orWhere('reference_number', 'like', "%{$search}%")
                            ->orWhereHas('store', fn($sq) => $sq->where('store_name', 'like', "%{$search}%"));
                    });
                });

            $query->get()->each(function($p) use (&$activities) {
                $methodLabel = ucfirst($p->method);
                $methodIcon = match($p->method) {
                    'cash' => '💵',
                    'gcash' => '📱',
                    'maya' => '💜',
                    'bank_transfer' => '🏦',
                    'check' => '📝',
                    default => '💰',
                };

                $activities->push([
                    'type' => 'payment',
                    'icon' => $methodIcon,
                    'color' => '#22c55e',
                    'title' => 'Payment Recorded',
                    'reference' => $p->payment_number,
                    'store_id' => $p->store_id,
                    'store_name' => $p->store->store_name ?? '-',
                    'store_code' => $p->store->code ?? '',
                    'amount' => (float) $p->amount,
                    'status' => 'paid',
                    'description' => "{$methodLabel}" . ($p->reference_number ? " · Ref: {$p->reference_number}" : '') . ($p->deliveryReceipt ? " · DR: {$p->deliveryReceipt->dr_number}" : ''),
                    'url' => route('consignment.payments.show', $p->id),
                    'created_at' => $p->created_at,
                ]);
            });
        }

        // ==========================================
        // 4. SALES REPORTS
        // ==========================================
        if (in_array($type, ['all', 'report'])) {
            $query = SalesReport::with(['store', 'deliveryReceipt'])
                ->when($storeId, fn($q) => $q->where('store_id', $storeId))
                ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
                ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
                ->when($search, function($q) use ($search) {
                    $q->where(function($sub) use ($search) {
                        $sub->where('report_number', 'like', "%{$search}%")
                            ->orWhereHas('store', fn($sq) => $sq->where('store_name', 'like', "%{$search}%"));
                    });
                });

            $query->get()->each(function($r) use (&$activities) {
                $activities->push([
                    'type' => 'report',
                    'icon' => '📊',
                    'color' => '#8b5cf6',
                    'title' => 'Sales Report Created',
                    'reference' => $r->report_number,
                    'store_id' => $r->store_id,
                    'store_name' => $r->store->store_name ?? '-',
                    'store_code' => $r->store->code ?? '',
                    'amount' => (float) $r->total_sales,
                    'status' => $r->status,
                    'description' => "Total: ₱" . number_format($r->total_sales, 2) . " · Paid: ₱" . number_format($r->amount_paid, 2),
                    'url' => route('consignment.reports.show', $r->id),
                    'created_at' => $r->created_at,
                ]);
            });
        }

        // ==========================================
        // 5. STORE REGISTRATIONS
        // ==========================================
        if (in_array($type, ['all', 'store']) && !$storeId) {
            $query = Store::when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
                ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
                ->when($search, function($q) use ($search) {
                    $q->where('store_name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
                });

            $query->get()->each(function($s) use (&$activities) {
                $activities->push([
                    'type' => 'store',
                    'icon' => '🏪',
                    'color' => '#06b6d4',
                    'title' => 'Store Registered',
                    'reference' => $s->code ?? 'STORE-' . $s->id,
                    'store_id' => $s->id,
                    'store_name' => $s->store_name,
                    'store_code' => $s->code ?? '',
                    'amount' => null,
                    'status' => $s->status ?? 'active',
                    'description' => ($s->owner_name ?? '') . ' · ' . ($s->city ?? ''),
                    'url' => route('stores.show', $s->id),
                    'created_at' => $s->created_at,
                ]);
            });
        }

        // ==========================================
        // SORT + PAGINATE
        // ==========================================
        $activities = $activities->sortByDesc('created_at')->values();

        // Manual pagination
        $perPage = 10;
        $currentPage = $request->input('page', 1);
        $total = $activities->count();
        $paged = $activities->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $paged, $total, $perPage, $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Stats
        $stats = [
            'total_today' => $activities->filter(fn($a) => $a['created_at']->isToday())->count(),
            'total_week' => $activities->filter(fn($a) => $a['created_at']->isCurrentWeek())->count(),
            'total_orders' => $activities->where('type', 'order')->count(),
            'total_payments' => $activities->where('type', 'payment')->count(),
            'amount_today' => $activities->where('type', 'payment')->filter(fn($a) => $a['created_at']->isToday())->sum('amount'),
            'amount_week' => $activities->where('type', 'payment')->filter(fn($a) => $a['created_at']->isCurrentWeek())->sum('amount'),
        ];

        $stores = Store::orderBy('store_name')->get();

        return view('transactions.index', compact('paginator', 'activities', 'stats', 'stores', 'type', 'storeId', 'search', 'dateFrom', 'dateTo'));
    }
}