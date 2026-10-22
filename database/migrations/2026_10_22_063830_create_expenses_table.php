<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->enum('category', [
                'cherry_purchase',   // pagpalit og coffee cherries
                'labor',             // suhol sa mag-uuma/workers
                'processing',        // processing cost (water, electricity)
                'packaging',         // packs, labels
                'utilities',         // kuryente, tubig
                'transportation',    // delivery/transport
                'harvesting',        // harvesting cost
                'other',
            ]);
            $table->string('description');
            $table->decimal('amount', 12, 2);
            $table->decimal('quantity', 10, 2)->nullable();      // optional: e.g., 100 kg cherries
            $table->string('unit', 20)->nullable();              // kg, pcs, etc.
            $table->decimal('unit_cost', 12, 2)->nullable();     // per kg price
            $table->date('expense_date');
            $table->string('supplier_name')->nullable();         // kung cherry purchase
            $table->string('reference_number')->nullable();      // receipt/invoice no.
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('category');
            $table->index('expense_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};