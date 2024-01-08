<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocSubmissionAttachPropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('doc_submission_attach_properties', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('doc_submission_id')->comment('提出書類のID(doc_submissions.id)');
            $table->string('name', 255)->comment('物件名');
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
        Schema::dropIfExists('doc_submission_attach_properties');
    }
}
