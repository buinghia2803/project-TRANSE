<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldIsUpdatedChangeInfoRegistersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('change_info_registers', function (Blueprint $table) {
            $table->tinyInteger('is_updated')->default(0)->after('representative_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('change_info_registers', function (Blueprint $table) {
            $table->dropColumn('is_updated');
        });
    }
}
