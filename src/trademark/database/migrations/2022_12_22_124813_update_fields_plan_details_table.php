<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateFieldsPlanDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plan_details', function (Blueprint $table) {
            $table->bigInteger('type_plan_id')->nullable()->change();
            $table->string('plan_description', 1000)->nullable()->change();
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
            $table->bigInteger('type_plan_id')->nullable(false)->change();
            $table->string('plan_description', 1000)->nullable(false)->change();
        });
    }
}
