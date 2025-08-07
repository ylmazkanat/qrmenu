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
        DB::statement("ALTER TABLE business_subscriptions MODIFY COLUMN status ENUM('active', 'inactive', 'cancelled', 'expired', 'pending_cancellation') NOT NULL DEFAULT 'active'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE business_subscriptions MODIFY COLUMN status ENUM('active', 'inactive', 'cancelled', 'expired') NOT NULL DEFAULT 'active'");
    }
};
