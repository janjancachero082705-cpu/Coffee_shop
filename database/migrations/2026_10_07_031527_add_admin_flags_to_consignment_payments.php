<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consignment_payments', function (Blueprint $table) {
            if (!Schema::hasColumn('consignment_payments', 'is_read_by_admin')) {
                $table->boolean('is_read_by_admin')->default(false)->after('notes');
            }
            if (!Schema::hasColumn('consignment_payments', 'is_read_at')) {
                $table->timestamp('is_read_at')->nullable()->after('is_read_by_admin');
            }
        });
    }

    public function down(): void
    {
        Schema::table('consignment_payments', function (Blueprint $table) {
            $table->dropColumn(['is_read_by_admin', 'is_read_at']);
        });
    }
};