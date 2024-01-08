<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddResponseDeadlineAmsToNoticeDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notice_details', function (Blueprint $table) {
            $table->date('response_deadline_ams')->nullable()->after('response_deadline')->comment('期限日, AMSへの回答期限日 for U000');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notice_details', function (Blueprint $table) {
            $table->dropColumn('response_deadline_ams');
        });
    }
}
