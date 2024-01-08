<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSftCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sft_comments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('admin_id')->comment('管理者のID（admins.id)');
            $table->bigInteger('support_first_time_id')->comment('はじめからサポートのID（support_first_times.id）');
            $table->tinyInteger('type')->default(1)->comment('1: 社内用コメント | 2: AMSからお客様へのコメント');
            $table->string('content', 1000)->comment('コメントの内容');
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
        Schema::dropIfExists('sft_comments');
    }
}
