<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgentGroupMapsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agent_group_maps', function (Blueprint $table) {
            $table->bigInteger('agent_group_id')->comment('セットのID（agent_groups.id）');
            $table->bigInteger('agent_id')->comment('代理人のID（agents.id）');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agent_group_maps');
    }
}
