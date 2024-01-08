<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMyFolderProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('my_folder_products', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('my_folder_id')->comment('フォルダのID（my_folders.id）');
            $table->bigInteger('m_product_id')->comment('商品・サービスのID（m_products.id）');
            $table->tinyInteger('type')->default(1)->comment('1: 既存リスト | 2: 追加リスト');
            $table->boolean('is_additional_search')->default(0)->comment('【検索結果】の選択. 0: false | 1: true');
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
        Schema::dropIfExists('my_folder_products');
    }
}
