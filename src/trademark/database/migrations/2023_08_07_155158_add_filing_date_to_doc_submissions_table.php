<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFilingDateToDocSubmissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('doc_submissions', function (Blueprint $table) {
            $table->date('filing_date')->nullable()->after('flag_role');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('doc_submissions', function (Blueprint $table) {
            $table->dropColumn('filing_date');
        });
    }
}
