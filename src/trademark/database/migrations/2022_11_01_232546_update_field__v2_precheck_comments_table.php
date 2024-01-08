<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateFieldV2PrecheckCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('precheck_comments', function (Blueprint $table) {
            $table->text('content')->comment('コメントの内容')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('precheck_comments', function (Blueprint $table) {
            $table->dropColumn('content');
        });
    }
}
