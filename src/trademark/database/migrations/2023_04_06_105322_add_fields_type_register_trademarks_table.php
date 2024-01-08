<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsTypeRegisterTrademarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('register_trademarks', function (Blueprint $table) {
            $table->string('type', 50)->default('U302');
            $table->tinyInteger('type_page')->after('type')->comment('1: u302,2: u302_402,3: U402')->nullable();
            $table->tinyInteger('type_notices')->comment('1: now() >= 期限日 - 6月, 2: now() >= 期限日 - 4月,3: now() >= 期限日 - 2月,4: now() >= 期限日 - 1月,5: now() >= 期限日 - 2週,6: now() >= 期限日 + 6月 - 1日')
                ->nullable()->after('type_page');
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
            $table->dropColumn(['type',
                'type_page',
                'type_notices']);
        });
    }
}
