<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSftKeepDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sft_keep_datas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('support_first_time_id')->index();
            $table->foreign('support_first_time_id')->references('id')->on('support_first_times');
            $table->text('comment_from_ams', 1000)->nullable();
            $table->text('comment_internal', 1000)->nullable();
            $table->string('content_product')->nullable();
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
        Schema::dropIfExists('sft_keep_datas');
    }
}
