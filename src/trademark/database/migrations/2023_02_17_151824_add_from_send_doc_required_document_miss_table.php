<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFromSendDocRequiredDocumentMissTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('required_document_miss', function (Blueprint $table) {
            $table->string('from_send_doc')->after('plan_id')->nullable()->comment('// save: u204, u204_2, u204_3');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('required_document_miss', function (Blueprint $table) {
            $table->dropColumn('from_send_doc');
        });
    }
}
