<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddResponseDeadlineToRequiredDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('required_documents', function (Blueprint $table) {
            $table->date('response_deadline')->nullable()->after('trademark_plan_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('required_documents', function (Blueprint $table) {
            $table->dropColumn('response_deadline');
        });
    }
}
