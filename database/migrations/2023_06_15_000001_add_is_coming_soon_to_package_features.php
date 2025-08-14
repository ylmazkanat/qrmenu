<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Sütun var mı diye kontrol edelim
        if (!Schema::hasColumn('package_features', 'is_coming_soon')) {
            Schema::table('package_features', function (Blueprint $table) {
                $table->boolean('is_coming_soon')->default(false);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('package_features', function (Blueprint $table) {
            $table->dropColumn('is_coming_soon');
        });
    }
};