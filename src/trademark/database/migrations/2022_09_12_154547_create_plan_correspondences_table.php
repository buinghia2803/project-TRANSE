<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanCorrespondencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plan_correspondences', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('comparison_trademark_result_id')->comment('拒絶理由通知対応：突合結果のID（comparison_trademark_results.id）');
            $table->tinyInteger('type')->default(1)->comment('1: シンプル | 2: セレクトン');
            $table->boolean('is_ext_period')->default(0)->comment('期限日前期間延長のお申し込みのステータス（u201simple01.html）. 0: false | 1: true');
            $table->boolean('is_ext_period_2')->default(0)->comment('期限日前期間延長のお申し込みのステータス（u201select01.html）. 0: false | 1: true');
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
        Schema::dropIfExists('plan_correspondences');
    }
}
