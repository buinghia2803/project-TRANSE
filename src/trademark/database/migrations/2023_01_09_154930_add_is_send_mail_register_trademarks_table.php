<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsSendMailRegisterTrademarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('register_trademarks', function (Blueprint $table) {
            $table->tinyInteger('is_send_mail')->default(0)->after('is_cancel')->comment('0: not send, 1: sended');
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
            $table->dropColumn('is_send_mail');
        });
    }
}
