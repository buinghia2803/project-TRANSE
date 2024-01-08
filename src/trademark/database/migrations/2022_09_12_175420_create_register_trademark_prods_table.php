<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegisterTrademarkProdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('register_trademark_prods', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('register_trademark_id')->comment('商標登録のID(register_trademarks.id)');
            $table->bigInteger('app_trademark_prod_id')->comment('出願申込む商品・サービスのID（app_trademark_prods.id）');
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
        Schema::dropIfExists('register_trademark_prods');
    }
}
