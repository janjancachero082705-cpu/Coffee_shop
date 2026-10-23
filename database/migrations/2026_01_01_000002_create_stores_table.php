<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string('store_name');
            $table->string('address')->nullable();
            $table->string('customer_type')->default('regular'); // MERGED MULA SA ADD_CUSTOMER_TYPE
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('stores'); }
};