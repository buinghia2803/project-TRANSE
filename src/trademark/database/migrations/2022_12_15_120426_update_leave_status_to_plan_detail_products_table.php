<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateLeaveStatusToPlanDetailProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plan_detail_products', function (Blueprint $table) {
            $table->smallInteger('leave_status')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plan_detail_products', function (Blueprint $table) {
            $table->smallInteger('leave_status')->nullable(false)->change();
        });
    }
}
