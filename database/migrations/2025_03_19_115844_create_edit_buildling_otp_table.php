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
    Schema::create('edit_building_otp', function (Blueprint $table) {
        $table->id();
        $table->string('building_id')->nullable();
        $table->string('otp')->nullable();
        $table->string('otp_verify')->nullable();
        $table->dateTime('otp_date')->nullable();  // Change to datetime
        $table->dateTime('otp_verify_date')->nullable();  // Change to datetime
        $table->string('edited_by')->nullable();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('edit_buildling_otp');
    }
};
