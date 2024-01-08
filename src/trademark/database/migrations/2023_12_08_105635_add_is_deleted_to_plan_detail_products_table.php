<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsDeletedToPlanDetailProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plan_detail_products', function (Blueprint $table) {
            $table->boolean('is_deleted')->default(0)->after('role_add')->comment('0 false | 1 true');
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
            $table->dropColumn('is_deleted');
        });
    }
}
