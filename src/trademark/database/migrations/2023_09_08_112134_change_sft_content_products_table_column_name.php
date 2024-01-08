<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeSftContentProductsTableColumnName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sft_content_products', function (Blueprint $table) {
            $table->string('name', 255)->comment('お考えの商品・サービス内容')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sft_content_products', function (Blueprint $table) {
            $table->string('name', 25)->comment('お考えの商品・サービス内容')->change();
        });
    }
}
