<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrademarkInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trademark_infos', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('target_id')->comment('「出願」か「はじめからサポート お申し込み」か「はじめからサポートサービス：AMSからの提案」などサービスのID');
            $table->tinyInteger('type_acc')->comment('法人または個人. 1: 法人 | 2: 個人');
            $table->string('name', 50)->comment('法人名');
            $table->bigInteger('m_nation_id')->comment('所在国のID（m_nations.id)');
            $table->bigInteger('m_prefecture_id')->comment('都道府県のID（m_prefectures.id）');
            $table->string('address_second', 255)->nullable()->comment('所在地または住所-2');
            $table->string('address_three', 255)->nullable()->comment('所在地または住所-3');
            $table->tinyInteger('type')->default(1)->comment('1: 出願 | 2: はじめからサポート お申し込み | 3: はじめからサポートサービス：AMSからの提案');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trademark_infos');
    }
}
