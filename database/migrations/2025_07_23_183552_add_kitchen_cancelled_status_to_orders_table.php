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
        // Eğer enum ise ALTER TABLE ile kitchen_cancelled eklenebilir, yoksa açıklama bırakıyoruz.
        // Kodda string olarak kullanılacak.
        // Schema::table('orders', function (Blueprint $table) {
        //     // Enum ise burada ALTER TABLE ile eklenebilir.
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Geri alma işlemi gerekmez.
    }
};
