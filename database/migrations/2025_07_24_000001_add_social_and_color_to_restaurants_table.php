<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            if (!Schema::hasColumn('restaurants', 'instagram')) {
                $table->string('instagram')->nullable();
            }
            if (!Schema::hasColumn('restaurants', 'whatsapp')) {
                $table->string('whatsapp')->nullable();
            }
            if (!Schema::hasColumn('restaurants', 'twitter')) {
                $table->string('twitter')->nullable();
            }
            if (!Schema::hasColumn('restaurants', 'facebook')) {
                $table->string('facebook')->nullable();
            }
            if (!Schema::hasColumn('restaurants', 'color_primary')) {
                $table->string('color_primary', 10)->nullable();
            }
            if (!Schema::hasColumn('restaurants', 'color_secondary')) {
                $table->string('color_secondary', 10)->nullable();
            }
            if (!Schema::hasColumn('restaurants', 'color_cart')) {
                $table->string('color_cart', 10)->nullable();
            }
            if (!Schema::hasColumn('restaurants', 'wifi_password')) {
                $table->string('wifi_password', 50)->nullable();
            }
        });
    }
    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn(['instagram', 'whatsapp', 'twitter', 'facebook', 'color_primary', 'color_secondary', 'color_cart', 'wifi_password']);
        });
    }
};
