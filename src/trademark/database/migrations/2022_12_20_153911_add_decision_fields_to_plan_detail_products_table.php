<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDecisionFieldsToPlanDetailProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plan_detail_products', function (Blueprint $table) {
            $table->string('product_name_decision', 255)->nullable()->after('product_name_edit');

            $table->text('leave_status_other_edit')->nullable()->after('leave_status_edit');

            $table->tinyInteger('leave_status_decision')->nullable()->after('leave_status_other_edit')->comment('1: 残す | 2: 削除 | 3: ※ | 4: - | 5: NG | 6: 追加 | 7: 追加せず | 8: ※（追加）| 9: ※（追加せず）');
            $table->text('leave_status_other_decision')->nullable()->after('leave_status_decision');
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
            $table->dropColumn([
                'product_name_decision',
                'leave_status_other_edit',
                'leave_status_decision',
                'leave_status_other_decision',
            ]);
        });
    }
}
