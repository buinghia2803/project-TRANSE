<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdminIdToSupportFirstTimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('support_first_times', function (Blueprint $table) {
            $table->unsignedBigInteger('admin_id')->after('id')->comment('管理者のID(admins.id)');
            $table->foreign('admin_id')->references('id')->on('admins');
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
            $table->dropForeign('support_first_times_admin_id_foreign');
        });

        Schema::table('support_first_times', function (Blueprint $table) {
            $table->dropColumn('admin_id');
        });
    }
}
