<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTrademarkIdToComparisonTrademarkResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('comparison_trademark_results', function (Blueprint $table) {
            $table->unsignedBigInteger('trademark_id')->after('admin_id')->comment('商標登録のID（trademarks.id）');

            $table->foreign('trademark_id')->references('id')->on('trademarks');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('comparison_trademark_results', function (Blueprint $table) {
            $table->dropForeign('comparison_trademark_results_trademark_id_foreign');

            $table->dropColumn('trademark_id');
        });
    }
}
