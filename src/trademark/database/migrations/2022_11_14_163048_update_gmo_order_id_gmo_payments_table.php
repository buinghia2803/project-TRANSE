<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateGmoOrderIdGmoPaymentsTable extends Migration
{
    /*
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('gmo_payments', function (Blueprint $table) {
            $table->string('gmo_order_id', 50)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('gmo_payments', function (Blueprint $table) {
            $table->bigInteger('gmo_order_id')->change();
        });
    }
}
