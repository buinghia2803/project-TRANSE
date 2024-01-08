<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsAnswerToNoticeDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notice_details', function (Blueprint $table) {
            $table->boolean('is_answer')->default(0)->after('is_action')->comment('0: false | 1: true');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notice_details', function (Blueprint $table) {
            $table->dropColumn('is_answer');
        });
    }
}
