<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComparisonTrademarkResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comparison_trademark_results', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('maching_result_id')->comment('発送書類取り込み：突合結果のID （maching_results.id）');
            $table->bigInteger('admin_id')->comment('管理者のID（admins.id）');
            $table->dateTime('sending_noti_rejection_date')->nullable()->comment('拒絶通知書発送日');
            $table->dateTime('response_deadline')->nullable()->comment('許庁への応答期限日');
            $table->dateTime('user_response_deadline')->nullable()->comment('お客様回答期限日');
            $table->boolean('is_cancel')->default(0)->comment('依頼しないステータス. 0: false | 1: true');
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
        Schema::dropIfExists('comparison_trademark_results');
    }
}
