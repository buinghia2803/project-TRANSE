<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plan_comments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('admin_id')->comment('管理者のID（admins.id）');
            $table->bigInteger('target_id')->comment('方針案の詳細のID(plan_details.id) | か対応方針案のID(trademark_plans.id)');
            $table->string('content', 500)->comment('コメントの内容');
            $table->tinyInteger('type')->default(1)->comment('1: 社内用コメント | 2:  AMSへのコメント欄');
            $table->tinyInteger('type_comment_step')->default(1)->comment('1: 対応方針案作成 | 2: 拒絶理由通知対応：必要資料');
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
        Schema::dropIfExists('plan_comments');
    }
}
