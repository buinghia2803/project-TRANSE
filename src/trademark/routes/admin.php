<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::group([
    'namespace' => 'Admin\Auth',
    'prefix' => config('app.admin_dir'),
    'as' => 'admin.'
], function () {
    // Admin Auth Routes
    Route::get('login', 'LoginController@showLoginForm')
        ->name('login');
    Route::post('login', 'LoginController@login');
    Route::get('logout', 'LoginController@logout')
        ->name('logout');

    Route::get('/forgot-password', 'ForgotPasswordController@forgotPassword')
        ->name('forgot-password');
    Route::post('/forgot-password', 'ForgotPasswordController@setForgotPassword')
        ->name('admin.set-forgot-password');

    Route::get('/reset-password', 'ResetPasswordController@resetPassword')
        ->name('reset-password');
    Route::post('/change-password', 'ResetPasswordController@setPassword')
        ->name('set-password');
});

// Admin Routes
Route::group([
    'namespace' => 'Admin',
    'prefix' => config('app.admin_dir'),
    'as' => 'admin.',
    'middleware' => 'auth:admin',
], function () {
    // Dashboard
    Route::get('/top', 'HomeController@index')
        ->name('home'); //a000top
    // Receipt
    Route::get('receipt/{id}', 'ReceiptController@receipt')
        ->name('receipt'); // receipt
    Route::get('invoice/{id}', 'ReceiptController@invoice')
        ->name('invoice'); // invoice
    Route::get('quote/{id}', 'ReceiptController@quote')
        ->name('quote'); // quote

    // List application
    Route::get('search/application-list', 'HomeController@search')
        ->name('search.application-list'); // a000list_anken

    // Support first time
    Route::get('hajime-support/{id}', 'SupportFirstTimeController@create')
        ->name('support-first-time.create')->where('id', '[0-9]+'); // a011
    Route::post('hajime-support/{id}', 'SupportFirstTimeController@store')
        ->name('support-first-time.store')->where('id', '[0-9]+');

    Route::get('hajime-support/supervisor/{id}', 'SupportFirstTimeController@index')
        ->name('support-first-time.index')->where('id', '[0-9]+'); // a011s
    Route::post('hajime-support/supervisor/{id}', 'SupportFirstTimeController@updateRoleSft')
        ->name('support-first-time.update-role-sft')->where('id', '[0-9]+');

    Route::get('hajime-support/supervisor/edit/{id}', 'SupportFirstTimeController@edit')
        ->name('support-first-time.edit')->where('id', '[0-9]+'); // a011shu
    Route::post('hajime-support/supervisor/edit/{id}', 'SupportFirstTimeController@editPost')
        ->name('support-first-time.edit-post')->where('id', '[0-9]+');

    //search master data product
    Route::post('search-recommend', 'SupportFirstTimeController@searchRecommend')
        ->name('sft.suggest-product');
    Route::post('search-recommend-item', 'SupportFirstTimeController@searchRecommendGetItem')
        ->name('sft.suggest-product-item');

    Route::post('/sft-suitable-product/update-is-block-ajax', 'SftSuitableProductController@updateIsBlockAjax')
        ->name('sft-suitable-product.update-is-block-ajax');
    Route::post('/m-product/update-type-ajax', 'MProductController@updateTypeAjax')
        ->name('m-product.update-type-ajax');
    Route::get('/m-product/get-code-and-distinction', 'MProductController@getCodeAndDistinction')
        ->name('m-product.get-code-and-distinction');

    // Agents
    Route::get('representative/setting', 'AgentController@index')
        ->name('agent.index'); // a000dairinin
    Route::post('representative/setting', 'AgentController@updateOrCreate')
        ->name('agent.update-create');
    Route::delete('representative/setting/{id}', 'AgentController@deleteAgent')
        ->name('agent.delete-agent');
    Route::get('representative/setting_group', 'AgentController@showSettingSet')
        ->name('agent.setting-set'); // a000dairinin_set
    Route::post('representative/setting_group', 'AgentController@crudSettingSet')
        ->name('agent.crud-setting-set');

    Route::group(['prefix' => 'ajax', 'as' => 'ajax.'], function () {
        Route::post('editor-uploads', 'AjaxController@editorUpload')
            ->name('editor-uploads');
        Route::post('quick-update', 'AjaxController@quickUpdate')
            ->name('quick-update');
        Route::post('generate-address', 'AjaxController@generateAddress')
            ->name('generate-address');
        Route::post('upload-files', 'AjaxController@uploadFile')
            ->name('upload-files');
        Route::post('remove-files', 'AjaxController@removeFile')
            ->name('remove-files');
    });

    //precheck
    Route::get('precheck/basic/{id}', 'PrecheckController@viewPrecheckSimple')
        ->name('precheck.view-precheck-simple'); // a021kan
    Route::get('precheck/basic/show-confirm/{id}', 'PrecheckController@viewPrecheckSimpleConfirm')
        ->name('precheck.show-simple-confirm'); // a021kan_confirm
    Route::post('precheck/basic/create-precheck-result/', 'PrecheckController@createPrecheckResult')
        ->name('precheck.create-precheck-result');
    Route::get('precheck/supervisor/{id}', 'PrecheckController@showConfirmPreCheck')
        ->name('precheck.view-precheck-confirm'); // a021s
    Route::post('precheck/precheck-confirm', 'PrecheckController@updateRolePrecheck')
        ->name('precheck.precheck-confirm');
    Route::get('precheck/distinct/{id}', 'PrecheckController@viewPrecheckSelectUnique')
        ->name('precheck.check-precheck-result'); // a021shiki
    Route::post('precheck/distinct/check-unique/create', 'PrecheckController@createPrecheckResultUnique')
        ->name('precheck_select.create-precheck-result-check-unique');
    Route::get('precheck/similar/{id}', 'PrecheckController@viewPrecheckSelectSimilar')
        ->name('precheck.check-similar'); // a021rui
    Route::post('precheck/distinct/check-similar/create', 'PrecheckController@createPrecheckResultSimilar')
        ->name('precheck_select.create-prrecheck-result-similar');
    Route::post('precheck/open-modal', 'PrecheckController@detailPrecheckModal')
        ->name('precheck.open-modal');
    Route::get('precheck/distinct/supervisor/{id}', 'PrecheckController@viewEditPrecheckUnique')
        ->name('precheck_select.show-edit-precheck-unique'); // a021shiki_shu
    Route::post('precheck/distinct/confirm-approve-unique/', 'PrecheckController@EditPrecheckUnique')
        ->name('precheck_select.edit-precheck-unique');
    Route::get('precheck/similar/supervisor/{id}', 'PrecheckController@viewEditPrecheckSimilar')
        ->name('precheck_select.view-edit-precheck-similar'); // a021rui_shu
    Route::post('precheck/distinct/confirm-approve-similar/', 'PrecheckController@EditPrecheckSimilar')
        ->name('precheck_select.edit-precheck-similar');

    // Question Answers
    Route::get('qa/a-to-customer/{user_id}', 'QuestionAnswersController@index') // ?qa_id={question_answers.id}
        ->name('question-answers.index')
        ->where('user_id', '[0-9]+'); // a000qa02
    Route::get('qa/search-a-to-customer/{user_id}', 'QuestionAnswersController@search') // ?qa_id={question_answers.id}
        ->name('question-answers.search')
        ->where('user_id', '[0-9]+');
    Route::post('question-answers/{id}', 'QuestionAnswersController@store')
        ->name('question-answers.store');
    Route::get('member-info/{user_id}', 'QuestionAnswersController@show')
        ->name('question-answers.show')
        ->where('user_id', '[0-9]+');  // a001kaiin
    Route::post('ajax-edit-name-detail', 'QuestionAnswersController@ajaxEditNameDetail')
        ->name('ajax-edit-name-detail');
    Route::get('qa/a-to-customer/supervisor/{user_id}', 'QuestionAnswersController@showQuestionAnswers02s') // ?qa_id={question_answers.id}
        ->name('question.answers.02s')
        ->where('user_id', '[0-9]+');  // a000qa02s
    Route::post('question-answers-update/{id}', 'QuestionAnswersController@updateQuestionAnswers')
        ->name('question-answers.update');
    Route::get('qa/q-to-customer/{user_id}', 'QuestionAnswersController@showQuestionAnswersFromAms')
        ->name('question.answers.from.ams')
        ->where('user_id', '[0-9]+');  // a000qa_from_ams
    Route::post('create-question/{id}', 'QuestionAnswersController@createQuestion')
        ->name('create.question');
    Route::get('qa/q-to-customer/supervisor/{id}', 'QuestionAnswersController@showQuestionAnswersFromAmsS')
        ->name('question.answers.from.ams.s')
        ->where('user_id', '[0-9]+');  // a000qa_from_ams_s
    Route::post('modify-question/{id}', 'QuestionAnswersController@modifyQuestion')
        ->name('modify.question');

    //{user_id}?qa_id={question_answers.id}
    Route::get('qa/a-from-customer/list/{user_id}', 'QuestionAnswersController@showKaitoList')
        ->name('question.answers.show.kaito.list')
        ->where('user_id', '[0-9]+');  // a000qa_kaito_list

    // Application Detail
    Route::get('application-detail/{id}', 'TrademarkController@index')
        ->name('application-detail.index'); // a000anken_top
    Route::post('application-detail/{id}/restore', 'TrademarkController@restore')
        ->name('application-detail.restore');
    Route::post('upload-pdf', 'TrademarkController@uploadPDF')
        ->name('application-detail.upload-pdf'); // a000anken_top BTN_TYPE_7
    Route::post('contact-customer', 'TrademarkController@contactCustomer')
        ->name('application-detail.contact-customer'); // a000anken_top BTN_TYPE_5

    // Notice Detail Btn Action
    Route::post('notice-detail-btns/create-html/{id}', 'NoticeDetailBtnController@createHtml')
        ->name('notice-detail-btns.create-html'); // a000anken_top BTN_TYPE_1
    Route::post('notice-detail-btns/upload-xml/{id}', 'NoticeDetailBtnController@uploadXML')
        ->name('notice-detail-btns.upload-xml'); // a000anken_top BTN_TYPE_2
    Route::post('notice-detail-btns/upload-pdf/{id}', 'NoticeDetailBtnController@uploadPDF')
        ->name('notice-detail-btns.upload-pdf'); // a000anken_top BTN_TYPE_7
    Route::post('notice-detail-btns/contact-customer/{id}', 'NoticeDetailBtnController@contactCustomer')
        ->name('notice-detail-btns.contact_customer'); // a000anken_top BTN_TYPE_5

    // A-031
    Route::get('/apply-trademark/document-to-check/{id}', 'TrademarkController@viewApplyTrademark')
        ->name('apply-trademark-document-to-check')
        ->where('id', '[0-9]+');
    Route::post('update-trademark/{id}', 'TrademarkController@updateTrademark')
        ->name('update.trademark');

    // Import
    Route::get('import-doc-xml', 'ImportController@viewImport01')
        ->name('import-doc-xml'); // a000import01
    Route::post('send-session', 'ImportController@sendSession')
        ->name('send-session');
    Route::get('import-doc-xml/show', 'ImportController@viewImport02')
        ->name('import-doc-xml-show'); // a000import02
    Route::post('save-doc-xml', 'ImportController@saveImportXML')
        ->name('save-xml-data'); // a000import02-save
    Route::get('save-doc-xml/completed', 'ImportController@showCompleted')
        ->name('save-xml-data-completed'); // a000import02-completed

    // Free History
    Route::get('free-history/{id}', 'FreeHistoryController@create')
        ->name('free-history.create'); // a000free
    Route::post('free-history/{id}', 'FreeHistoryController@store')
        ->name('free-history.store');
    Route::get('free-history/supervisor/{id}', 'FreeHistoryController@edit')
        ->name('free-history.edit'); // a000free_s
    Route::post('free-history/supervisor/{id}', 'FreeHistoryController@update')
        ->name('free-history.update');
    Route::get('free-history/re-confirm/{id}', 'FreeHistoryController@reConfirm')
        ->name('free-history.re-confirm'); // a000free02
    Route::post('free-history/re-confirm/{id}', 'FreeHistoryController@postReConfirm')
        ->name('free-history.post-re-confirm');

    Route::get('send-mail-remind/{email}', 'PaymentController@sendMailRemindPayment')
        ->name('send-mail-remind');

    Route::post('update-payment-bank-transfer', 'PaymentController@updatePaymentBankTransfer')
        ->name('update-payment-bank-transfer');
    Route::post('send-mail-remind/{email}', 'PaymentController@sendMailRemindPayment')
        ->name('send-mail-remind');

    // Payment
    Route::get('payment-check/bank-transfer', 'PaymentController@viewBankTransfer')
        ->name('payment-check.bank-transfer'); // payment
    //payment_all
    Route::get('payment-check/all', 'PaymentController@viewPaymentAll')
        ->name('payment-check.all'); // payment all

    Route::post('payment-check/update-payment-ajax', 'PaymentController@updatePaymentAjax')
        ->name('payment-check.update-payment-ajax');
    Route::get('payment-check/all/export-csv', 'PaymentController@exportCsvPaymentAll')
        ->name('payment-check.all.export-csv');
    Route::post('payment-check/all/search-condition', 'PaymentController@searchConditionPaymentAll')
        ->name('payment-check.all.search-condition');

    // A201 GROUP

    Route::get('refusal/request-review/{id}', 'MatchingResultController@refusalRequestReview')
        ->name('refusal-request-review')
        ->where('id', '[0-9]+'); // a201a
    Route::post('refusal/request-review/create', 'MatchingResultController@refusalRequestReviewCreate')
        ->name('refusal-request-review-create');

    // Refusal pre-question
    Route::group(['prefix' => 'refusal/pre-question', 'as' => 'refusal.pre-question.'], function () {
        Route::get('{id}', 'ComparisonTrademarkResultController@preQuestionIndex')
            ->name('index')
            ->where('id', '[0-9]+'); // a202
        Route::post('create', 'ComparisonTrademarkResultController@createPreQuestion')
            ->name('create');
        Route::post('ajax-delete-question', 'ComparisonTrademarkResultController@ajaxDeleteQuestion')
            ->name('ajax-delete-question');

        Route::get('supervisor/{id}', 'ComparisonTrademarkResultController@preQuestionSupervisor')
            ->name('supervisor')
            ->where('id', '[0-9]+'); // a202s
        Route::delete('supervisor/delete-question-detail/{id}', 'ComparisonTrademarkResultController@deleteQuestionDetail')
            ->name('delete-question-detail')
            ->where('id', '[0-9]+');
        Route::post('post-supervisor', 'ComparisonTrademarkResultController@postSupervisor')
            ->name('post-supervisor');
    });

    //a202n_s: id = comparison_trademark_result_id
    Route::get('refusal/pre-question-re/supervisor/{id}', 'ComparisonTrademarkResultController@preQuestionReShow')
        ->name('refusal.pre-question-re.supervisor.show')
        ->where('id', '[0-9]+'); // a202n_s
    Route::post('refusal/pre-question-re/supervisor/{id}', 'ComparisonTrademarkResultController@savePreQuestionReShow')
        ->name('refusal.pre-question-re.supervisor.save')
        ->where('id', '[0-9]+');

    Route::get('/refusal/eval-report/create-reason/{id}', 'ComparisonTrademarkResultController@createReason')
        ->name('refusal.eval-report.create-reason')
        ->where('id', '[0-9]+'); // a201b
    Route::post('/refusal/eval-report/create-reason', 'ComparisonTrademarkResultController@postCreateReason')
        ->name('refusal.eval-report.create-reason.post');
    Route::get('/refusal/eval-report/create-examine/{id}', 'ComparisonTrademarkResultController@createExamine')
        ->name('refusal.eval-report.create-examine')
        ->where('id', '[0-9]+'); // a201b02
    Route::post('/refusal/eval-report/create-examine/{id}', 'ComparisonTrademarkResultController@postCreateExamine')
        ->name('refusal.eval-report.create-examine.post')
        ->where('id', '[0-9]+');

    Route::get('refusal/eval-report/create-reason/supervisor/{id}', 'ComparisonTrademarkResultController@createReasonSupervisor')
        ->name('refusal.eval-report.create-reason.supervisor')
        ->where('id', '[0-9]+'); // a201b_s
    Route::post('refusal/eval-report/create-reason/supervisor', 'ComparisonTrademarkResultController@postCreateReasonSupervisor')
        ->name('refusal.eval-report.create-reason.supervisor.post');
    Route::get('/refusal/eval-report/create-examine/supervisor/{id}', 'ComparisonTrademarkResultController@createExamineSupervisor')
        ->name('refusal.eval-report.create-examine.supervisor')
        ->where('id', '[0-9]+'); // a201b02s
    Route::post('/refusal/eval-report/create-examine/supervisor/{id}', 'ComparisonTrademarkResultController@postCreateExamineSupervisor')
        ->name('refusal.eval-report.create-examine.supervisor.post')
        ->where('id', '[0-9]+');

    Route::get('refusal/eval-report/edit-reason/{id}', 'ComparisonTrademarkResultController@editReason')
        ->name('refusal.eval-report.edit-reason')
        ->where('id', '[0-9]+'); // a201b_n
    Route::post('refusal/eval-report/edit-reason', 'ComparisonTrademarkResultController@postEditReason')
        ->name('refusal.eval-report.edit-reason.post');

    Route::get('/refusal/eval-report/edit-examine/{id}', 'ComparisonTrademarkResultController@editExamine')
        ->name('refusal.eval-report.edit-examine')
        ->where('id', '[0-9]+'); // a201b02_n
    Route::post('/refusal/eval-report/edit-examine/{id}', 'ComparisonTrademarkResultController@postEditExamine')
        ->name('refusal.eval-report.edit-examine.post')
        ->where('id', '[0-9]+');

    Route::get('refusal/eval-report/edit-reason/supervisor/{id}', 'ComparisonTrademarkResultController@editReasonSupervisor')
        ->name('refusal.eval-report.edit-reason.supervisor')
        ->where('id', '[0-9]+'); // a201b_s_n
    Route::post('refusal/eval-report/edit-reason/supervisor', 'ComparisonTrademarkResultController@postEditReasonSupervisor')
        ->name('refusal.eval-report.edit-reason.supervisor.post');
    Route::get('/refusal/eval-report/edit-examine/supervisor/{id}', 'ComparisonTrademarkResultController@editExamineSupervisor')
        ->name('refusal.eval-report.edit-examine.supervisor')
        ->where('id', '[0-9]+'); // a201b02_s_n
    Route::post('/refusal/eval-report/edit-examine/supervisor/{id}', 'ComparisonTrademarkResultController@postEditExamineSupervisor')
        ->name('refusal.eval-report.edit-examine.supervisor.post')
        ->where('id', '[0-9]+');

    // END A201 GROUP

    // A203 GROUP

    Route::get('refusal/response-plan/{id}', 'PlanController@index')
        ->name('refusal.response-plan.index')->where('id', '[0-9]+'); // a203
    Route::post('refusal/response-plan', 'PlanController@store')
        ->name('refusal.response-plan.store');
    Route::post('refusal/response-plan/delete-plan-detail', 'PlanController@deletePlanDetail')
        ->name('refusal.response-plan.delete-plan-detail');
    Route::post('refusal/response-plan/delete-plan', 'PlanController@deletePlan')
        ->name('refusal.response-plan.delete-plan');
    Route::post('refusal/response-plan/create-plan-reason', 'PlanController@createPlanReason')
        ->name('refusal.response-plan.create-plan-reason');

    Route::get('refusal/response-plan/product/create/{id}', 'PlanController@productCreate')
        ->name('refusal.response-plan.product.create')
        ->where('id', '[0-9]+'); // a203c
    Route::post('refusal/response-plan/product/create/{id}', 'PlanController@postProductCreate')
        ->name('refusal.response-plan.product.create.post')
        ->where('id', '[0-9]+');

    //a203s: id - comparison_trademark_result_id?trademark_plan_id=
    Route::get('refusal/response-plan/supervisor/{id}', 'PlanController@getRefusalResponsePlaneSupervisor')
        ->name('refusal.response-plan.supervisor'); // a203s
    Route::post('refusal/response-plan/supervisor/{id}', 'PlanController@postRefusalResponsePlaneSupervisor')
        ->name('refusal.response-plan.supervisor.post'); // a203s - post

    Route::get('refusal/response-plan/edit/supervisor/{id}', 'PlanController@editSupervisor')
        ->name('refusal.response-plan.edit.supervisor'); // a203shu
    Route::post('refusal/response-plan/edit/supervisor/{id}', 'PlanController@postEditSupervisor')
        ->name('refusal.response-plan.edit.supervisor.post');

    Route::get('refusal/response-plan/product/edit/supervisor/{id}', 'PlanController@productEditSupervisor')
        ->name('refusal.response-plan.product.edit.supervisor'); // a203c_shu
    Route::post('refusal/response-plan/product/edit/supervisor/{id}', 'PlanController@postProductEditSupervisor')
        ->name('refusal.response-plan.product.edit.supervisor.post');

    Route::get('refusal/response-plan-re/supervisor/{id}', 'PlanController@getRefusalResponsePlanReSupervisor')
        ->name('refusal.response-plan-re.supervisor')
        ->where('id', '[0-9]+'); // a203n
    Route::post('refusal/response-plan-re/supervisor/{id}', 'PlanController@postRefusalResponsePlanReSupervisor')
        ->name('refusal.response-plan-re.post-supervisor');

    Route::get('refusal/response-plan/product/re-create/supervisor/{id}', 'PlanController@productReCreateSupervisor')
        ->name('refusal.response-plan.product.re-create.supervisor'); // a203c_n
    Route::post('refusal/response-plan/product/re-create/supervisor/{id}', 'PlanController@postProductReCreateSupervisor')
        ->name('refusal.response-plan.product.re-create.supervisor.post');

    //a203-sashi: id - comparison_trademark_result_id?trademark_plan_id=
    Route::get('refusal/response-plan/supervisor-reject/{id}', 'PlanController@getRefusalResponsePlaneSupervisorReject')
        ->name('refusal.response-plan.supervisor-reject'); // a203sashi
    Route::post('refusal/response-plan/supervisor-reject/{id}', 'PlanController@postRefusalResponsePlaneSupervisorReject')
        ->name('refusal.response-plan.supervisor-reject.post'); // a203sashi

    Route::get('refusal/response-plan/product-group/{id}', 'PlanController@showSimilarGroupCode') // id = trademark_plan_id
        ->name('refusal.response-plan.product-group'); // a203c_rui

    Route::get('refusal/response-plan/product-group-edit/{id}', 'PlanController@showSimilarGroupCodeEdit') // id = trademark_plan_id // GET
        ->name('refusal.response-plan.product-group-edit'); // a203c_rui_edit
    Route::post('refusal/response-plan/product-group-edit/{id}', 'PlanController@redirectSimilarGroupCodeEditConfirm') // id = trademark_plan_id // POST
        ->name('refusal.response-plan.product-group-edit-redirect'); // a203c_rui_edit (update)

    Route::get('refusal/response-plan/product-group-edit/confirm/{id}', 'PlanController@confirmSimilarGroupCodeEdit') // id = trademark_plan_id // GET
        ->name('refusal.response-plan.product-group-edit.confirm'); // a203c_rui_edit02
    Route::post('refusal/response-plan/product-group-edit/confirm/{id}', 'PlanController@updateSimilarGroupCodeEditConfirm') // id = trademark_plan_id // POST
        ->name('refusal.response-plan.product-group-edit.confirm-post'); // a203c_rui_edit02

    Route::get('refusal/response-plan/modal-a203check/{id}', 'PlanController@showModalA203check')
        ->name('refusal.response-plan.modal-a203check')
        ->where('id', '[0-9]+'); // a203check

    // END A203 GROUP

    // A205 GROUP
    Route::group(['prefix' => 'refusal/documents', 'as' => 'refusal.documents.'], function () {
        Route::get('/create/{id}', 'DocSubmissionController@showA205')
            ->name('create')
            ->where('id', '[0-9]+'); //a205
        Route::post('/create/{id}', 'DocSubmissionController@storeA205')
            ->name('store')
            ->where('id', '[0-9]+'); //a205-post

        //a205-kakunin: {comparison_trademark_result_id}?trademark_plan_id={trademark_plans.id}&doc_submission_id={doc_submissions.id}
        Route::get('/confirm/{id}', 'DocSubmissionController@showA205Kakunin')
            ->name('confirm')
            ->where('id', '[0-9]+'); //a205kakunin
        Route::post('/confirm/{id}', 'DocSubmissionController@postA205Kakunin')
            ->name('confirm')
            ->where('id', '[0-9]+'); //a205kakunin - post

        Route::get('/supervisor/{comparison_trademark_result_id}', 'DocSubmissionController@showA205s')
            ->name('supervisor')
            ->where('comparison_trademark_result_id', '[0-9]+'); //a205s

        Route::get('edit/supervisor/{id}', 'DocSubmissionController@showA205shu')
            ->name('edit.supervisor')
            ->where('id', '[0-9]+'); //a205shu
        Route::post('edit/supervisor/{id}', 'DocSubmissionController@saveA205shu')
            ->name('edit.supervisor.save')
            ->where('id', '[0-9]+'); //a205shu_ // POST

        Route::get('reject/supervisor/{comparison_trademark_result_id}', 'DocSubmissionController@showA205Sashi')
            ->name('reject.supervisor')
            ->where('comparison_trademark_result_id', '[0-9]+'); //a205sashi
        Route::post('/redirect-page/{comparison_trademark_result_id}', 'DocSubmissionController@redirectPage')
            ->name('redirect_page');

        Route::get('/increase/{id}', 'DocSubmissionController@showA205Hiki')
            ->name('increase')
            ->where('id', '[0-9]+'); //a205hiki

        //common a205hosei01window - id:trademark_plan_id?doc_submission_id=null
        Route::get('a205-hosei01-window/{id}', 'DocSubmissionController@getCommonA205Hosei01Window')
            ->name('a205-hosei01-window');

        //common a205iken02window - id: doc_submission_id
        Route::get('a205-iken02-window/{id}', 'DocSubmissionController@getCommonA205Iken02Window')
            ->name('a205-iken02-window');

        //common a205shu02window - params=trademark_plan_id?doc_submission_id
        Route::get('a205-shu02-window/{id}', 'DocSubmissionController@getCommonA205Shu02Window')
            ->name('a205-shu02-window');

        Route::post('a205-shu02-window/{id}', 'DocSubmissionController@postCommonA205Shu02Window')
            ->name('a205-shu02-window-post');
        Route::post('a205-shu02-window/doc-submission-attach-property/delete', 'DocSubmissionController@deletePropertyData')
            ->name('a205-shu02-window.doc-submission-attach-property.delete');
        Route::post('a205-shu02-window/doc-submission-attachment/delete', 'DocSubmissionController@deleteSubmissionAttachment')
            ->name('a205-shu02-window.doc-submission-attachment.delete')
            ->where('id', '[0-9]+');
    });

    // END A205 GROUP

    //A204 GROUP
    Route::get('refusal/materials/supervisor/{id}', 'MaterialController@supervisor')
        ->name('refusal.materials.supervisor')
        ->where('id', '[0-9]+'); // a204han
    Route::post('refusal/material/supervisor/{id}', 'MaterialController@postSupervisor')
        ->name('refusal.materials.supervisor.post')
        ->where('id', '[0-9]+');

    Route::get('refusal/materials-re/check/supervisor/{id}', 'MaterialController@checkSupervisor')
        ->name('refusal.materials-re.check.supervisor')
        ->where('id', '[0-9]+'); // a204han_n
    Route::post('refusal/materials-re/check/supervisor/{id}', 'MaterialController@postCheckSupervisor')
        ->name('refusal.materials-re.check.supervisor.post')
        ->where('id', '[0-9]+');

    Route::get('refusal/materials-re/supervisor/{id}', 'MaterialController@reSupervisor')
        ->name('refusal.materials-re.supervisor')
        ->where('id', '[0-9]+'); // a204n
    Route::post('refusal/materials-re/supervisor/{id}', 'MaterialController@postReSupervisor')
        ->name('refusal.materials-re.supervisor.post')
        ->where('id', '[0-9]+');

    Route::get('refusal/no-materials/{id}', 'MaterialController@noMaterial')
        ->name('refusal.material.no-material')
        ->where('id', '[0-9]+'); // a204_no_mat
    Route::post('refusal/no-materials/{id}', 'MaterialController@postNoMaterial')
        ->name('refusal.material.no-material.post')
        ->where('id', '[0-9]+');

    Route::get('refusal/materials/confirm/{id}', 'MaterialController@confirm')
        ->name('refusal.material.confirm')
        ->where('id', '[0-9]+'); // a203_204kakunin
    //END A204 GROUP
    // Group A210
    Route::group(['prefix' => 'refusal/create-request', 'as' => 'refusal.create-request.'], function () {
        // change controller before start
        Route::get('alert/{id}', 'TrademarkController@showA210alert')
            ->name('alert')
            ->where('id', '[0-9]+'); // A210alert
        Route::post('update-data-alert/{id}', 'TrademarkController@updateDataAlert')
            ->name('update_data_alert');
        Route::get('over/{id}', 'TrademarkController@showA210Over')
            ->name('over')
            ->where('id', '[0-9]+'); // A210over
    });
    // A206 GROUP

    // {trademarks.id}?maching_result_id=
    Route::get('refusal/final-refusal/{id}', 'TrademarkController@finalRefusal')
        ->name('refusal.final-refusal.index')
        ->where('id', '[0-9]+'); // a206kyo_s
    Route::post('refusal/post-final-refusal/{id}', 'TrademarkController@postFinalRefusal')
        ->name('refusal.final-refusal.post')
        ->where('id', '[0-9]+');

    //END A206 GROUP

    // A301
    // {trademarks.id}?maching_result_id=
    Route::get('registration/notify/{id}', 'TrademarkController@registrationNotify')
        ->name('registration.notify')
        ->where('id', '[0-9]+'); // a301
    Route::post('registration/notify-post/{id}', 'TrademarkController@postRegistrationNotify')
        ->name('registration.notify.post')
        ->where('id', '[0-9]+');

    Route::group(['prefix' => 'registration/change-address', 'as' => 'registration.change-address.'], function () {
        // A-700shutsugannin01
        Route::get('{id}', 'ChangeAddressController@showRegistration')
            ->where('id', '[0-9]+')
            ->name('index');
        Route::post('send-session', 'ChangeAddressController@sendSessionConfirm')
            ->name('send-session');
        // A-700shutsugannin02
        Route::get('confirm/{id}', 'ChangeAddressController@showConfirmRegistration')
            ->name('confirm');
        Route::post('update-info', 'ChangeAddressController@updateInfo')
            ->name('update-info');
        // A-700shutsugannin03
        // /sysmanagement/registration/change-address/confirm/{trademark_id}?change_info_register_id=&trademark_info_id=
        Route::get('document/{id}', 'ChangeAddressController@showDocumentRegistration')
            ->name('document');
        Route::post('save-document', 'ChangeAddressController@saveDataDocument')
            ->name('save.document');
    });

    Route::get('registration/skip/{id}', 'ChangeAddressController@showSkipRegistration')
        ->name('registration.skip');  // A-700shutsugannin01skip

    Route::group(['prefix' => 'update/change_address', 'as' => 'update.change_address.'], function () {
        // update/change_address/trademark_id={trademarks.id}&trademark_info_id={trademark_infos.id}
        // a700kenrisha01
        Route::get('{id}', 'ChangeAddressController@updateChangeAddress')
            ->name('index')
            ->where('id', '[0-9]+');
        Route::post('post/{id}', 'ChangeAddressController@postUpdateChangeAddress')
            ->name('post')
            ->where('id', '[0-9]+');

        // update/change_address/confirm/trademark_id={trademarks.id}&trademark_info_id={trademark_infos.id}
        // a700kenrisha02
        Route::get('confirm/{id}', 'ChangeAddressController@updateChangeAddressConfirm')
            ->name('confirm')
            ->where('id', '[0-9]+');

        // update/change_address/document/trademark_id={trademarks.id}&trademark_info_id={trademark_infos.id}
        // a700kenrisha03
        Route::get('document/{id}', 'ChangeAddressController@updateChangeAddressDocument')
            ->name('document')
            ->where('id', '[0-9]+');
        Route::post('document-post/{id}', 'ChangeAddressController@postUpdateChangeAddressDocument')
            ->name('document.post')
            ->where('id', '[0-9]+');

        // update/change_address/skip/register_trademark_id={register_trademarks}
        // a700kenrisha01skip
        Route::get('skip/{id}', 'ChangeAddressController@updateChangeAddressSkip') // id = register_trademark_id
            ->name('skip')
            ->where('id', '[0-9]+');
    });

    // A-302hosei01
    // /sysmanagement/registration/document/modification/product/maching_result_id={maching_results.id}?register_trademark_id={register_trademarks.id}
    Route::get('registration/document/modification/{id}', 'MatchingResultController@showDocumentModification')
        ->name('registration.document.modification');
    Route::get('list-product', 'MatchingResultController@showIframeListProduct')
        ->name('list_product');
    Route::post('redirect-page/{id}', 'MatchingResultController@redirectPageHosei')
        ->name('product.redirect_page');
    // A-302hosei02
    Route::get('registration/document/modification/product/{id}', 'MatchingResultController@showDocumentModificationProduct')
        ->name('registration.document.modification.product');
    Route::post('registration/document/modification/product/{id}', 'MatchingResultController@postShowDocumentModificationProduct')
        ->name('registration.document.modification.product.post');
    // a302hosei02skip
    // registration/document/modification/skip/maching_result_id={maching_results.id}?register_trademark_id={register_trademarks.id}
    Route::get('registration/document/modification/skip/{id}', 'MatchingResultController@registrationDocumentModificationSkip')
        ->name('registration.document.modification.skip')
        ->where('id', '[0-9]+'); // a302hosei02skip
    Route::post('registration/document/modification/skip/{id}', 'MatchingResultController@postRegistrationDocumentModificationSkip')
        ->name('registration.document.modification.skip.post')
        ->where('id', '[0-9]+');

    // A302
    // registration/document/maching_result_id={maching_results.id}?register_trademark_id={register_trademarks.id}
    Route::get('registration/document/{id}', 'MatchingResultController@registrationDocument') // id = maching_result_id
        ->name('registration.document')
        ->where('id', '[0-9]+');
    Route::post('registration/document-post/{id}', 'MatchingResultController@postRegistrationDocument')
        ->name('registration.document.post')
        ->where('id', '[0-9]+');

    // A303
    Route::get('registration/input-number/{id}', 'MatchingResultController@showRegistrationInput')
        ->name('registration.input_number');
    Route::post('save-data-registration-input', 'MatchingResultController@saveDataRegistrationInput')
        ->name('save_data_registration_input');

    Route::group(['prefix' => 'registration/procedure-latter-period', 'as' => 'registration.procedure-latter-period.'], function () {
        Route::get('/document/submit/{id}', 'RegisterTrademarkController@getRegisProcedureLatterPeriodDocumentSubmit')
            ->name('document.submit')
            ->where('id', '[0-9]+'); //a402for_submit

        Route::get('/document/{id}', 'RegisterTrademarkController@getRegisProcedureLatterPeriodDocument')
            ->name('document')
            ->where('id', '[0-9]+'); //a302_402_5yr_kouki
        Route::post('/document/{id}', 'RegisterTrademarkController@postRegisProcedureLatterPeriodDocument')
            ->name('document-post')
            ->where('id', '[0-9]+');
    });

    Route::group(['prefix' => '/update/document/modification/product', 'as' => 'update.document.modification.product.'], function () {
        Route::get('/{id}', 'RegisterTrademarkController@updateDocumentModifyProd')
            ->name('detail')
            ->where('id', '[0-9]+'); //a402hosoku01

        Route::get('/window/{id}', 'RegisterTrademarkController@updateDocumentModifyProdWindow')
            ->name('window')
            ->where('id', '[0-9]+'); //a402hosoku01window

        Route::get('/document/{id}', 'RegisterTrademarkController@getDocumentModifyProd')
            ->name('document')
            ->where('id', '[0-9]+'); //a402hosoku02

        Route::post('/document/{id}', 'RegisterTrademarkController@postDocumentModifyProd')
            ->name('document.post'); //a402hosoku02 post

        Route::get('/skip/{id}', 'RegisterTrademarkController@skipDocumentModifyProd')
            ->name('skip')
            ->where('id', '[0-9]+'); //a402hosoku02skip
    });

    Route::get('/update/procedure/document/{id}', 'RegisterTrademarkController@updateProcedureDocument')
         ->name('update.procedure.document')
         ->where('id', '[0-9]+'); //a402
    Route::post('/update/procedure/document/{id}', 'RegisterTrademarkController@updateProcedureDocumentPost')
        ->name('update.procedure.document.post')
        ->where('id', '[0-9]+');

    Route::get('/goods-master-search', 'MProductController@getGoodMasterSearch')
        ->name('goods-master-search'); //a000goods_master_search

    Route::post('/goods-master-search', 'MProductController@postGoodMasterSearch')
        ->name('goods-master-search-post');

    Route::get('/goods-master-result', 'MProductController@getGoodMasterResult')
        ->name('goods-master-result'); //a000goods_master_result

    Route::get('/goods-master-detail', 'MProductController@getGoodMasterDetail')
        ->name('goods-master-detail'); //a000goods_master_detail
    Route::post('/goods-master-detail', 'MProductController@postGoodMasterDetail')
        ->name('goods-master-detail-post');

    Route::post('/check-product-number-ajax', 'MProductController@checkProductNumberAjax')
        ->name('check-product-number.ajax');
    Route::get('/notify', 'NotifyController@index')
        ->name('notify.index'); // a000news_edit
    Route::post('/notify', 'NotifyController@sendComment')
        ->name('notify.sendComment');
    Route::get('/notify/confirm', 'NotifyController@confirm')
        ->name('notify.confirm'); // a000news_edit
    Route::post('/notify/confirm', 'NotifyController@postConfirm')
        ->name('notify.postConfirm');
});
