<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldToPlanDetailDistinctsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plan_detail_distincts', function (Blueprint $table) {
            $table->integer('m_distinction_id_edit')->after('is_leave_all')->nullable()->comment('m_distinctions.id');
            $table->boolean('is_distinct_settlement_edit')->after('m_distinction_id_edit')->default(0)->comment('決済が必要な区分を選択するステータス 0: false, 1: true');
            $table->boolean('is_leave_all_edit')->after('is_distinct_settlement_edit')->default(0)->comment('商品・サービス名全て残す 0: false, 1: true');
            $table->boolean('is_add')->after('is_leave_all_edit')->default(0)->comment('追加が必要な区分 of a203c, a203 0: false, 1: true');
            $table->tinyInteger('is_decision')->after('is_add')->default(0)->comment('0: not choose, 1: draft, 2: edit');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plan_detail_distincts', function (Blueprint $table) {
            $table->dropColumn([
                'm_distinction_id_edit',
                'is_distinct_settlement_edit',
                'is_leave_all_edit',
                'is_add',
                'is_decision'
            ]);
        });
    }
}
