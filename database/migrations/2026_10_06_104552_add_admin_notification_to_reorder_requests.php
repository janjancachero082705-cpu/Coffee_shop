<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reorder_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('reorder_requests', 'is_read_by_admin')) {
                $table->boolean('is_read_by_admin')->default(false);
            }
            if (!Schema::hasColumn('reorder_requests', 'is_read_at')) {
                $table->timestamp('is_read_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('reorder_requests', function (Blueprint $table) {
            $table->dropColumn(['is_read_by_admin', 'is_read_at']);
        });
    }
};