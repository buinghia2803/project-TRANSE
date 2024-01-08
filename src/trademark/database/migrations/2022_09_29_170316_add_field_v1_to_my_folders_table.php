<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldV1ToMyFoldersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('my_folders', function (Blueprint $table) {
            $table->tinyInteger('type_trademark')->default(1)->after('keyword')->comment('商標出願種別. 1: 文字 | 2: それ以外（装飾文字、ロゴ絵柄等）');
            $table->string('name_trademark', 30)->after('type_trademark')->comment('商標名');
            $table->text('image_trademark')->nullable()->after('name_trademark')->comment('装飾文字/ロゴ絵柄の画像');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('my_folders', function (Blueprint $table) {
            $table->dropColumn(['type_trademark', 'name_trademark', 'image_trademark']);
        });
    }
}
