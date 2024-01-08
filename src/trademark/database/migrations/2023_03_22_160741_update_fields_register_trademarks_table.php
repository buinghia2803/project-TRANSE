<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateFieldsRegisterTrademarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('register_trademarks', function (Blueprint $table) {
            $table->dropForeign('register_trademarks_regist_cert_nation_id_foreign');
            $table->dropForeign('register_trademarks_trademark_info_address_first_foreign');
            $table->dropForeign('register_trademarks_trademark_info_nation_id_foreign');
        });
        Schema::table('register_trademarks', function (Blueprint $table) {
            $table->integer('regist_cert_nation_id')->nullable()->change();
            $table->string('regist_cert_postal_code', 7)->nullable()->change();
            $table->string('regist_cert_address', 255)->nullable()->change();
            $table->string('regist_cert_payer_name', 50)->nullable()->change();
            $table->integer('trademark_info_nation_id')->nullable()->change();
            $table->integer('trademark_info_address_first')->nullable()->change();
            $table->boolean('is_register_change_info')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('register_trademarks', function (Blueprint $table) {
            $table->bigInteger('regist_cert_nation_id')->change();
            $table->string('regist_cert_postal_code', 7)->change();
            $table->string('regist_cert_address', 255)->change();
            $table->string('regist_cert_payer_name', 50)->change();
            $table->bigInteger('trademark_info_nation_id')->change();
            $table->bigInteger('trademark_info_address_first')->change();
            $table->boolean('is_register_change_info')->change();
        });
    }
}
