<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMProductIdToRegisterTrademarkProdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('register_trademark_prods', function (Blueprint $table) {
            $table->integer('m_product_id')->after('register_trademark_id')->nullable();
            $table->integer('is_apply')->after('m_product_id')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('register_trademark_prods', function (Blueprint $table) {
            $table->dropColumn('m_product_id');
            $table->dropColumn('is_apply');
        });
    }
}
