<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('module');
            $table->string('action');
            $table->text('description');
            
            // Gidungan nato ang tanang gipangdugang kaniadto
            $table->decimal('quantity', 12, 2)->nullable();
            $table->timestamp('read_at')->nullable();
            $table->nullableMorphs('reference'); // reference_id & reference_type
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};