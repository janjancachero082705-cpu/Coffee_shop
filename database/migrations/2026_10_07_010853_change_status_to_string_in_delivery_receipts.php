<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Change status column to string para mo-support sa bag-ong values
        DB::statement("ALTER TABLE delivery_receipts MODIFY COLUMN status VARCHAR(50) NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE delivery_receipts MODIFY COLUMN status ENUM('pending', 'partial', 'paid') NOT NULL DEFAULT 'pending'");
    }
};