<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldResultIdentificationDetailFinalPrecheckKeepDataProdResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('precheck_keep_data_prod_results', function (Blueprint $table) {
            $table->tinyInteger('result_identification_detail_final')->nullable()->comment('1: ○ - 登録可能性が高い。| 2: △ - 登録に期待が持てる。| 3: ▲ - 登録可能性は低い。| 4: × - 登録するのは難しい。')->after('result_identification_detail_edit');
            $table->tinyInteger('result_similar_detail_final')->nullable()->comment('1: ○ - 登録可能性が高い。| 2: △ - 登録に期待が持てる。| 3: ▲ - 登録可能性は低い。| 4: × - 登録するのは難しい。')->after('result_similar_detail_edit');
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
                'result_identification_detail_final',
                'result_similar_detail_final'
            ]);
        });
    }
}
