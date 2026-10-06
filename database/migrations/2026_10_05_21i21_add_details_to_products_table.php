<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'sku')) {
                $table->string('sku')->nullable()->unique()->after('id');
            }
            if (!Schema::hasColumn('products', 'cost_price')) {
                $table->decimal('cost_price', 10, 2)->default(0)->after('price');
            }
            if (!Schema::hasColumn('products', 'wholesale_price')) {
                $table->decimal('wholesale_price', 10, 2)->nullable()->after('cost_price');
            }
            if (!Schema::hasColumn('products', 'variety')) {
                $table->string('variety')->nullable()->after('description');
            }
            if (!Schema::hasColumn('products', 'origin')) {
                $table->string('origin')->nullable()->after('variety');
            }
            if (!Schema::hasColumn('products', 'roast_level')) {
                $table->string('roast_level')->nullable()->after('origin');
            }
            if (!Schema::hasColumn('products', 'process_method')) {
                $table->string('process_method')->nullable()->after('roast_level');
            }
            if (!Schema::hasColumn('products', 'altitude')) {
                $table->string('altitude')->nullable()->after('process_method');
            }
            if (!Schema::hasColumn('products', 'harvest_year')) {
                $table->string('harvest_year')->nullable()->after('altitude');
            }
            if (!Schema::hasColumn('products', 'cupping_notes')) {
                $table->text('cupping_notes')->nullable()->after('harvest_year');
            }
            if (!Schema::hasColumn('products', 'weight_grams')) {
                $table->integer('weight_grams')->nullable()->after('base_unit');
            }
            if (!Schema::hasColumn('products', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('is_active');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $cols = ['sku', 'cost_price', 'wholesale_price', 'variety', 'origin',
                     'roast_level', 'process_method', 'altitude', 'harvest_year',
                     'cupping_notes', 'weight_grams', 'is_featured'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('products', $col)) $table->dropColumn($col);
            }
        });
    }
};