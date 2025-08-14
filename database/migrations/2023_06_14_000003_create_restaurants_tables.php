<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRestaurantsTables extends Migration
{
    public function up()
    {
        Schema::dropIfExists('restaurants');
        Schema::create('restaurants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->onDelete('cascade');
            $table->foreignId('restaurant_manager_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('logo')->nullable();
            $table->string('header_image')->nullable();
            $table->string('primary_color')->nullable();
            $table->string('secondary_color')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('twitter')->nullable();
            $table->string('youtube')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('whatsapp')->nullable();
            $table->text('address')->nullable();
            $table->integer('table_count')->default(10);
            $table->json('working_hours')->nullable();
            $table->boolean('translation_enabled')->default(false);
            $table->string('default_language')->default('tr');
            $table->json('supported_languages')->nullable();
            $table->string('custom_domain')->nullable();
            $table->string('subdomain')->nullable()->unique();
            $table->string('wifi_password')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('deleted_restaurants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('original_id');
            $table->unsignedBigInteger('business_id');
            $table->string('name');
            $table->string('slug')->nullable();
            $table->text('description')->nullable();
            $table->string('logo')->nullable();
            $table->string('header_image')->nullable();
            $table->string('primary_color')->nullable();
            $table->string('secondary_color')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->text('address')->nullable();
            $table->integer('table_count')->nullable();
            $table->json('working_hours')->nullable();
            $table->json('settings')->nullable();
            $table->json('deleted_related_data')->nullable();
            $table->timestamp('deleted_at');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('deleted_restaurants');
        Schema::dropIfExists('restaurants');
    }
};
