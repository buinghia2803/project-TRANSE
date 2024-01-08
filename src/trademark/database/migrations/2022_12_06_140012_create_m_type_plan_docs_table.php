<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMTypePlanDocsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('m_type_plan_docs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('m_type_plan_id');
            $table->foreign('m_type_plan_id')->references('id')->on('m_type_plans');
            $table->string('name', 255)->nullable();
            $table->text('description')->nullable();
            $table->text('url')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('m_type_plan_docs');
    }
}
