<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agents', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('admin_id')->comment('管理者のID（admins.id）');
            $table->string('identification_number', 9)->unique()->comment('識別番号');
            $table->string('name', 50)->comment('代理人氏名');
            $table->string('deposit_account_number', 6)->nullable()->comment('予納台帳番号');
            $table->tinyInteger('deposit_type')->default(2)->comment('納付方法. 1: 予納 | 2: 指定立替納付（クレジット）');
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
        Schema::dropIfExists('agents');
    }
}
