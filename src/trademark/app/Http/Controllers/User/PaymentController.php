<?php

namespace App\Http\Controllers\User;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Models\AppTrademark;
use App\Models\MailTemplate;
use App\Models\Payment;
use App\Models\Precheck;
use App\Notices\PaymentNotice;
use App\Services\ChangeInfoRegisterService;
use App\Services\FreeHistoryService;
use App\Services\RegisterTrademarkService;
use App\Services\Common\TrademarkTableService;
use App\Services\PrecheckService;
use App\Services\SupportFirstTimeService;
use App\Services\MProductService;
use App\Models\MProduct;
use App\Models\MPrefecture;
use App\Models\RegisterTrademark;
use App\Models\RegisterTrademarkRenewal;
use App\Services\PaymentService;
use App\Services\TrademarkPlanService;
use App\Services\TrademarkService;
use App\Services\MailTemplateService;
use App\Services\SFTContentProductService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class PaymentController extends Controller
{
    protected $paymentService;
    protected $mProductService;
    protected TrademarkService $trademarkService;
    protected PaymentNotice $paymentNotice;
    protected SFTContentProductService $sftContentProductService;
    protected TrademarkTableService $trademarkTableService;
    protected SupportFirstTimeService $supportFirstTimeService;
    protected PrecheckService $precheckService;
    protected ChangeInfoRegisterService $changeInfoRegisterService;
    protected RegisterTrademarkService $registerTrademarkService;
    protected FreeHistoryService $freeHistoryService;
    protected MailTemplateService $mailTemplateService;
    protected TrademarkPlanService $trademarkPlanService;

    /**
     * Constructor
     *
     * @return  void
     */
    public function __construct(
        PaymentService $paymentService,
        MProductService $mProductService,
        TrademarkService $trademarkService,
        SupportFirstTimeService $supportFirstTimeService,
        TrademarkTableService $trademarkTableService,
        PaymentNotice $paymentNotice,
        SFTContentProductService $sftContentProductService,
        PrecheckService $precheckService,
        ChangeInfoRegisterService $changeInfoRegisterService,
        RegisterTrademarkService $registerTrademarkService,
        FreeHistoryService $freeHistoryService,
        MailTemplateService $mailTemplateService,
        TrademarkPlanService $trademarkPlanService
    )
    {
        $this->paymentNotice = $paymentNotice;
        $this->paymentService = $paymentService;
        $this->mProductService = $mProductService;
        $this->precheckService = $precheckService;
        $this->trademarkService = $trademarkService;
        $this->freeHistoryService = $freeHistoryService;
        $this->mailTemplateService = $mailTemplateService;
        $this->trademarkTableService = $trademarkTableService;
        $this->supportFirstTimeService = $supportFirstTimeService;
        $this->sftContentProductService = $sftContentProductService;
        $this->changeInfoRegisterService = $changeInfoRegisterService;
        $this->registerTrademarkService = $registerTrademarkService;
        $this->trademarkPlanService = $trademarkPlanService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request)
    {
        $isShowTblProd = true;
        if ($request->has('s') && $request->s) {
            if (!Session::has($request->s)) {
                abort(404);
            }
            $sft = null;
            $products = [];
            $payment = null;
            $productIds = [];
            $data = (array) Session::get($request->s);
            if (isset($data['payment_id']) && $data['payment_id']) {
                $payment = $this->paymentService->find($data['payment_id']);
                $payment->load(['paymentProds', 'payerInfo', 'trademark.appTrademark']);
                if ($payment->paymentProds && $data['from_page'] != U011) {
                    $productIds = $payment->paymentProds->pluck('m_product_id')->toArray();
                    $products = MProduct::whereIn('id', $productIds)->with(['mDistinction:id,name'])->get()->groupBy('mDistinction.name')->sortKeys();
                }
                if (isset($data['productIds']) && count($data['productIds'])) {
                    $products = MProduct::whereIn('id', $data['productIds'])->with(['mDistinction:id,name'])->get()->groupBy('mDistinction.name')->sortKeys();
                }
            }
            $payerInfo = $payment->payerInfo;
            if ($data['from_page'] == U011) {
                $isShowTblProd = false;
                if (isset($data['product_names']) && $data['product_names']) {
                    $products = $data['product_names'];
                } else {
                    $sftContentProds = $this->sftContentProductService->findByCondition(['support_first_time_id' => $payment->target_id])
                        ->pluck('name')
                        ->toArray();

                    $products = $sftContentProds;
                }
            }
            $contentPay = '';
            if (isset($data['from_page']) && $data['from_page']) {
                $contentPay = $this->paymentService->getFormPayment($data, $payment, $data['from_page']);
            }
            $payment->from_page = $data['from_page'];

            $data = array_merge($data, $payment->toArray());

            $data['payment_amount'] = $this->paymentService->calculatePaymentAmount($data);
            $trademark = $this->trademarkService->find($data['trademark_id']);
            $data['image_trademark'] = isset($data['image_trademark']) ? (array) $data['image_trademark'] : null;
            $data['image_url'] = isset($data['image_url']) ? (array) $data['image_url'] : null;

            // Remove session
            if (isset($data['m_prefecture_id']) && $data['m_prefecture_id']) {
                $mPrefecture = MPrefecture::find($data['m_prefecture_id']);
                $data['m_prefecture_name'] = $mPrefecture->name;
            }

            $data['image_trademark'] = (array) $data['image_trademark'];
            if (isset($data['image_url']) && !empty($data['image_url'])) {
                $data['image_url'] = (array) $data['image_url'];
            }

            $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_7, $trademark->id, [
                'payment_info' => $data,
                'payment_id' => $payment->id,
            ]);
        }

        $routeBack = $payment->getRouteBack($request->s, $data);

        return view('user.modules.common.payment', compact(
            'isShowTblProd',
            'data',
            'trademarkTable',
            'products',
            'contentPay',
            'routeBack',
            'payerInfo'
        ));
    }

    /**
     * Showing form input card.
     *
     * @param Request $request
     * @return View
     */
    public function indexGMO(Request $request)
    {
        $preURL = URL::previous();
        $index = strpos($preURL, '?');
        $previousPath = substr($preURL, 0, $index);
        $sessionData = Session::get($request->s);
        if (!$sessionData) {
            abort(404);
        }
        if (!in_array($previousPath, [route('user.payment.index'), route('user.payment.GMO.index')])) {
            abort(404);
        }
        $secret = $request->s;
        $currentMonth = now()->month;
        $months = array_keys(array_fill(1, 12, ''));
        $currentYear = now()->format('y');
        $years = array_keys(array_fill($currentYear, 11, ''));

        return view('user.modules.common.gmo_input_card', compact(
            'months',
            'years',
            'currentMonth',
            'currentYear',
            'secret',
        ));
    }

    /**
     * Showing thank you.
     */
    public function showThankYou(Request $request)
    {
        $paymentId = $request->payment_id;

        return view('user.modules.common.gmo_thank_you', compact('paymentId'));
    }

    /**
     * Create payment from GMO
     */
    public function storeGMO(Request $request)
    {
        try {
            DB::beginTransaction();
            $sessionData = Session::get($request->secret);
            if (empty($sessionData) || !$sessionData) {
                abort(404);
            }

            $payment = $this->paymentService->find($sessionData['payment_id']);
            if ($payment->payment_status == Payment::STATUS_PAID) {
                abort(404);
            }

            $cardExpire = $request->expire_year . ((int) $request->expire_month < 10 ? '0' . $request->expire_month : $request->expire_month);
            $params = [
                'payment_id' => $sessionData['payment_id'],
                'payment_amount' => $payment->payment_amount,
                'tax' => $payment->tax,
                'card_no' => str_replace(' ', '', $request->card_number),
                'card_expire' => $cardExpire,
                'cvc' => $request->card_cvc
            ];

            $result = $this->paymentService->paymentWithGMO($params);

            if ($result) {
                $payment->update([
                    'payment_status' => Payment::STATUS_PAID,
                ]);
                $paymentNotice = App::make(PaymentNotice::class);
                $paymentNotice->setData($sessionData);
                $paymentNotice->notice($payment, $sessionData['payment_type']);
                //update precheck screen U021 || U021N
                if ($sessionData['from_page'] == U021 || $sessionData['from_page'] == U021N) {
                    $this->precheckService->findByCondition(['id' => $sessionData['precheck_id']])
                        ->update(['status_register' => Precheck::HAS_STATUS_REGISTER]);
                } elseif ($sessionData['from_page'] == U000FREE) {
                    // Add freeHistory record to send mail
                    $params['freeHistory'] = $sessionData['freeHistory'];
                    $this->freeHistoryService->findByCondition(['id' => $sessionData['free_history_id']])
                        ->update(['is_answer' => IS_ANSWER_TRUE]);
                }
                $arrayPages = [U021B, U021B_31, U031, U031B, U031C, U031D, U031_EDIT_WITH_NUMBER, U011B_31, U011B];
                if (in_array($payment->from_page, $arrayPages) && isset($sessionData['app_trademark_id'])) {
                    $appTrademark = AppTrademark::find($sessionData['app_trademark_id']);
                    if ($appTrademark) {
                        $appTrademark->update([
                            'status' => AppTrademark::STATUS_WAITING_FOR_ADMIN_CONFIRM,
                        ]);
                        $params['appTrademark'] = $appTrademark;
                    }
                }

                $arrayPagesOverAlert = [U210_ALERT_02, U210_OVER_02];
                if (in_array($payment->from_page, $arrayPagesOverAlert)) {
                    $payment->trademark->load('registerTrademarkRenewals');
                    $registerTrademarkRenewals = $payment->trademark->registerTrademarkRenewals
                        ->where('type', $payment->from_page == U210_ALERT_02
                            ? RegisterTrademarkRenewal::TYPE_EXTENSION_PERIOD_BEFORE_DEADLINE
                            : RegisterTrademarkRenewal::TYPE_EXTENSION_OUTSIDE_PERIOD)->where('status', RegisterTrademarkRenewal::SAVE_DRAFT);
                    foreach ($registerTrademarkRenewals as $key => $dataUpdate) {
                        $dataUpdate->update(['status' => RegisterTrademarkRenewal::ADMIN_CONFIRM]);
                    }
                }

                if ($payment->from_page == U302) {
                    $this->registerTrademarkService->find($sessionData['register_trademark_id'])->update([
                        'is_register' => RegisterTrademark::IS_REGISTER,
                    ]);
                }
                if ($payment->from_page == U000LIST_CHANGE_ADDRESS_02_KENRISHA) {
                    $this->changeInfoRegisterService->find($sessionData['change_info_register_id'])->update([
                        'is_send' => true,
                    ]);
                }
                if (str_contains($payment->from_page, U203)) {
                    $trademarkPlanID = $sessionData['trademark_plan_id'] ?? null;
                    $this->trademarkPlanService->updateById($trademarkPlanID, [
                        'is_register' => IS_REGISTER_TRUE,
                        'sending_docs_deadline' => Carbon::now()->addDays(2),
                    ]);
                }

                $payment->load(['trademark.user', 'trademark.appTrademark']);
                $params['from_page'] = $payment->from_page;
                $params['payment'] = $payment;

                $isSendMail = $this->mailTemplateService->checkSendMailUser($params);
                if ($isSendMail) {
                    $this->mailTemplateService->sendMailRequest($params, MailTemplate::CREDIT_CARD);
                }

                DB::commit();
                if (isset($sessionData['redirect']) && $sessionData['redirect']) {
                    return redirect($sessionData['redirect']);
                } else {
                    Session::forget($request->secret);
                }

                return redirect()->route('user.payment.GMO.thank-you', ['payment_id' => $sessionData['payment_id']]);
            } else {
                $payment->update([
                    'payment_status' => Payment::STATUS_SAVE,
                ]);
                DB::commit();
            }

            return redirect()->back()->with('error', __('messages.general.Payment_notice_U000_E001'))->withInput();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            return redirect()->back()->with('error', __('messages.general.Payment_notice_U000_E001'))->withInput();
        }
    }

    /**
     * View confirm payment precheck, precheck-n
     *
     * @param Request $request
     * @return mixed
     */
    public function confirmPaymentPrecheck(Request $request)
    {
        if ($request->has('s') && $request->s) {
            if (!Session::has($request->s)) {
                abort(404);
            }
            $data = (array) Session::get($request->s);
            $data['image_trademark'] = isset($data['image_trademark']) ? (array) $data['image_trademark'] : null;
            $data['image_url'] = isset($data['image_url']) ? (array) $data['image_url'] : null;

            if (isset($data['m_prefecture_id']) && $data['m_prefecture_id']) {
                $mPrefecture = MPrefecture::find($data['m_prefecture_id']);
                $data['m_prefecture_name'] = $mPrefecture->name;
            }

            // if from precheck n0
            $idsProduct = $data['productIds'];
            $mProductChoose = $this->mProductService->getDataMproduct($idsProduct);

            $secretKey = $request->s;
            $result = [
                'mProductChoose' => $mProductChoose,
                'productsCount' => count($idsProduct),
                'key_session' => $secretKey,
                'data' => $data
            ];
            $data['from_page'] = U021N;
            Session::put($secretKey, json_encode($data));

            return view('user.modules.prechecks.precheck-n.payment_precheck_n', $result);
        }
    }

    /**
     * Payment with GMO and saving data into database.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function payment(Request $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $params = $request->all();
            $inputs = [];
            $sessionData = [];
            //if exists session key and from Precheck, precheck-n
            if ($request->s || $request->secret) {
                $key = $request->s ?? $request->secret;
                $sessionData = (array) Session::get($key);
                if (isset($sessionData['from_page']) && $sessionData['from_page']) {
                    //create info payments, payer_info, prechecks
                    $inputs = $sessionData;
                }
            } else {
                $inputs = $params;
            }

            $inputs['payment_status'] = Payment::STATUS_WAITING_PAYMENT;
            // $inputs['is_treatment'] = Payment::IS_TREATMENT_WAIT;

            if ((int) $inputs['payment_type'] == Payment::CREDIT_CARD) {
                // $inputs['is_treatment'] = Payment::IS_TREATMENT_DONE;
            } else {
                $inputs['is_treatment'] = Payment::IS_TREATMENT_WAIT;
            }

            $payment = $this->paymentService->payment($inputs);

            if ($payment) {
                // Update for U000LIST_CHANGE_ADDRESS_02
                if ($payment->from_page == U000LIST_CHANGE_ADDRESS_02) {
                    $changeInfoRegisterDraft = $this->changeInfoRegisterService->getChangeInfoRegister($payment->trademark_id);
                    if (!empty($changeInfoRegisterDraft)) {
                        $changeInfoRegisterDraft->update([
                            'is_send' => IS_SEND_TRUE,
                        ]);
                    }
                }

                // Update for U000LIST_CHANGE_ADDRESS_02_KENRISHA
                if ($payment->from_page == U000LIST_CHANGE_ADDRESS_02_KENRISHA) {
                    $changeInfoRegisterDraft = $this->changeInfoRegisterService->getChangeInfoRegisterKenrisha($payment->trademark_id);
                    $sessionData['change_info_register_id'] = $changeInfoRegisterDraft->id;
                    Session::put($params['secret'], $sessionData);
                    if (!empty($changeInfoRegisterDraft)) {
                        $changeInfoRegisterDraft->update([
                            'is_send' => IS_SEND_TRUE,
                        ]);
                    }
                }
            }
            $secretKey = $params['secret'];
            if ((int) $inputs['payment_type'] == Payment::CREDIT_CARD) {
                return redirect()->route('user.payment.GMO.index', ['s' => $secretKey]);
            } else {
                $arrayPagesOverAlert = [U210_ALERT_02, U210_OVER_02];
                if (in_array($payment->from_page, $arrayPagesOverAlert)) {
                    $payment->trademark->load('registerTrademarkRenewals');
                    $payment->trademark->registerTrademarkRenewals
                        ->where('type', RegisterTrademarkRenewal::TYPE_EXTENSION_PERIOD_BEFORE_DEADLINE)
                        ->where('status', RegisterTrademarkRenewal::SAVE_DRAFT)
                        ->map(function ($item) {
                            $item->update(['status' => RegisterTrademarkRenewal::ADMIN_CONFIRM]);
                        });
                }
                $paymentNotice = App::make(PaymentNotice::class);
                $paymentNotice->setData($sessionData);
                $paymentNotice->notice($payment, $inputs['payment_type']);
            }

            if ($payment->from_page == U302) {
                $this->registerTrademarkService->find($sessionData['register_trademark_id'])->update([
                    'is_register' => RegisterTrademark::IS_REGISTER,
                ]);
            }
            $payment->load('trademark.user');
            $params['payment'] = $payment;
            if (isset($sessionData['freeHistory']) && $sessionData['freeHistory']) {
                $params['freeHistory'] = $sessionData['freeHistory'];
            }
            $isSendMail = $this->mailTemplateService->checkSendMailUser($params);
            if ($isSendMail) {
                $this->mailTemplateService->sendMailRequest($params, MailTemplate::BANK_TRANSFER);
            }

            // from_page u203b02
            if (isset($sessionData['from_page']) && isset($sessionData['redirect']) && $sessionData['redirect']) {
                DB::commit();

                CommonHelper::setMessage($request, MESSAGE_SUCCESS, __('messages.general.Common_E048'));

                return redirect($sessionData['redirect']);
            }
            if ($payment) {
                //update precheck screen U021 || U021N
                if ($sessionData['from_page'] == U021 || $sessionData['from_page'] == U021N) {
                    $this->precheckService->findByCondition(['id' => $sessionData['precheck_id']])
                        ->update(['status_register' => Precheck::HAS_STATUS_REGISTER]);
                }

                $arrayPages = [U021B, U021B_31, U031, U031B, U031C, U031D, U031_EDIT_WITH_NUMBER, U011B_31, U011B];
                if (in_array($payment->from_page, $arrayPages) && isset($sessionData['app_trademark_id'])) {
                    $appTrademark = AppTrademark::find($sessionData['app_trademark_id']);
                    if ($appTrademark) {
                        $appTrademark->update([
                            'status' => AppTrademark::STATUS_WAITING_FOR_ADMIN_CONFIRM,
                        ]);
                    }
                }

                if ($payment->from_page == U302) {
                    $this->registerTrademarkService->find($sessionData['register_trademark_id'])->update([
                        'is_register' => RegisterTrademark::IS_REGISTER,
                    ]);
                }
                if ($sessionData['from_page'] == U000FREE) {
                    $this->freeHistoryService->findByCondition(['id' => $sessionData['free_history_id']])
                        ->update(['is_answer' => IS_ANSWER_TRUE]);
                }
                DB::commit();

                return redirect()->route('user.payment.index', ['s' => $secretKey])->with([
                    'message' => $request->payment_type == Payment::BANK_TRANSFER
                        ? __('messages.user_common_payment.Payment_notice_U000_S001')
                        : __('messages.user_common_payment.Payment_notice_U000_S002'),
                ]);
            }
            DB::commit();

            return redirect()->route('user.payment.index', ['s' => $secretKey, 'sft_011' => 1])->withErrors([
                'error' => 'System error',
            ]);
        } catch (\Exception $e) {
            Log::error($e);
            DB::rollBack();

            return redirect()->back()->withErrors([
                'error' => 'System error',
            ]);
        }
    }

    /**
     * Ajax get info payment
     *
     * @param Request $request
     *
     * @return void
     */
    public function ajaxGetCartInfoPayment(Request $request)
    {
        if ($request->ajax()) {
            $inputs = $request->all();
            $respon = $this->paymentService->ajaxGetCartInfoPayment($inputs);

            return response()->json([
                'status' => true,
                'data' => $respon
            ]);
        }
    }
}
