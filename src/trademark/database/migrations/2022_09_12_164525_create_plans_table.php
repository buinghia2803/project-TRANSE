<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('admin_id')->comment('管理者のID（admins.id）');
            $table->bigInteger('trademark_plan_id')->comment('対応方針案のID（trademark_plans.id）');
            $table->tinyInteger('plan_no')->comment('対応策の数');
            $table->boolean('is_completed')->default(0)->comment('この対応策を完了のステータス. 0: false | 1: true');
            $table->string('description_documents_miss', 255)->comment('足りない資料の説明');
            $table->boolean('is_cancel')->default(0)->comment('中止のステータス. 0: false | 1: true');
            $table->string('reason_cancel', 500)->comment('中止の理由');
            $table->boolean('flag_role')->default(1)->comment('画面に作動する管理者ステータス. 1: 担当者 | 2: 責任者');
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
        Schema::dropIfExists('plans');
    }
}
