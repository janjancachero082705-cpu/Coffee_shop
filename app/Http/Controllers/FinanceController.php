<?php

namespace App\Http\Controllers;

use App\Models\ConsignmentPayment;
use App\Models\DeliveryReceipt;
use App\Models\Product;
use App\Models\SalesReport;
use App\Models\SalesReportItem;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    public function dashboard(Request $request)
    {
        // ==================== DATE RANGE ====================
        $range = $request->input('range', '30d');
        $dateFrom = $request->input('from');
        $dateTo = $request->input('to');

        // Auto-calculate based sa preset range
        if (!$dateFrom || !$dateTo) {
            [$dateFrom, $dateTo] = match ($range) {
                'today'  => [now()->startOfDay(), now()->endOfDay()],
                '7d'     => [now()->subDays(6)->startOfDay(), now()->endOfDay()],
                '30d'    => [now()->subDays(29)->startOfDay(), now()->endOfDay()],
                '90d'    => [now()->subDays(89)->startOfDay(), now()->endOfDay()],
                'month'  => [now()->startOfMonth(), now()->endOfMonth()],
                'year'   => [now()->startOfYear(), now()->endOfYear()],
                default  => [now()->subDays(29)->startOfDay(), now()->endOfDay()],
            };
            $dateFrom = Carbon::parse($dateFrom);
            $dateTo = Carbon::parse($dateTo);
        } else {
            $dateFrom = Carbon::parse($dateFrom)->startOfDay();
            $dateTo = Carbon::parse($dateTo)->endOfDay();
        }

        // ==================== REVENUE ====================
        // Total sales gikan sa SalesReport (base sa period_from)
        $totalRevenue = (float) SalesReport::whereBetween('created_at', [$dateFrom, $dateTo])
            ->sum('total_sales');

        // ==================== COST OF GOODS SOLD (COGS) ====================
        // Base sa items sold (from sales_report_items joined sa products)
        $totalCost = (float) DB::table('sales_report_items')
            ->join('sales_reports', 'sales_report_items.sales_report_id', '=', 'sales_reports.id')
            ->join('products', 'sales_report_items.product_id', '=', 'products.id')
            ->whereBetween('sales_reports.created_at', [$dateFrom, $dateTo])
            ->sum(DB::raw('sales_report_items.quantity_sold * products.cost_price'));

        // ==================== PROFIT ====================
        $grossProfit = $totalRevenue - $totalCost;
        $profitMargin = $totalRevenue > 0 ? ($grossProfit / $totalRevenue) * 100 : 0;

        // ==================== PAYMENTS RECEIVED ====================
        $paymentsReceived = (float) ConsignmentPayment::verified()->whereBetween('payment_date', [$dateFrom, $dateTo])
            ->sum('amount');

        // Breakdown by method
        $paymentsByMethod = ConsignmentPayment::whereBetween('payment_date', [$dateFrom, $dateTo])
            ->select('method', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('method')
            ->get()
            ->map(fn($p) => [
                'method' => $p->method,
                'label' => ucfirst(str_replace('_', ' ', $p->method)),
                'total' => (float) $p->total,
                'count' => $p->count,
                'icon' => match ($p->method) {
                    'cash' => '’µ',
                    'gcash' => '“±',
                    'maya' => '’œ',
                    'bank_transfer' => '¦',
                    'check' => '“',
                    default => '’°',
                },
            ]);

        // ==================== CASH POSITION ====================
        // Money on hand (cash payments)
        $cashOnHand = (float) ConsignmentPayment::verified()->where('method', 'cash')
            ->whereBetween('payment_date', [$dateFrom, $dateTo])
            ->sum('amount');

        // Online (GCash, Maya, Bank)
        $onlineReceived = (float) ConsignmentPayment::verified()->whereIn('method', ['gcash', 'maya', 'bank_transfer'])
            ->whereBetween('payment_date', [$dateFrom, $dateTo])
            ->sum('amount');

        // Receivables (utang sa stores)
        $totalReceivables = (float) DeliveryReceipt::where('balance', '>', 0)->sum('balance');

        // Total paid (all time)
        $totalPaidAllTime = (float) DeliveryReceipt::sum('amount_paid');

        // Total outstanding (all time)
        $totalOutstanding = (float) DeliveryReceipt::sum('balance');

        // ==================== TOP PRODUCTS ====================
        $topProducts = DB::table('sales_report_items')
            ->join('sales_reports', 'sales_report_items.sales_report_id', '=', 'sales_reports.id')
            ->join('products', 'sales_report_items.product_id', '=', 'products.id')
            ->whereBetween('sales_reports.created_at', [$dateFrom, $dateTo])
            ->select(
                'products.id',
                'products.name',
                'products.sku',
                DB::raw('SUM(sales_report_items.quantity_sold) as qty_sold'),
                DB::raw('SUM(sales_report_items.subtotal) as revenue'),
                DB::raw('SUM(sales_report_items.quantity_sold * products.cost_price) as cost'),
            )
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get()
            ->map(function ($p) {
                $profit = $p->revenue - $p->cost;
                return [
                    'name' => $p->name,
                    'sku' => $p->sku,
                    'qty_sold' => (int) $p->qty_sold,
                    'revenue' => (float) $p->revenue,
                    'cost' => (float) $p->cost,
                    'profit' => (float) $profit,
                    'margin' => $p->revenue > 0 ? round(($profit / $p->revenue) * 100, 1) : 0,
                ];
            });

        // ==================== TOP STORES ====================
        $topStores = SalesReport::whereBetween('created_at', [$dateFrom, $dateTo])
            ->select('store_id', DB::raw('SUM(total_sales) as revenue'), DB::raw('SUM(amount_paid) as paid'), DB::raw('SUM(balance) as balance'), DB::raw('COUNT(*) as reports'))
            ->with('store')
            ->groupBy('store_id')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get()
            ->map(fn($s) => [
                'name' => $s->store->store_name ?? '-',
                'code' => $s->store->code ?? '',
                'revenue' => (float) $s->revenue,
                'paid' => (float) $s->paid,
                'balance' => (float) $s->balance,
                'reports' => $s->reports,
            ]);

        // ==================== DAILY TREND ====================
        $dailyTrend = SalesReport::whereBetween('created_at', [$dateFrom, $dateTo])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_sales) as revenue'),
                DB::raw('SUM(amount_paid) as paid')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(fn($d) => [
                'date' => $d->date,
                'revenue' => (float) $d->revenue,
                'paid' => (float) $d->paid,
            ]);

        // ==================== SPARKLINE DATA ====================
        $sparklineData = $dailyTrend->pluck('revenue')->values()->toArray();
        $maxRevenue = !empty($sparklineData) ? max($sparklineData) : 1;
        $sparklinePoints = [];
        if (count($sparklineData) > 1) {
            $step = 100 / (count($sparklineData) - 1);
            foreach ($sparklineData as $i => $val) {
                $x = $i * $step;
                $y = 100 - (($val / max($maxRevenue, 1)) * 90);
                $sparklinePoints[] = "{$x},{$y}";
            }
        }
        $sparklinePath = !empty($sparklinePoints) ? 'M' . implode(' L', $sparklinePoints) : '';
        $sparklineArea = !empty($sparklinePoints) ? 'M0,100 L' . implode(' L', $sparklinePoints) . ' L100,100 Z' : '';

        // ==================== REVENUE TREND % ====================
        $prevPeriodFrom = (clone $dateFrom)->subDays($dateTo->diffInDays($dateFrom) ?: 30);
        $prevPeriodTo = (clone $dateFrom)->subDay();

        $prevRevenue = (float) SalesReport::whereBetween('created_at', [$prevPeriodFrom, $prevPeriodTo])->sum('total_sales');
        $revenueChange = $prevRevenue > 0 ? (($totalRevenue - $prevRevenue) / $prevRevenue) * 100 : 0;

        $prevPayments = (float) ConsignmentPayment::verified()->whereBetween('payment_date', [$prevPeriodFrom, $prevPeriodTo])->sum('amount');
        $paymentsChange = $prevPayments > 0 ? (($paymentsReceived - $prevPayments) / $prevPayments) * 100 : 0;

        // ==================== BAR CHART SCALING ====================
        $maxDailyRevenue = $dailyTrend->max('revenue') ?: 1;

        $maxProductRevenue = $topProducts->max('revenue') ?: 1;

        $maxStoreRevenue = $topStores->max('revenue') ?: 1;

        $maxMethodTotal = $paymentsByMethod->max('total') ?: 1;

        return view('finance.dashboard', compact(
            'dateFrom', 'dateTo', 'range',
            'totalRevenue', 'totalCost', 'grossProfit', 'profitMargin',
            'paymentsReceived', 'paymentsByMethod',
            'cashOnHand', 'onlineReceived',
            'totalReceivables', 'totalPaidAllTime', 'totalOutstanding',
            'topProducts', 'topStores', 'dailyTrend', 'sparklinePath', 'sparklineArea', 'revenueChange', 'paymentsChange', 'maxDailyRevenue', 'maxProductRevenue', 'maxStoreRevenue', 'maxMethodTotal'
        ));
    }
}