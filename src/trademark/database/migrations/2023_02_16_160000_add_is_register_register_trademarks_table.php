<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsRegisterRegisterTrademarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('register_trademarks', function (Blueprint $table) {
            $table->boolean('is_register')->default(0)->comment('0: false, 1: true')->after('is_send_mail');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('register_trademarks', function (Blueprint $table) {
            $table->dropColumn('is_register');
        });
    }
}
