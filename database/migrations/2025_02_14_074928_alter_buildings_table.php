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
        Schema::table('buildings',function(Blueprint $table)
        {
            $table->string('suite_number',200)->nullable()->after('building_name');
            $table->string('unit_number',200)->nullable()->after('suite_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('buildings',function(Blueprint $table){
            $table->dropColumn('suite_number');
            $table->dropColumn('unit_number');

        });
    }
};
