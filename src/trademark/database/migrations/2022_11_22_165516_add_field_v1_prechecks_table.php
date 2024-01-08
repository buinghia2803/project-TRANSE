<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldV1PrechecksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('prechecks', function (Blueprint $table) {
            $table->tinyInteger('is_confirm')->default(0)->after('flag_role');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('prechecks', function (Blueprint $table) {
            $table->dropColumn([
                'is_confirm'
            ]);
        });
    }
}
