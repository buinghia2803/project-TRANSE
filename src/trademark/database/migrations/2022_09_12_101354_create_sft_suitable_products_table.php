<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSftSuitableProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sft_suitable_products', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('admin_id')->comment('管理者のID（admins.id）');
            $table->bigInteger('support_first_time_id')->comment('はじめからサポートのID（support_first_times.id）');
            $table->bigInteger('m_product_id')->comment('商品・サービス名のID（m_products.id）');
            $table->boolean('is_block')->default(0)->comment('確認＆ロックのステータス. 0: false | 1: true');
            $table->boolean('is_choice_user')->default(0)->comment('商品・サービス名を選択するお客様ステータス. 0: false | 1: true');
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
        Schema::dropIfExists('sft_suitable_products');
    }
}
