<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusRegisterSupportFirstTimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('support_first_times', function (Blueprint $table) {
            $table->tinyInteger('status_register')->default(1)->after('is_confirm')->comment('1:  保存,//save 2: 申し込み//submit');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('support_first_times', function (Blueprint $table) {
            $table->dropColumn('status_register');
        });
    }
}
