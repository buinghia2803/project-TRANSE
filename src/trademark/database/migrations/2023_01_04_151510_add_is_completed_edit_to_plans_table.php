<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsCompletedEditToPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->boolean('is_completed_edit')->default(false)->after('is_completed')->comment('0: false, 1: true');
        });

        Schema::table('plan_detail_docs', function (Blueprint $table) {
            $table->boolean('is_completed_edit')->default(false)->after('is_completed')->comment('0: false, 1: true');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn(['is_completed_edit']);
        });

        Schema::table('plan_detail_docs', function (Blueprint $table) {
            $table->dropColumn(['is_completed_edit']);
        });
    }
}
