<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldPaymentIdPaymentStatusNoticeDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notice_details', function (Blueprint $table) {
            $table->integer('payment_id')->nullable()->after('is_answer');
            $table->tinyInteger('payment_status')->nullable()->comment(' 0: 保存, // 見積書 1: お支払待ち, // 請求書 2: お支払済み // 領収書')->after('payment_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notice_details', function (Blueprint $table) {
            $table->dropColumn([
                'payment_id',
                'payment_status']);
        });
    }
}
