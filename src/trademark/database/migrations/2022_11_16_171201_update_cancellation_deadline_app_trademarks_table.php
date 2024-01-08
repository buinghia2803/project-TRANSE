<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCancellationDeadlineAppTrademarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('app_trademarks', function (Blueprint $table) {
            $table->dateTime('cancellation_deadline')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('app_trademarks', function (Blueprint $table) {
            $table->dateTime('cancellation_deadline')->change();
        });
    }
}
