<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('histories', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('admin_id')->comment('管理者のID（admins.id）');
            $table->tinyInteger('target_id')->comment('「出願」か「はじめからサポート お申し込み」か「はじめからサポートサービス：AMSからの提案」などサービスのID');
            $table->string('page', 255)->comment('画面のURL');
            $table->tinyInteger('action')->comment('1: 作成 | 2: 編集 | 3: 削除');
            $table->tinyInteger('type')->comment('1: はじめから | 2: プレチェック | 3: 出願 | 4: 商標登録 | 5: 拒絶理由通知対応 | 6: 後期納付期限,');
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
        Schema::dropIfExists('histories');
    }
}
