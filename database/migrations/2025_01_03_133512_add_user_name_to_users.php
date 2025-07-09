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
        Schema::table('users', function (Blueprint $table) {
            $table->string('user_name')->nullable()->after('name');
            $table->string('register_lat')->nullable()->after('branch_id');
            $table->string('register_long')->nullable()->after('register_lat');
            $table->string('login_lat')->nullable()->after('register_long');
            $table->string('login_long')->nullable()->after('login_lat');
        });
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('user_name');
            $table->dropColumn('register_lat');
            $table->dropColumn('register_long');
            $table->dropColumn('login_lat');
            $table->dropColumn('login_long');
        });
    }
    
};
