<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldToPlanDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plan_details', function (Blueprint $table) {
            $table->integer('type_plan_id_edit')->after('possibility_resolution')->nullable()->comment('方針案 = m_type_plans.id');
            $table->string('plan_description_edit', 1000)->after('type_plan_id_edit')->nullable()->comment('方針案の説明');
            $table->tinyInteger('possibility_resolution_edit')->after('plan_description_edit')->nullable()->default(null)->comment('解消可能性 1: ◎, 2: ○, 3: △, 4: ×');
            $table->tinyInteger('is_decision')->after('is_choice')->default(0)->comment('0: not choose, 1: draft, 2: edit');
            $table->tinyInteger('is_choice_past')->default(0)->after('is_decision')->comment('0: false, 1: true');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plan_details', function (Blueprint $table) {
            $table->dropColumn([
                'type_plan_id_edit',
                'plan_description_edit',
                'possibility_resolution_edit',
                'is_decision',
                'is_choice_past'
            ]);
        });
    }
}
