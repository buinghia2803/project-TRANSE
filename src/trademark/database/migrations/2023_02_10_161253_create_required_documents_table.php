<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequiredDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('required_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('trademark_plan_id')->unsigned()->nullable();
            $table->tinyInteger('is_confirm')->default(0)->comment('0: false, 1: true | a204n');
            $table->tinyInteger('is_send')->default(0)->comment('0: false, 1: true | u204n, u204');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('trademark_plan_id')->references('id')->on('trademark_plans');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('required_documents');
    }
}
