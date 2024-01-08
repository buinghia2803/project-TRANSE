<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReasonQuestionNoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reason_question_no', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reason_question_id');
            $table->boolean('is_confirm')->default(0)->comment('0: false,1: true');
            $table->date('user_response_deadline')->nullable()->comment('お客様回答期限日');
            $table->unsignedBigInteger('admin_id');
            $table->tinyInteger('flag_role')->default(1)->comment('画面に作動する管理者ステータス. 1: 担当者 | 2: 責任者');
            $table->tinyInteger('question_status')->default(2)->comment('1: 不要, 2: 必要');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reason_question_no');
    }
}
