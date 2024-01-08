<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequiredDocumentMissTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('required_document_miss', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('required_document_id');
            $table->unsignedBigInteger('plan_id');
            $table->string('description_docs_miss')->nullable()->comment('足りない資料の説明 of page a204n.html');
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
        Schema::dropIfExists('required_document_miss');
    }
}
