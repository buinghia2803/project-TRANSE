<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateFieldTypePlanCorrespondencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plan_correspondences', function (Blueprint $table) {
            $table->text('type')->comment('1: シンプル, 2: セレクトン, 3: パックC')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plan_correspondences', function (Blueprint $table) {
            $table->text('type')->comment('1: シンプル, 2: セレクトン')->change();
        });
    }
}
