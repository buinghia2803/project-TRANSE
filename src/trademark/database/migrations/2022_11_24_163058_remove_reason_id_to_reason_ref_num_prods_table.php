<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveReasonIdToReasonRefNumProdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reason_ref_num_prods', function (Blueprint $table) {
            $table->dropForeign('reason_ref_num_prods_reason_id_foreign');

            $table->dropColumn(['reason_id']);
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
            $table->bigInteger('reason_id')->comment('理由のID（reasons.id）');
        });

        Schema::table('reason_ref_num_prods', function (Blueprint $table) {
            $table->unsignedBigInteger('reason_id')->change();
            $table->foreign('reason_id')->references('id')->on('reasons');
        });
    }
}
