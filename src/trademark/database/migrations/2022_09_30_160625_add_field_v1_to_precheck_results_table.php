<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldV1ToPrecheckResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('precheck_results', function (Blueprint $table) {
            $table->bigInteger('precheck_id')->after('admin_id')->comment('プレチェックのID（prechecks.id）');
            $table->bigInteger('m_code_id')->after('precheck_product_id')->comment('類似群コードのID（m_code.id）');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('precheck_results', function (Blueprint $table) {
            $table->dropColumn([
                'precheck_id',
                'm_code_id',
            ]);
        });
    }
}
