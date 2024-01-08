<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocSubmissionAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('doc_submission_attachments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('doc_submission_attach_property_id')->comment('提案書類の添付物件の【内容】（ファイル）のID( doc_submission_attach_properties.id)');
            $table->text('attachment')->comment('ファイルのURL');
            $table->string('file_no', 10)->comment('順番');
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
        Schema::dropIfExists('doc_submission_attachments');
    }
}
