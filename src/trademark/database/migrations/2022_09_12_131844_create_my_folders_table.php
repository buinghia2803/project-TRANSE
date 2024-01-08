<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMyFoldersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('my_folders', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->comment('ユーザのID（users.id)');
            $table->bigInteger('target_id')->comment('「はじめからサポート 」か「プレチェック 」か「その他 」などサービスのID');
            $table->string('folder_number', 12)->unique()->comment('フォルダの番号. フォーマット： MLAnnnyynnnn');
            $table->string('keyword', 255)->comment('キーワード か【検索結果】. フォーマット：["keyword 1", "keyword 2"]');
            $table->tinyInteger('type')->default(1)->comment('1: はじめからサポート | 2: プレチェック | 3: その他');
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
        Schema::dropIfExists('my_folders');
    }
}
