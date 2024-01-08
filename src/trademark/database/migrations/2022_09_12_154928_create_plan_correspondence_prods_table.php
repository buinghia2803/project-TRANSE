<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanCorrespondenceProdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plan_correspondence_prods', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('plan_correspondence_id')->comment('プラン申込むのID（plan_correspondences.id）');
            $table->bigInteger('application_trademark_product_id')->comment('出願申込む商品・サービスのID（application_trademark_products.id）');
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
        Schema::dropIfExists('plan_correspondence_prods');
    }
}
