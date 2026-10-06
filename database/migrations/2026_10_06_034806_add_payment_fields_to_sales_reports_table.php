<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_reports', function (Blueprint $table) {
            if (!Schema::hasColumn('sales_reports', 'amount_paid')) {
                $table->decimal('amount_paid', 12, 2)->default(0)->after('amount_due');
            }
            if (!Schema::hasColumn('sales_reports', 'balance')) {
                $table->decimal('balance', 12, 2)->default(0)->after('amount_paid');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sales_reports', function (Blueprint $table) {
            $table->dropColumn(['amount_paid', 'balance']);
        });
    }
};