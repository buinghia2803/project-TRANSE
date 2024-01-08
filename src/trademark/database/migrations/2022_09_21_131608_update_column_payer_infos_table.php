<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateColumnPayerInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payer_infos', function(Blueprint $table) {
            $table->string('postal_code', 7)->comment('郵便番号')->nullable()->change();
            $table->bigInteger('m_prefecture_id')->comment('都道府県のID（m_prefectures.id）')->nullable()->change();
            $table->string('address_second', 255)->nullable()->comment('住所-2')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payer_infos', function(Blueprint $table) {
            $table->string('postal_code', 7)->comment('郵便番号')->change();
            $table->bigInteger('m_prefecture_id')->comment('都道府県のID（m_prefectures.id）')->change();
            $table->string('address_second', 255)->nullable()->comment('住所-2')->change();
        });
    }
}
