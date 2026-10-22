<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->enum('customer_type', ['regular', 'suki', 'friend'])->default('regular')->after('status');
            $table->decimal('default_discount_percent', 5, 2)->default(0)->after('customer_type');
            $table->text('discount_notes')->nullable()->after('default_discount_percent');
        });
    }

    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn(['customer_type', 'default_discount_percent', 'discount_notes']);
        });
    }
};