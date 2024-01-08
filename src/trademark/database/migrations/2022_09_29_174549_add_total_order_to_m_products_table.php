<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTotalOrderToMProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('m_products', function (Blueprint $table) {
            $table->integer('total_order')->after('rank')->default(0);
            $table->string('block', 20)->nullable()->after('total_order');
            $table->dropUnique(['products_number']);
            $table->dropUnique(['name']);
            $table->string('name', 255)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('m_products', function (Blueprint $table) {
            $table->dropColumn('total_order', 'block');
            $table->unique(['products_number']);
            $table->unique(['name']);
            $table->string('name', 30)->change();
        });
    }
}
