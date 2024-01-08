<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plan_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('admin_id')->comment('管理者のID（admins.id）');
            $table->bigInteger('plan_id')->comment('プランのID（plans.id）');
            $table->bigInteger('type_plan_id')->comment('方針案のID（type_plans.id）');
            $table->string('plan_description', 500)->comment('方針案の説明');
            $table->tinyInteger('possibility_resolution')->nullable()->comment('解消可能性. 1: ◎ | 2: ○ | 3: △ | 4: ×');
            $table->boolean('is_confirm')->default(0)->comment('確認＆ロックのステータス. 0: false | 1: true');
            $table->boolean('is_choice')->default(0)->comment('お客様の対応策の方針案を選択するステータス. 0: false | 1: true');
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
        Schema::dropIfExists('plan_details');
    }
}
