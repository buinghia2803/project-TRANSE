<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdToNoticesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notices', function (Blueprint $table) {
            $table->bigInteger('trademark_id')->nullable()->change();

            $table->unsignedBigInteger('user_id')->after('trademark_id')->comment('ユーザのID（users.id)');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notices', function (Blueprint $table) {
            $table->bigInteger('trademark_id')->change();

            $table->dropForeign('notices_user_id_foreign');
            $table->dropColumn('user_id');
        });
    }
}
