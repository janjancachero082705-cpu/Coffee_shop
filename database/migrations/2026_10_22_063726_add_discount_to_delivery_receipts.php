<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('delivery_receipts', function (Blueprint $table) {
            $table->decimal('subtotal', 12, 2)->default(0)->after('total_amount');
            $table->decimal('discount_amount', 12, 2)->default(0)->after('subtotal');
            $table->enum('discount_type', ['percent', 'fixed'])->default('percent')->after('discount_amount');
            $table->decimal('discount_percent', 5, 2)->default(0)->after('discount_type');
            $table->string('discount_reason', 100)->nullable()->after('discount_percent');
        });
    }

    public function down(): void
    {
        Schema::table('delivery_receipts', function (Blueprint $table) {
            $table->dropColumn(['subtotal', 'discount_amount', 'discount_type', 'discount_percent', 'discount_reason']);
        });
    }
};