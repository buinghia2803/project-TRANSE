<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| User Routes
|--------------------------------------------------------------------------
*/

// Route for Auth. EG: login, forgot password,...
Route::group(['namespace' => 'User\Auth', 'as' => 'auth.'], function () {
    // Login and Logout
    Route::get('login', 'LoginController@showLoginForm')
        ->name('login'); // u000login
    Route::post('login', 'LoginController@login')
        ->name('post-login');
    Route::post('logout', 'LoginController@logout')
        ->name('logout');

    // Sign Up
    Route::get('member-register-pre', 'RegistrationController@showSignUpForm')
        ->name('signup'); // u001temp01
    Route::post('member-register-pre', 'RegistrationController@register')
        ->name('register');
    Route::get('member-register-pre/confirm', 'RegistrationController@success')
        ->name('signup-success'); // u001temp02
    Route::get('member-register-pre/verification', 'RegistrationController@showVerifyCodeForm')
        ->name('verify-code'); // u001temp03
    Route::post('member-register-pre/verification', 'RegistrationController@verifyCode')
        ->name('post-verify-code');

    Route::get('member-register', 'RegistrationController@getFormUpdateProfile')
        ->name('form-update-profile'); // u001
    Route::post('member-register', 'RegistrationController@updateProfile')
        ->name('form-update-profile-post');
    Route::post('check-member-id', 'RegistrationController@checkMemberId')
        ->name('check-member-id');
    Route::get('member-register/confirm', 'RegistrationController@viewConfirm')
        ->name('view-confirm'); // u001_confirm
    Route::post('member-register/confirm', 'RegistrationController@updateProfileConfirm')
        ->name('update-profile-confirm');
    Route::get('member-register/finish', 'RegistrationController@registerFinish')
        ->name('register-finish'); // u001finish

//    Route::get('/registration/notify-number/{trademark_id}', function () {
//        return true;
//    })->name('registration-notify-number');

    // Forgot Password
    Route::get('forgot-password', 'ForgotPasswordController@index')
        ->name('forgot-password.index'); // u000pass01 index
    Route::post('forgot-password', 'ForgotPasswordController@sendMailForgotPassword')
        ->name('forgot-password.send-mail');
    Route::get('forgot-password/confirm', 'ForgotPasswordController@showKakunin')
        ->name('forgot-password.confirm'); // u000pass01_kakunin
    Route::get('forgot-password/reset/confirm', 'ForgotPasswordController@showPass02Kakunin')
        ->name('forgot-password.reset.confirm'); // u000pass02_kakunin
    Route::get('forgot-password/reset/{token}', 'ForgotPasswordController@showPass02')
        ->name('forgot-password.reset'); // u000pass02
    Route::post('forgot-password/reset', 'ForgotPasswordController@store')
        ->name('forgot-password.reset.post');
    Route::get('forgot-password-no-email', 'ForgotPasswordController@showPass01NoEmail')
        ->name('forgot-password.no-email'); // u000pass01no_email
    Route::post('forgot-password-no-email', 'ForgotPasswordController@checkUserInActive')
        ->name('forgot-password.no-email.post');
    Route::get('forgot-password-no-email/secret-answer', 'ForgotPasswordController@showPass02NoEmail')
        ->name('forgot-password.no-email.secret-answer'); // u000pass02no_email
    Route::post('forgot-password-no-email/secret-answer', 'ForgotPasswordController@checkUserInfoAnswer')
        ->name('forgot-password.no-email.secret-answer.post');
    Route::get('forgot-password-no-email/other-email', 'ForgotPasswordController@showPass03NoEmail')
        ->name('forgot-password.no-email.other-email'); // u000pass03no_email
    Route::post('forgot-password-no-email/other-email', 'ForgotPasswordController@setNewEmail')
        ->name('forgot-password.no-email.other-email.post');
    Route::get('forgot-password-no-email/other-email/confirm', 'ForgotPasswordController@showPass03NoEmailKakunin')
        ->name('forgot-password.no-email.other-email.confirm'); // u000pass03no_email_kakunin
    Route::get('forgot-password-no-email/verification/{token}', 'ForgotPasswordController@showPass04NoEmail')
        ->name('forgot-password.no-email.verification'); // u000pass04no_email
    Route::post('forgot-password-no-email/verification/{token}', 'ForgotPasswordController@verifyUserNoEmail')
        ->name('forgot-password.no-email.verification.post');
    Route::get('forgot-password-no-email/reset/{token}', 'ForgotPasswordController@showPass05NoEmail')
        ->name('forgot-password.no-email.reset'); // u000pass05no_email
    Route::post('forgot-password-no-email/reset/{token}', 'ForgotPasswordController@setNewPassNoEmail')
        ->name('forgot-password.no-email.reset.post');

    // Recover id
    Route::get('recover-id', 'RecoverIdController@showRecoverId')
        ->name('show-recover-id'); // u000id01
    Route::post('recover-id', 'RecoverIdController@recoverId')
        ->name('recover-id');
    Route::get('recover-id-no-email', 'RecoverIdController@showRecoverIdNoEmail')
        ->name('show-recover-id-no-email'); // u000id01no_email
    Route::post('recover-id-no-email', 'RecoverIdController@recoverIdNoEmail')
        ->name('recover-id-no-email');
    Route::match(['GET', 'POST'], 'secret-answer', 'RecoverIdController@recoverIdByAnswer')
        ->name('recover-id-secret-answer'); // u000id02no_email, u000id03no_email
});

