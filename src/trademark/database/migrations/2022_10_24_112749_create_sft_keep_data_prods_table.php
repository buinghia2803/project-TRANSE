<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSftKeepDataProdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sft_keep_data_prods', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sft_keep_data_id')->index();
            $table->foreign('sft_keep_data_id')->references('id')->on('sft_keep_datas');
            $table->integer('sft_suitable_product_id')->nullable();
            $table->integer('product_id')->nullable();
            $table->string('product_name_edit')->nullable();
            $table->tinyInteger('type_product');
            $table->integer('m_distinction_id')->nullable();
            $table->boolean('is_decision')->default(0)->comment('0:not choose,1:draft,2:edit');
            $table->boolean('is_block')->nullable();
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
        Schema::dropIfExists('sft_keep_data_prods');
    }
}
