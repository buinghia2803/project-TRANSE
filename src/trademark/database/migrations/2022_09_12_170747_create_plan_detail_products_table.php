<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanDetailProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plan_detail_products', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('plan_detail_id')->comment('方針案の詳細のID（plan_details.id）');
            $table->bigInteger('plan_correspondence_prod_id')->comment('拒絶理由通知対応申し込みの商品・サービス名のID（plan_correspondence_prods.id）');
            $table->tinyInteger('leave_status')->comment('1: 残す | 2: 削除 | 3: ※ | 4: - | 5: NG | 6: 追加 | 7: 追加せず | 8: ※（追加） | 9: ※（追加せず）');
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
        Schema::dropIfExists('plan_detail_products');
    }
}
