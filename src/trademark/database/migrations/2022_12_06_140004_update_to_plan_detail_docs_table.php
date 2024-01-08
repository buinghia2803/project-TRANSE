<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateToPlanDetailDocsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plan_detail_docs', function (Blueprint $table) {
            $table->dropColumn(['doc_requirement']);
            $table->string('doc_requirement_des', 1000)->nullable()->change();
            $table->integer('m_type_plan_doc_id');
            $table->integer('m_type_plan_doc_id_edit')->nullable();
            $table->string('doc_requirement_des_edit', 1000)->nullable();
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
            $table->tinyInteger('doc_requirement')->after('plan_detail_id')->default(1)->comment('1: 資格を有することを証明する書面 | 2: 新聞・雑誌の記事、チラシ・ホームページなどの印刷物 | 3: 使用意思（フォーマット付き） | 4: 承諾書（フォーマット付き） | 5: 自由記述 | 6: 不要');
            $table->string('doc_requirement_des', 500)->nullable(false)->change();
            $table->dropColumn([
                'm_type_plan_doc_id',
                'm_type_plan_doc_id_edit',
                'doc_requirement_des_edit'
            ]);
        });
    }
}
