<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReasonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reasons', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('plan_correspondence_id')->comment('プラン申込むのID（plan_correspondences.id）');
            $table->bigInteger('admin_id')->comment('管理者のID（admins.id）');
            $table->bigInteger('reason_no_id')->comment('理由のID(reason_no.id)');
            $table->bigInteger('m_laws_regulation_id')->comment('法令のID（m_laws_and_regulations.id）');
            $table->string('reference_number', 255)->nullable()->comment('引例番号。フォルダ：["引例番号1", "引例番号2"]');
            $table->tinyInteger('question_status')->default(2)->comment('事前質問は不要. 1: 不要 | 2: 必要');
            $table->tinyInteger('flag_role')->default(1)->comment('画面に作動する管理者ステータス. 1: 担当者 | 2: 責任者');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reasons');
    }
}
