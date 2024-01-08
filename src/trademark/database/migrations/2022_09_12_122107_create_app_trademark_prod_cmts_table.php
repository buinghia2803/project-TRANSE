<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppTrademarkProdCmtsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_trademark_prod_cmts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('app_trademark_id')->comment('出願登録のID（app_trademarks.id）');
            $table->bigInteger('m_distinction_id')->comment('区分のID(m_distinctions.id)');
            $table->string('internal_remark', 500)->nullable()->comment('社内用備考');
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
        Schema::dropIfExists('app_trademark_prod_cmts');
    }
}
