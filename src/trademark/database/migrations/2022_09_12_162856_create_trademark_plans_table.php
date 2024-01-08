<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrademarkPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trademark_plans', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('plan_correspondence_id')->comment('プラン申込むのID（plan_correspondences.id）');
            $table->boolean('is_cancel')->default(0)->comment('中止のステータス. 0: false | 1: true');
            $table->date('response_deadline')->nullable()->comment('方針案回答期日');
            $table->date('sending_docs_deadline')->nullable()->comment('必要資料送付期日');
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
        Schema::dropIfExists('trademark_plans');
    }
}
