<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReasonQuestionNoIdToReasonQuestionDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reason_question_details', function (Blueprint $table) {
            $table->unsignedBigInteger('reason_question_no_id')->after('reason_question_id');
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
            $table->dropColumn('reason_question_no_id');
        });
    }
}
