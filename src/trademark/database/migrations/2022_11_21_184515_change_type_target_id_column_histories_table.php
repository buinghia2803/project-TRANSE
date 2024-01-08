<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTypeTargetIdColumnHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('histories', function (Blueprint $table) {
            $table->integer('target_id')->comment('「出願」か「はじめからサポート お申し込み」か「はじめからサポートサービス：AMSからの提案」などサービスのID')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('histories', function (Blueprint $table) {
            $table->tinyInteger('target_id')->comment('「出願」か「はじめからサポート お申し込み」か「はじめからサポートサービス：AMSからの提案」などサービスのID')->change();
        });
    }
}
