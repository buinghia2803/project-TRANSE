<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldV1PrecheckKeepDataProdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('precheck_keep_data_prods', function (Blueprint $table) {
            $table->bigInteger('m_product_id')->after('precheck_keep_data_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('precheck_keep_data_prods', function (Blueprint $table) {
            $table->dropColumn('m_product_id');
        });
    }
}
