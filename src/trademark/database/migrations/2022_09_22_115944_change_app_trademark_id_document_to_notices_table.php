<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeAppTrademarkIdDocumentToNoticesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notices', function (Blueprint $table) {
            $table->bigInteger('app_trademark_id')->comment('出願登録のID（app_trademarks.id）')->nullable()->change();
            $table->string('document', 255)->comment('関連書類')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notices', function (Blueprint $table) {
            $table->bigInteger('app_trademark_id')->comment('出願登録のID（app_trademarks.id）')->change();
            $table->string('document', 255)->comment('関連書類')->change();
        });
    }
}
