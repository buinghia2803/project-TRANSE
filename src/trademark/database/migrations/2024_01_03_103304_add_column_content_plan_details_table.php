<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnContentPlanDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plan_details', function (Blueprint $table) {
            $table->string('plan_content', 1000)->after('plan_description')->nullable();
            $table->string('plan_content_edit', 1000)->after('plan_description_edit')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plan_details', function (Blueprint $table) {
            $table->dropColumn('plan_content');
            $table->dropColumn('plan_content_edit');
        });
    }
}
