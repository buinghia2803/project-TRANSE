<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToTrademarkPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trademark_plans', function (Blueprint $table) {
            $table->string('from_send_doc', 100)->nullable()->comment('save: u204, u204+1, u204+2')->after('is_decision');
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
            $table->dropColumn(['from_send_doc']);
        });
    }
}
