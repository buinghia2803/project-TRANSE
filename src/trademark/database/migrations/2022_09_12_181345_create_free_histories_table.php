<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFreeHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('free_histories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('trademark_id')->comment('商標のID（trademarks.id）');
            $table->bigInteger('admin_id_create')->comment('事務担当, 担当者のID（admins.id）');
            $table->bigInteger('admin_id_confirm')->comment('責任者者のID（admins.id）');
            $table->date('XML_delivery_date')->comment('作成日');
            // AP: Agency Procedures
            $table->boolean('is_not_report_ap')->default(0)->comment('お客様へ報告なし（庁手続きあり）. 0:false | 1:true');
            $table->boolean('is_not_report_not_ap')->default(0)->comment('お客様へ報告なし（庁手続きなし）. 0:false | 1:true');
            $table->boolean('is_report_only')->default(0)->comment('お客様へ報告のみ（庁手続きなし）. 0:false | 1:true');
            $table->boolean('is_customer_response')->default(0)->comment('お客様からの回答が必要. 0:false | 1:true');
            $table->date('user_response_deadline')->comment('お客様回答期限日');
            $table->tinyInteger('property')->default(1)->comment('属性. 1: 特許庁から | 2: 特許庁へ | 3: お客様から | 4: お客様へ | 5: 所内処理');
            $table->string('status_name', 255)->comment('ステータス名');
            $table->date('patent_response_deadline')->comment('特許庁への応答期限日');
            $table->text('attachment')->comment('関連書類');
            $table->tinyInteger('amount_type')->nullable()->comment('金額. 1: 5,000円 | 2: その他 | 3: 課金しない');
            $table->tinyInteger('amount')->nullable()->comment('金額');
            $table->string('internal_remark', 500)->comment('所内備考');
            $table->string('comment', 500)->nullable()->comment('AMSからのコメント');
            $table->boolean('is_check_amount')->default(0)->comment('a000free_s.htmlの金額. 0:false | 1:true');
            $table->string('content_answer', 500)->nullable()->comment('【ご回答】');
            $table->boolean('is_cancel')->default(0)->comment('依頼しない のステータス. 0:false | 1:true');
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
        Schema::dropIfExists('free_histories');
    }
}