// Route for user not login'
Route::group(['namespace' => 'User', 'as' => 'user.'], function () {
    Route::get('/comming-soon', 'ComingSoonController@index')->name('comming-soon');
});

// Route for user login
Route::group(['namespace' => 'User', 'as' => 'user.', 'middleware' => ['web', 'isUser']], function () {

    Route::get('/', function () {
        return view('welcome');
    })->name('index');

    // Receipt
    Route::get('receipt/{id}', 'ReceiptController@receipt')
        ->name('receipt'); // receipt
    Route::get('invoice/{id}', 'ReceiptController@invoice')
        ->name('invoice'); // invoice
    Route::get('quote/{id}', 'ReceiptController@quote')
        ->name('quote'); // quote

    // Search AI Group
    Route::get('search-ai', 'SearchAIController@searchAI')
        ->name('search-ai'); // u020a
    Route::post('search-ai', 'SearchAIController@postSearchAI')
        ->name('search-ai.post');
    Route::get('search-ai/result', 'SearchAIController@suggestAI')
        ->name('search-ai.result'); // u020b
    Route::post('search-ai/result', 'SearchAIController@postSuggestAI')
        ->name('search-ai.result.post');
    Route::post('search-ai/result/ajax', 'SearchAIController@ajaxSuggestAI')
        ->name('ajax-suggest-ai');
    Route::get('search-ai/quote', 'SearchAIController@getViewSearchAiReport')
        ->name('search-ai.quote'); // u020c
    Route::post('search-ai/report', 'SearchAIController@showSearchAiReport')
        ->name('search-ai.quote.post');
    Route::post('search-ai/report/send-data', 'SearchAIController@postSearchAiReport')
        ->name('search-ai.quote.send-data');
    Route::get('search-ai/result/{folder_id}', 'SearchAIController@gotoSearchAiResult')
        ->name('search-ai.goto-result');

    // Redirect from search AI
    Route::get('apply-trademark-after-search/{id?}', 'AppTrademarkController@getApplyTrademarkAfterSearch')
        ->name('register-apply-trademark-after-search'); // u031b
    Route::post('apply-trademark-after-search/{id?}', 'AppTrademarkController@postApplyTrademarkAfterSearch')
        ->name('register-apply-trademark-after-search-post'); // u031b - post
    Route::post('get-info-payment-u031b-ajax', 'AppTrademarkController@getInfoPaymentU031b')
        ->name('get-info-payment-u031b-ajax');
    //redirect from u031b to u020b
    Route::post('apply-trademark-after-search-to-search-ai', 'AppTrademarkController@redirectToSearchAiFromU031b')
        ->name('apply-trademark-after-search.redirect-to-search-ai');
    //redirect from u031b to u021
    Route::post('u031b-redirect-to-regis-precheck', 'AppTrademarkController@redirectToU021FromU031b')
        ->name('apply-trademark-after-search.redirect-to-regis-precheck');

    Route::get('/apply-trademark-without-number', 'AppTrademarkController@applyTrademarkWithoutNumber')
        ->name('apply-trademark-without-number')
        ->where('id', '[0-9]+'); // u031d
    Route::post('apply-trademark-without-number/{id?}', 'AppTrademarkController@postApplyTrademarkWithoutNumber')
        ->name('apply-trademark-without-number-post'); // u031dPost
    //redirect from u031d to u020b
    Route::post('apply-trademark-without-number-to-search-ai', 'AppTrademarkController@redirectToSearchAiFromU031d')
        ->name('apply-trademark-with-number.redirect-to-search-ai');
    //redirect from u031d to u021
    Route::post('u031d-redirect-to-regis-precheck', 'AppTrademarkController@redirectToU021FromU031d')
        ->name('apply-trademark-with-number.redirect-to-regis-precheck');

    Route::get('/apply-trademark-with-product-copied', 'AppTrademarkController@applyTrademarkWithProductCopied')
        ->name('apply-trademark-with-product-copied')
        ->where('id', '[0-9]+'); // u031c
    Route::post('apply-trademark-with-product-copied/{id?}', 'AppTrademarkController@postApplyTrademarkWithProductCopied')
        ->name('apply-trademark-with-product-copied-post'); // u031cPost
    //redirect from u031c to u020b
    Route::post('apply-trademark-with-product-copied-to-search-ai', 'AppTrademarkController@redirectToSearchAiFromU031c')
        ->name('apply-trademark-with-product-copied.redirect-to-search-ai');
    //redirect from u031c to u021
    Route::post('u031c-redirect-to-regis-precheck', 'AppTrademarkController@redirectToU021FromU031c')
        ->name('apply-trademark-with-product-copied.redirect-to-regis-precheck');

    // Profile
    Route::prefix('profile')->as('profile.')->group(function () {
        Route::get('edit', 'UserController@getProfileEdit')
            ->name('edit'); // u001edit
        Route::post('edit', 'UserController@sendInfoProfile')
            ->name('edit');
        Route::get('edit/confirm', 'UserController@confirmUpdateProfile')
            ->name('edit.confirm'); // none
        Route::post('edit/confirm', 'UserController@updateProfile')
            ->name('edit.confirm.post');
        Route::post('check-exists-member-id', 'UserController@checkExistsMemberId')
            ->name('check-exists-member-id');
    });

    // Edit email
    Route::group(['prefix' => 'profile/change-email', 'as' => 'profile.change-email.'], function () {
        Route::get('/', 'UserController@editEmail')
            ->name('index'); // u001edit_mail01
        Route::post('/', 'UserController@editEmailSendInfo')
            ->name('index');
        Route::get('confirm', 'UserController@editEmailSendInfoSuccess')
            ->name('confirm'); // u001edit_mail02
        Route::get('verification', 'UserController@editEmailConfirm')
            ->name('verification'); // u001edit_mail03
        Route::post('verification', 'UserController@editEmailVerifyCode')
            ->name('verification.post');
        Route::get('finish', 'UserController@editEmailVerifyCodeFinish')
            ->name('finish.get'); // u001edit_mail04
    });

    Route::get('top', 'DashboardController@showTop')
        ->name('top'); // u000top
    Route::post('update-notice-detail/{id}', 'DashboardController@updateNotice')
        ->name('update-notice-detail');
    Route::get('all-my-folder', 'DashboardController@showAllMyFolderAjax')
        ->name('top.all-my-folder');
    Route::get('all-to-do', 'DashboardController@showAllToDoAjax')
        ->name('top.all-to-do');
    Route::get('all-not-apply', 'DashboardController@showALlAppTrademarkNotApply')
        ->name('top.all-not-apply');
    Route::get('all-apply', 'DashboardController@showAllApplyTrademarkApply')
        ->name('top.all-apply');
    Route::get('all-notice', 'DashboardController@showAllNotice')
        ->name('top.all-notice');
    Route::get('all-prod-name-trademark/{id}', 'DashboardController@showAllProdNameTrademark')
        ->name('top.all-product-name-trademark');
    Route::get('close-prod-name-trademark/{id}', 'DashboardController@closeProdNameTrademark')
        ->name('top.close-product-name-trademark');
    Route::get('all-prod-name-app-trademark/{id}', 'DashboardController@showAllProdNameAppTrademark')
        ->name('top.all-product-name-app-trademark');
    Route::get('close-prod-name-app-trademark/{id}', 'DashboardController@closeProdNameAppTrademark')
        ->name('top.close-product-name-app-trademark');
    Route::delete('delete-anken/{id}', 'DashboardController@deleteAnken')
        ->name('top.delete-anken');
    Route::delete('delete-my-folder/{id}', 'DashboardController@deleteMyFolder')
        ->name('top.delete-anken');
    Route::get('get-route-redirect', 'DashboardController@getRedirectRouteWithType')
        ->name('top.route.redirect.type');

    Route::get('menu-new-apply', 'NewApplyController@index')
        ->name('menu-new-apply'); // u000new_apply
    Route::post('redirect/menu-new-apply', 'NewApplyController@store')
        ->name('redirect.menu-new-apply');

    Route::get('application-list', 'TrademarkController@list')
        ->name('application-list'); // u000list

    // u000list_change_address
    Route::get('/application-list/change-address', 'TradeMarkInfoController@index')
        ->name('application-list.change-address'); // u000list_change_address
    Route::get('/application-list/change-address/applied/{id}', 'TradeMarkInfoController@chageAddress02')
        ->name('application-list.change-address.applicant'); // u000list_change_address02
    Route::get('get-trademark-info-ajax', 'TradeMarkInfoController@getTradeMarkInfoAjax')
        ->name('get-trademark-info-ajax');
    Route::post('app-list/create-payment/{id}', 'TradeMarkInfoController@store')
        ->name('app-list.create-payment');
    Route::get('/application-list/change-address/registered/{id}', 'TradeMarkInfoController@getChangeAddressKenrisha')
        ->name('application-list.change-address.registered'); // u000list_change_address02_kenrisha

    Route::get('get-info-user-ajax', 'UserController@getInfoUserAjax')->name('get-info-user-ajax');

    // Common payment
    Route::get('payment-info', 'PaymentController@index')
        ->name('payment.index'); // u000common_payment
    Route::get('payment-info/card-information', 'PaymentController@indexGMO')
        ->name('payment.GMO.index'); // u000GMO
    Route::get('payment-info/thank-you', 'PaymentController@showThankYou')
        ->name('payment.GMO.thank-you'); // u000thanktou
    Route::post('payment-info', 'PaymentController@payment')
        ->name('payment.payment.store');
    Route::post('/payment-info/card-information', 'PaymentController@storeGMO')
        ->name('payment.GMO.store'); // u000GMO

    Route::get('confirm-precheck-payment', 'PaymentController@confirmPaymentPrecheck')
        ->name('payment.confirm.precheck');
    // Apply Trademark
    Route::get('/apply-trademark/register/{id?}', 'PrecheckController@applyTrademarkRegister')
        ->name('apply-trademark-register'); // u031
    Route::post('/apply-trademark/register-create', 'PrecheckController@applyTrademarkRegisterCreate')
        ->name('apply-trademark-register-create');

    Route::get('/apply-trademark-free-input/{id?}', 'AppTrademarkController@applyTrademarkFreeInput')
        ->name('apply-trademark-free-input'); // u031edit
    Route::post('/apply-trademark-free-input-create', 'AppTrademarkController@applyTrademarkFreeInputCreate')
        ->name('apply-trademark-free-input-create');
    Route::post('redirect-to-u031edit/{id?}', 'AppTrademarkController@redirectU031Edit')
        ->name('redirect-to-u031edit');
    Route::post('redirect-to-u031edit-with-number', 'AppTrademarkController@redirectU031EditWithNumber')
        ->name('redirect-to-u031edit-with-number');

    // Precheck
    Route::prefix('precheck')->as('precheck.')->group(function () {
        // Precheck first time
        Route::get('/{id}', 'PrecheckController@registerPrecheck')
            ->name('register-precheck')
            ->where('id', '[0-9]+'); // u021
        Route::post('/{id}', 'PrecheckController@postRegisterPrecheckTimeN')
            ->name('register-precheck-post')
            ->where('id', '[0-9]+');
        Route::post('ajax-get-info-payment', 'PrecheckController@ajaxGetInfoPayment')
            ->name('ajax-get-info-payment');

        // Precheck-n: params {trademark_id}?precheck_id={prechecks.id}
        Route::get('re-register/{id}', 'PrecheckController@getRegisterTimeN')
            ->name('register-time-n')
            ->where('id', '[0-9]+'); // u021n
        Route::post('re-register/{id}', 'PrecheckController@postRegisterPrecheckTimeN')
            ->name('register-time-n')
            ->where('id', '[0-9]+');

        // Register different brand
        Route::get('apply-trademark-different/{id}', 'PrecheckController@registerDifferentBrand')
            ->name('register-different-brand')
            ->where('id', '[0-9]+'); // u021c
        Route::post('apply-trademark-different/{id}', 'PrecheckController@postRegisterDifferentBrand')
            ->name('post-register-different-brand')
            ->where('id', '[0-9]+');
        // Route redirect from u021c to u020b
        Route::get('/redirect-to-search-ai', 'PrecheckController@redirectToU020b')
            ->name('redirect-to-search-ai');
        // Route redirect from u021c to u011
        Route::get('/redirect-to-u011/{id}', 'PrecheckController@redirectToU011')
            ->name('redirect-to-u011');

        // Precheck report precheck from AMS
        Route::get('report-to-customer/{id}', 'PrecheckController@applicationTrademark') // ?precheck_id={prechecks.id}
            ->name('application-trademark'); // u021b
        Route::post('report-to-customer', 'PrecheckController@postApplicationTrademark')
            ->name('post-application-trademark');
        Route::post('redirect-u021c', 'PrecheckController@redirectU021c')
            ->name('redirect-u021c');
        Route::get('/apply-trademark/{id}', 'PrecheckController@applicationTrademarkV2')
            ->name('application-trademark-v2'); // u021b_31
        Route::post('redirect-u020b', 'PrecheckController@redirectU020b')
            ->name('redirect-u020b'); // Redirect u020b ajax
        Route::post('redirect-u021', 'PrecheckController@redirectU021')
            ->name('redirect-u021'); // Redirect u021 ajax

        // Redirect u031_edit_with_number
        Route::post('redirect-u031_edit_with_number', 'PrecheckController@redirectU031EditWithNumber')
            ->name('redirect-u031_edit_with_number');
        Route::get('/apply-trademark-with-number/{id}', 'PrecheckController@applyTrademarkWithNumber')
            ->name('apply-trademark-with-number'); // u031edit_with_number
        Route::post('/apply-trademark-with-number-create', 'PrecheckController@applyTrademarkWithNumberCreate')
            ->name('apply-trademark-with-number-create'); // create u031edit_with_number

        //search master data product
        Route::post('search-recommend', 'PrecheckController@searchRecommend')
            ->name('search-recommend');
        Route::post('search-recommend-item', 'PrecheckController@searchRecommendGetItem')
            ->name('search-recommend-item');
    });

    // Support first time.
    Route::get('hajime-support', 'SupportFirstTimeController@index')
        ->name('sft.index'); // u011
    Route::post('hajime-support/support-first-time', 'SupportFirstTimeController@store')
        ->name('sft.store');
    Route::get('hajime-support/apply-trademark/{id}', 'SupportFirstTimeController@indexSFTProposalAMS')
        ->name('sft.proposal-ams'); // u011b_31
    Route::post('hajime-support/apply-trademark', 'SupportFirstTimeController@createPaymentSft')
        ->name('sft.proposal-ams.store');
    Route::get('hajime-support/suggest-to-customer/{id}', 'SupportFirstTimeController@showSupportFirstTimeU011b')
        ->name('support.first.time.u011b'); // u011b
    Route::post('hajime-support/create-payment', 'SupportFirstTimeController@createPaymentSft')
        ->name('sft.create.payment');
    Route::post('hajime-support/create-support-first-time', 'SupportFirstTimeController@createSupportFirstTime')
        ->name('sft.create');
    Route::post('hajime-support/create-support-first-time/create-session', 'SupportFirstTimeController@createSession')
        ->name('sft.create.session');
    // Application Detail
    // Route::resource('app-detail', ApplicationDetailController::class);

    Route::get('/application-detail/{id}', 'ApplicationDetailController@index')
        ->name('application-detail.index'); // u000anken_top
    Route::get('/u031pass', 'ApplicationDetailController@index')->name('u031pass'); //change before start
    // Question Answer
    Route::get('a-faq', 'QAController@showQA01')
        ->name('qa.01.faq');
    Route::get('qa/q-from-customer', 'QAController@showQA02')
        ->name('qa.02.qa'); // u000qa02
    Route::post('qa-create', 'QAController@createQA')
        ->name('qa.02.qa.create');
    Route::get('/qa/a-from-customer/list/{id}', 'QAController@showQA02Kaito')
        ->name('qa.02.kaito')
        ->where('id', '[0-9]+');
    Route::post('qa-create-answers', 'QAController@createAnswerToUser')
        ->name('qa.02.qa.kaito.create.answer');
    Route::get('/qa/list/{id?}', 'QAController@showQA03Kaito')
        ->name('qa.03.kaito.list')
        ->where('id', '[0-9]+');

    //Apply trademark
    Route::group(['prefix' => 'apply-trademark', 'as' => 'apply-trademark.'], function () {
        Route::get('/confirm/{id}', 'AppTrademarkController@confirm')
            ->name('confirm'); //u032
        Route::post('/confirm/update/{id}', 'AppTrademarkController@updateApptrademarkConfirm')
            ->name('update.confirm');
        Route::get('/confirm-completed', 'AppTrademarkController@confirmCompleted')
            ->name('confirm.completed');

        // u032_cancel - {id}: app_trademark_id
        Route::get('/register/cancel/{id}', 'AppTrademarkController@viewCancelAppTrademark')->name('cancel-register');
        Route::post('/register/cancel/{id}', 'AppTrademarkController@cancelAppTrademark')->name('cancel');

        Route::get('/show-pass/{id}', 'AppTrademarkController@showPass')->name('show-pass'); // u031past
    });

    Route::get('/refusal/extension-period/alert/{id}', 'TrademarkController@showExtensionPeriodAlert')
        ->name('refusal.extension-period.alert')
        ->where('id', '[0-9]+'); // u210alert02
    Route::post('/save-data-extension-period/{id}', 'TrademarkController@saveDataExtensionPeriod')
        ->name('save_data_extension_period');
    Route::post('/ajax-extension-period', 'TrademarkController@ajaxExtensionPeriod')
        ->name('ajax_extension_period');
    Route::get('/refusal/extension-period/over/{id}', 'TrademarkController@showExtensionPeriodOver')
        ->name('refusal.extension-period.over'); // u210over02
    // {trademarks.id}?register_trademark_renewal_id= register_trademark_renewals=register_trademark_renewals.id
    Route::get('/refusal/notice/extension-period/{id}', 'TrademarkController@showEncho')
        ->name('refusal.notice')
        ->where('id', '[0-9]+'); // u210encho
    //Free history
    Route::group(['prefix' => 'free-history', 'as' => 'free-history.'], function () {
        Route::get('create/{id}', 'FreeHistoryController@index')
            ->name('show-create'); //u000free
        Route::post('name', 'FreeHistoryController@create')
            ->name('create');
        Route::get('cancel/{id}', 'FreeHistoryController@showCancel')
            ->name('show-cancel'); //u000free_cancel
        Route::post('cancel', 'FreeHistoryController@cancel')
            ->name('cancel');
    });

    // Refusal notification
    Route::group(['prefix' => 'refusal/notification', 'as' => 'refusal.notification.'], function () {
        Route::get('{id}', 'ComparisonTrademarkResultController@index')
            ->name('index')
            ->where('id', '[0-9]+'); //u201
        Route::get('over/{id}', 'ComparisonTrademarkResultController@over')
            ->name('over')
            ->where('id', '[0-9]+'); //u201_over
        Route::get('cancel/{comparison_trademark_result_id}', 'CancelController@viewNoticationCancel')
            ->name('cancel'); //u201b_cancel
        Route::post('cancel/update/{id}', 'CancelController@updateNotificationCancel')
            ->name('cancel.update');
    });

    // Refusal response
    Route::group(['prefix' => 'refusal/response-plan', 'as' => 'refusal.response-plan.'], function () {
        Route::post('save-data-choose-plan', 'RefusalController@saveDataChoosePlan')
            ->name('save_data_choose_plan');
        Route::get('product/{comparison_trademark_result_id}', 'RefusalController@showProduct')
            ->name('refusal_product')
            ->where('id', '[0-9]+'); // u203c
        Route::post('post-product', 'RefusalController@postProduct')
            ->name('post-product');
        Route::get('product-re/{comparison_trademark_result_id}', 'RefusalController@showProductRe')
            ->name('refusal_product_re')
            ->where('id', '[0-9]+'); // u203c_n
        Route::get('choose-plan/confirm/{comparison_trademark_result_id}', 'RefusalController@showU203b02')
            ->name('refusal_response_plan.confirm')
            ->where('id', '[0-9]+'); // u203b02 // GET
        Route::post('choose-plan/confirm/{comparison_trademark_result_id}', 'RefusalController@saveU203b02')
            ->name('refusal_response_plan.confirm.save')
            ->where('id', '[0-9]+'); // u203b02 //POST
        Route::get('choose-plan-re/{comparison_trademark_result_id}', 'RefusalController@showChoosePlanRe')
            ->name('refusal_response_plan_re')
            ->where('id', '[0-9]+'); // u203n
        Route::get('stop/{id}', 'RefusalController@stop')
            ->name('stop')
            ->where('id', '[0-9]+'); // u203stop
        Route::post('post-stop', 'RefusalController@postStop')
            ->name('post-stop');
        Route::get('choose-plan/notice_next/{comparison_trademark_result_id}', 'RefusalController@paymentCompleted')
            ->name('notice_next'); // u203b02paid
    });

    // GROUP U204
    Route::get('refusal/materials/{id}', 'RefusalController@materialIndex')
        ->name('refusal.materials.index')
        ->where('id', '[0-9]+'); // u204
    Route::post('refusal/post-materials/{id}', 'RefusalController@postMaterial')
        ->name('refusal.materials.post')
        ->where('id', '[0-9]+');
    Route::post('refusal/ajax-materials', 'RefusalController@ajaxMaterial')
        ->name('refusal.materials.ajax');
    Route::post('refusal/ajax-materials-delete', 'RefusalController@ajaxMaterialDelete')
        ->name('refusal.materials.ajax_delete');

    Route::get('refusal/materials/confirm/{id}', 'RefusalController@materialIndex')
        ->name('refusal.materials.confirm.index')
        ->where('id', '[0-9]+'); // u204kakunin

    Route::get('refusal/materials-re/{id}', 'RefusalController@materialReIndex')
        ->name('refusal.materials-re.index')
        ->where('id', '[0-9]+'); // u204n
    Route::post('refusal/post-materials-re/{id}', 'RefusalController@postMaterialRe')
        ->name('refusal.materials-re.post')
        ->where('id', '[0-9]+');
    Route::post('refusal/ajax-materials-re-download', 'RefusalController@ajaxMaterialReDownload')
        ->name('refusal.materials-re.ajax_download');

    Route::get('refusal/materials-re/confirm/{id}', 'RefusalController@materialReIndex')
        ->name('refusal.materials-re.confirm.index')
        ->where('id', '[0-9]+'); // u204kakunin

    // Refusal plans
    Route::group(['prefix' => 'refusal/plans', 'as' => 'refusal.plans.'], function () {
        Route::get('{id}', 'ComparisonTrademarkResultController@plansIndex')
            ->name('index')
            ->where('id', '[0-9]+'); //u201plans

        Route::get('pack/{id}', 'ComparisonTrademarkResultController@pack')
            ->name('pack')
            ->where('id', '[0-9]+'); //u201pack
        Route::post('create-pack', 'ComparisonTrademarkResultController@createPack')
            ->name('create-pack')
            ->where('id', '[0-9]+');

        Route::get('simple/{comparison_trademark_result_id}', 'PlanController@showSimplePlan')
            ->name('simple')
            ->where('id', '[0-9]+'); // u201simple01

        Route::get('simple/alert/{comparison_trademark_result_id}', 'PlanController@showSimplePlan')
            ->name('simple_alert')
            ->where('id', '[0-9]+'); // u201simple01alert
        Route::get('simple/over/{comparison_trademark_result_id}', 'PlanController@showSimplePlan')
            ->name('simple_over')
            ->where('id', '[0-9]+'); // u201simple01over
        Route::post('ajax-calculator', 'PlanController@ajaxCalculateCart')
            ->name('ajax-caculator');
        Route::post('create-comparison', 'PlanController@store')
            ->name('create-comparison');
        Route::get('select/{comparison_trademark_result_id}', 'PlanController@showSelectPlan')
            ->name('select')
            ->where('comparison_trademark_result_id', '[0-9]+'); // u201select01
        Route::get('select/alert/{comparison_trademark_result_id}', 'PlanController@showSelectPlan')
            ->name('select_01_alert')
            ->where('comparison_trademark_result_id', '[0-9]+'); // u201select01alert
        Route::get('select/over/{comparison_trademark_result_id}', 'PlanController@showSelectPlan')
            ->name('select_01_over')
            ->where('comparison_trademark_result_id', '[0-9]+'); // u201select01over
        Route::get('select-eval-report-re/{comparison_trademark_result_id}', 'PlanController@showSelectPlan01n')
            ->name('select-eval-report-re')
            ->where('comparison_trademark_result_id', '[0-9]+'); // u201select01n
    });

    Route::group(['prefix' => 'refusal/', 'as' => 'refusal.'], function () {
        Route::get('/select-eval-report-show/{id}', 'ComparisonTrademarkResultController@showSelectPlan02')
            ->name('select-eval-report-show'); // u201select02
        Route::post('/select-eval-report-show/{id}', 'ComparisonTrademarkResultController@saveSelectPlan02')
            ->name('select-eval-report-save'); // u201select02
    });

    Route::get('refusal/pre-question/{id}', 'ComingSoonController@index')
        ->name('refusal.pre-question');

    //u202: id - comparison_trademark_result_id?reason_question_no={reason_question_no.id}
    Route::get('refusal/pre-question/reply/{id}', 'ComparisonTrademarkResultController@getRefusalPreQuestionReply')
        ->name('refusal.pre-question.reply')
        ->where('id', '[0-9]+');
    Route::post('refusal/pre-question/reply/{id}', 'ComparisonTrademarkResultController@postRefusalPreQuestionReply')
        ->name('refusal.pre-question.reply.post')
        ->where('id', '[0-9]+');

    Route::post('refusal/pre-question/reply/delete-file/ajax', 'ComparisonTrademarkResultController@postRefusalPreQuestionReplyDeleteFileAjax')
        ->name('refusal.pre-question.reply.delete-file-ajax');

    //u202kakunin: id - comparison_trademark_result_id
    Route::get('refusal/pre-question/reply/kakunin/{id}', 'ComparisonTrademarkResultController@getRefusalPreQuestionReplyKakunin')
        ->name('u202refusal.pre-question.reply.kakunin')
        ->where('id', '[0-9]+');

    //u202n: id - {comparison_trademark_result_id}?reason_question_no={reason_question_no.id}
    Route::get('/refusal/pre-question/re-reply/{id}', 'ComparisonTrademarkResultController@getReReplyRefusalPreQuestion')
        ->name('refusal.pre-question.re-reply')
        ->where('id', '[0-9]+');
    Route::post('/refusal/pre-question/re-reply/{id}', 'ComparisonTrademarkResultController@postReReplyRefusalPreQuestion')
        ->name('refusal.pre-question.re-reply.post')
        ->where('id', '[0-9]+');

    //u202n-kakunin: id - {comparison_trademark_result_id}?reason_question_no={reason_question_no.id}
    Route::get('/refusal/pre-question/re-reply/kakunin/{id}', 'ComparisonTrademarkResultController@getReReplyRefusalPreQuestionKakunin')
        ->name('refusal.pre-question.re-reply-u202n-kakunin')
        ->where('id', '[0-9]+');
    // u205
    Route::get('/refusal/documents/confirm/{comparison_trademark_result_id}', 'DocSubmissionController@index')
        ->name('refusal_documents_confirm')
        ->where('comparison_trademark_result_id', '[0-9]+');
    // u207kyo
    Route::get('/refusal/final-refusal/{trademark_id}', 'ContactController@index')
        ->name('refusal.final-refusal')
        ->where('trademark_id', '[0-9]+');
    Route::post('/redirect-page-final-refusal', 'ContactController@redirectPage')
        ->name('redirect_page_find_refusal');
    // Phase 3
    Route::get('/withdraw/application-list', 'TrademarkController@applicationList') // id = user.id
        ->name('withdraw.application-list')
        ->where('id', '[0-9]+'); // u000list_taikai
    Route::post('/withdraw/application-list-post', 'TrademarkController@applicationListPost') // id = user.id
        ->name('withdraw.application-list-post')
        ->where('id', '[0-9]+');
    Route::get('/withdraw', 'UserController@showUserInfo')
        ->name('withdraw.index'); // u000taikai01
    Route::post('/withdraw', 'UserController@confirmCapabilityWithdrawUser')
        ->name('withdraw.store');
    Route::get('/withdraw/check', 'UserController@verificationCode')
        ->name('withdraw.verification'); // u000taikai02
    Route::post('/withdraw/pre-confirm/verification', 'UserController@preConfirmWithdraw')
        ->name('withdraw.pre-confirm.verification');
    Route::post('/withdraw/check', 'UserController@confirmVerifyCode')
        ->name('withdraw.confirm.verification');
    Route::get('/withdraw/confirm', 'UserController@showConfirm')
        ->name('withdraw.confirm'); // u000taikai01kakunin
    Route::post('/withdraw/confirm/send_mail', 'UserController@sendMailConfirmWithdraw')
        ->name('withdraw.confirm.send_mail');
    Route::get('/withdraw/confirm-ng', 'UserController@showConfirmNG')
        ->name('withdraw.confirm-ng'); // u000taikai01ng
    Route::get('/withdraw/application-list/confirm', 'UserController@showWithdrawConfirm')
        ->name('withdraw.anken-list'); //  u000list_taikai_kakunin
    Route::get('withdraw/application-list/confirm/sort', 'UserController@getTrademarksOfUserWithdrawAjax')
        ->name('withdraw.anken-list.sort');

    Route::get('/update/notify-procedure/{id}', 'RegisterTrademarkController@getUpdateNotifyProcedure')
        ->name('update.notify-procedure')
        ->where('id', '[0-9]+'); //u402
    Route::post('/update/notify-procedure/{id}', 'RegisterTrademarkController@postUpdateNotifyProcedure')
        ->name('update.notify-procedure.post')
        ->where('id', '[0-9]+');

    Route::get('/update/notify-procedure/overdue/{id}', 'RegisterTrademarkController@getUpdateNotifyProcedureOverdue')
        ->name('update.notify-procedure.overdue')
        ->where('id', '[0-9]+'); //u402tsuino

    Route::get('/cancel-trademark/{id}', 'RegisterTrademarkController@cancelTrademark')
        ->name('register-trademark.cancel-trademark')
        ->where('id', '[0-9]+'); //u402_cancel
    Route::post('/cancel-trademark/{id}', 'RegisterTrademarkController@cancelTrademarkPost')
        ->name('register-trademark.cancel-trademark.post')
        ->where('id', '[0-9]+');
    // End Phase 3

    Route::group(['prefix' => 'registration', 'as' => 'registration.'], function () {
        Route::get('/procedure/{id}', 'TrademarkRegistrationController@index')
            ->name('procedure') // U302
            ->where('id', '[0-9]+');
        Route::post('/procedure/{id}', 'TrademarkRegistrationController@saveDataU302')
            ->name('procedure') // U302
            ->where('id', '[0-9]+');
        Route::get('procedure/cancel/{id}', 'TrademarkRegistrationController@showCancel')
            ->name('cancel'); // u302_cancel
        Route::post('update/cancel/{id}', 'TrademarkRegistrationController@updateCancel')
            ->name('update.cancel');
        Route::get('document/completed/{id}', 'TrademarkRegistrationController@documentCompleted')
            ->name('document.completed')
            ->where('id', '[0-9]+'); // U303
        Route::post('document/completed/{id}', 'TrademarkRegistrationController@postDocumentCompleted')
            ->name('document.completed.post');
        Route::get('notify-number/{id}', 'TrademarkRegistrationController@notifyNumber')
            ->name('notify-number')
            ->where('id', '[0-9]+');// U304
        Route::post('notify-number/{id}', 'TrademarkRegistrationController@postNotifyNumber')
            ->name('notify-number.post');

        Route::get('notice-later-period/{id}', 'TrademarkRegistrationController@notifyLatterPeriod')
            ->name('notice-latter-period')
            ->where('id', '[0-9]+');// u302_402_5yr_kouki
        Route::post('notice-later-period/{id}', 'TrademarkRegistrationController@postNotifyLatterPeriod')
            ->name('notice-latter-period.post')
            ->where('id', '[0-9]+');

        Route::get('/notice-later-period/overdue/{id}', 'TrademarkRegistrationController@notifyLatterPeriodOverdue')
            ->name('notice-later-period.overdue')
            ->where('id', '[0-9]+'); // u302_402tsuino_5yr_kouki

        // u302_402kakunin_ininjo
        Route::get('attorney_letter/confirm/{id}', 'TrademarkRegistrationController@showU302402KakuninIninjo')
            ->name('attorney-letter-confirm')
            ->where('id', '[0-9]+');
        Route::post('attorney_letter/confirm/{id}', 'TrademarkRegistrationController@redirectPayment')
            ->name('save-attorney-letter-confirm')
            ->where('id', '[0-9]+');
    });
});

