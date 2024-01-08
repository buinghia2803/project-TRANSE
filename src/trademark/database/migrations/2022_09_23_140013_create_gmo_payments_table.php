<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGmoPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gmo_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payment_id')->comment('請求金額(payments.id)');
            $table->bigInteger('gmo_order_id');
            $table->string('job_cd', 7)->default(2)->comment('処理タイプ. 1: CHECK | 2: CAPTURE | 3: AUTH | 4: SAUTH');
            $table->tinyInteger('pay_type')->default(0)->comment('お支払い. 0: クレジット | 1: 銀行振込');
            $table->string('access_id', 100)->comment('EntryTransステップで取得');
            $table->string('access_pass', 100)->comment('EntryTransステップで取得');
            $table->string('forward', 50)->comment('決済成功時に受け取る');
            $table->string('approve', 50)->comment('決済成功時に受け取る');
            $table->string('tran_id', 50)->comment('決済成功時に受け取る');
            $table->timestamp('tran_date')->comment('決済成功時に受け取る');
            $table->tinyInteger('status')->default(1)->comment('支払い状況. 1: Success | 2: Failed');
            $table->text('error_info')->nullable()->comment('エラー情報');
            $table->timestamp('created_at');

            $table->foreign('payment_id')->references('id')->on('payments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gmo_payments');
    }
}
