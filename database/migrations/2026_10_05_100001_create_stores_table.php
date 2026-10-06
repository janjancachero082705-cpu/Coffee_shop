<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('store_name');
            $table->string('owner_name');
            $table->string('contact_number');
            $table->string('email')->nullable();
            $table->text('address');
            $table->string('barangay')->nullable();
            $table->string('city')->nullable();
            $table->decimal('credit_limit', 12, 2)->default(0);
            $table->enum('payment_terms', ['weekly', 'semi_monthly', 'monthly', 'flexible'])->default('monthly');
            $table->string('payment_day')->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};