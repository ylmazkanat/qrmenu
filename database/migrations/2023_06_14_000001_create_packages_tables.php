<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackagesTables extends Migration
{
    public function up()
    {
        Schema::dropIfExists('packages');
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('duration_months');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::dropIfExists('package_features');
        Schema::create('package_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('limit')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_coming_soon')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('package_features');
        Schema::dropIfExists('packages');
    }
};
