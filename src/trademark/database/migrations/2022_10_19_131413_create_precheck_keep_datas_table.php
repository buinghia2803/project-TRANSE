<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrecheckKeepDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('precheck_keep_datas', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('precheck_id');
            $table->string('comment_from_ams_identification', 1000)->nullable();
            $table->string('comment_from_ams_similar', 1000)->nullable();
            $table->string('comment_internal')->nullable();
            $table->tinyInteger('step');
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
        Schema::dropIfExists('precheck_keep_datas');
    }
}
