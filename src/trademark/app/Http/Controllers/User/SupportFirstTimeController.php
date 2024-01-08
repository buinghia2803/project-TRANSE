<?php

namespace App\Http\Controllers\User;

use App\Helpers\FileHelper;
use App\Services\Common\TrademarkTableService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use App\Models\Payment;
use App\Models\MPriceList;
use App\Http\Controllers\Controller;
use App\Models\AppTrademark;
use App\Models\MProduct;
use App\Models\Precheck;
use App\Models\SFTComment;
use App\Models\PayerInfo;
use App\Models\SupportFirstTime;
use App\Models\SFTSuitableProduct;
use App\Models\Trademark;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\PaymentService;
use App\Services\MNationService;
use App\Services\MProductService;
use App\Services\MPriceListService;
use App\Services\MPrefectureService;
use App\Services\SupportFirstTimeService;
use App\Services\TrademarkService;
use App\Services\PayerInfoService;
use App\Services\TrademarkInfoService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SupportFirstTimeController extends Controller
{
    protected PaymentService $paymentService;
    protected MNationService $mNationService;
    protected MPrefectureService $mPrefectureService;
    protected SupportFirstTimeService $supportFirstTimeService;
    protected MPriceListService $mPriceListService;
    protected MProductService $mProductService;
    protected TrademarkService $trademarkService;
    protected PayerInfoService $payerInfoService;
    protected TrademarkInfoService $trademarkInfoService;
    protected TrademarkTableService $trademarkTableService;
    /**
     * Constructor
     *
     * @return  void
     */
    public function __construct(
        MNationService $mNationService,
        MProductService $mProductService,
        PaymentService $paymentService,
        MPrefectureService $mPrefectureService,
        SupportFirstTimeService $supportFirstTimeService,
        TrademarkService $trademarkService,
        PayerInfoService $payerInfoService,
        TrademarkTableService $trademarkTableService,
        TrademarkInfoService $trademarkInfoService
    )
    {
        $this->mNationService = $mNationService;
        $this->paymentService = $paymentService;
        $this->mProductService = $mProductService;
        $this->mPrefectureService = $mPrefectureService;
        $this->supportFirstTimeService = $supportFirstTimeService;
        $this->mProductService = $mProductService;
        $this->trademarkService = $trademarkService;
        $this->payerInfoService = $payerInfoService;
        $this->trademarkTableService = $trademarkTableService;
        $this->trademarkInfoService = $trademarkInfoService;
    }

    /**
     * Display support first time basic screen.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $oldData = [];
        if ($request->has('s') && $request->s) {
            $oldData = Session::get($request->s);
            $oldData['payerInfo'] = $this->payerInfoService->findPayerWithSFT($oldData['sft']);
        } else if (Session::get('quote_' . auth()->user()->id)) {
            $oldData = Session::get('quote_' . auth()->user()->id);
            Session::forget('quote_' . auth()->user()->id);
        }

        $fees = $this->supportFirstTimeService->getSystemFee(MPriceList::BEFORE_FILING, MPriceList::SFT_SELECT_SUPPORT);
        $paymentFee = $this->supportFirstTimeService->getSystemFee(MPriceList::EACH_PAYMENT, MPriceList::BANK_TRANSFER_HANDLING);
        $nations = $this->mNationService->listNationOptions();
        $prefectures = $this->mPrefectureService->listPrefectureOptions();

        //if redirect from page u021c
        $productsSession = [];
        if (Session::has(SESSION_MPRODUCT_FORM_U021C)) {
            $productsSession = Session::get(SESSION_MPRODUCT_FORM_U021C)['data']['sftContentProds'];
        }

        // Get keyword of search ai
        if (Session::has(SESSION_MPRODUCT_NAME)) {
            $keywords = Session::get(SESSION_MPRODUCT_NAME);
            foreach ($keywords as $productName) {
                if (!empty($productName)) {
                    $productsSession[]['name'] = $productName;
                }
            }
            Session::forget(SESSION_MPRODUCT_NAME);
        }

        return view('user.modules.support_first_times.u011', compact(
            'fees',
            'oldData',
            'nations',
            'paymentFee',
            'prefectures',
            'productsSession'
        ));
    }

    /**
     * Showing support first time u001b.
     *
     * @param Request $request
     * @param string|int $id - sft_id
     * @return View
     */
    public function showSupportFirstTimeU011b(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('auth.login');
        }
        $sft = $this->supportFirstTimeService->getSupportFirstTime($id);
        if (!$sft) {
            abort(404);
        }
        $trademark = Trademark::find($sft->trademark_id)->load('appTrademark', 'prechecks');
        if ($trademark->user_id != $user->id) {
            abort(403);
        }
        $payerInfo = null;
        $appTrademark = $trademark->appTrademark;
        $prechecks = $trademark->prechecks->where('status_register', Precheck::STATUS_REGISTER_SAVE);
        $data['reference_number'] = $trademark->reference_number;
        $data['trademark_number'] = $trademark->trademark_number;
        $data['created_at'] = $trademark->created_at;
        $data['name_trademark'] = $trademark->name_trademark;
        $data['image_trademark'] = $trademark->image_trademark;
        $data['type_trademark'] = $trademark->getTypeName();
        $data['pack'] = $appTrademark ? $appTrademark->pack : null;
        $paymentFee = $this->supportFirstTimeService->getSystemFee(MPriceList::EACH_PAYMENT, MPriceList::BANK_TRANSFER_HANDLING);
        $nations = $this->mNationService->listNationOptions();
        $prefectures = $this->mPrefectureService->listPrefectureOptions();
        $trademarkInfos = $appTrademark ? $this->trademarkInfoService->findTrademarkInfo($appTrademark) : null;
        $products = $this->supportFirstTimeService->getDistinctionService($id);
        if (!$products->count()) {
            abort(403);
        }
        $suitProductChoosedIds = $sft->StfSuitableProduct->where('is_choice_user', SFTSuitableProduct::IS_CHOICE_USER_TRUE)->pluck('id')->toArray();

        $countDistinct = MProduct::whereIn('id', $suitProductChoosedIds)
            ->with('mDistinction:id,name', 'SftSuitableProduct')
            ->get()
            ->groupBy('mDistinction.name')->count();

        $pricePackage = $this->supportFirstTimeService->getPricePackService();
        $mailRegisterCert = $this->supportFirstTimeService->getSystemFee(MPriceList::AT_REGISTRATION, MPriceList::MAILING_CERTIFICATE_REGISTRATION);
        $periodRegistration = $this->supportFirstTimeService->getPeriodRegistrationService(MPriceList::REGISTRATION, MPriceList::REGISTRATION_UP_3_PRODS);
        $registerTermChange = $this->supportFirstTimeService->getPeriodRegistrationService(MPriceList::REGISTRATION, MPriceList::REGISTRATION_TERM_CHANGE);

        $setting = $this->supportFirstTimeService->getSetting();
        $feeSubmit = $this->supportFirstTimeService->getFeeSubmit();
        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_3, $sft->trademark->id);
        // Check payment with status draft
        $paymentDraft = $this->paymentService->findByCondition(['target_id' => $appTrademark ? $appTrademark->id : $sft->id, 'from_page' => U011B])->first();

        if ($paymentDraft) {
            // Get payer info by payment
            $payerInfo = $this->payerInfoService->findPayerWithPayment($paymentDraft);
        }
        $routeRegisterPrecheck = route('user.precheck.register-precheck', ['id' => $trademark['id']]);


        $routeCancel = $appTrademark ? route('user.apply-trademark.cancel-register', $appTrademark->id) : ''; //change before start

        $auth = Auth::user();
        $redirectEntry = [
            'quote' => SupportFirstTime::SENT_SESSION_TO_QUOTE,
            'anken_top' => SupportFirstTime::SENT_SESSION_TO_ANKEN_TOP,
            'u020b' => SupportFirstTime::TYPE_SUBMIT_SENT_SESSION,
            'draft' => SupportFirstTime::SAVE_DATA_NO_SENT_SESSION,
            'u021' => SupportFirstTime::SENT_SESSION_TO_U021,
            'u021c' => SupportFirstTime::SENT_SESSION_TO_U021C,
            'common_payment' => SupportFirstTime::REDIRECT_TO_COMMON_PAYMENT,
        ];

        $sftCommentType2 = $sft->stfComment->where('type', SFTComment::TYPE_COMMENT_CUSTOMER)->first();
        $statusUnregisteredSave = AppTrademark::STATUS_UNREGISTERED_SAVE;

        return view('user.modules.support_first_times.u011b', compact(
            'id',
            'sft',
            'auth',
            'data',
            'nations',
            'setting',
            'products',
            'payerInfo',
            'feeSubmit',
            'trademark',
            'prechecks',
            'paymentFee',
            'routeCancel',
            'prefectures',
            'appTrademark',
            'pricePackage',
            'countDistinct',
            'redirectEntry',
            'trademarkInfos',
            'trademarkTable',
            'sftCommentType2',
            'mailRegisterCert',
            'periodRegistration',
            'registerTermChange',
            'routeRegisterPrecheck',
            'statusUnregisteredSave',
        ));
    }

    /**
     * Create Session
     *
     * @param  mixed $request
     * @return void
     */
    public function createSession(Request $request)
    {
        if ($request->is_mailing_register_cert == 'on') {
            $request['is_mailing_register_cert'] = AppTrademark::IS_MAILING_REGIS_CERT_TRUE;
        } else {
            $request['is_mailing_register_cert'] = AppTrademark::IS_MAILING_REGIS_CERT_FAlSE;
        }
        if ($request->period_registration == 'on') {
            $request['period_registration'] = AppTrademark::PERIOD_REGISTRATION_TRUE;
        } else {
            $request['period_registration'] = AppTrademark::PERIOD_REGISTRATION_FALSE;
        }
        $params = $request->all();
        $sendSession = $this->supportFirstTimeService->sendSession($request);
        switch ($sendSession['redirect_to']) {
            case SupportFirstTime::REDIRECT_TO_ANKEN_TOP:
                $this->supportFirstTimeService->createPaymentSftService($request);
                return route('user.application-detail.index', ['id' => $request->trademark_id, 'from' => FROM_U000_TOP]);
            case SupportFirstTime::REDIRECT_TO_QUOTE:
                $this->supportFirstTimeService->createPaymentSftService($request);
                return route('user.quote', ['id' => $request->id, 's' => $sendSession['key_session']]);
            case SupportFirstTime::REDIRECT_TO_SUGGEST_AI:
                Session::put(SESSION_REFERER_SEARCH_AI, [
                    'referer' => FROM_SUPPORT_FIRST_TIME,
                    'support_first_time_id' => $request['id'],
                    'trademark_id' => $request['trademark_id'],
                ]);
                $productIds = [];
                foreach ($params as $key => $value) {
                    if (str_contains($key, 'is_choice_user_') && $value) {
                        $arrKey = explode('_', $key);
                        array_push($productIds, $arrKey[count($arrKey) - 1]);
                    }
                }
                Session::put(SESSION_SUGGEST_PRODUCT, $productIds);

                return route('user.search-ai.result');
            case SupportFirstTime::REDIRECT_TO_U021:
                return route('user.precheck.register-precheck', ['id' => $request['id'], 's' => $sendSession['key_session']]);
            case SupportFirstTime::REDIRECT_TO_U021C:
                return route('user.precheck.register-different-brand', ['id' => $request['id'], 's' => $sendSession['key_session']]);
            case SupportFirstTime::REDIRECT_TO_U031_PASS:
                return route('user.u031pass');
            case 'false_ajax':
                return ['error' => __('messages.common.errors.Common_E025')];
            default:
                break;
        }
    }

    /**
     * Create Payment For Sft
     *
     * @param  mixed $request
     * @return void
     */
    public function createPaymentSft(Request $request)
    {
        try {
            if ($request->is_mailing_register_cert == 'on') {
                $request['is_mailing_register_cert'] = AppTrademark::IS_MAILING_REGIS_CERT_TRUE;
            } else {
                $request['is_mailing_register_cert'] = AppTrademark::IS_MAILING_REGIS_CERT_FAlSE;
            }
            if ($request->period_registration == 'on') {
                $request['period_registration'] = AppTrademark::PERIOD_REGISTRATION_TRUE;
            } else {
                $request['period_registration'] = AppTrademark::PERIOD_REGISTRATION_FALSE;
            }

            $createPayment = $this->supportFirstTimeService->createPaymentSftService($request);

            switch ($request['redirect_to']) {
                case SupportFirstTime::REDIRECT_TO_COMMON_PAYMENT:
                    return redirect()->route('user.payment.index', ['s' => $createPayment['key_session'], 'sft_011' => 1]);
                case SupportFirstTime::REDIRECT_TO_QUOTE:
                    return redirect()->route('user.quote', ['id' => $createPayment['payment_id']]);
                case 'false':
                    return redirect()->back()->with('error', __('messages.common.errors.Common_E025'));
            }
        } catch (\Exception $e) {
            Log::error($e);
            $error = $e->getMessage();
            if ($error == 'PROD_REQUIRED') {
                return redirect()->back()->with('error', __('messages.common.errors.Common_E025'));
            }
        }
    }

    /**
     *  Index SFT Proposal AMS u011b_31
     *
     * @param Request $request
     * @param string|int $id - sft_id
     * @return \Illuminate\Http\Response
     */
    public function indexSFTProposalAMS(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('auth.login');
        }
        $sft = $this->supportFirstTimeService->getSupportFirstTime($id);
        if (!$sft) {
            abort(404);
        }
        $trademark = Trademark::find($sft->trademark_id)->load('appTrademark');

        if ($trademark->user_id != $user->id) {
            abort(403);
        }
        $payerInfo = null;
        $data['reference_number'] = $trademark->reference_number;
        $data['trademark_number'] = $trademark->trademark_number;
        $data['created_at'] = $trademark->created_at;
        $data['name_trademark'] = $trademark->name_trademark;
        $data['image_trademark'] = $trademark->image_trademark;
        $data['type_trademark'] = $trademark->type_trademark;
        $data['pack'] = $trademark->appTrademark->pack ?? AppTrademark::PACK_C;
        $appTrademark = $trademark->appTrademark;
        $products = $this->supportFirstTimeService->getDistinctionService($id);

        if (!$products->count()) {
            abort(403);
        }

        $fees = $this->supportFirstTimeService->getSystemFee(MPriceList::BEFORE_FILING, MPriceList::SFT_SELECT_SUPPORT);
        $paymentFee = $this->supportFirstTimeService->getSystemFee(MPriceList::EACH_PAYMENT, MPriceList::BANK_TRANSFER_HANDLING);
        $mailRegisterCert = $this->supportFirstTimeService->getSystemFee(MPriceList::AT_REGISTRATION, MPriceList::MAILING_CERTIFICATE_REGISTRATION);
        $sft = $this->supportFirstTimeService->getSupportFirstTime($id);
        $suitProductChoosedIds = $sft->StfSuitableProduct->where('is_choice_user', SFTSuitableProduct::IS_CHOICE_USER_TRUE)->pluck('id')->toArray();

        $countDistinct = MProduct::whereIn('id', $suitProductChoosedIds)
            ->with('mDistinction:id,name', 'SftSuitableProduct')
            ->get()
            ->groupBy('mDistinction.name')->count();

        $nations = $this->mNationService->listNationOptions();
        $prefectures = $this->mPrefectureService->listPrefectureOptions();
        $pricePackage = $this->supportFirstTimeService->getPricePackService();
        $serviceType = MPriceList::REGISTRATION;
        $packageType = MPriceList::REGISTRATION_UP_3_PRODS;
        $periodRegistration = $this->supportFirstTimeService->getPeriodRegistrationService($serviceType, $packageType);
        $setting = $this->supportFirstTimeService->getSetting();
        $feeSubmit = $this->supportFirstTimeService->getFeeSubmit();
        $registerTermChange = $this->supportFirstTimeService->getPeriodRegistrationService(MPriceList::REGISTRATION, MPriceList::REGISTRATION_TERM_CHANGE);
        $trademarkInfos = $appTrademark ? $this->trademarkInfoService->findTrademarkInfo($appTrademark) : null;
        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_3, $sft->trademark->id);
        // Check payment with status draft
        $paymentDraft = $this->paymentService->findByCondition(['target_id' => $appTrademark ? $appTrademark->id : $sft->id, 'from_page' => U011B_31])
            ->where('payment_status', '!=', Payment::STATUS_PAID)
            ->first();
        if ($paymentDraft) {
            // Get payer info by payment
            $payerInfo = $this->payerInfoService->findPayerWithPayment($paymentDraft);
        }

        $routeRegisterPrecheck = route('user.precheck.register-precheck', ['id' => $trademark['id']]);
        if ($appTrademark) {
            $routeCancel = route('user.apply-trademark.cancel-register', $appTrademark->id); //change before start
        } else {
            $routeCancel = '';
        }

        $redirectEntry = [
            'quote' => SupportFirstTime::REDIRECT_TO_QUOTE,
            'anken_top' => SupportFirstTime::REDIRECT_TO_ANKEN_TOP,
            'u020b' => SupportFirstTime::TYPE_SUBMIT_SENT_SESSION,
            'draft' => SupportFirstTime::SAVE_DATA_NO_SENT_SESSION,
            'u021' => SupportFirstTime::SENT_SESSION_TO_U021,
            'u021c' => SupportFirstTime::SENT_SESSION_TO_U021C,
            'common_payment' => SupportFirstTime::REDIRECT_TO_COMMON_PAYMENT,
        ];

        return view('user.modules.support_first_times.u011b_31', compact(
            'id',
            'fees',
            'data',
            'nations',
            'setting',
            'products',
            'feeSubmit',
            'paymentFee',
            'prefectures',
            'pricePackage',
            'trademarkInfos',
            'countDistinct',
            'mailRegisterCert',
            'periodRegistration',
            'registerTermChange',
            'trademarkTable',
            'appTrademark',
            'trademark',
            'payerInfo',
            'routeCancel',
            'routeRegisterPrecheck',
            'redirectEntry',
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $params = $request->all();
        $isShowTblProd = false;
        // Gonna common payment screen.
        $secretKey = Str::random(11);

        $data = $this->supportFirstTimeService->createSFTTrademark($params);
        // Create payer information
        $payerInfo = $this->payerInfoService->updateOrCreate([
            'target_id' => $data['sft']['id'],
            'type' => TYPE_SFT,
        ], [
            'target_id' => $data['sft']['id'],
            'payment_type' => $params['payment_type'] ?? null,
            'payer_type' => $params['payer_type'] ?? 0,
            'm_nation_id' => $params['m_nation_id'] ?? null,
            'payer_name' => $params['payer_name'] ?? '',
            'payer_name_furigana' => $params['payer_name_furigana'] ?? '',
            'postal_code' => $params['postal_code'] ?? null,
            'm_prefecture_id' => $params['m_prefecture_id'] ?? null,
            'address_second' => $params['address_second'] ?? '',
            'address_three' => $params['address_three'] ?? '',
            'type' => TYPE_SFT,
        ]);

        $quoteNumber = $this->paymentService->generateQIR($data['trademark']['trademark_number'], 'quote');
        $invoiceNumber = $this->paymentService->generateQIR('', 'invoice');
        $receiptNumber = $this->paymentService->generateQIR('', 'receipt');

        // Create payment with payment status is
        $dataSFT = [
            'target_id' => $data['sft']['id'],
            'payer_info_id' => $payerInfo->id,
            'cost_bank_transfer' => null,
            'subtotal' => $params['subtotal'] ?? 0,
            'commission' => $params['commission'] ?? 0,
            'tax' => $params['tax'] ?? 0,
            'quote_number' => $quoteNumber,
            'invoice_number' => $invoiceNumber,
            'receipt_number' => $receiptNumber,
            'cost_service_base' => floor($params['cost_service_base'] ?? 0),
            'total_amount' => $params['subtotal'] ?? 0,
            'payment_status' => $params['payment_status'] ?? Payment::STATUS_SAVE,
            'tax_withholding' => 0,
            'product_names' => $params['product_names'],
            'payment_amount' => 0,
            'type' => TYPE_SFT,
            'from_page' => U011,
        ];
        $dataSFT['trademark_id'] = $data['trademark']->id;
        $paymentFee = $this->supportFirstTimeService->getSystemFee(MPriceList::EACH_PAYMENT, MPriceList::BANK_TRANSFER_HANDLING);
        $trademark = $data['trademark'];
        $params['sft'] = $data['sft'];
        $params['trademark'] = $data['trademark'];
        $params['product_names'] = $params['product_names'];
        $params['trademark_id'] = $data['trademark']->id;
        $params['payer_info_id'] = $payerInfo->id;
        $params['image_url'] = FileHelper::uploads($request->image_trademark)[0];
        $params['type'] = TYPE_SFT;
        $params['from_page'] = U011;
        unset($params['image_trademark']);
        if ($params['payment_type'] == Payment::BANK_TRANSFER) {
            $dataSFT['cost_bank_transfer'] = $paymentFee['cost_service_base'] ?? 0;
        }
        if ($request->has('redirect') && $request->redirect) {
            $dataSFT['payment_status'] = Payment::STATUS_SAVE;
            // Go to quote
            $dataSFT['tax_withholding'] = floor($this->getTaxWithHolding($payerInfo, $params['subtotal']));
            $payment = $this->paymentService->createPaymentWithSFT($dataSFT);
            $trademark = $data['trademark'];
            if ($request->redirect == 'quote') {
                $params['payerInfo'] = $payerInfo;
                Session::put('quote_' . auth()->user()->id, $params);
                return redirect()->route('user.quote', ['id' => $payment->id]);
            }

            return redirect()->route('user.application-detail.index', ['id' => $trademark->id]);
        } else {
            $dataSFT['payment_status'] = Payment::STATUS_WAITING_PAYMENT;
            $dataSFT['trademark_id'] = $data['trademark']->id;
            $params['total_amount'] = $this->paymentService->calculateTotalAmount($params);
            $params['subtotal'] = $this->paymentService->calculateSubtotal($params);
            $params['payment_amount'] = $this->paymentService->calculatePaymentAmount($params);
            $dataSFT['subtotal'] = $params['subtotal'];
            $dataSFT['total_amount'] = $params['total_amount'];
            $dataSFT['tax_withholding'] = floor($this->getTaxWithHolding($payerInfo, $dataSFT['subtotal']));

            $payment = $this->paymentService->createPaymentWithSFT($dataSFT);

            $params['payment_id'] = $payment->id;
            $params['target_id'] = $payment->target_id;

            Session::put($secretKey, $params);

            return redirect()->route('user.payment.index', ['s' => $secretKey, 'sft_011' => 1]);
        }

        return view(
            'user.modules.common.payment',
            compact('isShowTblProd', 'payment', 'trademarkInfos', 'trademark')
        );
    }

    /**
     * Get tax withholding
     *
     * @param string $payerType
     * @param string $subTotal
     * @return string
     */
    public function getTaxWithHolding(PayerInfo $payerInfo, string $subTotal): string
    {
        $taxWithholdingPercent = 0;
        if ($payerInfo->payer_type == User::INFO_TYPE_ACC_GROUP && $payerInfo->m_nation_id == 1) {
            if ($subTotal > Payment::WITH_HOLDING_TAX_NUM) {
                $taxWithholdingPercent = Payment::WITH_HOLDING_TAX_MAX;
            } else {
                $taxWithholdingPercent = Payment::WITH_HOLDING_TAX_MIN;
            }
        }

        return $subTotal * $taxWithholdingPercent / 100;
    }
}
