<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasColumn('stores', 'profile_image')) {
            Schema::table('stores', function (Blueprint $table) {
                $table->string('profile_image')->nullable()->after('logo');
            });
        }
    }
    public function down(): void {}
};