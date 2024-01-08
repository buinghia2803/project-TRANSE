<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrechecksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prechecks', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('trademark_id')->comment('商標のID（trademarks.id）');
            $table->tinyInteger('type_precheck')->default(1)->comment('【プレチェックサービスお申込み】. 1: 簡易レポート | 2: 詳細レポート');
            $table->tinyInteger('pack')->default(1)->comment('【プラン選択】. 1: パックA | 2: パックB | 3: パック C');
            $table->boolean('is_cancel')->default(0)->comment('出願をやめるステータス. 0: false | 1: true');
            $table->boolean('is_mailing_regis_cert')->default(1)->comment('登録証の郵送ステータス. 0: false | 1: true');
            $table->tinyInteger('period_registration')->default(1)->comment('【登録期間】. 1: 5年 | 2: 10年');
            $table->tinyInteger('flag_role')->default(1)->comment('画面に作動する管理者ステータス. 1: 担当者 | 2: 責任者');
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
        Schema::dropIfExists('prechecks');
    }
}
