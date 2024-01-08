<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTrademarkPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trademark_plans', function (Blueprint $table) {
            $table->tinyInteger('is_reject')->default(0)->after('is_cancel')->comment('0: false, 1: true');
            $table->tinyInteger('flag_role')->after('sending_docs_deadline')->default(1)->comment('1: 担当者, 2: 責任者');
            $table->tinyInteger('is_confirm')->default(0)->after('flag_role')->comment('0: false, 1: true');
            $table->tinyInteger('is_register')->default(0)->after('is_reject')->comment('0: false, 1: true');
            $table->string('reason_cancel', 500)->after('is_register')->nullable()->comment('u203stop.html');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trademark_plans', function (Blueprint $table) {
            $table->dropColumn([
                'is_reject',
                'flag_role',
                'is_confirm',
                'is_register',
                'reason_cancel',
            ]);
        });
    }
}
