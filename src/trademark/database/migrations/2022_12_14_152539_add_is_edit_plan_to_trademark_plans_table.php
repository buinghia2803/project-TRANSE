<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsEditPlanToTrademarkPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trademark_plans', function (Blueprint $table) {
            $table->boolean('is_edit_plan')->after('is_confirm')->default(0)->comment('IsSave at a203c_shu');
            $table->boolean('is_decision')->after('is_edit_plan')->default(0)->comment('0: not choose | 1: draft | 2: edit');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trademark_plans', function (Blueprint $table) {
            $table->dropColumn(['is_edit_plan', 'is_decision']);
        });
    }
}
