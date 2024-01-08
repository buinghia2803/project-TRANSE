<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocSubmissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('doc_submissions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('trademark_plan_id')->comment('対応方針案のID(trademark_plans.id)');
            $table->bigInteger('admin_id')->comment('管理者のID（admins.id）');
            $table->boolean('is_register_change_info')->default(0)->comment('「登録名義人の表示変更登録申請書を提出」のステータス. 0:false | 1:true');
            $table->boolean('is_written_opinion')->default(0)->comment('意見書不要のステータス. 0:false | 1:true');
            $table->string('description_written_opinion', 500)->comment('意見の内容');
            $table->boolean('is_confirm')->default(0)->comment('確認のステータス. 0:false | 1:true');
            $table->tinyInteger('flag_role')->default(1)->comment('画面に作動する管理者ステータス. 1: 担当者 | 2: 責任者');
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
        Schema::dropIfExists('doc_submissions');
    }
}
