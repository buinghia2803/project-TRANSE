<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDescriptionToMTypePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('m_type_plans', function (Blueprint $table) {
            $table->text('description')->nullable()->comment('方針案の説明')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('m_type_plans', function (Blueprint $table) {
            $table->string('description', 1000)->nullable(false)->comment('方針案の説明')->change();
        });
    }
}
