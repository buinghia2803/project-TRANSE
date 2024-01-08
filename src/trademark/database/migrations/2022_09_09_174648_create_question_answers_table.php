<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('question_answers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->comment('ユーザのID（users.id)');
            $table->bigInteger('admin_id')->comment('管理者のID（admins.id)');
            $table->string('question_content', 500)->comment('質問内容');
            $table->text('question_attaching_file')->nullable()->comment('ファイル添付. フォーマット： ["file1", "file 2"]');
            $table->tinyInteger('question_type')->default(1)->comment('1: お客様からのご質問 | 2: AMSからの質問');
            $table->string('answer_content', 500)->nullable()->comment('内容');
            $table->text('answer_attaching_file')->nullable()->comment('ファイル添付. フォーマット： ["file1", "file 2"]');
            $table->timestamp('question_date')->comment('質問日月');
            $table->timestamp('answer_date')->comment('回答日月');
            $table->date('response_deadline')->nullable()->comment('・AMSからの質問：ご回答期限日・お客様からのご質問：お客様回答期限日');
            $table->string('office_comments', 255)->nullable()->comment('社内用コメント');
            $table->boolean('is_confirm')->default(0)->comment('確定. タイプ：0: false,1: true');
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
        Schema::dropIfExists('question_answers');
    }
}
