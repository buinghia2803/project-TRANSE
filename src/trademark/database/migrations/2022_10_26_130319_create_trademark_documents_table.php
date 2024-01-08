<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrademarkDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trademark_documents', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('notice_detail_btn_id');
            $table->text('name')->comment('name of documents');
            $table->text('url')->comment('link of 関連書類');

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
        Schema::dropIfExists('trademark_documents');
    }
}
