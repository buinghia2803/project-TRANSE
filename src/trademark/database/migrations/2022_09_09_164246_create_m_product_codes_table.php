<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMProductCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('m_product_codes', function (Blueprint $table) {
            $table->bigInteger('m_product_id')->comment('商品・サービスのID（m_products.id）');
            $table->bigInteger('m_code_id')->comment('類似群コードのID（m_code.id）');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('m_product_codes');
    }
}
