<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMCodeIdColumnSftKeepDataProdCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sft_keep_data_prod_codes', function (Blueprint $table) {
            $table->integer('m_code_id')->nullable()->after('code')->comment('product_type: 4');
            $table->string('code', 30)->nullable()->comment('product_type: 3')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sft_keep_data_prod_codes', function (Blueprint $table) {
            $table->dropColumn('m_code_id');
            $table->string('code', 30)->change();
        });
    }
}
