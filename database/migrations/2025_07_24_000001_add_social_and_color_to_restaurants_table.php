<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->string('instagram')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('twitter')->nullable();
            $table->string('facebook')->nullable();
            $table->string('color_primary', 10)->nullable();
            $table->string('color_secondary', 10)->nullable();
            $table->string('color_cart', 10)->nullable();
            $table->string('wifi_password', 50)->nullable();
        });
    }
    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn(['instagram', 'whatsapp', 'twitter', 'facebook', 'color_primary', 'color_secondary', 'color_cart', 'wifi_password']);
        });
    }
};
