<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequiredDocumentPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('required_document_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('required_document_id');
            $table->unsignedBigInteger('plan_id');
            $table->tinyInteger('is_completed')->default(0)->comment('0: false, 1: true | この対応策を完了 of page a204han.html');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('required_document_id')->references('id')->on('required_documents');
            $table->foreign('plan_id')->references('id')->on('plans');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('required_document_plans');
    }
}
