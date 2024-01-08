<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrecheckCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('precheck_comments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('precheck_id')->comment('プレチェックサービス結果のID（prechecks.id）');
            $table->bigInteger('admin_id')->comment('担当者のID（admins.id）');
            $table->tinyInteger('type')->default(1)->comment('1: 社内用コメント | 2: AMSからお客様へのコメント');
            $table->string('content', 255)->comment('コメントの内容');
            $table->tinyInteger('input_of_page')->comment('1: a021rui and a021kan| 2: a021shiki');
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
        Schema::dropIfExists('precheck_comments');
    }
}
