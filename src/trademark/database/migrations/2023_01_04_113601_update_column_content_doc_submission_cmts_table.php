<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateColumnContentDocSubmissionCmtsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('doc_submission_cmts', function (Blueprint $table) {
            $table->text('content', 1000)->comment('コメントの内容')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('doc_submission_cmts', function (Blueprint $table) {
            $table->string('content', 500)->comment('コメントの内容')->change();
        });
    }
}
