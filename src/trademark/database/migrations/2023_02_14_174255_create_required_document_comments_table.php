<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequiredDocumentCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('required_document_comments', function (Blueprint $table) {
            $table->id();
            $table->integer('required_document_id');
            $table->string('from_send_doc', 100)->nullable();
            $table->string('content', 500);
            $table->tinyInteger('type_comment_step')->nullable();
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
        Schema::dropIfExists('required_document_comments');
    }
}
