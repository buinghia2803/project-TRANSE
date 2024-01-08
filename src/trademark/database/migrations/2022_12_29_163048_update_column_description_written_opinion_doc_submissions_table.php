<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateColumnDescriptionWrittenOpinionDocSubmissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('doc_submissions', function (Blueprint $table) {
            $table->text('description_written_opinion', 1000)->comment('意見の内容')->nullable()->change();
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
            $table->string('description_written_opinion', 500)->comment('意見の内容')->change();
        });
    }
}
