<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequiredDocumentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('required_document_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('required_document_id');
            $table->unsignedBigInteger('plan_detail_doc_id');
            $table->string('attachment_user')->nullable()->comment('u204 || format [[sending_date => "yyyy/mm/dd", value => "attach 1", type => "attach"][sending_date => "yyyy/mm/dd", value => "attach 2", type => "attach"][sending_date => "yyyy/mm/dd", value => "url", type => "url"]]');
            $table->tinyInteger('is_completed')->default(0)->comment('0: false, 1: true | 完了 of page a204han');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('required_document_id')->references('id')->on('required_documents');
            $table->foreign('plan_detail_doc_id')->references('id')->on('plan_detail_docs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('required_document_details');
    }
}
