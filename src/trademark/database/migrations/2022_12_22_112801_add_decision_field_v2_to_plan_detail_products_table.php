<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDecisionFieldV2ToPlanDetailProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plan_detail_products', function (Blueprint $table) {
            $table->bigInteger('m_distinction_id_edit')->nullable()->after('plan_detail_distinct_id');
            $table->bigInteger('m_distinction_id_decision')->nullable()->after('m_distinction_id_edit');
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
            $table->dropColumn([
                'm_distinction_id_edit',
                'm_distinction_id_decision',
            ]);
        });
    }
}
