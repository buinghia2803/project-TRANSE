<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateIsMailingRegisCertToAppTrademarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('app_trademarks', function (Blueprint $table) {
            $table->boolean('is_mailing_regis_cert')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('app_trademarks', function (Blueprint $table) {
            $table->boolean('is_mailing_regis_cert')->default(1)->change();
        });
    }
}
