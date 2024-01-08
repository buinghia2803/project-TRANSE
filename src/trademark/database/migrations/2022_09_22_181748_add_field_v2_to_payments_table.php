<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldV2ToPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->float('extension_of_period_before_expiry')->nullable()->after('cost_registration_certificate')->comment('期限日前期間延長サービス');
            $table->float('application_discount')->nullable()->after('extension_of_period_before_expiry')->comment('同時申込割引');
            $table->float('print_fee')->nullable()->after('tax')->comment('印紙代');
            $table->float('cost_print_application_one_distintion')->nullable()->after('print_fee');
            $table->float('cost_print_application_add_distintion')->nullable()->after('cost_print_application_one_distintion');
            $table->float('costs_correspondence_of_one_prod')->nullable()->after('cost_print_application_add_distintion')->comment('決済が必要な特許庁への費用');
            $table->float('reduce_number_distitions')->nullable()->after('costs_correspondence_of_one_prod')->comment('登録区分数削減手続きサービス');
            $table->renameColumn('cost_5_year','cost_5_year_one_distintion');
            $table->renameColumn('cost_10_year','cost_10_year_one_distintion');
            $table->float('cost_change_address')->nullable()->after('reduce_number_distitions')->comment('出願人名称変更手続きサービス');
            $table->float('cost_change_name')->nullable()->after('cost_change_address')->comment('出願人住所変更手続きサービス');
            $table->float('cost_print_name')->nullable()->after('cost_change_name')->comment('登録名義人名変更印紙代');
            $table->float('cost_print_address')->nullable()->after('cost_print_name')->comment('登録名義人住所変更印紙代');
            $table->tinyInteger('payment_status')->default(0)->after('is_treatment')->comment(' 0: 保存, // 見積書 1: お支払待ち, // 請求書  2: お支払済み // 領収書');
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
                'extension_of_period_before_expiry',
                'application_discount',
                'print_fee',
                'cost_print_application_one_distintion',
                'cost_print_application_add_distintion',
                'costs_correspondence_of_one_prod',
                'reduce_number_distitions',
                'cost_change_address',
                'cost_change_name',
                'cost_print_name',
                'cost_print_address',
                'payment_status'
            ]);
            $table->renameColumn('cost_5_year_one_distintion','cost_5_year');
            $table->renameColumn('cost_10_year_one_distintion','cost_10_year');
        });
    }
}
