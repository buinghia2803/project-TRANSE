<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeToTrademarkDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trademark_documents', function (Blueprint $table) {
            $table->text('trademark_id')->after('notice_detail_btn_id');
            $table->text('type')->after('trademark_id')->nullable()->comment('1: 特許庁からの通知書を見る');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trademark_documents', function (Blueprint $table) {
            $table->dropColumn([
                'trademark_id',
                'type'
            ]);
        });
    }
}
