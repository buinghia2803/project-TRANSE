<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLeaveStatusOtherToPlanDetailProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plan_detail_products', function (Blueprint $table) {
            $table->text('leave_status_other')->nullable()->after('leave_status')->comment('Save json');
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
            $table->dropColumn(['leave_status_other']);
        });
    }
}
