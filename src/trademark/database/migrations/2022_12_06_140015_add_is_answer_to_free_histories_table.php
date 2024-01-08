<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsAnswerToFreeHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('free_histories', function (Blueprint $table) {
            $table->boolean('is_answer')->default(0)->after('content_answer')->comment('0: false, 1: true');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('free_histories', function (Blueprint $table) {
            $table->dropColumn([
                'is_answer'
            ]);
        });
    }
}
