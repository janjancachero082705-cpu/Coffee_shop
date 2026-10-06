<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('delivery_receipts', function (Blueprint $table) {
            if (Schema::hasColumn('delivery_receipts', 'due_date')) {
                $table->dropColumn('due_date');
            }
            if (Schema::hasColumn('delivery_receipts', 'amount_paid')) {
                // Keep amount_paid, it's useful
            }
        });

        // Change status enum to just pending/partial/paid (remove overdue)
        \DB::statement("ALTER TABLE delivery_receipts MODIFY COLUMN status ENUM('pending', 'partial', 'paid') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        Schema::table('delivery_receipts', function (Blueprint $table) {
            $table->date('due_date')->nullable();
        });

        \DB::statement("ALTER TABLE delivery_receipts MODIFY COLUMN status ENUM('pending', 'partial', 'paid', 'overdue') NOT NULL DEFAULT 'pending'");
    }
};