<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCommentTypeCodeAndMProduct extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('m_code', function (Blueprint $table) {
            $table->boolean('type')->default(1)->comment('1: オリジナルクリーン | 2: 登録クリーン | 3: 創作クリーン, 4: =準クリーン')->change();
        });
        Schema::table('m_products', function (Blueprint $table) {
            $table->boolean('type')->default(1)->comment('1: オリジナルクリーン | 2: 登録クリーン | 3: 創作クリーン, 4: =準クリーン')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('m_code', function (Blueprint $table) {
            $table->boolean('type')->default(1)->comment('1: オリジナルクリーン | 2: 登録クリーン | 3: 創作クリーン')->change();
        });
        Schema::table('m_products', function (Blueprint $table) {
            $table->boolean('type')->default(1)->comment('1: オリジナルクリーン | 2: 登録クリーン | 3: 創作クリーン')->change();
        });
    }
}
