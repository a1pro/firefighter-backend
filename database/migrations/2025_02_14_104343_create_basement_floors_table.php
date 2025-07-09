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
        Schema::create('basement_floors', function (Blueprint $table) {
            $table->id();
            $table->string('building_id')->nullable();
            $table->string('basement_name')->nullable();
            $table->string('basement_image')->nullable();
            $table->enum('status',['0','1'])->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('basement_floors');
    }
};
