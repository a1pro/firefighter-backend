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
        Schema::create('drag_icon', function (Blueprint $table) {
            $table->id();
            $table->string('user_id')->nullable();
            $table->string('building_id')->nullable();
            $table->string('floor_id')->nullable();
            $table->string('basement_id')->nullable();
            $table->string('icon_id')->nullable();
            $table->decimal('latitude',10,8)->nullable();
            $table->decimal('longitude',10,8)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drag_icon');
    }
};
