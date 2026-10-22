<?php

namespace App\Services;

use App\Models\DeliveryReceipt;
use App\Models\DeliveryReceiptItem;
use App\Models\Expense;
use Carbon\Carbon;

class BusinessMetricsService
{
    /**
     * Get complete metrics para sa specific date range
     */
    public function getMetrics($from = null, $to = null): array
    {
        $from = $from ?? now()->startOfMonth();
        $to = $to ?? now();

        // SALES — sum sa delivery_receipts.total_amount
        $sales = DeliveryReceipt::whereBetween('delivery_date', [$from, $to])
            ->whereIn('status', ['delivered', 'out_for_delivery', 'pending'])
            ->sum('total_amount');

        // COGS — sum sa (qty_delivered × product.cost_price)
        $cogs = DeliveryReceiptItem::query()
            ->join('delivery_receipts', 'delivery_receipts.id', '=', 'delivery_receipt_items.delivery_receipt_id')
            ->join('products', 'products.id', '=', 'delivery_receipt_items.product_id')
            ->whereBetween('delivery_receipts.delivery_date', [$from, $to])
            ->sum(\DB::raw('delivery_receipt_items.quantity_delivered * COALESCE(products.cost_price, 0)'));

        // GROSS PROFIT — sales - cogs
        $grossProfit = $sales - $cogs;

        // EXPENSES — sum sa expenses table
        $expenses = Expense::whereBetween('expense_date', [$from, $to])
            ->sum('amount');

        // NET PROFIT — gross profit - expenses
        $netProfit = $grossProfit - $expenses;

        // MARGIN % — (net/sales) × 100
        $margin = $sales > 0 ? ($netProfit / $sales) * 100 : 0;

        // Transactions count
        $transactions = DeliveryReceipt::whereBetween('delivery_date', [$from, $to])->count();

        return [
            'sales' => round($sales, 2),
            'cogs' => round($cogs, 2),
            'gross_profit' => round($grossProfit, 2),
            'expenses' => round($expenses, 2),
            'net_profit' => round($netProfit, 2),
            'margin' => round($margin, 2),
            'transactions' => $transactions,
            'from' => $from,
            'to' => $to,
        ];
    }

    /**
     * Sales per store (para sa dashboard)
     */
    public function getSalesPerStore($from = null, $to = null): array
    {
        $from = $from ?? now()->startOfMonth();
        $to = $to ?? now();

        return DeliveryReceipt::query()
            ->join('stores', 'stores.id', '=', 'delivery_receipts.store_id')
            ->whereBetween('delivery_receipts.delivery_date', [$from, $to])
            ->groupBy('stores.id', 'stores.store_name', 'stores.customer_type')
            ->selectRaw('
                stores.id,
                stores.store_name,
                stores.customer_type,
                COUNT(delivery_receipts.id) as deliveries,
                SUM(delivery_receipts.total_amount) as total_sales
            ')
            ->orderByDesc('total_sales')
            ->get()
            ->toArray();
    }

    /**
     * Sales per product
     */
    public function getSalesPerProduct($from = null, $to = null): array
    {
        $from = $from ?? now()->startOfMonth();
        $to = $to ?? now();

        return DeliveryReceiptItem::query()
            ->join('delivery_receipts', 'delivery_receipts.id', '=', 'delivery_receipt_items.delivery_receipt_id')
            ->join('products', 'products.id', '=', 'delivery_receipt_items.product_id')
            ->whereBetween('delivery_receipts.delivery_date', [$from, $to])
            ->groupBy('products.id', 'products.name', 'products.unit_type')
            ->selectRaw('
                products.id,
                products.name,
                products.unit_type,
                SUM(delivery_receipt_items.quantity_delivered) as total_qty,
                SUM(delivery_receipt_items.subtotal) as total_sales
            ')
            ->orderByDesc('total_sales')
            ->get()
            ->toArray();
    }

    /**
     * Expenses by category
     */
    public function getExpensesByCategory($from = null, $to = null): array
    {
        $from = $from ?? now()->startOfMonth();
        $to = $to ?? now();

        return Expense::whereBetween('expense_date', [$from, $to])
            ->groupBy('category')
            ->selectRaw('category, COUNT(*) as count, SUM(amount) as total')
            ->orderByDesc('total')
            ->get()
            ->toArray();
    }

    /**
     * Daily trend (para sa chart)
     */
    public function getDailyTrend($days = 30): array
    {
        $from = now()->subDays($days);
        $to = now();

        $data = DeliveryReceipt::whereBetween('delivery_date', [$from, $to])
            ->groupBy('delivery_date')
            ->selectRaw('
                delivery_date,
                SUM(total_amount) as sales,
                COUNT(*) as transactions
            ')
            ->orderBy('delivery_date')
            ->get()
            ->toArray();

        return $data;
    }
}
