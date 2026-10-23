<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('productions', function (Blueprint $table) {
            $table->id();
            $table->date('production_date');
            $table->foreignId('raw_variant_id')->constrained('product_variants')->cascadeOnDelete(); // Cherries
            $table->foreignId('finished_variant_id')->constrained('product_variants')->cascadeOnDelete(); // Powder/Beans
            $table->decimal('raw_qty_used', 10, 2);
            $table->decimal('finished_qty_produced', 10, 2);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('productions'); }
};