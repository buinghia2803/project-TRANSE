<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNoticeDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notice_details', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('notice_id')->comment('お知らせのID（notices.id）');
            $table->bigInteger('target_id')->comment('管理者のID（admins.id） か ユーザのID（users.id)');
            $table->tinyInteger('type_acc')->default(1)->comment('1: ユーザー enduser | 2: 事務担当 jimu | 3: 担当者 tantou | 4: 責任者 seki');
            $table->date('response_deadline')->nullable()->comment('期限日, AMSへの回答期限日');
            $table->string('content', 255)->comment('作業内容, 現状, 作業名');
            $table->string('target_page', 255)->comment('send notice from page');
            $table->string('redirect_page', 255)->comment('url page, link of 作業内容, 現状');
            $table->string('attribute', 50)->nullable()->comment('属性 of a000anken_top');
            $table->tinyInteger('type_notify')->default(1)->comment('1: Todo一覧 | 2: 通知一覧 | 3: AMSからのお知らせ');
            $table->tinyInteger('type_page')->default(1)->comment('1: top | 2: anken_top');
            $table->boolean('is_open')->default(1)->comment('0: not open | 1: opened');
            $table->date('completion_date')->nullable()->comment('完了日');

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
        Schema::dropIfExists('notice_details');
    }
}
