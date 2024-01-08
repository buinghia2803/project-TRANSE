<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddData205ToDocSubmissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('doc_submissions', function (Blueprint $table) {
            $table->text('data_a205')->nullable()->after('description_written_opinion');
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
            $table->dropColumn('data_a205');
        });
    }
}
