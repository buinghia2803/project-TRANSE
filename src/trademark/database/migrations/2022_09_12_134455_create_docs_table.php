<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('docs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('application_trademark_id')->comment('出願登録のID（application_trademarks.id）');
            $table->bigInteger('admin_id')->comment('管理者のID（admins.id）');
            $table->text('attachment_xml')->nullable()->comment('ファイルxmlのURL');
            $table->text('attachment_pdf')->nullable()->comment('ファイルpdfのURL');
            $table->dateTime('sending_noti_rejection_date')->nullable()->comment('拒絶通知書発送日');
            $table->date('response_deadline')->nullable()->comment('特許庁への応答期限日');
            $table->tinyInteger('type')->default(1)->comment('1: 拒絶通知書 | 2: 登録');
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
        Schema::dropIfExists('docs');
    }
}
