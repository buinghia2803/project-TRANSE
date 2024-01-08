<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImportTypeToMachingResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('maching_results', function (Blueprint $table) {
            $table->tinyInteger('import_type')->nullable()->after('pi_dd_time')->comment('1: IMPORT_DEFAULT, 2: IMPORT_ANKEN_TOP');
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
            $table->dropColumn('import_type');
        });
    }
}
