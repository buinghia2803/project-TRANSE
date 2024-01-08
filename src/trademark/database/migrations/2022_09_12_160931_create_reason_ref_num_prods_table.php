<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReasonRefNumProdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reason_ref_num_prods', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('reason_id')->comment('理由のID（reasons.id）');
            $table->bigInteger('plan_correspondence_prod_id')->comment('拒絶理由通知対応申し込みの商品・サービス名のID（plan_correspondence_prods.id）');
            $table->bigInteger('admin_id')->comment('管理者のID（admins.id）');
            $table->string('comment_patent_agent', 255)->nullable()->comment('弁理士からのコメント');
            $table->string('vote_laws_regulation_id', 255)->comment('法令に商品・サービスの評価。フォーマット： ["1", "2", "3"]');
            $table->boolean('is_choice')->default(0)->comment('拒絶理由通知対応/費用を選択するステータス. 0:false | 1: true');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reason_ref_num_prods');
    }
}
