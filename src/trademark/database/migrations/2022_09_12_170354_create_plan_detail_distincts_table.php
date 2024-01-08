<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanDetailDistinctsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plan_detail_distincts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('plan_detail_id')->comment('方針案の詳細のID（plan_details.id）');
            $table->bigInteger('m_distinction_id')->comment('区分のID（m_distinctions.id）');
            $table->boolean('is_distinct_settlement')->default(0)->comment('決済が必要な区分を選択するステータス. 0:false | 1:true');
            $table->boolean('is_leave_all')->default(0)->comment('商品・サービス名全て残すのステータス. 0:false | 1:true');
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
        Schema::dropIfExists('plan_detail_distincts');
    }
}
