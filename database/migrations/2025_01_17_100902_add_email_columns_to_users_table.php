<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmailColumnsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('otplogin_email')->nullable();
            $table->string('otpregister_email')->nullable();
            $table->boolean('email_login_verify')->default(false);
            $table->timestamp('email_otp_time')->nullable();
            $table->boolean('email_reg_verify')->default(false);
            $table->timestamp('email_reg_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'otplogin_email',
                'otpregister_email',
                'email_login_verify',
                'email_otp_time',
                'email_reg_verify',
                'email_reg_time',
            ]);
        });
    }
}
