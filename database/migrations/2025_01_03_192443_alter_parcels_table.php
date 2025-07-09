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
        Schema::table('parcels', function (Blueprint $table) {
            $table->decimal('pickup_lat', 10, 8)->nullable()->after('assigned_driver_id');
            $table->decimal('pickup_long', 10, 8)->nullable()->after('pickup_lat');

            $table->decimal('deliver_lat', 10, 8)->nullable()->after('pickup_long');
            $table->decimal('deliver_long', 10, 8)->nullable()->after('deliver_lat');


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
