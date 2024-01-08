<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldV1ToFreeHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('free_histories', function (Blueprint $table) {
            $table->dropColumn([
                'is_not_report_ap',
                'is_not_report_not_ap',
                'is_report_only',
                'is_customer_response',
            ]);
            $table->tinyInteger('type')->after('XML_delivery_date')->comment('1: お客様へ報告なし（庁手続きあり）| 2: お客様へ報告なし（庁手続きなし）| 3: お客様へ報告のみ（庁手続きなし）| 4: お客様からの回答が必要');
            $table->tinyInteger('flag_role')->default(1)->after('is_cancel')->comment('1: 担当者 | 2: 責任者');
            $table->boolean('is_confirm')->default(0)->after('flag_role');
            $table->bigInteger('amount')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('free_histories', function (Blueprint $table) {
            $table->boolean('is_not_report_ap')->default(0)->comment('お客様へ報告なし（庁手続きあり）. 0:false | 1:true');
            $table->boolean('is_not_report_not_ap')->default(0)->comment('お客様へ報告なし（庁手続きなし）. 0:false | 1:true');
            $table->boolean('is_report_only')->default(0)->comment('お客様へ報告のみ（庁手続きなし）. 0:false | 1:true');
            $table->boolean('is_customer_response')->default(0)->comment('お客様からの回答が必要. 0:false | 1:true');
            $table->dropColumn([
                'type',
                'flag_role',
                'is_confirm',
            ]);
            $table->tinyInteger('amount')->change();
        });
    }
}
