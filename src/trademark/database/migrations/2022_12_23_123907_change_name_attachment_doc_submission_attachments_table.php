<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeNameAttachmentDocSubmissionAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('doc_submission_attachments', function (Blueprint $table) {
            $table->renameColumn('attachment', 'attach_file');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('doc_submission_attachments', function (Blueprint $table) {
            $table->renameColumn('attach_file', 'attachment');
        });
    }
}
