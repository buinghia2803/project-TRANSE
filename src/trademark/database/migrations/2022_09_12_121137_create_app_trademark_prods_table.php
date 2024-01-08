<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppTrademarkProdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_trademark_prods', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('app_trademark_id')->comment('出願登録のID（app_trademarks.id）');
            $table->bigInteger('m_product_id')->comment('商品・サービスのID（m_products.id)');
            $table->boolean('is_apply')->default(0)->comment('出願するには、商品・サービスを選択するお客様のステータス. 0: false | 1: true');
            $table->boolean('is_remove')->default(0)->comment('の削除のステータス. 0: false | 1: true');
            $table->boolean('is_new_prod')->default(0)->comment('の商品・サービスを追加のステータス. 0: false | 1: true');
            $table->boolean('is_block')->default(0)->comment('の確認＆ロックのステータス. 0: false | 1: true');
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
        Schema::dropIfExists('app_trademark_prods');
    }
}
