<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnTrademarkPlanIdPlanCommentsTableV2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plan_comments', function (Blueprint $table) {
            $table->unsignedBigInteger('trademark_plan_id')->after('trademark_id')->index();
            $table->foreign('trademark_plan_id')->references('id')->on('trademark_plans');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plan_comments', function (Blueprint $table) {
            $table->dropForeign('plan_comments_trademark_plan_id_foreign');
            $table->dropColumn(['trademark_plan_id']);
        });
    }
}
