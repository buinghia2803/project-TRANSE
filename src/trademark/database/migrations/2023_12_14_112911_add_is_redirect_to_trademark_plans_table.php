<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsRedirectToTrademarkPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trademark_plans', function (Blueprint $table) {
            $table->boolean('is_redirect')->default(0)->after('is_confirm')->comment('0 false | 1 true');
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
            $table->dropColumn('is_redirect');
        });
    }
}
