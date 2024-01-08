<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateNoticesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('notices');

        Schema::create('notices', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('trademark_id')->comment('商標登録のID（trademarks.id）');
            $table->tinyInteger('flow')->default(4)->comment('1: はじめからサポート | 2: プレチェックサービス | 3: フリー履歴 | 4: Q&A | 5: 出願 | 6: 拒絶理由通知対応 | 7: 登録 | 8: 更新(5年, 10年) | 9: 期限日前期間延長 | 10: 出願人名・住所変更');
            $table->string('step', 10)->nullable();

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
        Schema::create('notices', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('target_id')->nullable()->comment('管理者のID（admins.id）かユーザのID（users.id)');
            $table->tinyInteger('type_acc')->default(1)->comment('1: ユーザー | 2: 事務担当 | 3: 担当者 | 4: 責任者');
            $table->string('content', 255)->comment('作業内容か現状');
            $table->string('page', 255)->comment('ファイルのURL');
            $table->bigInteger('app_trademark_id')->comment('出願登録のID（app_trademarks.id）');
            $table->date('ams_response_deadline')->nullable()->comment('期限日, AMSへの回答期限日');
            $table->date('patent_response_deadline')->nullable()->comment('特許庁への応答期限日');
            $table->string('document', 255)->comment('関連書類');
            $table->boolean('is_completed')->default(0)->comment('完了のステータス. 0:false | 1: true');
            $table->tinyInteger('type_notify')->comment('1: Todo一覧 | 2: お知らせ一覧');
            $table->softDeletes();
            $table->timestamps();
        });
    }
}
