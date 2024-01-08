<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNoticesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
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

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notices');
    }
}
