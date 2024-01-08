<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsDeleteToSftKeepDataProdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sft_keep_data_prods', function (Blueprint $table) {
            $table->boolean('is_delete')->default(0)->after('is_block')->comment('0 false | 1 true');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sft_keep_data_prods', function (Blueprint $table) {
            $table->dropColumn('is_delete');
        });
    }
}
