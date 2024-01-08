<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrecheckResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('precheck_results', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('admin_id')->comment('担当者のID（admins.id）');
            $table->bigInteger('precheck_product_id')->comment('プレチェックの商品・サービスのID（precheck_products.id）');
            $table->tinyInteger('result_similar_simple')->nullable()->comment('簡易調査結果. 1: 有 | 2: なし');
            $table->tinyInteger('result_identification_detail')->nullable()->comment('識別力調査結果. 1: ○, // 登録可能性が高い。 | 2: △, // 登録に期待が持てる。 | 3: ▲, // 登録可能性は低い。 | 4: ×　//登録するのは難しい。');
            $table->tinyInteger('result_similar_detail')->nullable()->comment('類似調査結果. 1: ○, // 登録可能性が高い。 | 2: △, // 登録に期待が持てる。 | 3: ▲, // 登録可能性は低い。 | 4: ×　//登録するのは難しい。');
            $table->boolean('is_block_identification')->default(0)->comment('識別の確認＆ロックステータス. 0: false | 1: true');
            $table->boolean('is_block_similar')->default(0)->comment('類似の確認＆ロックのステータス. 0: false | 1: true');
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
        Schema::dropIfExists('precheck_results');
    }
}
