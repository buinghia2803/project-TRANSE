<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegisterTrademarkRenewalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('register_trademark_renewals', function (Blueprint $table) {
            $table->id();
            $table->integer('register_trademark_id');
            $table->tinyInteger('type')->comment(' 1: 期限日前期間延長,2: 期間外延長')->default(1);
            $table->date('registration_period');
            $table->tinyInteger('is_send_mail')->comment('0: not send,1: sended')->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->tinyInteger('status')->comment('1:save draft ,2:admin confirm ,3:completed')->default(0);
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
        Schema::dropIfExists('register_trademark_renewals');
    }
}
