<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTrademarkIdToPlanCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plan_comments', function (Blueprint $table) {
            $table->bigInteger('trademark_id')->default(0)->after('admin_id')->comment('商標のID（trademarks.id）');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plan_comments', function (Blueprint $table) {
            $table->dropColumn('trademark_id');
        });
    }
}
