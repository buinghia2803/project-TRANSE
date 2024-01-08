<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldV2ToFreeHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('free_histories', function (Blueprint $table) {
            $table->bigInteger('maching_result_id')->nullable()->after('trademark_id');
            $table->date('user_response_deadline')->nullable()->change();
            $table->date('patent_response_deadline')->nullable()->change();
            $table->text('attachment')->nullable()->change();
            $table->string('internal_remark', 1000)->nullable()->change();
            $table->string('comment', 1000)->comment('AMSからのコメント')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('free_histories', function (Blueprint $table) {
            $table->date('user_response_deadline')->nullable(false)->change();
            $table->date('patent_response_deadline')->nullable(false)->change();
            $table->text('attachment')->nullable(false)->change();
            $table->string('internal_remark', 1000)->nullable(false)->change();
            $table->string('comment', 1000)->change();

            $table->dropColumn([
                 'maching_result_id',
            ]);
        });
    }
}
