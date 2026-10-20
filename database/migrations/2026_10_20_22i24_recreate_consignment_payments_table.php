<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        // I-drop ang lumang table
        Schema::dropIfExists('consignment_payments');

        // Gumawa ng bago at perpektong table
        Schema::create('consignment_payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_number')->unique();
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->foreignId('delivery_receipt_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('sales_report_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('method'); // cash, gcash, etc.
            $table->string('reference_number')->nullable();
            $table->string('proof_of_payment')->nullable(); // DITO NAKA-SAVE ANG IMAGE
            $table->date('payment_date');
            $table->text('notes')->nullable();
            $table->boolean('is_read_by_admin')->default(false);
            $table->timestamp('is_read_at')->nullable();
            $table->string('verification_status')->default('pending');
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('consignment_payments');
    }
};