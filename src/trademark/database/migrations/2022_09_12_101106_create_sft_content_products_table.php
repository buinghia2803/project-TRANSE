<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSftContentProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sft_content_products', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('support_first_time_id')->comment('はじめからサポートのID（support_first_times.id）');
            $table->string('name', 25)->comment('お考えの商品・サービス内容');
            $table->boolean('is_choice_admin')->default(0)->comment('【商品・サービス内容】を選択する管理者のステータス(a011.html). 0: false | 1: true');
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
        Schema::dropIfExists('sft_content_products');
    }
}
