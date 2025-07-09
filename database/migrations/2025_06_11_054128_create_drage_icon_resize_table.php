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
        Schema::create('drage_icon_resize', function (Blueprint $table) {
            $table->id();
            $table->string('icon_id');
            $table->text('icon_lat')->nullable();
            $table->text('icon_long')->nullable();
            $table->text('point1_lat')->nullable();
            $table->text('pont1_long')->nullable();
            $table->text('point2_lat')->nullable();
            $table->text('pont2_long')->nullable();
            $table->text('point3_lat')->nullable();
            $table->text('pont3_long')->nullable();
            $table->text('point4_lat')->nullable();
            $table->text('pont4_long')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drage_icon_resize');
    }
};
