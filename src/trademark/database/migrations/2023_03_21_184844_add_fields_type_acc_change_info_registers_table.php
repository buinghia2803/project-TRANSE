<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsTypeAccChangeInfoRegistersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('change_info_registers', function (Blueprint $table) {
            $table->tinyInteger('type_acc')->default(1)->comment('1: 法人 // Corporation, 2: 個人 // Individual')->after('payment_id');
            $table->integer('register_trademark_id')->nullable()->after('trademark_info_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('change_info_registers', function (Blueprint $table) {
            $table->dropColumn([
                'type_acc',
                'register_trademark_id'
            ]);
        });
    }
}
