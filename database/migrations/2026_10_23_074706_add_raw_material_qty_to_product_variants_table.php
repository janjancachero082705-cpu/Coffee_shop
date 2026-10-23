<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('product_variants', function (Blueprint $table) {
            // Pila ka kilo nga cherries ang magamit para makahimo og 1 unit ani nga variant
            $table->decimal('raw_material_qty', 10, 4)->default(0)->after('stock'); 
        });
    }
    public function down(): void {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn('raw_material_qty');
        });
    }
};