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
        Schema::table('default_settings', function (Blueprint $table) {
            $table->enum('building_approval',['0','1'])->default('0')->after('youtube_link');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('default_settings', function (Blueprint $table) {
            $table->dropColumn('building_approval');
        });
    }
};
