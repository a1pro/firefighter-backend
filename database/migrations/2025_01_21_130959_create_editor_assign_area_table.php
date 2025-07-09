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
        Schema::create('editor_assign_area', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('area',200)->nullable();
            $table->string('country',200)->nullable();
            $table->string('state',200)->nullable();
            $table->string('city',200)->nullable();
            $table->string('pincode',200)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('editor_assign_area');
    }
};
