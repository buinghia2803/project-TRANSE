<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldV1ToPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('payment_number', 100)->after('payer_info_id')->comment('請求書番号');
            $table->float('cost_service_base')->after('payment_number')->comment('サービスの料金');
            $table->float('cost_service_add_prod')->nullable()->after('cost_service_base')->comment('商品・サービス数');
            $table->string('comment', 500)->nullable()->after('is_confirm')->comment('payment.htmlの備考');
            $table->dateTime('payment_date')->after('comment')->comment('payment.htmlの入金日');
            $table->boolean('is_treatment')->nullable()->after('payment_date')->comment('payment.htmlの処理済み. 0: 処理待ち | 1: 処理済み');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'payment_number',
                'cost_service_base',
                'cost_service_add_prod',
                'comment',
                'payment_date',
                'is_treatment',
            ]);
        });
    }
}
