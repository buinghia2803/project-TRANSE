<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class CreateForeignKeyToAllTable extends Migration
{
    public $foreignKeyData = [
        'm_distinctions' => [
            'admin_id' => 'admins.id',
        ],
        'm_products' => [
            'm_distinction_id' => 'm_distinctions.id'
        ],
        'm_code' => [
            'admin_id' => 'admins.id',
        ],
        'm_product_codes' => [
            'm_product_id' => 'm_products.id',
            'm_code_id' => 'm_code.id',
        ],
        'users' => [
            'info_nation_id' => 'm_nations.id',
            'info_prefectures_id' => 'm_prefectures.id',
            'contact_prefectures_id' => 'm_prefectures.id',
        ],
        'authentications' => [
            'user_id' => 'users.id',
        ],
        'agents' => [
            'admin_id' => 'admins.id',
        ],
        'agent_groups' => [
            'admin_id' => 'admins.id',
        ],
        'agent_group_maps' => [
            'agent_group_id' => 'agent_groups.id',
            'agent_id' => 'agents.id',
        ],
        'question_answers' => [
            'admin_id' => 'admins.id',
            'user_id' => 'users.id',
        ],
        'trademarks' => [
            'user_id' => 'users.id',
        ],
        'support_first_times' => [
            'trademark_id' => 'trademarks.id',
        ],
        'sft_contents_products' => [
            'support_first_time_id' => 'support_first_times.id',
        ],
        'sft_suitable_products' => [
            'admin_id' => 'admins.id',
            'support_first_time_id' => 'support_first_times.id',
            'm_product_id' => 'm_products.id',
        ],
        'sft_comments' => [
            'admin_id' => 'admins.id',
            'support_first_time_id' => 'support_first_times.id',
        ],
        'app_trademarks' => [
            'admin_id' => 'admins.id',
            'trademark_id' => 'trademarks.id',
            'agent_group_id' => 'agent_groups.id',
        ],
        'app_trademark_prods' => [
            'app_trademark_id' => 'app_trademarks.id',
            'm_product_id' => 'm_products.id',
        ],
        'app_trademark_prod_cmts' => [
            'app_trademark_id' => 'app_trademarks.id',
            'm_distinction_id' => 'm_distinctions.id',
        ],
        'trademark_infos' => [
            'm_nation_id' => 'm_nations.id',
            'm_prefecture_id' => 'm_prefectures.id',
        ],
        'payments' => [
            'payer_info_id' => 'payer_infos.id',
        ],
        'payment_prods' => [
            'payment_id' => 'payments.id',
            'm_product_id' => 'm_products.id',
        ],
        'payer_infos' => [
            'm_nation_id' => 'm_nations.id',
            'm_prefecture_id' => 'm_prefectures.id',
        ],
        'my_folders' => [
            'user_id' => 'users.id',
        ],
        'my_folder_products' => [
            'my_folder_id' => 'my_folders.id',
            'm_product_id' => 'm_products.id',
        ],
        'prechecks' => [
            'trademark_id' => 'trademarks.id',
        ],
        'precheck_products' => [
            'admin_id' => 'admins.id',
            'precheck_id' => 'prechecks.id',
            'm_product_id' => 'm_products.id',
        ],
        'precheck_results' => [
            'admin_id' => 'admins.id',
            'precheck_product_id' => 'precheck_products.id',
        ],
        'precheck_result_comments' => [
            'admin_id' => 'admins.id',
            'precheck_result_id' => 'precheck_results.id',
        ],
        'docs' => [
            'admin_id' => 'admins.id',
            'app_trademark_id' => 'app_trademarks.id',
        ],
        'maching_results' => [
            'admin_id' => 'admins.id',
            'application_trademark_id' => 'app_trademarks.id',
        ],
        'comparison_trademark_results' => [
            'admin_id' => 'admins.id',
            'maching_result_id' => 'maching_results.id',
        ],
        'plan_correspondences' => [
            'comparison_trademark_result_id' => 'comparison_trademark_results.id',
        ],
        'plan_correspondence_prods' => [
            'plan_correspondence_id' => 'plan_correspondences.id',
            'app_trademark_prod_id' => 'app_trademark_prods.id',
        ],
        'reasons' => [
            'admin_id' => 'admins.id',
            'plan_correspondence_id' => 'plan_correspondences.id',
            'reason_no_id' => 'reason_no.id',
            'm_laws_regulation_id' => 'm_laws_regulations.id',
        ],
        'reason_ref_num_prods' => [
            'reason_id' => 'reasons.id',
            'plan_correspondence_prod_id' => 'plan_correspondence_prods.id',
            'admin_id' => 'admins.id',
        ],
        'reason_comments' => [
            'plan_correspondence_id' => 'plan_correspondences.id',
            'admin_id' => 'admins.id',
        ],
        'reason_questions' => [
            'admin_id' => 'admins.id',
            'plan_correspondence_id' => 'plan_correspondences.id',
        ],
        'trademark_plans' => [
            'plan_correspondence_id' => 'plan_correspondences.id',
        ],
        'plans' => [
            'admin_id' => 'admins.id',
            'trademark_plan_id' => 'trademark_plans.id',
        ],
        'plan_reasons' => [
            'plan_id' => 'plans.id',
            'reason_id' => 'reasons.id',
        ],
        'plan_details' => [
            'admin_id' => 'admins.id',
            'plan_id' => 'plans.id',
            'type_plan_id' => 'type_plans.id',
        ],
        'plan_detail_docs' => [
            'plan_detail_id' => 'plan_details.id',
        ],
        'plan_detail_distincts' => [
            'plan_detail_id' => 'plan_details.id',
            'm_distinction_id' => 'm_distinctions.id',
        ],
        'plan_detail_products' => [
            'plan_detail_id' => 'plan_details.id',
            'plan_correspondence_prod_id' => 'plan_correspondence_prods.id',
        ],
        'plan_comments' => [
            'admin_id' => 'admins.id',
        ],
        'doc_submissions' => [
            'admin_id' => 'admins.id',
            'trademark_plan_id' => 'trademark_plans.id',
        ],
        'doc_submission_sets' => [
            'admin_id' => 'admins.id',
            'doc_submission_id' => 'doc_submissions.id',
            'agent_group_id' => 'agent_groups.id',
        ],
        'doc_submission_cmts' => [
            'admin_id' => 'admins.id',
            'doc_submission_id' => 'doc_submissions.id',
        ],
        'doc_submission_attach_properties' => [
            'doc_submission_id' => 'doc_submissions.id',
        ],
        'doc_submission_attachments' => [
            'doc_submission_attach_property_id' => 'doc_submission_attach_properties.id',
        ],
        'register_trademarks' => [
            'admin_id' => 'admins.id',
            'trademark_id' => 'trademarks.id',
            'regist_cert_nation_id' => 'm_nations.id',
            'trademark_info_nation_id' => 'm_nations.id',
            'trademark_info_address_first' => 'm_prefectures.id',
            'agent_group_id' => 'agent_groups.id',
        ],
        'register_trademark_docs' => [
            'register_trademark_id' => 'register_trademarks.id',
        ],
        'register_trademark_prods' => [
            'register_trademark_id' => 'register_trademarks.id',
            'app_trademark_prod_id' => 'app_trademark_prods.id',
        ],
        'histories' => [
            'admin_id' => 'admins.id',
        ],
        'free_histories' => [
            'trademark_id' => 'trademarks.id',
            'admin_id_create' => 'admins.id',
            'admin_id_confirm' => 'admins.id',
        ],
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach ($this->foreignKeyData as $currenTable => $foreignArray) {
            foreach ($foreignArray as $currenTableField => $foreignTable) {
                try {
                    $foreignTable = explode('.', $foreignTable);
                    $foreignTableName = $foreignTable[0];
                    $foreignTableField = $foreignTable[1];

                    Schema::table($currenTable, function (Blueprint $table) use ($currenTableField, $foreignTableName, $foreignTableField) {
                        $table->unsignedBigInteger($currenTableField)->change();
                        $table->foreign($currenTableField)->references($foreignTableField)->on($foreignTableName);
                    });
                } catch (\Exception $e) {
                    Log::error($e);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach ($this->foreignKeyData as $currenTable => $foreignArray) {
            foreach ($foreignArray as $currenTableField => $foreignTable) {
                Schema::table($currenTable, function (Blueprint $table) use ($currenTable, $currenTableField) {
                    $table->dropForeign($currenTable . '_' . $currenTableField . '_foreign');
                });

                Schema::table($currenTable, function (Blueprint $table) use ($currenTableField) {
                    $table->bigInteger($currenTableField)->change();
                });
            }
        }
    }
}
