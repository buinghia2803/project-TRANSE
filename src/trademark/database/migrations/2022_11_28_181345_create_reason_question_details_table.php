<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReasonQuestionDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reason_question_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('reason_question_id');
            $table->string('question', 500)->comment('原案：質問');
            $table->string('question_edit', 500)->comment('修正：質問')->nullable();
            $table->string('question_decision', 500)->comment('修正：質問')->nullable();
            $table->string('answer', 500)->comment('回答')->nullable();
            $table->boolean('is_answer')->default(0)->comment('0:false | 1:true');
            $table->text('attachment', 500)->comment('書類のアップロード')->nullable();

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
        Schema::dropIfExists('reason_question_details');
    }
}
