<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE stores MODIFY COLUMN registration_status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'approved'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE stores MODIFY COLUMN registration_status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending'");
    }
};