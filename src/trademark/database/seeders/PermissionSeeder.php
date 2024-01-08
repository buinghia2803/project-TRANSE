<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use App\Repositories\RoleRepository;
use Spatie\Permission\PermissionRegistrar;


class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Truncate permission table
        Schema::disableForeignKeyConstraints();
        DB::table('roles')->truncate();
        DB::table('permissions')->truncate();
        DB::table('role_has_permissions')->truncate();
        Schema::enableForeignKeyConstraints();

        // Forget cache permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create and get all Permission
        $dataPermissions = [
            'agents' => [
                // a000dairinin
                'index' => ['guard_name' => 'admin'], // get
                'updateOrCreate' => ['guard_name' => 'admin'], // post

                // a000dairinin_set
                'showSettingSet' => ['guard_name' => 'admin'], // get
                'crudSettingSet' => ['guard_name' => 'admin'], // post
            ],
            'sft' => [
                // a011
                'create' => ['guard_name' => 'admin'], // get
                'store' => ['guard_name' => 'admin'],  // post

                // a011s
                'index' => ['guard_name' => 'admin'], // get

                // a011shu
                'edit' => ['guard_name' => 'admin'], // get
                'editPost' => ['guard_name' => 'admin'], // post
            ],
            'prechecks' => [
                // a021kan
                'viewPrecheckSimple' => ['guard_name' => 'admin'], // get
                'createPrecheckResult' => ['guard_name' => 'admin'], // post

                // a021shiki
                'viewPrecheckSelectUnique' => ['guard_name' => 'admin'], // get
                'createPrecheckResultUnique' => ['guard_name' => 'admin'], // post

                // a021rui
                'viewPrecheckSelectSimilar' => ['guard_name' => 'admin'],  // get
                'createPrecheckResultSimilar' => ['guard_name' => 'admin'], // post

                // a021s
                'showConfirmPreCheck' => ['guard_name' => 'admin'], // get
                'updateRolePrecheck' => ['guard_name' => 'admin'], // post

                // a021shiki_shu
                'viewEditPrecheckUnique' => ['guard_name' => 'admin'], // get
                'EditPrecheckUnique' => ['guard_name' => 'admin'], // post

                // a021rui_shu
                'viewEditPrecheckSimilar' => ['guard_name' => 'admin'], // get
                'EditPrecheckSimilar' => ['guard_name' => 'admin'], // post
            ],
            "payment-all" => [
                //payment-all
                'viewPaymentAll' => ['guard_name' => 'admin'], //get
            ],
            "payment" => [
                //payment
                'viewBankTransfer' => ['guard_name' => 'admin'], //get
                'sendMailRemindPayment' => ['guard_name' => 'admin'], //get
                'updatePaymentBankTransfer' => ['guard_name' => 'admin'], //post
            ],
            'free_histories' => [
                // a000free
                'create' => ['guard_name' => 'admin'], // get
                'store' => ['guard_name' => 'admin'],  // post

                // a000free_s
                'edit' => ['guard_name' => 'admin'], // get
                'update' => ['guard_name' => 'admin'],  // post

                'reConfirm' => ['guard_name' => 'admin'], // get
                'postReConfirm' => ['guard_name' => 'admin'],  // post
            ],
            'import_01' => [
                'viewImport01' => ['guard_name' => 'admin'], // get
                'sendSession' => ['guard_name' => 'admin'], // post
            ],
            'import_02' => [
                'viewImport02' => ['guard_name' => 'admin'], // get
                'saveImportXML' => ['guard_name' => 'admin'], // post
                'showCompleted' => ['guard_name' => 'admin'], // get
            ],
            'maching_results' => [
                // a201a
                'refusalRequestReview' => ['guard_name' => 'admin'], // get
                'refusalRequestReviewCreate' => ['guard_name' => 'admin'],  // post
                //a302hosei01
                'showDocumentModification' => ['guard_name' => 'admin'], // get
                'redirectPageHosei' => ['guard_name' => 'admin'],  // post
                //a302hosei02
                'showDocumentModificationProduct' => ['guard_name' => 'admin'], // get
                //a302hosei02skip
                'registrationDocumentModificationSkip' =>  ['guard_name' => 'admin'], // get
                //a303
                'showRegistrationInput' => ['guard_name' => 'admin'], // get,
                // A302
                'registrationDocument' => ['guard_name' => 'admin'], // get,
                'postRegistrationDocument' => ['guard_name' => 'admin'], // get,
            ],
            'comparison_trademark_result' => [
                // a201b02
                'createExamine' => ['guard_name' => 'admin'], // get
                'postCreateExamine' => ['guard_name' => 'admin'],  // post

                //a202
                'preQuestionIndex' => ['guard_name' => 'admin'], // get
                'createPreQuestion' => ['guard_name' => 'admin'], // post

                // a201b
                'createReason' => ['guard_name' => 'admin'],  // get
                'postCreateReason' => ['guard_name' => 'admin'], // post

                // a201b_s
                'createReasonSupervisor' => ['guard_name' => 'admin'],  // get
                'postCreateReasonSupervisor' => ['guard_name' => 'admin'], // post

                // a201b02s
                'createExamineSupervisor' => ['guard_name' => 'admin'], // get
                'postCreateExamineSupervisor' => ['guard_name' => 'admin'],  // post

                // a201b02_s_n
                'editExamineSupervisor' => ['guard_name' => 'admin'], // get
                'postEditExamineSupervisor' => ['guard_name' => 'admin'],  // post

                // a201b02_n
                'editExamine' => ['guard_name' => 'admin'], // get
                'postEditExamine' => ['guard_name' => 'admin'],  // post

                // a201b_n
                'editReason' => ['guard_name' => 'admin'],  // get
                'postEditReason' => ['guard_name' => 'admin'], // post

                // a201b_s_n
                'editReasonSupervisor' => ['guard_name' => 'admin'],  // get
                'postEditReasonSupervisor' => ['guard_name' => 'admin'], // post

                // a202s
                'preQuestionSupervisor' => ['guard_name' => 'admin'], // get
                'postSupervisor' => ['guard_name' => 'admin'],  // post

                //a202n_s
                'preQuestionReShow' => ['guard_name' => 'admin'], //get
                'savePreQuestionReShow' => ['guard_name' => 'admin'], //post
            ],
            'document_to_check' => [
                // A-031
                'viewApplyTrademark' => ['guard_name' => 'admin'], // get
                'updateTrademark' => ['guard_name' => 'admin'], // post
            ],
            'plans' => [
                //a203
                'index' =>  ['guard_name' => 'admin'], //get
                'store' =>  ['guard_name' => 'admin'], //post

                // a203c
                'productCreate' => ['guard_name' => 'admin'], //get
                'postProductCreate' => ['guard_name' => 'admin'], //post

                //a203shu
                'editSupervisor' =>  ['guard_name' => 'admin'], //get
                'postEditSupervisor' =>  ['guard_name' => 'admin'], //post

                // a203c_shu
                'productEditSupervisor' => ['guard_name' => 'admin'], //get
                'postProductEditSupervisor' => ['guard_name' => 'admin'], //post

                // a203n
                'getRefusalResponsePlanReSupervisor' => ['guard_name' => 'admin'], //get
                'postRefusalResponsePlanReSupervisor' => ['guard_name' => 'admin'], //post

                // a203c_n
                'productReCreateSupervisor' => ['guard_name' => 'admin'], //get
                'postProductReCreateSupervisor' => ['guard_name' => 'admin'], //post

                //a203s
                'getRefusalResponsePlaneSupervisor' =>  ['guard_name' => 'admin'], //get
                'postRefusalResponsePlaneSupervisor' =>  ['guard_name' => 'admin'], //post

                //a203sashi
                'getRefusalResponsePlaneSupervisorReject' =>  ['guard_name' => 'admin'], //get
                'postRefusalResponsePlaneSupervisorReject' =>  ['guard_name' => 'admin'], //post

                // a203c_rui
                'showSimilarGroupCode' => ['guard_name' => 'admin'], //get

                // a203c_rui_edit
                'showSimilarGroupCodeEdit' => ['guard_name' => 'admin'], //get
                'redirectSimilarGroupCodeEditConfirm' => ['guard_name' => 'admin'], //post

                // a203c_rui_edit02
                'confirmSimilarGroupCodeEdit' => ['guard_name' => 'admin'], //get
                'updateSimilarGroupCodeEditConfirm' => ['guard_name' => 'admin'], //post

                //a203check
                'showModalA203check' =>  ['guard_name' => 'admin'], //get
            ],
            'materials' => [
                // a204han
                'supervisor' => ['guard_name' => 'admin'], //get
                'postSupervisor' => ['guard_name' => 'admin'], //post

                // a204han_n
                'checkSupervisor' => ['guard_name' => 'admin'], //get
                'postCheckSupervisor' => ['guard_name' => 'admin'], //post

                // a204n
                'reSupervisor' => ['guard_name' => 'admin'], //get
                'postReSupervisor' => ['guard_name' => 'admin'], //post

                // a204no_mat
                'noMaterial' => ['guard_name' => 'admin'], //get
                'postNoMaterial' => ['guard_name' => 'admin'], //post

                // a203_204kakunin
                'confirm' => ['guard_name' => 'admin'], //get
            ],
            'refusal_documents' => [
                //a205
                'showA205' => ['guard_name' => 'admin'], // get
                'storeA205' => ['guard_name' => 'admin'], // post

                //a205kakunin
                'showA205Kakunin' => ['guard_name' => 'admin'], // get
                'postA205Kakunin' => ['guard_name' => 'admin'], // post

                //a205shu
                'showA205shu' => ['guard_name' => 'admin'], // get
                'saveA205Shu' => ['guard_name' => 'admin'], // post

                // a205hiki
                'showA205Hiki' => ['guard_name' => 'admin'], //get

                // a205s
                'showA205s' => ['guard_name' => 'admin'], //get

                // a205shashi
                'showA205Sashi' => ['guard_name' => 'admin'], //get
            ],
            'decided_to_refuse' => [
                // a206kyo_s
                'finalRefusal' => ['guard_name' => 'admin'], // get
                'postFinalRefusal' => ['guard_name' => 'admin'], // post
            ],
            'extension_period' => [
                // a210
                'showA210alert' => ['guard_name' => 'admin'], // get
                'showA210Over' => ['guard_name' => 'admin'], // get
                'updateDataAlert' => ['guard_name' => 'admin'], // post
                // a301
                'registrationNotify' => ['guard_name' => 'admin'], // get
                'postRegistrationNotify' => ['guard_name' => 'admin'], // post

                //A-700shutsugannin01
                'showRegistration' => ['guard_name' => 'admin'], // get
                'sendSessionConfirm' => ['guard_name' => 'admin'], // post

                'showConfirmRegistration' => ['guard_name' => 'admin'], // get
                'updateInfo' => ['guard_name' => 'admin'], // post

                'showDocumentRegistration' => ['guard_name' => 'admin'], // get
                'saveDataDocument' => ['guard_name' => 'admin'], // post

                'showSkipRegistration' => ['guard_name' => 'admin'], // get
            ],
            'register_trademark' => [
                //a402hosoku01
                'updateDocumentModifyProd' => ['guard_name' => 'admin'], //get

                //a302_402_5yr_kouki
                'getRegisProcedureLatterPeriodDocument' => ['guard_name' => 'admin'], //get
                'postRegisProcedureLatterPeriodDocument' => ['guard_name' => 'admin'], // post

                'getDocumentModifyProd' => ['guard_name' => 'admin'], //get,
                'postDocumentModifyProd' => ['guard_name' => 'admin'], //post

                //a402hosoku02skip
                'skipDocumentModifyProd' => ['guard_name' => 'admin'], //get

                //a402
                'updateProcedureDocument' => ['guard_name' => 'admin'], //get
                'updateProcedureDocumentPost' => ['guard_name' => 'admin'], //post
            ],
            'change_address' => [
                //a700kenrisha01
                'updateChangeAddress' => ['guard_name' => 'admin'], //get
                'postUpdateChangeAddress' => ['guard_name' => 'admin'], //post
            ]
        ];

        // Create and get all Role
        $dataRoles = [
            [
                'id' => ROLE_OFFICE_MANAGER,
                'name' => 'jimu',
                'guard_name' => 'admin',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'permissions' => [
                    'agents.index',
                    'agents.updateOrCreate',
                    'agents.showSettingSet',
                    'agents.crudSettingSet',
                    'payment-all.viewPaymentAll',
                    'payment.viewBankTransfer',
                    'payment.sendMailRemindPayment',
                    'payment.updatePaymentBankTransfer',
                    'free_histories.create',
                    'free_histories.store',
                    'free_histories.edit',
                    'free_histories.reConfirm',
                    'free_histories.postReConfirm',
                    'import_01.viewImport01',
                    'import_01.sendSession',
                    'import_02.viewImport02',
                    'import_02.saveImportXML',
                    'import_02.showCompleted',
                    'maching_results.refusalRequestReview',
                    'maching_results.refusalRequestReviewCreate',
                    'maching_results.showDocumentModification',
                    'maching_results.redirectPageHosei',
                    'maching_results.showDocumentModificationProduct',
                    'maching_results.registrationDocumentModificationSkip',
                    'maching_results.showRegistrationInput',
                    'maching_results.registrationDocument',
                    'maching_results.postRegistrationDocument',
                    'document_to_check.viewApplyTrademark',
                    'document_to_check.updateTrademark',
                    'extension_period.showA210alert',
                    'extension_period.showA210Over',
                    'extension_period.updateDataAlert',
                    'extension_period.registrationNotify',
                    'extension_period.postRegistrationNotify',
                    'extension_period.showRegistration',
                    'extension_period.sendSessionConfirm',
                    'extension_period.showConfirmRegistration',
                    'extension_period.updateInfo',
                    'extension_period.showDocumentRegistration',
                    'extension_period.saveDataDocument',
                    'extension_period.showSkipRegistration',
                    'register_trademark.updateDocumentModifyProd',
                    'register_trademark.getRegisProcedureLatterPeriodDocument',
                    'register_trademark.postRegisProcedureLatterPeriodDocument',
                    'register_trademark.getDocumentModifyProd',
                    'register_trademark.postDocumentModifyProd',
                    'register_trademark.skipDocumentModifyProd',
                    'change_address.updateChangeAddress',
                    'change_address.postUpdateChangeAddress',
                    'register_trademark.updateProcedureDocument',
                    'register_trademark.updateProcedureDocumentPost',
                ],
            ],
            [
                'id' => ROLE_MANAGER,
                'name' => 'tantou',
                'guard_name' => 'admin',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'permissions' => [
                    'sft.create',
                    'sft.store',
                    'prechecks.viewPrecheckSimple',
                    'prechecks.createPrecheckResult',
                    'prechecks.viewPrecheckSelectUnique',
                    'prechecks.createPrecheckResultUnique',
                    'prechecks.viewPrecheckSelectSimilar',
                    'prechecks.createPrecheckResultSimilar',
                    'payment-all.viewPaymentAll',
                    'payment.viewBankTransfer',
                    'comparison_trademark_result.createExamine',
                    'comparison_trademark_result.postCreateExamine',
                    'comparison_trademark_result.preQuestionIndex',
                    'comparison_trademark_result.createPreQuestion',
                    'comparison_trademark_result.createReason',
                    'comparison_trademark_result.postCreateReason',
                    'comparison_trademark_result.editExamine',
                    'comparison_trademark_result.postEditExamine',
                    'comparison_trademark_result.editReason',
                    'comparison_trademark_result.postEditReason',
                    'comparison_trademark_result.preQuestionReShow',
                    'plans.productCreate',
                    'plans.postProductCreate',
                    'plans.index',
                    'plans.store',
                    'plans.showSimilarGroupCode',
                    'plans.showSimilarGroupCodeEdit',
                    'plans.redirectSimilarGroupCodeEditConfirm',
                    'plans.confirmSimilarGroupCodeEdit',
                    'plans.updateSimilarGroupCodeEditConfirm',
                    'plans.showModalA203check',
                    'plans.getRefusalResponsePlaneSupervisor',
                    'materials.noMaterial',
                    'materials.postNoMaterial',
                    'materials.confirm',
                    'refusal_documents.showA205',
                    'refusal_documents.storeA205',
                    'refusal_documents.showA205Kakunin',
                    'refusal_documents.postA205Kakunin',
                ],
            ],
            [
                'id' => ROLE_SUPERVISOR,
                'name' => 'seki',
                'guard_name' => 'admin',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'permissions' => [
                    'agents.index',
                    'agents.updateOrCreate',
                    'agents.showSettingSet',
                    'agents.crudSettingSet',
                    'sft.create',
                    'sft.index',
                    'sft.edit',
                    'sft.editPost',
                    'prechecks.viewPrecheckSimple',
                    'prechecks.viewPrecheckSelectUnique',
                    'prechecks.viewPrecheckSelectSimilar',
                    'prechecks.showConfirmPreCheck',
                    'prechecks.updateRolePrecheck',
                    'prechecks.viewEditPrecheckUnique',
                    'prechecks.EditPrecheckUnique',
                    'prechecks.viewEditPrecheckSimilar',
                    'prechecks.EditPrecheckSimilar',
                    'payment-all.viewPaymentAll',
                    'payment-all.updatePaymentAjax',
                    'payment.viewBankTransfer',
                    'payment.sendMailRemindPayment',
                    'payment.updatePaymentBankTransfer',
                    'free_histories.create',
                    'free_histories.store',
                    'free_histories.edit',
                    'free_histories.update',
                    'import_01.viewImport01',
                    'import_02.viewImport02',
                    'maching_results.refusalRequestReview',
                    'maching_results.showDocumentModification',
                    'maching_results.showDocumentModificationProduct',
                    'maching_results.registrationDocumentModificationSkip',
                    'maching_results.showRegistrationInput',
                    'maching_results.registrationDocument',
                    'comparison_trademark_result.createExamine',
                    'comparison_trademark_result.postCreateExamine',
                    'comparison_trademark_result.createReason',
                    'comparison_trademark_result.postCreateReason',
                    'comparison_trademark_result.createExamineSupervisor',
                    'comparison_trademark_result.postCreateExamineSupervisor',
                    'comparison_trademark_result.preQuestionIndex',
                    'comparison_trademark_result.editExamine',
                    'comparison_trademark_result.postEditExamine',
                    'document_to_check.viewApplyTrademark',
                    'comparison_trademark_result.createReasonSupervisor',
                    'comparison_trademark_result.postCreateReasonSupervisor',
                    'comparison_trademark_result.editExamineSupervisor',
                    'comparison_trademark_result.postEditExamineSupervisor',
                    'comparison_trademark_result.editReason',
                    'comparison_trademark_result.postEditReason',
                    'comparison_trademark_result.preQuestionSupervisor',
                    'comparison_trademark_result.postSupervisor',
                    'comparison_trademark_result.editReasonSupervisor',
                    'comparison_trademark_result.postEditReasonSupervisor',
                    'comparison_trademark_result.preQuestionReShow',
                    'comparison_trademark_result.savePreQuestionReShow',
                    'plans.index',
                    'plans.store',
                    'plans.productCreate',
                    'plans.productEditSupervisor',
                    'plans.postProductEditSupervisor',
                    'plans.productReCreateSupervisor',
                    'plans.postProductReCreateSupervisor',
                    'plans.getRefusalResponsePlaneSupervisor',
                    'plans.postRefusalResponsePlaneSupervisor',
                    'plans.getRefusalResponsePlaneSupervisorReject',
                    'plans.postRefusalResponsePlaneSupervisorReject',
                    'plans.editSupervisor',
                    'plans.postEditSupervisor',
                    'plans.getRefusalResponsePlanReSupervisor',
                    'plans.postRefusalResponsePlanReSupervisor',
                    'plans.showSimilarGroupCode',
                    'plans.showSimilarGroupCodeEdit',
                    'plans.redirectSimilarGroupCodeEditConfirm',
                    'plans.confirmSimilarGroupCodeEdit',
                    'plans.updateSimilarGroupCodeEditConfirm',
                    'plans.showModalA203check',
                    'materials.supervisor',
                    'materials.postSupervisor',
                    'materials.noMaterial',
                    'materials.checkSupervisor',
                    'materials.postCheckSupervisor',
                    'materials.reSupervisor',
                    'materials.postReSupervisor',
                    'materials.confirm',
                    'refusal_documents.showA205',
                    'refusal_documents.showA205Kakunin',
                    'refusal_documents.postA205Kakunin',
                    'refusal_documents.showA205Hiki',
                    'refusal_documents.showA205s',
                    'refusal_documents.showA205Sashi',
                    'refusal_documents.showA205shu',
                    'refusal_documents.saveA205Shu',
                    'decided_to_refuse.finalRefusal',
                    'decided_to_refuse.postFinalRefusal',
                    'extension_period.showA210alert',
                    'extension_period.showA210Over',
                    'extension_period.registrationNotify',
                    'extension_period.showRegistration',
                    'extension_period.showConfirmRegistration',
                    'extension_period.showDocumentRegistration',
                    'register_trademark.updateDocumentModifyProd',
                    'register_trademark.getRegisProcedureLatterPeriodDocument',
                    'register_trademark.getDocumentModifyProd',
                    'register_trademark.skipDocumentModifyProd',
                    'change_address.updateChangeAddress',
                    'register_trademark.updateProcedureDocument',
                ],
            ],
        ];

        foreach ($dataPermissions as $key => $permissions) {
            foreach ($permissions as $permission => $value) {
                $name = $key . '.' . $permission;
                $value['name'] = $name;
                $value['created_at'] = date('Y-m-d H:i:s');
                $value['updated_at'] = date('Y-m-d H:i:s');
                Permission::insert($value);
            }
        }

        $permissions = Permission::all();

        foreach ($dataRoles as $roleItem) {
            $permissionRole = $permissions->whereIn('name', $roleItem['permissions'] ?? []);
            $permissionIds = $permissionRole->pluck('id');

            $role = Role::create([
                'name' => $roleItem['name'],
                'guard_name' => $roleItem['guard_name'],
            ]);

            $role->syncPermissions($permissionIds);
        }
    }
}
