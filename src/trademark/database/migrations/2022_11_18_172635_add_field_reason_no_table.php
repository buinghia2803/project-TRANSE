<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldReasonNoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reason_no', function (Blueprint $table) {
            $table->bigInteger('plan_correspondence_id')->after('id');
            $table->tinyInteger('reason_number')->after('plan_correspondence_id')->comment('理由の数');
            $table->tinyInteger('reason_branch_number')->after('reason_number')->comment('枝番を付ける理由');
            $table->date('response_deadline')->nullable()->after('reason_branch_number')->comment('お客様回答期限日');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reason_no', function (Blueprint $table) {
            $table->dropColumn([
                'reason_number',
                'plan_correspondence_id',
                'reason_branch_number',
                'response_deadline',
            ]);
        });
    }
}
