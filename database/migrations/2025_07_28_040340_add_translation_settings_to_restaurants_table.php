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
            $table->boolean('translation_enabled')->default(false)->after('is_active');
            $table->string('default_language', 10)->default('tr')->after('translation_enabled');
            $table->json('supported_languages')->nullable()->after('default_language');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn(['translation_enabled', 'default_language', 'supported_languages']);
        });
    }
};
