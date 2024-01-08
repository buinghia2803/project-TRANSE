<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrecheckKeepDataProdResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('precheck_keep_data_prod_results', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('precheck_keep_data_prod_id');
            $table->bigInteger('m_code_id');
            $table->tinyInteger('result_identification_detail_edit')->nullable()->comment('1: ○ - 登録可能性が高い。| 2: △ - 登録に期待が持てる。| 3: ▲ - 登録可能性は低い。| 4: × - 登録するのは難しい。');
            $table->tinyInteger('result_similar_detail_edit')->nullable()->comment('1: ○ - 登録可能性が高い。| 2: △ - 登録に期待が持てる。| 3: ▲ - 登録可能性は低い。| 4: × - 登録するのは難しい。');
            $table->tinyInteger('is_decision_draft')->default(0)->comment(' 0: false, 1: true');
            $table->tinyInteger('is_decision_edit')->default(0)->comment(' 0: false, 1: true');
            $table->tinyInteger('is_decision_similar_draft')->default(0)->comment(' 0: false, 1: true');
            $table->tinyInteger('is_decision_similar_edit')->default(0)->comment(' 0: false, 1: true');
            $table->tinyInteger('is_block_identification')->default(0)->comment(' 0: false, 1: true');
            $table->tinyInteger('is_block_similar')->default(0)->comment(' 0: false, 1: true');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('precheck_keep_data_prod_results');
    }
}
