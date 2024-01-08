<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrademarkRenewalNoticesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trademark_renewal_notices', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('trademark_id');
            $table->bigInteger('notice_detail_id');
            $table->tinyInteger('pattern')->default(1)->comment('1: 何も延長してない人, 2: 期限日前期間延長のみの人, 3: 期間外延長のみの人');
            $table->tinyInteger('type')->default(1)->comment('1: 3.5 < due date <= 10, 2: 0 <= due date <= 3.5, 3: due date > 0, 4: 0 <= due date <= 3.5 (2nd)');
            $table->tinyInteger('is_send_notice')->default(0)->comment('0: not send, 1: sended');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trademark_renewal_notices');
    }
}
