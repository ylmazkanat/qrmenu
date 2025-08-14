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
        Schema::table('restaurants', function (Blueprint $table) {
            if (!Schema::hasColumn('restaurants', 'primary_color')) {
                $table->string('primary_color', 7)->default('#f19c01')->after('custom_domain');
            }
            if (!Schema::hasColumn('restaurants', 'secondary_color')) {
                $table->string('secondary_color', 7)->default('#212121')->after('primary_color');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn(['primary_color', 'secondary_color']);
        });
    }
};
