<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsPrecheckKeepDataProdResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('precheck_keep_data_prod_results', function (Blueprint $table) {
            $table->bigInteger('precheck_keep_data_id')->after('id');
            $table->bigInteger('m_product_id')->after('precheck_keep_data_prod_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('precheck_keep_data_prod_results', function (Blueprint $table) {
            $table->dropColumn([
                'question_content_edit',
                'question_content_decision',
                'answer_content_edit',
                'answer_content_decision'
            ]);
        });
    }
}
