<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('deleted_restaurants')) {
            Schema::create('deleted_restaurants', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('original_id');
                $table->unsignedBigInteger('business_id');
                $table->string('name');
                $table->string('slug')->nullable();
                $table->string('address')->nullable();
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->text('description')->nullable();
                $table->string('currency')->nullable();
                $table->string('timezone')->nullable();
                $table->string('logo')->nullable();
                $table->string('cover')->nullable();
                $table->boolean('is_featured')->default(false);
                $table->boolean('is_active')->default(true);
                $table->json('settings')->nullable();
                $table->json('deleted_related_data')->nullable(); // This will store related data in JSON format
                $table->timestamp('deleted_at');
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('deleted_restaurants');
    }
};
