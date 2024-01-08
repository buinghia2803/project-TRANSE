<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPageTypeAppTrademarksTable extends Migration
{
        /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('app_trademarks', function (Blueprint $table) {
            $table->tinyInteger('type_page')->default('1')->after('is_cancel')->comment('1: u011b, 2: u011b_31, 3: u021b, 4: u021b_31, 5: u031, 6: u031edit, 7: u031b, 8: u031c, 9: u031edit_with_number, 10: u031d');
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
            $table->dropColumn('type_page');
        });
    }
}
