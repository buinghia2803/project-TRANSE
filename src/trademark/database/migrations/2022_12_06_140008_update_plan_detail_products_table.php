<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePlanDetailProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plan_detail_products', function (Blueprint $table) {
            $table->dropForeign('plan_detail_products_plan_correspondence_prod_id_foreign');
        });

        Schema::table('plan_detail_products', function (Blueprint $table) {
            $table->dropColumn(['plan_correspondence_prod_id']);

            $table->integer('m_product_id')->after('plan_detail_id')->nullable();
            $table->string('product_name_edit', 255)->after('m_product_id')->nullable();
            $table->tinyInteger('leave_status_edit')->after('leave_status')->nullable()->default(null)->comment('1: 残す, 2: 削除, 3: ※, 4: -, 5: NG, 6: 追加, 7: 追加せず, 8: ※（追加）, 9: ※（追加せず）');
            $table->tinyInteger('role_add')->default(0)->after('leave_status_edit')->comment('1: その他, 2: 担当者, 3: 責任者');
            $table->boolean('is_choice')->after('role_add')->default(0)->comment('0: false, 1: true');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plan_detail_products', function (Blueprint $table) {
            $table->bigInteger('plan_correspondence_prod_id')->comment('拒絶理由通知対応申し込みの商品・サービス名のID（plan_correspondence_prods.id）');

            $table->dropColumn([
                'm_product_id',
                'leave_status_edit',
                'is_choice',
                'product_name_edit',
                'role_add'

            ]);
        });

        Schema::table('plan_detail_products', function (Blueprint $table) {
            $table->unsignedBigInteger('plan_correspondence_prod_id')->after('id')->change();
            $table->foreign('plan_correspondence_prod_id')->references('id')->on('plan_correspondence_prods');
        });
    }
}
