<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnFilingDateRegisterTrademarks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('register_trademarks', function (Blueprint $table) {
            $table->date('filing_date')->nullable()->after('deadline_update');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('register_trademarks', function (Blueprint $table) {
            $table->dropColumn('filing_date');
        });
    }
}
