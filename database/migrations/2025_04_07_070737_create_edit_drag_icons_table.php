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
        Schema::create('edit_drag_icons', function (Blueprint $table) {
            $table->id();
            
            $table->string('drag_icon');
            $table->string('opt')->nullable();
            $table->string('opt_verify')->nullable();
            $table->dateTime('otp_date')->nullable();
            $table->dateTime('otp_verify_date')->nullable();
            $table->string('edited_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('edit_drag_icons');
    }
};
