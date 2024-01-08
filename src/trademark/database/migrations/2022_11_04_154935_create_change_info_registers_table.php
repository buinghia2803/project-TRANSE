<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChangeInfoRegistersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('change_info_registers', function (Blueprint $table) {
            $table->id();
            $table->integer('trademark_id');
            $table->integer('trademark_info_id')->nullable();
            $table->integer('payment_id');
            $table->string('name');
            $table->integer('m_nation_id');
            $table->integer('m_prefectures_id')->nullable();
            $table->string('address_second')->nullable();
            $table->string('address_three')->nullable();
            $table->tinyInteger('type')->comment('1:出願, 2: 登録');
            $table->boolean('is_send')->comment('0: false, 1: true')->default(0);
            $table->boolean('is_change_address_free')->comment('0:not free, 1: free')->default(0);
            $table->string('representative_name')->nullable();
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
        Schema::dropIfExists('change_info_registers');
    }
}
