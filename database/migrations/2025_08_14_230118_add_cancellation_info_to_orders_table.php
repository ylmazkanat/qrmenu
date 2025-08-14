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
        Schema::table('orders', function (Blueprint $table) {
            // İptal/zafiyat bilgilerini kalıcı olarak tutmak için
            $table->enum('original_status', ['pending', 'preparing', 'ready', 'delivered', 'cancelled', 'zafiyat', 'kitchen_cancelled', 'musteri_iptal'])->nullable()->after('status');
            $table->timestamp('status_changed_at')->nullable()->after('original_status');
            $table->string('status_changed_by')->nullable()->after('status_changed_at'); // Hangi kullanıcı değiştirdi
            $table->text('status_change_reason')->nullable()->after('status_changed_by'); // Değişiklik nedeni
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['original_status', 'status_changed_at', 'status_changed_by', 'status_change_reason']);
        });
    }
};
