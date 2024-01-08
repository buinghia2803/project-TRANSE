<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppTrademarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_trademarks', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('trademark_id')->comment('商標のID（trademarks.id）');
            $table->bigInteger('admin_id')->comment('管理者のID（admins.id)');
            $table->bigInteger('agent_group_id')->comment('代理人のセットのID（agent_groups.id)');
            $table->dateTime('cancellation_deadline')->comment('中止可能期限日時');
            $table->string('comment_office', 255)->nullable()->comment('事務担当者');
            $table->tinyInteger('status')->default(1)->comment('1: 管理者の確認待ち | 2: ユーザの確認待ち | 3: ユーザの確認済み');
            $table->tinyInteger('pack')->default(1)->comment('1: パックA | 2: パックB | 3: パックC');
            $table->boolean('is_mailing_regis_cert')->default(1)->comment('登録証の郵送を登録ステータス| 0: false | 1: true');
            $table->tinyInteger('period_registration')->default(1)->comment(' 【登録期間】. 1: 5年 | 2: 10年');
            $table->boolean('is_cancel')->default(0)->comment('0: false | 1: true');
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
        Schema::dropIfExists('app_trademarks');
    }
}
