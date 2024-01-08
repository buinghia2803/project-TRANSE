<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnQuoteInvoiceReceiptPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('payment_number');
            $table->string('receipt_number', 100)->after('payer_info_id')->nullable();
            $table->string('invoice_number', 100)->after('payer_info_id')->nullable();
            $table->string('quote_number', 100)->after('payer_info_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('support_first_times', function (Blueprint $table) {
            $table->dropColumn(['quote_number', 'invoice_number', 'receipt_number']);
            $table->string('payment_number', 100)->after('payer_info_id')->comment('請求書番号');
        });
    }
}
