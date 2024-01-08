<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCommentToReasonRefNumProdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reason_ref_num_prods', function (Blueprint $table) {
            $table->string('comment_patent_agent', 1000)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reason_ref_num_prods', function (Blueprint $table) {
            $table->string('comment_patent_agent', 255)->change();
        });
    }
}
