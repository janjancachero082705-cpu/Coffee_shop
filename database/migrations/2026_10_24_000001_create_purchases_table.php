<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('supplier_name');
            $table->date('purchase_date');
            $table->foreignId('variant_id')->constrained('product_variants')->cascadeOnDelete();
            $table->decimal('quantity', 10, 2);
            $table->decimal('cost_per_unit', 12, 2);
            $table->decimal('total_cost', 12, 2);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('purchases'); }
};