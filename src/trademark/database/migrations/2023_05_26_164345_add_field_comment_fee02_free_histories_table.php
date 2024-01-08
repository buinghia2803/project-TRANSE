<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldCommentFee02FreeHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('free_histories', function (Blueprint $table) {
            $table->string('comment_free02', 1000)->nullable()->after('comment')->comment('AMSからのコメント free02');
            $table->boolean('is_completed')->default(0)->after('is_confirm');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('free_histories', function (Blueprint $table) {
            $table->dropColumn([
                'comment_free02',
                'is_completed'
            ]);
        });
    }
}
