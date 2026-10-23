<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('purchases', function (Blueprint $table) {
            $table->decimal('quantity', 10, 3)->change(); // 3 decimal places for 1/4 (0.250)
        });
    }
    public function down(): void {
        Schema::table('purchases', function (Blueprint $table) {
            $table->integer('quantity')->change();
        });
    }
};