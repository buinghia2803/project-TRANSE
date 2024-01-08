<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnContentMTypePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('m_type_plans', function (Blueprint $table) {
            $table->string('description', 2000)->nullable()->change();
            $table->string('content', 1000)->after('description')->nullable();
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
            $table->text('description')->nullable()->change();
            $table->dropColumn('content');
        });
    }
}
