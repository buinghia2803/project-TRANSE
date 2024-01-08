<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanDetailProductCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plan_detail_product_codes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('plan_detail_product_id');
            $table->foreign('plan_detail_product_id')->references('id')->on('plan_detail_products');
            $table->integer('m_code_id');
            $table->string('code_name_edit', 255)->nullable();

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
        Schema::dropIfExists('plan_detail_product_codes');
    }
}
