<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanDocCmtsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plan_doc_cmts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('plan_id');
            $table->foreign('plan_id')->references('id')->on('plans');
            $table->string('from_send_doc', 100)->nullable()->comment('save: u204, u204+1, u204+2');
            $table->string('content', 1000);
            $table->dateTime('date_send')->nullable()->comment('click submit of u204');
            $table->tinyInteger('type')->default(1)->comment('1: u204, 2: u204n');

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
        Schema::dropIfExists('plan_doc_cmts');
    }
}
