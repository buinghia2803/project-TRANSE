<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateColumnMProductsMCodesMProductCodeMDistinctionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('m_code', function (Blueprint $table) {
            $table->string('branch_number')->nullable()->after('type');
        });

        Schema::table('m_products', function (Blueprint $table) {
            $table->bigInteger('parent_id')->nullable()->after('block');
            $table->boolean('is_parent')->default(0)->after('block');
            $table->boolean('is_check')->default(0)->after('block');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('m_code', function (Blueprint $table) {
            $table->dropColumn('branch_number');
        });

        Schema::table('m_products', function (Blueprint $table) {
            $table->dropColumn(['is_parent', 'parent_id', 'is_check']);
        });
    }
}
