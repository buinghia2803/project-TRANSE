<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReasonQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reason_questions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('plan_correspondence_id')->comment('プラン申込むのID（plan_correspondences.id）');
            $table->bigInteger('admin_id')->comment('管理者のID（admins.id）');
            $table->string('question', 500)->comment('質問');
            $table->string('answer', 500)->nullable()->comment('回答');
            $table->text('attachment')->nullable()->comment('書類');
            $table->tinyInteger('is_confirm')->default(0)->comment('確認＆ロックのステータス. 0: false | 1: true');
            $table->date('user_response_deadline')->nullable()->comment('お客様回答期限日');
            $table->tinyInteger('flag_role')->default(0)->comment('画面に作動する管理者ステータス. 1: 担当者 | 2: 責任者');
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
        Schema::dropIfExists('reason_questions');
    }
}
