<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnMyFoldersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('my_folders', function (Blueprint $table) {
            $table->string('name_trademark', 30)->after('type_trademark')->comment('商標名')->nullable()->change();
            $table->text('image_trademark')->nullable()->after('name_trademark')->comment('装飾文字/ロゴ絵柄の画像')->nullable()->change();
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
            $table->string('name_trademark', 30)->after('type_trademark')->comment('商標名')->change();
            $table->text('image_trademark')->nullable()->after('name_trademark')->comment('装飾文字/ロゴ絵柄の画像')->change();
        });
    }
}
