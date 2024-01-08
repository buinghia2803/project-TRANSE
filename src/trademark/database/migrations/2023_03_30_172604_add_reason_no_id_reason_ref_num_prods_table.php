<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReasonNoIdReasonRefNumProdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reason_ref_num_prods', function (Blueprint $table) {
            $table->bigInteger('reason_no_id')->after('admin_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reason_ref_num_prods', function (Blueprint $table) {
            $table->dropColumn('reason_no_id');
        });
    }
}
