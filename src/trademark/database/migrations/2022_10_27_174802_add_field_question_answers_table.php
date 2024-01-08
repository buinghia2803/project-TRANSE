<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldQuestionAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('question_answers', function (Blueprint $table) {
            $table->string('question_content_edit', 500)->after('question_content')->nullable()->comment('修正：質問内容');
            $table->string('question_content_decision', 500)->after('question_content_edit')->nullable()->comment('修正：質問内容');
            $table->string('answer_content_edit', 500)->after('answer_content')->nullable()->comment('修正：内容');
            $table->string('answer_content_decision', 500)->after('answer_content_edit')->nullable()->comment('修正：内容');
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
            $table->dropColumn([
                'question_content_edit',
                'question_content_decision',
                'answer_content_edit',
                'answer_content_decision'
            ]);
        });
    }
}
