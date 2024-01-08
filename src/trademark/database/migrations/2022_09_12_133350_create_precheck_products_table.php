<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrecheckProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('precheck_products', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('precheck_id')->comment('プレチェックのID（prechecks.id）');
            $table->bigInteger('m_product_id')->comment('商品・サービスのID（m_products.id）');
            $table->bigInteger('admin_id')->comment('管理者のID（admins.id）');
            $table->boolean('is_register_product')->default(0)->comment('プレチェックレポートの登録する商品・サービスのステータス. 0:false | 1: true');
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
        Schema::dropIfExists('precheck_products');
    }
}
