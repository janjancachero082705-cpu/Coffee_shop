<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consignment_payments', function (Blueprint $table) {
            // Verification fields
            $table->enum('verification_status', ['pending', 'verified', 'rejected'])
                  ->default('pending')
                  ->after('is_read_at');

            $table->unsignedBigInteger('verified_by')->nullable()->after('verification_status');
            $table->timestamp('verified_at')->nullable()->after('verified_by');
            $table->text('rejection_reason')->nullable()->after('verified_at');

            $table->index('verification_status');
        });

        // Mark existing payments as verified (para dili mawala sa reports)
        DB::table('consignment_payments')->update([
            'verification_status' => 'verified',
            'verified_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::table('consignment_payments', function (Blueprint $table) {
            $table->dropIndex(['verification_status']);
            $table->dropColumn(['verification_status', 'verified_by', 'verified_at', 'rejection_reason']);
        });
    }
};