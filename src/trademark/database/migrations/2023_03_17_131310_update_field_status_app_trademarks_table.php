<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateFieldStatusAppTrademarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('app_trademarks', function (Blueprint $table) {
            $table->integer('status')->comment('0: 保存1: 管理者の確認待ち 2: ユーザの確認待ち 3: ユーザの確認済み')->default('0')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('app_trademarks', function (Blueprint $table) {
            $table->tinyInteger('status')->default(1)->change();
        });
    }
}
