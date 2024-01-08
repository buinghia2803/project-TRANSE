<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeAgentGroupMapsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('agent_group_maps', function (Blueprint $table) {
            $table->tinyInteger('type')->default(1)->after('agent_id')->comment('1: 代理人選択, 2: 選任代理人選択');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('agent_group_maps', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}
