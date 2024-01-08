<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateFieldsPlanDetailDocsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plan_detail_docs', function (Blueprint $table) {
            $table->integer('m_type_plan_doc_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plan_detail_docs', function (Blueprint $table) {
            $table->integer('m_type_plan_doc_id')->nullable(false)->change();

        });
    }
}
