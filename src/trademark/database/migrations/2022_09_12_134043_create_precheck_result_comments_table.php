<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrecheckResultCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('precheck_result_comments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('precheck_result_id')->comment('プレチェックサービス結果のID（precheck_results.id）');
            $table->bigInteger('admin_id')->comment('担当者のID（admins.id）');
            $table->tinyInteger('type')->default(1)->comment('1: 社内用コメント | 2: AMSからお客様へのコメント');
            $table->string('content', 255)->comment('コメントの内容');
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
        Schema::dropIfExists('precheck_result_comments');
    }
}
