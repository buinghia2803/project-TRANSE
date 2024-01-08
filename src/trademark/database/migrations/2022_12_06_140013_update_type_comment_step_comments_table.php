<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTypeCommentStepCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plan_comments', function (Blueprint $table) {
            $table->bigInteger('type_comment_step')->default(1)->comment('1: 対応方針案作成, //a203c.html, 2: 拒絶理由通知対応：対応方針案　承認・差し戻し //a203s, 3: 対応方針案差し戻し, //a203sashi, 4: 拒絶理由通知対応：対応方針案修正 //a203shu, 5: 対応方針案再作成, // a203n.html')->change();
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
            $table->bigInteger('type_comment_step')->default(1)->comment('1: 対応方針案作成 | 2: 拒絶理由通知対応：必要資料')->change();
        });
    }
}
