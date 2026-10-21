<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('stores', function (Blueprint $table) {
            if (!Schema::hasColumn('stores', 'profile_image')) {
                $table->string('profile_image')->nullable()->after('logo');
            }
        });
    }
    public function down(): void {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn('profile_image');
        });
    }
};