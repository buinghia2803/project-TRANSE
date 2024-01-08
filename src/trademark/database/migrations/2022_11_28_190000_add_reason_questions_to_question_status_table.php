<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReasonQuestionsToQuestionStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reason_questions', function (Blueprint $table) {
            $table->tinyInteger('question_status')->default(2)->after('flag_role')->comment('1: 不要, 2: 必要');
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
            $table->dropColumn([ 'question_status' ]);
        });
    }
}
