<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanReasonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plan_reasons', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('plan_id')->comment('プランのID（plans.id）');
            $table->bigInteger('reason_id')->comment('理由のID（reasons.id）');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plan_reasons');
    }
}
