<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('consignment_payments', function (Blueprint $table) {
            if (!Schema::hasColumn('consignment_payments', 'proof_of_payment')) {
                $table->string('proof_of_payment')->nullable()->after('reference_number');
            }
        });
    }
    public function down(): void {
        Schema::table('consignment_payments', function (Blueprint $table) {
            $table->dropColumn('proof_of_payment');
        });
    }
};