<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayerInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payer_infos', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('target_id')->comment('「出願」か「はじめからサポート お申し込み」か「はじめからサポートサービス：AMSからの提案」などサービスのID');
            $table->tinyInteger('payment_type')->default(1)->comment('支払方法. 1: クレジットカード | 2: 銀行振込');
            $table->tinyInteger('payer_type')->default(1)->comment('支払者種別. 1: 法人等、源泉徴収義務者 | 2: 個人、登記住所が海外の法人');
            $table->bigInteger('m_nation_id')->comment('所在国のID（m_nations.id）');
            $table->string('payer_name', 50)->comment('支払者（法人）名');
            $table->string('payer_name_furigana', 50)->comment('支払者（法人）名（ふりがな）');
            $table->string('postal_code', 7)->comment('郵便番号');
            $table->bigInteger('m_prefecture_id')->comment('都道府県のID（m_prefectures.id）');
            $table->string('address_second', 255)->nullable()->comment('住所-2');
            $table->string('address_three', 255)->nullable()->comment('住所-3');
            $table->tinyInteger('type')->default(1)->comment('1: 出願 | 2: はじめからサポート お申し込み | 3: はじめからサポートサービス：AMSからの提案 | 4: プレチェックサービス | 5: プレチェックサービス：AMSからのレポート | 6: 拒絶理由通知対応 | 7: 拒絶理由通知対応：方針案選択 | 8: 商標登録 | 9: 後期納付期限のお知らせ・納付手続きのお申込み');
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
        Schema::dropIfExists('payer_infos');
    }
}
