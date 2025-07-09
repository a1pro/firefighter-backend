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
        Schema::create('building_layout', function (Blueprint $table) {
            $table->id();
            $table->string('building_name',200)->nullable();
            $table->string('address',200)->nullable();
            $table->string('number_of_floors',300)->nullable();
            $table->string('floor_image',300)->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('building_layout');
    }
};