// Route ajax for user login
Route::group(['namespace' => 'User', 'prefix' => 'ajax', 'as' => 'user.ajax.', 'middleware' => ['web', 'isUser']], function () {
    // Change Reference Number
    Route::post('change-reference-number/{id}', 'AjaxController@changeReferenceNumber')
        ->name('change-reference-number');

    // Common Handle Ajax
    Route::post('handle', 'AjaxController@handle')
        ->name('handle');
});

// Route ajax for user login
Route::group(['namespace' => 'User', 'prefix' => 'ajax', 'as' => 'user.ajax.', 'middleware' => ['web']], function () {
    // Set Session
    Route::post('set-session', 'AjaxController@setSession')
        ->name('set-session');
});

// Route for user or admin login
Route::group(['namespace' => 'User', 'as' => 'user.', 'middleware' => ['web', 'isLogin']], function () {
    // App Product List
    Route::get('/application/product-list/{id}', 'ApplicationProductController@viewAppProductList')
        ->name('app.product.list'); // u003db

    Route::group(['prefix' => 'refusal/response-plan', 'as' => 'refusal.response-plan.'], function () {
        Route::get('choose-plan/{comparison_trademark_result_id}', 'RefusalController@showChoosePlan')
            ->name('refusal_response_plan')
            ->where('id', '[0-9]+'); // u203
        Route::post('ajax-choose-plan', 'RefusalController@ajaxCalculatorChoosePlan')
            ->name('ajax-choose-plan'); // ajax u203
    });
});
