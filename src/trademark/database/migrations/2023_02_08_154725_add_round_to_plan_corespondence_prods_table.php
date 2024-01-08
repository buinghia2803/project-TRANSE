<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRoundToPlanCorespondenceProdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plan_correspondence_prods', function (Blueprint $table) {
            $table->string('round', 50)->nullable()->after('completed_evaluation');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plan_correspondence_prods', function (Blueprint $table) {
            $table->dropColumn('round');
        });
    }
}
