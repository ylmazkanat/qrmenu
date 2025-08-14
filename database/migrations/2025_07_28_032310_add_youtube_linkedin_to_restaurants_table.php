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
            if (!Schema::hasColumn('restaurants', 'youtube')) {
                $table->string('youtube')->nullable()->after('twitter');
            }
            if (!Schema::hasColumn('restaurants', 'linkedin')) {
                $table->string('linkedin')->nullable()->after('youtube');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn(['youtube', 'linkedin']);
        });
    }
};
