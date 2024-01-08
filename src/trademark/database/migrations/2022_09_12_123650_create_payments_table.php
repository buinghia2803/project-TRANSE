<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('target_id')->comment('「出願」か「はじめからサポート お申し込み」か「はじめからサポートサービス：AMSからの提案」などサービスのID');
            $table->float('cost_bank_transfer')->nullable()->comment('銀行振込の料金');
            $table->float('subtotal')->comment('小計');
            $table->float('commission')->comment('実手数料');
            $table->float('tax')->comment('消費税');
            $table->float('cost_print_application')->nullable()->comment('出願時の印紙代');
            $table->float('cost_print_5year_or_10year')->nullable()->comment('5年登録時の印紙代');
            $table->float('cost_registration_certificate')->nullable()->comment('登録証の郵送を希望する時料金');
            $table->float('cost_application')->nullable()->comment('特許庁への費用');
            $table->float('cost_5_year')->nullable()->comment('特許庁への費用（登録料・5年、預かり）');
            $table->float('cost_10_year')->nullable()->comment('特許庁への費用（登録料・10年、預かり）');
            $table->float('total_amount')->comment('合計');
            $table->float('tax_withholding')->comment('源泉徴収税額');
            $table->float('payment_amount')->comment('お支払額');
            $table->tinyInteger('type')->default(1)->comment('1: 出願 | 2: はじめからサポート お申し込み | 3: はじめからサポートサービス：AMSからの提案 | 4: プレチェックサービス | 5: プレチェックサービス：AMSからのレポート | 6: 拒絶理由通知対応 | 7: 拒絶理由通知対応：方針案選択 | 8: 商標登録 | 9: 後期納付期限のお知らせ・納付手続きのお申込み');
            $table->boolean('is_confirm')->default(0)->comment('確認ステータス. 0: false | 1: true');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
