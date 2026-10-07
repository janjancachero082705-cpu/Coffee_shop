<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_reports', function (Blueprint $table) {
            if (!Schema::hasColumn('sales_reports', 'delivery_receipt_id')) {
                $table->foreignId('delivery_receipt_id')
                    ->nullable()
                    ->after('store_id')
                    ->constrained('delivery_receipts')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('sales_reports', function (Blueprint $table) {
            $table->dropForeign(['delivery_receipt_id']);
            $table->dropColumn('delivery_receipt_id');
        });
    }
};