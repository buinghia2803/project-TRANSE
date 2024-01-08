<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMPrefecturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('m_prefectures', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('m_nation_id')->comment('所在国のid（m_nations.id）');
            $table->string('name', 255)->comment('都道府県');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('m_prefectures');
    }
}
