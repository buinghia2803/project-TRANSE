<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateReasonQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reason_questions', function (Blueprint $table) {
            $table->dropForeign('reason_questions_admin_id_foreign');
            $table->dropColumn([
                'admin_id',
                'question',
                'answer',
                'attachment',
                'is_confirm',
                'user_response_deadline',
                'flag_role',
                'question_status',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reason_questions', function (Blueprint $table) {
            $table->unsignedBigInteger('admin_id')->after('plan_correspondence_id')->comment('管理者のID（admins.id）');
            $table->string('question', 500)->nullable()->comment('質問');
            $table->string('answer', 500)->nullable()->comment('回答');
            $table->text('attachment')->nullable()->comment('書類');
            $table->tinyInteger('is_confirm')->default(0)->comment('確認＆ロックのステータス. 0: false | 1: true');
            $table->date('user_response_deadline')->nullable()->comment('お客様回答期限日');
            $table->tinyInteger('flag_role')->default(0)->comment('画面に作動する管理者ステータス. 1: 担当者 | 2: 責任者');
            $table->tinyInteger('question_status')->default(2)->after('flag_role')->comment('1: 不要, 2: 必要');
        });

        Schema::table('reason_questions', function (Blueprint $table) {
            $table->foreign('admin_id')->references('id')->on('admins');
        });
    }
}
