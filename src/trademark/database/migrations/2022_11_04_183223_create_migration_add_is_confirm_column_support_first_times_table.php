<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMigrationAddIsConfirmColumnSupportFirstTimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('support_first_times', function (Blueprint $table) {
            $table->tinyInteger('is_confirm')->default(0)->after('flag_role')->comment('0: not confirm;1: confirm');
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
            $table->dropColumn('is_confirm');
        });
    }
}
