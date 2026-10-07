<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('delivery_receipts', function (Blueprint $table) {
            // Out for delivery
            if (!Schema::hasColumn('delivery_receipts', 'out_for_delivery_at')) {
                $table->timestamp('out_for_delivery_at')->nullable();
            }
            if (!Schema::hasColumn('delivery_receipts', 'out_for_delivery_by')) {
                $table->unsignedBigInteger('out_for_delivery_by')->nullable();
            }

            // Customer confirmation
            if (!Schema::hasColumn('delivery_receipts', 'customer_confirmed')) {
                $table->boolean('customer_confirmed')->default(false);
            }
            if (!Schema::hasColumn('delivery_receipts', 'confirmed_at')) {
                $table->timestamp('confirmed_at')->nullable();
            }
            if (!Schema::hasColumn('delivery_receipts', 'confirmed_notes')) {
                $table->text('confirmed_notes')->nullable();
            }

            // Delivery received (physical handoff)
            if (!Schema::hasColumn('delivery_receipts', 'delivered_at')) {
                $table->timestamp('delivered_at')->nullable();
            }

            // Sales report link
            if (!Schema::hasColumn('delivery_receipts', 'sales_report_id')) {
                $table->unsignedBigInteger('sales_report_id')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('delivery_receipts', function (Blueprint $table) {
            $table->dropColumn([
                'out_for_delivery_at',
                'out_for_delivery_by',
                'customer_confirmed',
                'confirmed_at',
                'confirmed_notes',
                'delivered_at',
                'sales_report_id',
            ]);
        });
    }
};