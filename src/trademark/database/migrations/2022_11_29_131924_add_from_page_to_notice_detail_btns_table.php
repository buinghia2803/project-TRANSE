<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFromPageToNoticeDetailBtnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notice_detail_btns', function (Blueprint $table) {
            $table->string('from_page', 255)->nullable()->after('date_click');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notice_detail_btns', function (Blueprint $table) {
            $table->dropColumn(['from_page']);
        });
    }
}
