<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRankToMLawsRegulationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('m_laws_regulations', function (Blueprint $table) {
            $table->string('rank', 50)->after('name')->comment('法令名');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('m_laws_regulations', function (Blueprint $table) {
            $table->dropColumn('rank');
        });
    }
}
