<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsConfirmToReasonQuestionDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reason_question_details', function (Blueprint $table) {
            $table->tinyInteger('is_confirm')->default(0)->after('is_answer')->comment('確認＆ロック off a202s 0: false, 1: true');
            $table->string('question', 500)->comment('原案：質問')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reason_question_details', function (Blueprint $table) {
            $table->dropColumn([
                'is_confirm'
            ]);
            $table->string('question', 500)->comment('原案：質問')->nullable(false)->change();
        });
    }
}
