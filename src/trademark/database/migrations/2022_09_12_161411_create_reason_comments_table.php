<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReasonCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reason_comments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('plan_correspondence_id')->comment('プラン申込むのID（plan_correspondences.id）');
            $table->bigInteger('admin_id')->comment('管理者のID（admins.id）');
            $table->string('content', 500)->comment('コメントの内容');
            $table->tinyInteger('type')->default(1)->comment('1: 社内用コメント | 2: AMSからお客様へのコメン');
            $table->tinyInteger('type_comment_step')->default(1)->comment('1: 登録可能性評価レポート　作成 | 2: 事前質問作成');
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
        Schema::dropIfExists('reason_comments');
    }
}
