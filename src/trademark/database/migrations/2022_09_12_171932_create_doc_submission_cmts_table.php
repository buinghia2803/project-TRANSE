<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocSubmissionCmtsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('doc_submission_cmts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('doc_submission_id')->comment('提出書類のID(doc_submissions.id)');
            $table->bigInteger('admin_id')->comment('管理者のID（admins.id）');
            $table->string('content', 500)->comment('コメントの内容');
            $table->tinyInteger('type')->default(1)->comment('1: 社内用コメント');
            $table->tinyInteger('type_comment_of_step')->default(1)->comment('1: 拒絶理由通知対応：必要資料');
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
        Schema::dropIfExists('doc_submission_cmts');
    }
}
