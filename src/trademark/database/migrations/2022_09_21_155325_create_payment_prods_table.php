<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentProdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_prods', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('payment_id')->comment('請求金額のテーブルのID(payments.id)');
            $table->bigInteger('m_product_id')->comment('商品・サービス名のID（m_products.id)');
            $table->timestamp('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_prods');
    }
}
