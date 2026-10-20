<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('consignment_payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_number')->unique();
            $table->foreignId('store_id')->constrained()->onDelete('cascade');
            $table->foreignId('delivery_receipt_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('sales_report_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('amount', 12, 2);
            $table->string('method');
            $table->string('reference_number')->nullable();
            $table->string('proof_of_payment')->nullable();
            $table->date('payment_date');
            $table->text('notes')->nullable();
            $table->boolean('is_read_by_admin')->default(false);
            $table->timestamp('is_read_at')->nullable();
            $table->string('verification_status')->default('pending');
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('verified_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('consignment_payments');
    }
};