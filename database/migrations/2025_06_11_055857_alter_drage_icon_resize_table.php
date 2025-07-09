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
        Schema::table('drage_icon_resize',function(Blueprint $table){
            $table->string('building_id')->nullable()->after('icon_id');
            $table->string('floor_id')->nullable()->after('building_id');
            $table->string('basement_id')->nullable()->after('building_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::table('drage_icon_resize',function(Blueprint $table){
            $table->dropColumn('building_id');
            $table->dropColumn('floor_id');
            $table->dropColumn('basement_id');
        });
    }
};
