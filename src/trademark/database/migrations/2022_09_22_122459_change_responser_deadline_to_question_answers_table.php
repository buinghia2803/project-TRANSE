<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeResponserDeadlineToQuestionAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('question_answers', function (Blueprint $table) {
            $table->dropColumn('response_deadline');
            $table->date('response_deadline_user')->nullable()->comment('お客様からのご質問：お客様回答期限日');
            $table->date('response_deadline_admin')->nullable()->comment('・AMSからの質問：ご回答期限日');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('question_answers', function (Blueprint $table) {
            $table->date('response_deadline')->nullable()->comment('・AMSからの質問：ご回答期限日・お客様からのご質問：お客様回答期限日');
        });
    }
}
