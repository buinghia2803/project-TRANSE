<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsApplyToPrecheckProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('precheck_products', function (Blueprint $table) {
            $table->bigInteger('is_apply')->default(0)->after('is_register_product')->comment('0: uncheck;1: checked');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('precheck_products', function (Blueprint $table) {
            $table->dropColumn([ 'is_apply' ]);
        });
    }
}
