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
        Schema::table('editor_assign_area', function (Blueprint $table) {
            $table->string('county', 100)->nullable()->after('state');
         


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('editor_assign_area', function (Blueprint $table) {

            $table->dropColumn('county');

        });
    }
};
