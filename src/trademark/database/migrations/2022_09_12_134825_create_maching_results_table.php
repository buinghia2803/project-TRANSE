<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMachingResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maching_results', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('admin_id')->comment('管理者のID（admins.id）');
            $table->bigInteger('application_trademark_id')->comment('出願登録のID（application_trademarks.id）');
            $table->string('XML_result', 255)->comment('XML: 結果');
            $table->string('XML_document_name', 255)->comment('XML: 書類名');
            $table->string('XML_reference_number', 255)->comment('XML: 整理番号');
            $table->string('XML_application_number', 255)->comment('XML: 出願番号');
            $table->dateTime('XML_delivery_date')->comment('XML: 発送日');
            $table->string('XML_shipping_number', 255)->comment('XML: 発送番号');
            $table->string('target_reference_number', 255)->comment('突合先: 整理番号');
            $table->string('target_application_number', 255)->comment('突合先: 出願番号');
            $table->tinyInteger('type')->default(1)->comment('1: 拒絶理由通知対応 | 2: 登録');
            $table->boolean('is_confirm')->default(0)->comment('確認のステータス. 0: false | 1: true');
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
        Schema::dropIfExists('maching_results');
    }
}
