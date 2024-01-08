<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAdminIdConfirmToFreeHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('free_histories', function (Blueprint $table) {
            $table->dropForeign('free_histories_admin_id_confirm_foreign');
        });

        Schema::table('free_histories', function (Blueprint $table) {
            $table->bigInteger('admin_id_confirm')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('free_histories', function (Blueprint $table) {
            $table->unsignedBigInteger('admin_id_confirm')->nullable(false)->change();
            $table->foreign('admin_id_confirm')->references('id')->on('admins');
        });
    }
}
