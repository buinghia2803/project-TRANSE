<?php

use App\Models\PlanCorrespondenceProd;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateRoundPlanCorrespondenceProdsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        PlanCorrespondenceProd::where('round', '')->update([ 'round' => 0 ]);
        PlanCorrespondenceProd::whereNull('round')->update([ 'round' => 0 ]);
        Schema::table('plan_correspondence_prods', function (Blueprint $table) {
            $table->integer('round')->default(0)->change();
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
            $table->string('round', 50)->change();
        });
    }
}
