<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCommentOfficeToAppTrademarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('app_trademarks', function (Blueprint $table) {
            $table->text('comment_office', 500)->nullable()->comment('事務担当者')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('app_trademarks', function (Blueprint $table) {
            $table->string('comment_office', 255)->nullable()->comment('事務担当者')->change();
        });
    }
}
