<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Mevcut enum değerlerini güncelle (örnek: 'pending','preparing','ready','delivered','cancelled','zafiyat','kitchen_cancelled')
        DB::statement("ALTER TABLE orders CHANGE status status ENUM('pending','preparing','ready','delivered','cancelled','zafiyat','kitchen_cancelled','musteri_iptal') NOT NULL DEFAULT 'pending'");
    }

    public function down()
    {
        // Geri alırken musteri_iptal'i kaldır
        DB::statement("ALTER TABLE orders CHANGE status status ENUM('pending','preparing','ready','delivered','cancelled','zafiyat','kitchen_cancelled') NOT NULL DEFAULT 'pending'");
    }
};
