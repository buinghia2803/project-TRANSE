<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTrademarkIdColumnMachingResultTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('maching_results', function (Blueprint $table) {
            $table->bigInteger('trademark_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('maching_results', function (Blueprint $table) {
            $table->bigInteger('trademark_id')->nullable(false)->change();
        });
    }
}
