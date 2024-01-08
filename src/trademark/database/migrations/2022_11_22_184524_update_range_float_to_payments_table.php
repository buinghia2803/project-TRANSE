<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateRangeFloatToPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->decimal('cost_bank_transfer', 15, 2)->nullable()->comment('銀行振込の料金')->change();
            $table->decimal('subtotal', 15, 2)->comment('小計')->change();
            $table->decimal('commission', 15, 2)->comment('実手数料')->change();
            $table->decimal('tax', 15, 2)->comment('消費税')->change();
            $table->decimal('cost_service_base', 15, 2)->comment('消費税')->change();
            $table->decimal('cost_print_application', 15, 2)->nullable()->comment('出願時の印紙代')->change();
            $table->decimal('cost_print_5year_or_10year', 15, 2)->nullable()->comment('5年登録時の印紙代')->change();
            $table->decimal('cost_registration_certificate', 15, 2)->nullable()->comment('登録証の郵送を希望する時料金')->change();
            $table->decimal('cost_application', 15, 2)->nullable()->comment('特許庁への費用')->change();
            $table->decimal('cost_5_year_one_distintion', 15, 2)->nullable()->comment('特許庁への費用（登録料・5年、預かり）')->change();
            $table->decimal('cost_10_year_one_distintion', 15, 2)->nullable()->comment('特許庁への費用（登録料・10年、預かり）')->change();
            $table->decimal('total_amount', 15, 2)->comment('合計')->change();
            $table->decimal('tax_withholding', 15, 2)->comment('源泉徴収税額')->change();
            $table->decimal('payment_amount', 15, 2)->comment('お支払額')->change();
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
            $table->float('cost_bank_transfer')->nullable()->comment('銀行振込の料金')->change();
            $table->float('subtotal')->comment('小計')->change();
            $table->float('commission')->comment('実手数料')->change();
            $table->float('tax')->comment('消費税')->change();
            $table->float('cost_print_application')->nullable()->comment('出願時の印紙代')->change();
            $table->float('cost_print_5year_or_10year')->nullable()->comment('5年登録時の印紙代')->change();
            $table->float('cost_registration_certificate')->nullable()->comment('登録証の郵送を希望する時料金')->change();
            $table->float('cost_application')->nullable()->comment('特許庁への費用')->change();
            $table->float('cost_5_year_one_distintion')->nullable()->comment('特許庁への費用（登録料・5年、預かり）')->change();
            $table->float('cost_10_year_one_distintion')->nullable()->comment('特許庁への費用（登録料・10年、預かり）')->change();
            $table->float('total_amount')->comment('合計')->change();
            $table->float('tax_withholding')->comment('源泉徴収税額')->change();
            $table->float('payment_amount')->comment('お支払額')->change();
        });
    }
}
