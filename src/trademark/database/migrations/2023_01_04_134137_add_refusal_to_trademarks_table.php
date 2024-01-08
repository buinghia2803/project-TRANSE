<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRefusalToTrademarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trademarks', function (Blueprint $table) {
            $table->text('comment_refusal', 1000)->nullable()->after('status_management');
            $table->tinyInteger('is_refusal')->comment('1: not refusal, 2: refusal, 3: confirm')->default(1)->nullable()->after('comment_refusal');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trademarks', function (Blueprint $table) {
            $table->dropColumn(['comment_refusal']);
            $table->dropColumn(['is_refusal']);
        });
    }
}
