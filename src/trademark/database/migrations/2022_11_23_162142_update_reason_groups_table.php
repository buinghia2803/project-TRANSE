<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateReasonGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            DB::beginTransaction();

            Schema::table('comparison_trademark_results', function (Blueprint $table) {
                $table->string('step', 10)->default(1)->after('is_cancel');
            });

            Schema::table('plan_correspondences', function (Blueprint $table) {
                $table->date('register_date')->nullable()->after('is_ext_period_2');
            });

            Schema::table('plan_correspondence_prods', function (Blueprint $table) {
                $table->boolean('is_register')->default(0)->after('plan_correspondence_id');
                $table->boolean('completed_evaluation')->default(0)->after('application_trademark_product_id');
                $table->renameColumn('application_trademark_product_id', 'app_trademark_prod_id');
            });

            Schema::table('reason_ref_num_prods', function (Blueprint $table) {
                $table->dropColumn(['vote_laws_regulation_id']);

                $table->string('vote_reason_id', 255)->after('comment_patent_agent');
                $table->string('rank', 50)->nullable()->after('vote_reason_id');
            });

            Schema::table('reasons', function (Blueprint $table) {
                $table->dropForeign('reasons_plan_correspondence_id_foreign');

                $table->dropColumn([
                    'plan_correspondence_id',
                    'question_status',
                    'flag_role',
                ]);
            });

            Schema::table('reason_no', function (Blueprint $table) {
                $table->dropColumn(['name']);

                $table->tinyInteger('flag_role')->default(1)->comment('1: 担当者 | 2: 責任者');
                $table->boolean('is_confirm')->default(0);

                $table->timestamps();
                $table->softDeletes();
            });

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        try {
            DB::beginTransaction();

            Schema::table('comparison_trademark_results', function (Blueprint $table) {
                $table->dropColumn(['step']);
            });

            Schema::table('plan_correspondences', function (Blueprint $table) {
                $table->dropColumn(['register_date']);
            });

            Schema::table('plan_correspondence_prods', function (Blueprint $table) {
                $table->dropColumn(['is_register', 'completed_evaluation']);
                $table->renameColumn('app_trademark_prod_id', 'application_trademark_product_id');
            });

            Schema::table('reason_ref_num_prods', function (Blueprint $table) {
                $table->dropColumn(['vote_reason_id', 'rank']);

                $table->string('vote_laws_regulation_id', 255)->comment('法令に商品・サービスの評価。フォーマット： ["1", "2", "3"]');
            });

            Schema::table('reasons', function (Blueprint $table) {
                $table->bigInteger('plan_correspondence_id')->comment('プラン申込むのID（plan_correspondences.id）');
                $table->tinyInteger('question_status')->default(2)->comment('事前質問は不要. 1: 不要 | 2: 必要');
                $table->tinyInteger('flag_role')->default(1)->comment('画面に作動する管理者ステータス. 1: 担当者 | 2: 責任者');
            });

            Schema::table('reasons', function (Blueprint $table) {
                $table->unsignedBigInteger('plan_correspondence_id')->change();
                $table->foreign('plan_correspondence_id')->references('id')->on('plan_correspondences');
            });

            Schema::table('reason_no', function (Blueprint $table) {
                $table->dropColumn(['flag_role', 'is_confirm', 'created_at', 'updated_at', 'deleted_at']);

                $table->string('name', 255)->comment('理由の数');
            });

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }
}
