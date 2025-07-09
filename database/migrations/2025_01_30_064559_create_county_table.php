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
        Schema::create('county', function (Blueprint $table) {
            $table->id();
            $table->string('county',200)->nullable();
            $table->string('county_ascii',200)->nullable();
            $table->string('county_full',200)->nullable();
            $table->string('county_fips',200)->nullable();
            $table->string('state_id',200)->nullable();
            $table->string('state_name',200)->nullable();
            $table->string('lat',200)->nullable();
            $table->string('lng',200)->nullable();
            $table->string('population',200)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('county');
    }
};
