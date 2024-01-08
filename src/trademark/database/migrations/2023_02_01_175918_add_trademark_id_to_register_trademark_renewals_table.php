<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTrademarkIdToRegisterTrademarkRenewalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('register_trademark_renewals', function (Blueprint $table) {
            $table->integer('trademark_id')->after('register_trademark_id');
            $table->integer('register_trademark_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('register_trademark_renewals', function (Blueprint $table) {
            $table->dropColumn('trademark_id');
            $table->integer('register_trademark_id')->nullable(false)->change();
        });
    }
}
