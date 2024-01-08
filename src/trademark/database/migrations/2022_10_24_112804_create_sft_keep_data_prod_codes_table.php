<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSftKeepDataProdCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sft_keep_data_prod_codes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sft_keep_data_prod_id');
            $table->foreign('sft_keep_data_prod_id')->references('id')->on('sft_keep_data_prods');
            $table->string('code', 30);
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
        Schema::dropIfExists('sft_keep_data_prod_codes');
    }
}
