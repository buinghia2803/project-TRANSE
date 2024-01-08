<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegisterTrademarkDocsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('register_trademark_docs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('register_trademark_id')->nullable()->comment('商標登録のID(register_trademarks.id)');
            $table->text('attachment')->comment('ファイルのURL');
            $table->tinyInteger('type')->default(1)->comment('1: PDF | 2: XML');
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
        Schema::dropIfExists('register_trademark_docs');
    }
}
