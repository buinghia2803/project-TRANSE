<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgentGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agent_groups', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('admin_id')->comment('管理者のID（admins.id）');
            $table->string('name', 50)->comment('セット名');
            $table->boolean('status_choice')->default(0)->comment('0: チェック外 | 1: チェック');
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
        Schema::dropIfExists('agent_groups');
    }
}
