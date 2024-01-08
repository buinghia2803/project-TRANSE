<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('m_products', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('m_distinction_id')->comment('区分のID（m_distinctions.id）');
            $table->bigInteger('admin_id')->comment('管理者のID(admins.id)');
            $table->string('products_number', 8)->unique()->comment('申込番号、フォーマット：1:0KKNNNNN(type=1) | 2:1KKNNNNN(type=2,3)');
            $table->string('name', 30)->unique()->comment('商品名');
            $table->tinyInteger('type')->default(1)->comment('1: オリジナルクリーン | 2: 登録クリーン | 3: 創作クリーン');
            $table->tinyInteger('rank')->nullable()->comment('1: A | 2: B | 3: C | 4: D | 5: E');
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
        Schema::dropIfExists('m_products');
    }
}
