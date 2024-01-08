<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateFieldsPlansTableV1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->string('description_documents_miss', 255)->nullable()->comment('足りない資料の説明')->change();
            $table->dropColumn(['reason_cancel', 'is_cancel']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->string('description_documents_miss', 255)->nullable(false)->change();
            $table->boolean('is_cancel')->default(0)->comment('中止のステータス. 0: false | 1: true');
            $table->string('reason_cancel', 500)->comment('中止の理由');
        });
    }
}
