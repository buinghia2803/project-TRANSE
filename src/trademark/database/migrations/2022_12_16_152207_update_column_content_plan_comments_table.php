<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateColumnContentPlanCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plan_comments', function (Blueprint $table) {
            $table->string('content', 500)->comment('コメントの内容')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plan_comments', function (Blueprint $table) {
            $table->string('content', 500)->comment('コメントの内容')->change();
        });
    }
}
