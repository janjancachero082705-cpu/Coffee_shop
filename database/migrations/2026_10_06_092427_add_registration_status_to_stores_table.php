<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            if (!Schema::hasColumn('stores', 'registration_status')) {
                $table->enum('registration_status', ['pending', 'approved', 'rejected'])
                      ->default('approved')
                      ->after('status');
            }
            if (!Schema::hasColumn('stores', 'registration_notes')) {
                $table->text('registration_notes')->nullable()->after('registration_status');
            }
            if (!Schema::hasColumn('stores', 'rejected_reason')) {
                $table->string('rejected_reason')->nullable()->after('registration_notes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn(['registration_status', 'registration_notes', 'rejected_reason']);
        });
    }
};