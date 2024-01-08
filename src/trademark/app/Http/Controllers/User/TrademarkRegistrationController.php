<?php

namespace App\Http\Controllers\User;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\MailTemplate;
use App\Models\AppTrademark;
use App\Models\MPrefecture;
use App\Models\MPriceList;
use App\Models\Payment;
use App\Models\RegisterTrademark;
use App\Models\RegisterTrademarkProd;
use App\Models\Trademark;
use App\Models\TrademarkInfo;
use App\Services\AppTrademarkService;
use App\Models\TrademarkDocument;
use App\Services\MPriceListService;
use App\Services\MProductService;
use App\Notices\PaymentNotice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\MatchingResultService;
use App\Services\TrademarkService;
use App\Services\MNationService;
use App\Services\MPrefectureService;
use App\Services\TrademarkInfoService;
use App\Services\Common\TrademarkTableService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use App\Services\PaymentService;
use App\Services\TrademarkDocumentService;
use App\Services\RegisterTrademarkService;
use App\Services\AgentGroupService;
use App\Services\RegisterTrademarkProdService;
use App\Services\PayerInfoService;
use App\Services\MailTemplateService;
use App\Repositories\PaymentProductRepository;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TrademarkRegistrationController extends Controller
{
    protected MatchingResultService         $matchingResultService;
    protected TrademarkService              $trademarkService;
    protected PaymentService                $paymentService;
    protected MNationService                $mNationService;
    protected MPrefectureService            $mPrefectureService;
    protected TrademarkInfoService          $trademarkInfoService;
    protected TrademarkTableService         $trademarkTableService;
    protected AppTrademarkService           $appTrademarkService;
    protected TrademarkDocumentService      $trademarkDocumentService;
    protected RegisterTrademarkService      $registerTrademarkService;
    protected RegisterTrademarkProdService  $registerTrademarkProdService;
    protected PaymentProductRepository      $paymentProductRepository;
    protected PayerInfoService              $payerInfoService;
    protected MPriceListService             $mPriceListService;
    protected AgentGroupService             $agentGroupService;
    protected MProductService               $mProductService;
    protected MailTemplateService           $mailTemplateService;

    /**
     * Constructor
     *
     * @return  void
     */
    public function __construct(
        PaymentService  $paymentService,
        MatchingResultService $matchingResultService,
        TrademarkService $trademarkService,
        MNationService $mNationService,
        MPrefectureService $mPrefectureService,
        TrademarkInfoService $trademarkInfoService,
        TrademarkTableService $trademarkTableService,
        AppTrademarkService $appTrademarkService,
        RegisterTrademarkService $registerTrademarkService,
        RegisterTrademarkProdService  $registerTrademarkProdService,
        TrademarkDocumentService  $trademarkDocumentService,
        PayerInfoService  $payerInfoService,
        MProductService $mProductService,
        MPriceListService $mPriceListService,
        MailTemplateService $mailTemplateService,
        AgentGroupService  $agentGroupService
    )
    {
        $this->matchingResultService = $matchingResultService;
        $this->trademarkService = $trademarkService;
        $this->mNationService = $mNationService;
        $this->payerInfoService = $payerInfoService;
        $this->paymentService = $paymentService;
        $this->mPrefectureService = $mPrefectureService;
        $this->trademarkInfoService = $trademarkInfoService;
        $this->trademarkTableService = $trademarkTableService;
        $this->appTrademarkService = $appTrademarkService;
        $this->registerTrademarkService = $registerTrademarkService;
        $this->trademarkDocumentService = $trademarkDocumentService;
        $this->registerTrademarkProdService = $registerTrademarkProdService;
        $this->mProductService = $mProductService;
        $this->mPriceListService = $mPriceListService;
        $this->agentGroupService = $agentGroupService;
        $this->mailTemplateService = $mailTemplateService;
    }

    /**
     * Get list free history
     *
     * @param int $id
     * @return View
     */
    public function index(Request $request, int $id)
    {
        if (!$request->has('register_trademark_id') || !$request->register_trademark_id) {
            abort(CODE_ERROR_404);
        }
        $user = \Auth::user();
        $matchingResult = $this->matchingResultService->find($id);
        $matchingResult->load('trademark');
        $trademark = $matchingResult->trademark;
        if ($user->id != $trademark->user_id) {
            abort(CODE_ERROR_404);
        }

        $registerTrademark = $this->registerTrademarkService->find($request->register_trademark_id);

        if (!$registerTrademark || $trademark->id != $registerTrademark->trademark_id) {
            abort(CODE_ERROR_404);
        }

        $appTrademark = $trademark->appTrademark;
        $isBlockScreen = false;
        $nations = $this->mNationService->listNationOptions();
        $prefectures = $this->mPrefectureService->listPrefectureOptions();
        $overDate = Carbon::parse($matchingResult->pi_dd_date)->addDays(30)->timestamp;
        if (($registerTrademark && $registerTrademark->is_register == RegisterTrademark::IS_REGISTER)
            || $overDate < now()->timestamp
            || $appTrademark->is_cancel == AppTrademark::IS_CANCEL_TRUE) {
            $isBlockScreen = true;
        }

        $appTrademark->load('appTrademarkProd.mProduct.mDistinction');
        $registerTrademark->load('registerTrademarkProds.mProduct');
        $dataProds = isset($registerTrademark->registerTrademarkProds) && $registerTrademark->registerTrademarkProds->count()
            ? $registerTrademark->registerTrademarkProds
            : $appTrademark->appTrademarkProd;
        $productsDistinct = $dataProds->groupBy('mProduct.m_distinction_id')->sortKeys();

        $trademarkInfos = $this->trademarkInfoService->findByCondition(['target_id' => $appTrademark->id, 'type' => TYPE_APP_TRADEMARK])->orderBy('id', SORT_TYPE_DESC)->get();
        $payerInfo = $this->payerInfoService->findByCondition([
            'target_id' => $registerTrademark->id,
            'type' => TYPE_TRADEMARK_REGISTRATION
        ])->orderBy('id', SORT_TYPE_DESC)->first();

        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_4, $trademark->id, [
            U302 => true,
        ]);
        // hiding button submit and showing text
        $hideButtonSubmit = $appTrademark->pack == AppTrademark::PACK_A && now()->timestamp > $overDate;

        $setting = $this->matchingResultService->getSetting();
        $paymentFee = $this->matchingResultService->getSystemFee(MPriceList::EACH_PAYMENT, MPriceList::BANK_TRANSFER_HANDLING);
        $registerTermChange = $this->matchingResultService->getPeriodRegistrationRepository(MPriceList::REGISTRATION, MPriceList::REGISTRATION_TERM_CHANGE);
        $mailRegisterCert = $this->matchingResultService->getSystemFee(MPriceList::AT_REGISTRATION, MPriceList::MAILING_CERTIFICATE_REGISTRATION);
        $regisProcedureServiceFee = $this->matchingResultService->getPeriodRegistrationRepository(MPriceList::REGISTRATION, MPriceList::REGISTRATION_UP_3_PRODS);
        $productAddOnFee = $this->matchingResultService->getSystemFee(MPriceList::REGISTRATION, MPriceList::REGISTRATION_EACH_3_PRODS);
        $reduceNumberDistinctFee = $this->matchingResultService->getSystemFee(MPriceList::REGISTRATION, MPriceList::REGISTRATION_REDUCTION_PROCEDURE);
        $changeNameFee = $this->matchingResultService->getSystemFee(MPriceList::CHANGE_PROCEDURE, MPriceList::CHANGE_NAME_PROCEDURE);
        $changeAddressFee = $this->matchingResultService->getSystemFee(MPriceList::CHANGE_PROCEDURE, MPriceList::CHANGE_ADDRESS_PROCEDURE);
        // Waiting confirm
        $mailingRegisterCertFee = $mailRegisterCert['cost_service_base'];

        $trademarkDocuments = $this->trademarkDocumentService->findByCondition([
            'trademark_id' => $trademark->id,
            'type' => TrademarkDocument::TYPE_5
        ])->get()->map(function ($item) {
            $item->url = !empty($item->url) ? asset($item->url) : null;

            return $item;
        });

        return view('user.modules.registration.u302', compact(
            'setting',
            'nations',
            'payerInfo',
            'paymentFee',
            'prefectures',
            'appTrademark',
            'changeNameFee',
            'matchingResult',
            'trademarkInfos',
            'trademarkTable',
            'productAddOnFee',
            'changeAddressFee',
            'productsDistinct',
            'mailRegisterCert',
            'trademarkDocuments',
            'registerTermChange',
            'registerTrademark',
            'mailingRegisterCertFee',
            'reduceNumberDistinctFee',
            'regisProcedureServiceFee',
            'hideButtonSubmit',
            'isBlockScreen'
        ));
    }


    /**
     * Save data from Form request.
     */
    public function saveDataU302(Request $request, int $id)
    {
        try {
            DB::beginTransaction();
            if (!$request->has('register_trademark_id') && !$request->register_trademark_id) {
                abort(CODE_ERROR_404);
            }
            $params = $request->all();
            $matchingResult = $this->matchingResultService->find($id);
            $matchingResult->load('trademark.appTrademark.appTrademarkProd');
            $trademark = $matchingResult->trademark;
            $registerTrademark = $this->registerTrademarkService->find($request->register_trademark_id);
            if ($trademark->id != $registerTrademark->trademark_id || $registerTrademark->is_register == RegisterTrademark::IS_REGISTER) {
                abort(CODE_ERROR_404);
            }
            $appTrademark = $trademark->appTrademark;

            $paymentFee = $this->matchingResultService->getSystemFee(MPriceList::EACH_PAYMENT, MPriceList::BANK_TRANSFER_HANDLING);
            $registerTermChange = $this->matchingResultService->getPeriodRegistrationRepository(MPriceList::REGISTRATION, MPriceList::REGISTRATION_TERM_CHANGE);
            $mailRegisterCert = $this->matchingResultService->getSystemFee(MPriceList::AT_REGISTRATION, MPriceList::MAILING_CERTIFICATE_REGISTRATION);
            $regisProcedureServiceFee = $this->matchingResultService->getPeriodRegistrationRepository(MPriceList::REGISTRATION, MPriceList::REGISTRATION_UP_3_PRODS);
            $changeAddressFee = $this->matchingResultService->getSystemFee(MPriceList::CHANGE_PROCEDURE, MPriceList::CHANGE_ADDRESS_PROCEDURE);
            $changeNameFee = $this->matchingResultService->getSystemFee(MPriceList::CHANGE_PROCEDURE, MPriceList::CHANGE_NAME_PROCEDURE);

            $mailingRegisterCertFee = 0;
            if ($request->has('is_mailing_register_cert') && $request->is_mailing_register_cert) {
                $mailingRegisterCertFee = floor($mailRegisterCert['cost_service_base']);
            }
            $payerInfo = $this->payerInfoService->updateOrCreate([
                'target_id' => $registerTrademark->id,
                'type' => TYPE_TRADEMARK_REGISTRATION
            ], [
                'target_id' => $registerTrademark->id,
                'payment_type' => $request['payment_type'] ?? null,
                'payer_type' => $request['payer_type'] ?? 0,
                'm_nation_id' => $request['m_nation_id'] ?? 0,
                'payer_name' => $request['payer_name'] ?? '',
                'payer_name_furigana' => $request['payer_name_furigana'] ?? '',
                'postal_code' => $request['postal_code'] ?? null,
                'm_prefecture_id' => $request['m_prefecture_id'] ?? null,
                'address_second' => $request['address_second'] ?? '',
                'address_three' => $request['address_three'] ?? '',
                'type' => TYPE_TRADEMARK_REGISTRATION,
            ]);
            $dataSFT = [
                'cost_change_address' => null,
                'cost_change_name' => null,
                'from_page' => U302,
                'type' => TYPE_TRADEMARK_REGISTRATION,
                'tax_withholding' => 0,
                'payment_amount' => 0,
                'payment_status' => Payment::STATUS_SAVE,
                'cost_service_base' => floor($request['cost_service_base']) ?? 0,
                'cost_service_add_prod' => floor($request['cost_service_add_prod']) ?? 0,
                'subtotal' => floor($request['subtotal']) ?? 0,
                'tax' => floor($request['tax']) ?? 0,
                'commission' => floor($request['commission']) ?? 0,
                'total_amount' => floor($request['total_amount']) ?? 0,
                'cost_bank_transfer' => null,
                'payer_info_id' => $payerInfo->id,
                'trademark_id' => $trademark->id,
                'reduce_number_distitions' => $request->total_product_each_add ?? 0,
                'target_id' => $registerTrademark->id,

            ];
            $deadlineUpdate = null;
            $periodRegistrationFee = 0;
            if ($appTrademark->period_registration != $request->period_registration && $request->period_registration == 2) {
                if ($appTrademark->pack == AppTrademark::PACK_A) {
                    $periodRegistrationFee = $regisProcedureServiceFee['pof_1st_distinction_10yrs'] ?? 0;
                    $dataSFT['cost_5_year_one_distintion'] = $regisProcedureServiceFee['pof_1st_distinction_5yrs'];
                } else {
                    $periodRegistrationFee = $registerTermChange['pof_1st_distinction_10yrs'] ?? 0;
                    $dataSFT['cost_5_year_one_distintion'] = $registerTermChange['pof_1st_distinction_5yrs'] ?? 0;
                }

                $dataSFT['cost_10_year_one_distintion'] = $periodRegistrationFee;
                $deadlineUpdate = now()->addYears(10);
            } else {
                if ($appTrademark->pack == AppTrademark::PACK_A) {
                    $periodRegistrationFee = $regisProcedureServiceFee['pof_1st_distinction_5yrs'] ?? 0;
                } else {
                    $periodRegistrationFee = $registerTermChange['pof_1st_distinction_5yrs'] ?? 0;
                }
                $dataSFT['cost_5_year_one_distintion'] = $request->period_registration == 1 ? $periodRegistrationFee : 0;
                $dataSFT['cost_10_year_one_distintion'] = 0;
                $deadlineUpdate = now()->addYears(5);
            }
            $trademarkInfos = null;
            $dataTrademarkInfo = [];
            if ($request->has('change_trademark_info') && $request->change_trademark_info) {
                $trademarkInfos = $this->trademarkInfoService->find($request->trademark_info_id);
                $dataTrademarkInfo['trademark_info_id'] = $request->trademark_info_id;
                $addressFirst = $request->trademark_info_m_prefecture_id ?? '';
                if ($request->trademark_info_nation_id != NATION_JAPAN_ID) {
                    $mPrefectureId = MPrefecture::where('m_nation_id', $request->trademark_info_nation_id)->first()->id;
                    $addressFirst = $mPrefectureId;
                }
                switch (+$request->change_trademark_info) {
                    case TYPE_CHANGE_NAME:
                        $dataTrademarkInfo['cost_change_name'] = floor($changeNameFee['cost_service_base']) ?? 0;
                        $dataTrademarkInfo['cost_change_address'] = 0;
                        $dataTrademarkInfo['trademark_info_name'] = $request->trademark_info_name;
                        $dataTrademarkInfo['trademark_info_nation_id'] = $trademarkInfos->m_nation_id;
                        $dataTrademarkInfo['trademark_info_address_first'] = $request->trademark_info_nation_id == NATION_JAPAN_ID
                            ? $request->trademark_info_m_prefecture_id
                            : ($addressFirst ? $addressFirst : $trademarkInfos->m_prefecture_id);
                        $dataTrademarkInfo['trademark_info_address_second'] = $trademarkInfos->address_second ?? null;
                        $dataTrademarkInfo['trademark_info_address_three'] = null;
                        break;
                    case TYPE_CHANGE_ADDRESS:
                        $dataTrademarkInfo['cost_change_address'] = floor($changeAddressFee['cost_service_base']) ?? 0;
                        $dataTrademarkInfo['cost_change_name'] = 0;

                        $dataTrademarkInfo['trademark_info_name'] = $trademarkInfos->name;
                        $dataTrademarkInfo['trademark_info_nation_id'] = $request->trademark_info_nation_id;
                        $dataTrademarkInfo['trademark_info_address_first'] = $request->trademark_info_nation_id == NATION_JAPAN_ID ? $request->trademark_info_nation_id : $addressFirst;
                        $dataTrademarkInfo['trademark_info_address_second'] = $request->trademark_info_nation_id == NATION_JAPAN_ID ? $request->trademark_info_address_second : null;
                        $dataTrademarkInfo['trademark_info_address_three'] = $request->trademark_info_address_three ?? null;
                        break;
                    case TYPE_CHANG_DOUBLE:
                        $dataTrademarkInfo['cost_change_address'] = floor($changeAddressFee['cost_service_base']) ?? 0;
                        $dataTrademarkInfo['cost_change_name'] = floor($changeNameFee['cost_service_base']) ?? 0;

                        $dataTrademarkInfo['trademark_info_name'] = $request->trademark_info_name;
                        $dataTrademarkInfo['trademark_info_nation_id'] = $request->trademark_info_nation_id;
                        $dataTrademarkInfo['trademark_info_address_first'] = $request->trademark_info_nation_id == NATION_JAPAN_ID ? $request->trademark_info_nation_id : $addressFirst;
                        $dataTrademarkInfo['trademark_info_address_second'] = $request->trademark_info_nation_id == NATION_JAPAN_ID ? $request->trademark_info_address_second : null;
                        $dataTrademarkInfo['trademark_info_address_three'] = $request->trademark_info_address_three ?? null;
                        break;
                    default:
                        $dataTrademarkInfo['cost_change_name'] = 0;
                        $dataTrademarkInfo['cost_change_address'] = 0;
                        break;
                }
            } else {
                $trademarkInfos = $this->trademarkInfoService->findByCondition(['target_id' => $appTrademark->id, 'type' => TYPE_APP_TRADEMARK])->orderBy('id', SORT_TYPE_DESC)->first();
                $dataTrademarkInfo['trademark_info_nation_id'] = $trademarkInfos->m_nation_id;
                $dataTrademarkInfo['trademark_info_address_first'] = $request->trademark_info_nation_id == NATION_JAPAN_ID ? $trademarkInfos->m_prefecture_id : null;
                $dataTrademarkInfo['trademark_info_address_second'] = $request->trademark_info_nation_id == NATION_JAPAN_ID ? $trademarkInfos->address_second : null;
                $dataTrademarkInfo['trademark_info_address_three'] = $request->trademark_info_nation_id == NATION_JAPAN_ID ? $trademarkInfos->address_three : null;
                $dataTrademarkInfo['trademark_info_name'] = $trademarkInfos->name;
                $dataTrademarkInfo['trademark_info_id'] = $trademarkInfos->id;
            }

            $dataRegisterTrademark = array_merge([
                'period_registration' => $request->period_registration ?? 1,
                'period_registration_fee' => $periodRegistrationFee,
                'period_change_fee' => floor($request->period_change_fee) ?? 0,
                'reg_period_change_fee' => floor($request->reg_period_change_fee) ?? 0,
                'mailing_register_cert_fee' => floor($mailingRegisterCertFee),
                'regist_cert_nation_id' => $request->regist_cert_nation_id ?? null,
                'regist_cert_postal_code' => $request->regist_cert_postal_code ?? null,
                'regist_cert_address' => $request->regist_cert_address ?? null,
                'regist_cert_payer_name' => $request->regist_cert_payer_name ?? null,
                'trademark_info_change_status' => +$request->change_trademark_info ?? null,
                'info_type_acc' => $trademarkInfos->type_acc ?? null,
                'trademark_id' => $trademark->id,
                'deadline_update' => $deadlineUpdate
                ], $dataTrademarkInfo);
            $registerTrademark = $this->registerTrademarkService->updateOrCreate(
                ['id' => $request->register_trademark_id],
                $dataRegisterTrademark
            );

            if ($request['payment_type'] == Payment::BANK_TRANSFER) {
                $paymentFee = $this->trademarkInfoService->getSystemFee(MPriceList::EACH_PAYMENT, MPriceList::BANK_TRANSFER_HANDLING);
                $dataSFT['cost_bank_transfer'] = floor($paymentFee['cost_service_base']) ?? 0;
            }
            if ($request->has('is_mailing_register_cert') && $request->is_mailing_register_cert) {
                $mailRegisterCert = $this->trademarkInfoService->getSystemFee(MPriceList::AT_REGISTRATION, MPriceList::MAILING_CERTIFICATE_REGISTRATION);
                $dataSFT['cost_registration_certificate'] = floor($mailRegisterCert['cost_service_base']);
            }

            $dataSFT['tax_withholding'] = floor($this->paymentService->getTaxWithHolding($payerInfo, $dataSFT['subtotal']));
            $dataSFT['payment_amount'] = floor($dataSFT['total_amount'] - $dataSFT['tax_withholding']);

            $dataUpdate = array_merge($dataSFT, $dataTrademarkInfo);
            $payment = $this->paymentService->updateOrCreate(
                ['target_id' => $dataUpdate['target_id'], 'from_page' => $dataUpdate['from_page']],
                $dataUpdate
            );

            $dataUpdatePayment = [];
            if (!$payment->quote_number) {
                $dataUpdatePayment['quote_number'] = $this->paymentService->generateQIR($trademark->trademark_number, 'quote');
            }
            if (!$payment->invoice_number) {
                $dataUpdatePayment['invoice_number'] = $this->paymentService->generateQIR('', 'invoice');
            }
            if (!$payment->invoice_number) {
                $dataUpdatePayment['receipt_number'] = $this->paymentService->generateQIR('', 'receipt');
            }

            $appTrademark->load('appTrademarkProd.mProduct');
            $productsDistinct = $appTrademark->appTrademarkProd->groupBy('mProduct.m_distinction_id');

            $distinctApply = $request->distinct_apply;
            if (isset($request->distinct_apply) && $request->distinct_apply) {
                foreach ($request->distinct_apply as $key => $value) {
                    if (isset($productsDistinct[$key])) {
                        $distinctApply[$key] = $productsDistinct[$key];
                    }
                }
            } else {
                $distinctApply = [];
            }

            $dataUpdatePayment['reduce_distinctions'] = $productsDistinct->count() - count($distinctApply);
            $dataUpdatePayment['reduce_number_distitions'] = count($distinctApply);
            $dataUpdatePayment['payment_date'] = now();
            $payment->update($dataUpdatePayment);

            $productIds = collect($distinctApply)->flatten()->pluck('m_product_id')->toArray();

            $dataPaymentPro = [];
            $dataPaymentPro['payment_id'] = $payment->id;
            $dataPaymentPro['productIds'] = $productIds;
            $params['payment_id'] = $payment->id;
            $params['productIds'] = $productIds;
            $appTrademarkProds = $appTrademark->appTrademarkProd;
            foreach ($appTrademarkProds as $key => $appProd) {
                $this->registerTrademarkProdService->updateOrCreate([
                    'register_trademark_id' => $registerTrademark->id,
                    'app_trademark_prod_id' => $appProd->id,
                    'm_product_id' => $appProd->m_product_id,
                ], [
                    'register_trademark_id' => $registerTrademark->id,
                    'app_trademark_prod_id' => $appProd->id,
                    'm_product_id' => $appProd->m_product_id,
                    'is_apply' => isset($distinctApply[$appProd->mProduct->m_distinction_id]) ? true : false,
                ]);
            }

            $this->paymentService->createPaymentProds($dataPaymentPro);
            DB::commit();

            switch ($request->redirect_to) {
                case COMMON_PAYMENT:
                    $key = Str::random(11);
                    $request->session()->put($key, $params);

                    return redirect()->route('user.payment.index', ['s' => $key]);
                    break;
                case QUOTE:
                    return redirect()->route('user.quote', ['id' => $payment->id]);
                    break;
                case U000ANKEN_TOP:
                    CommonHelper::setMessage($request, MESSAGE_SUCCESS, __('messages.general.Common_E047'));

                    return redirect()->route('user.application-detail.index', ['id' => $trademark->id]);
                    break;
                default:
                    break;
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
        }
    }

    /**
     * Show Cancel
     *
     * @param  int $id
     * @return view
     */
    public function showCancel($id): View
    {
        $registerTrademark = $this->registerTrademarkService->find($id);
        if (!$registerTrademark) {
            abort(CODE_ERROR_404);
        }
        $isBlockScreen = false;
        if ($registerTrademark->is_cancel == RegisterTrademark::IS_CANCEL) {
            $isBlockScreen = true;
        }

        return view('user.modules.registration.u302cancel', compact('registerTrademark', 'isBlockScreen'));
    }

    /**
     * Update Cancel
     *
     * @param  int $id
     * @return RedirectResponse
     */
    public function updateCancel($id): RedirectResponse
    {
        $user = Auth::user();
        $registerTrademark = $this->registerTrademarkService->find($id);
        $registerTrademark->update(['is_cancel' => AppTrademark::IS_CANCEL_TRUE]);
        $registerTrademark->load('trademark');
        $paymentNotice = App::make(PaymentNotice::class);
        $paymentNotice->setTrademark($registerTrademark->trademark);
        $paymentNotice->setCurrentUser($user);
        $paymentNotice->setData([]);

        $paymentNotice->noticeU302ACancel($id);
        $dataSendMail = [
            'user' => $user,
            'from_page' => U302CANCEL,
        ];
        // Send mail 拒絶理由通知書：対応不要
        $this->mailTemplateService->sendMailRequest($dataSendMail, MailTemplate::TYPE_OTHER);

        return redirect()->route('user.top')->with('messages', __('messages.general.update_success'));
    }

    /**
     * View page u303
     *
     * @param  int $id
     *  @param  Request $request
     * @return View
     */
    public function documentCompleted(Request $request, int $id): View
    {

        $trademark = $this->trademarkService->find($id);
        if (!$trademark) {
            abort(CODE_ERROR_404);
        }
        $userLogin = Auth::user();
        if ($userLogin->id != $trademark->user_id) {
            abort(CODE_ERROR_404);
        }
        $registerTrademark = null;

        $mDistincts = collect([]);
        $redirectTo = null;

        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_4, $trademark->id, [
            U303 => true,
        ]);
        $registerTrademark = $this->registerTrademarkService->findByCondition([
            'id' => $request['register_trademark_id'],
            'trademark_id' => $trademark->id,
        ])->first();
        if (!$registerTrademark) {
            abort(404);
        }

        $trademarkDocumentType6 = $this->trademarkDocumentService->findByCondition([
            'trademark_id' => $trademark->id,
            'type' => TrademarkDocument::TYPE_6
        ])->get();

        $trademarkDocumentType2 = $this->trademarkDocumentService->findByCondition([
            'trademark_id' => $trademark->id,
            'type' => TrademarkDocument::TYPE_2
        ])->get();

        $trademarkDocumentType7 = $this->trademarkDocumentService->findByCondition([
            'trademark_id' => $trademark->id,
            'type' => TrademarkDocument::TYPE_7
        ])->get();

        if (isset($registerTrademark) && $registerTrademark) {
            $registerTrademark->load('registerTrademarkProds');
            $registerTrademarkProds = $registerTrademark->registerTrademarkProds->where('is_apply', true);
            $mProductIds = $registerTrademarkProds->pluck('m_product_id')->toArray();
            $registerTrademarkProdIds = $registerTrademarkProds->pluck('id')->toArray();
            $dataListProducts = $this->mProductService->getProductAppTrademark($mProductIds, $registerTrademarkProdIds);

            // Hidden 補正書PDF when count(register_trademark_prods[is_apply = 0]) = 0
            $registerTrademarkProdNotApply = $registerTrademark->registerTrademarkProds->where('is_apply', false);
            if (count($registerTrademarkProdNotApply) == 0) {
                $trademarkDocumentType2 = collect([]);
            }
        }

        return view('user.modules.registration.u303', compact(
            'redirectTo',
            'id',
            'registerTrademark',
            'trademarkTable',
            'dataListProducts',
            'trademarkDocumentType6',
            'trademarkDocumentType2',
            'trademarkDocumentType7',
            'trademark'
        ));
    }

    /**
     * Post page u303
     *
     * @param  int $id
     *  @param  Request $request
     * @return RedirectResponse
     */
    public function postDocumentCompleted($id, Request $request)
    {
        $trademark = $this->trademarkService->find($id);
        if (!$trademark) {
            abort(CODE_ERROR_404);
        }
        $registerTrademark = null;

        $mDistincts = collect([]);
        $redirectTo = null;

        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_ADMIN_4, $trademark->id, [
            SHOW_LINK_ANKEN_TOP => true,
        ]);

        $registerTrademark = $this->registerTrademarkService->findByCondition([
            'id' => $request['register_trademark_id'],
            'trademark_id' => $trademark->id,
        ])->first();
        if (!$registerTrademark) {
            abort(404);
        }

        if (isset($registerTrademark) && $registerTrademark) {
            $registerTrademark->load('registerTrademarkProds');
            $mProductIds = $registerTrademark->registerTrademarkProds->pluck('m_product_id')->toArray();
            $registerTrademarkProdIds = $registerTrademark->registerTrademarkProds->pluck('id')->toArray();
            $dataListProducts = $this->mProductService->getProductAppTrademark($mProductIds, $registerTrademarkProdIds);
        }
        $params['trademark_id'] = $id;
        $params['register_trademark_id'] = $registerTrademark->id;
        $key = Str::random(11);
        if ($request->submit == U031C) {
            $params['products'] = $mProductIds;
            $request->session()->put($key, $params);

            return redirect()->route('user.apply-trademark-with-product-copied', ['s' => $key]);
        } elseif ($request->submit == U031D) {
            $request->session()->put($key, $params);

            return redirect()->route('user.apply-trademark-without-number', ['s' => $key]);
        } elseif ($request->submit == U021) {
            $params['m_product_ids'] = $mProductIds;
            $request->session()->put($key, $params);
            $trademarkNew = $this->trademarkService->createTrademark([
                'type_trademark' => Trademark::TRADEMARK_TYPE_LETTER,
                'name_trademark' => $trademark->name_trademark,
                'image_trademark' => null,
            ]);
            return redirect()->route('user.precheck.register-precheck', ['id' => $trademarkNew->id, 's' => $key]);
        }
    }

    /**
     * View page u304
     *
     * @param  int $id
     *  @param  Request $request
     * @return View
     */
    public function notifyNumber(Request $request, int $id): View
    {
        $trademark = $this->trademarkService->find($id);
        if (!$trademark) {
            abort(CODE_ERROR_404);
        }
        $userLogin = Auth::user();
        if ($userLogin->id != $trademark->user_id) {
            abort(CODE_ERROR_404);
        }
        $registerTrademark = null;

        $mDistincts = collect([]);
        $redirectTo = null;

        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_6, $trademark->id, [
            U303 => true,
        ]);

        $registerTrademark = $this->registerTrademarkService->findByCondition([
            'id' => $request['register_trademark_id'],
            'trademark_id' => $trademark->id,
        ])->first();
        if (!$registerTrademark) {
            abort(404);
        }
        if ($registerTrademark->period_registration == RegisterTrademark::PERIOD_REGISTRATION_5_YEAR) {
            $registerTrademark->term_of_benefits = Carbon::createFromFormat('Y-m-d', $registerTrademark->date_register)->addYears(5)->toDateString();
        } else {
            $registerTrademark->term_of_benefits = Carbon::createFromFormat('Y-m-d', $registerTrademark->date_register)->addYears(10)->toDateString();
        }
        $trademarkDocumentType8 = $this->trademarkDocumentService->findByCondition([
            'trademark_id' => $trademark->id,
            'type' => TrademarkDocument::TYPE_8
        ])->get();

        if (isset($registerTrademark) && $registerTrademark) {
            $registerTrademark->load('registerTrademarkProds');
            $mProductIds = $registerTrademark->registerTrademarkProds->pluck('m_product_id')->toArray();
            $registerTrademarkProdIds = $registerTrademark->registerTrademarkProds->pluck('id')->toArray();
            $dataListProducts = $this->mProductService->getProductAppTrademarkIsApplyTrue($mProductIds, $registerTrademarkProdIds);
        }

        return view('user.modules.registration.u304', compact(
            'redirectTo',
            'id',
            'registerTrademark',
            'trademarkTable',
            'dataListProducts',
            'trademarkDocumentType8',
            'trademark'
        ));
    }

    /**
     * Post page u303
     *
     * @param  int $id
     *  @param  Request $request
     * @return RedirectResponse
     */
    public function postNotifyNumber($id, Request $request)
    {
        $trademark = $this->trademarkService->find($id);
        if (!$trademark) {
            abort(CODE_ERROR_404);
        }
        $registerTrademark = null;

        $mDistincts = collect([]);
        $redirectTo = null;

        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_ADMIN_4, $trademark->id, [
            SHOW_LINK_ANKEN_TOP => true,
        ]);

        $registerTrademark = $this->registerTrademarkService->findByCondition([
            'id' => $request['register_trademark_id'],
            'trademark_id' => $trademark->id,
        ])->first();
        if (!$registerTrademark) {
            abort(404);
        }

        if (isset($registerTrademark) && $registerTrademark) {
            $registerTrademark->load('registerTrademarkProds');
            $mProductIds = $registerTrademark->registerTrademarkProds->pluck('m_product_id')->toArray();
            $registerTrademarkProdIds = $registerTrademark->registerTrademarkProds->pluck('id')->toArray();
            $dataListProducts = $this->mProductService->getProductAppTrademarkIsApplyTrue($mProductIds, $registerTrademarkProdIds);
        }
        $params['trademark_id'] = $id;
        $params['from_page'] = U304;
        $key = Str::random(11);
        if ($request->submit == U031C) {
            $params['products'] = $dataListProducts;
            $request->session()->put($key, $params);

            return redirect()->route('user.apply-trademark-with-product-copied', ['s' => $key]);
        } elseif ($request->submit == U031D) {
            $request->session()->put($key, $params);

            return redirect()->route('user.apply-trademark-without-number', ['s' => $key]);
        } elseif ($request->submit == U021) {
            $params['m_product_ids'] = $mProductIds;
            $request->session()->put($key, $params);
            $trademarkNew = $this->trademarkService->createTrademark([
                'type_trademark' => Trademark::TRADEMARK_TYPE_LETTER,
                'name_trademark' => $trademark->name_trademark,
                'image_trademark' => null,
            ]);
            return redirect()->route('user.precheck.register-precheck', ['id' => $trademarkNew->id, 's' => $key]);
        }
    }

    /**
     * View page u302_402_5yr_kouki
     *
     * @param  int $id
     * @param  Request $request
     * @return View
     */
    public function notifyLatterPeriod(Request $request, int $id): View
    {
        $registerTrademark = $this->registerTrademarkService->find($id);
        if (empty($registerTrademark)
            || $registerTrademark->is_register != RegisterTrademark::IS_REGISTER
            && $registerTrademark->period_registration != RegisterTrademark::PERIOD_REGISTRATION_5_YEAR) {
            abort(CODE_ERROR_404);
        }
        $currentUser = Auth::guard('web')->user();
        $trademark = $registerTrademark->trademark ?? null;
        if (empty($trademark) || $trademark->user_id != $currentUser->id) {
            abort(CODE_ERROR_404);
        }

        $trademark->load('registerTrademarks');

        // Check access page with deadline_update
        $dateUpdate = $registerTrademark->deadline_update;
        $dateUpdateCurrent = Carbon::parse($dateUpdate);
        $dateUpdateBefore6Month = Carbon::parse($dateUpdate)->subMonth(6)->startOfDay();
        $dateUpdateNext6Month = Carbon::parse($dateUpdate)->addMonth(6)->subDay(1)->endOfDay();

        if (!($dateUpdateBefore6Month <= now())) {
            abort(CODE_ERROR_404);
        }

        // register_trademarks.deadline_update + 6 months - 1 day <= now() => disable all
        // Block when now > dateRegisterNext6Month
        $isBlock = false;
        if (now() > $dateUpdateNext6Month) {
            $isBlock = true;
        }

        // And register_trademarks.deadline_update + 6 months - 5 day then block only payment_type bank_transfer
        $isDisableBankTransfer = false;
        if (now() > Carbon::parse($dateUpdate)->subDay(5)
            && now() < Carbon::parse($dateUpdate)) {
            $isDisableBankTransfer = true;
        }

        // Block when has next registerTrademarks
        $registerTrademarks = $trademark->registerTrademarks ?? collect([]);
        $nextRegisterTrademark = $registerTrademarks->where('id', '>', $registerTrademark->id)->first();
        if (!empty($nextRegisterTrademark) && (
                $nextRegisterTrademark->is_register == RegisterTrademark::IS_REGISTER
                || $nextRegisterTrademark->is_cancel == RegisterTrademark::IS_CANCEL
            )) {
            $isBlock = true;
        }
        $registerTrademarks = $registerTrademarks->where('id', '<=', $registerTrademark->id)
            ->where('is_register', RegisterTrademark::IS_REGISTER);

        // Trademark Table
        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_5, $trademark->id);

        // Get RegisterTrademarkProds
        $registerTrademarkProds = $registerTrademark->registerTrademarkProds ?? collect();
        $registerTrademarkProds->load('appTrademarkProd.mProduct.mDistinction');
        $registerTrademarkProds = $registerTrademarkProds->groupBy('appTrademarkProd.mProduct.mDistinction.name')->sortKeys();

        $typeChangeName = TrademarkInfo::TYPE_CHANGE_NAME;
        $typeChangeAddress = TrademarkInfo::TYPE_CHANGE_ADDRESS;
        $typeChangeDouble = TrademarkInfo::TYPE_CHANGE_NAME_AND_ADDRESS;

        // Price Base
        $priceService = $this->mPriceListService->getPriceCommonOfPrecheck(MPriceList::LATE_PAYMENT_RENEWAL, MPriceList::PAYMENT_SERVICE_UP_3_CATEGORY);
        $priceServiceFee = $this->mPriceListService->getPriceIncludesTax(MPriceList::LATE_PAYMENT_RENEWAL, MPriceList::PAYMENT_SERVICE_UP_3_CATEGORY);

        $priceServiceAddProd = $this->mPriceListService->getPriceCommonOfPrecheck(MPriceList::LATE_PAYMENT_REGISTRATION, MPriceList::PAYMENT_EACH_1_CATEGORY);
        $priceServiceAddProdFee = $this->mPriceListService->getPriceIncludesTax(MPriceList::LATE_PAYMENT_REGISTRATION, MPriceList::PAYMENT_EACH_1_CATEGORY);

        $priceServiceChangeAddress = $this->mPriceListService->getPriceCommonOfPrecheck(MPriceList::CHANGE_PROCEDURE, MPriceList::CHANGE_ADDRESS_PROCEDURE);
        $priceServiceChangeAddressFee = $this->mPriceListService->getPriceIncludesTax(MPriceList::CHANGE_PROCEDURE, MPriceList::CHANGE_ADDRESS_PROCEDURE);

        $priceServiceChangeName = $this->mPriceListService->getPriceCommonOfPrecheck(MPriceList::CHANGE_PROCEDURE, MPriceList::CHANGE_NAME_PROCEDURE);
        $priceServiceChangeNameFee = $this->mPriceListService->getPriceIncludesTax(MPriceList::CHANGE_PROCEDURE, MPriceList::CHANGE_NAME_PROCEDURE);

        $priceBankTransfer = $this->mPriceListService->getPriceCommonOfPrecheck(MPriceList::EACH_PAYMENT, MPriceList::BANK_TRANSFER_HANDLING);
        $priceBankTransferFee = $this->mPriceListService->getPriceIncludesTax(MPriceList::EACH_PAYMENT, MPriceList::BANK_TRANSFER_HANDLING);

        // Payment
        $payment = $this->paymentService->findByCondition([
            'type' => Payment::TYPE_LATE_PAYMENT,
            'target_id' => $nextRegisterTrademark->id ?? 0,
        ])->with(['payerInfo'])->first();

        $priceData = [
            'priceService' => $priceService,
            'priceServiceFee' => $priceServiceFee,
            'priceServiceAddProd' => $priceServiceAddProd,
            'priceServiceAddProdFee' => $priceServiceAddProdFee,
            'priceServiceChangeAddress' => $priceServiceChangeAddress,
            'priceServiceChangeAddressFee' => $priceServiceChangeAddressFee,
            'priceServiceChangeName' => $priceServiceChangeName,
            'priceServiceChangeNameFee' => $priceServiceChangeNameFee,
            'priceBankTransfer' => $priceBankTransfer,
            'priceBankTransferFee' => $priceBankTransferFee,
        ];

        // Get Other Data
        $setting = $this->mPriceListService->getSetting();
        $nations = $this->mNationService->listNationOptions();
        $prefectures = $this->mPrefectureService->listPrefectureOptions();

        // Check pack C
        $appTrademark = $trademark->appTrademark;
        $isPackC = false;
        if ($appTrademark->pack == AppTrademark::PACK_C) {
            $isPackC = true;
        }

        return view('user.modules.registration.u302_402_5yr_kouki', compact(
            'registerTrademark',
            'nextRegisterTrademark',
            'trademark',
            'trademarkTable',
            'registerTrademarkProds',
            'registerTrademarks',
            'isBlock',
            'isDisableBankTransfer',
            'isPackC',
            'dateUpdateCurrent',
            'dateUpdateBefore6Month',
            'dateUpdateNext6Month',
            'typeChangeName',
            'typeChangeAddress',
            'typeChangeDouble',
            'priceData',
            'setting',
            'nations',
            'prefectures',
            'payment',
        ));
    }

    /**
     * Post u302_402_5yr_kouki
     *
     * @param  Request $request
     * @param  int $id
     * @return RedirectResponse
     */
    public function postNotifyLatterPeriod(Request $request, int $id): RedirectResponse
    {
        $registerTrademark = $this->registerTrademarkService->find($id);
        if (empty($registerTrademark)
            || $registerTrademark->is_register != RegisterTrademark::IS_REGISTER
            && $registerTrademark->period_registration != RegisterTrademark::PERIOD_REGISTRATION_5_YEAR) {
            abort(CODE_ERROR_404);
        }

        $currentUser = Auth::guard('web')->user();
        $trademark = $registerTrademark->trademark ?? null;
        if (empty($trademark) || $trademark->user_id != $currentUser->id) {
            abort(CODE_ERROR_404);
        }
        $trademark->load('registerTrademarks');

        try {
            DB::beginTransaction();

            $params = $request->all();

            $fromPage = $params['from_page'] ?? null;

            $registerTrademarkIsRegister = $trademark->registerTrademarks->where('is_register', RegisterTrademark::IS_REGISTER);

            // Set Trademark Info Data
            $typeChange = $params['type_change'] ?? null;
            if (empty($typeChange)) {
                $trademarkInfoNationId = $registerTrademark->trademark_info_nation_id ?? null;
                $trademarkInfoAddressFirst = $registerTrademark->trademark_info_address_first ?? null;
                $trademarkInfoAddressSecond = $registerTrademark->trademark_info_address_second ?? null;
                $trademarkInfoAddressThree = $registerTrademark->trademark_info_address_three ?? null;
                $trademarkInfoName = $registerTrademark->trademark_info_name ?? null;
            } else {
                $trademarkInfoNationId = $params['trademark_info_nation_id'] ?? null;
                $trademarkInfoAddressFirst = $params['trademark_info_address_first'] ?? null;
                $trademarkInfoAddressSecond = $params['trademark_info_address_second'] ?? null;
                $trademarkInfoAddressThree = $params['trademark_info_address_three'] ?? null;
                $trademarkInfoName = $params['trademark_info_name'] ?? null;

                if ($typeChange == TrademarkInfo::TYPE_CHANGE_NAME) {
                    $trademarkInfoNationId = null;
                    $trademarkInfoAddressFirst = null;
                    $trademarkInfoAddressSecond = null;
                    $trademarkInfoAddressThree = null;
                } elseif ($typeChange == TrademarkInfo::TYPE_CHANGE_ADDRESS) {
                    $trademarkInfoName = null;
                }
            }

            if ($trademarkInfoNationId != NATION_JAPAN_ID) {
                $trademarkInfoAddressFirst = null;
                $trademarkInfoAddressSecond = null;
            }

            if ($fromPage == U302_402TSUINO_5YR_KOUKI) {
                $type = U302_402TSUINO . '_' . count($registerTrademarkIsRegister);
            } else {
                $type = U302_402 . '_' . count($registerTrademarkIsRegister);
            }

            $dataRegisterTrademark = [
                'trademark_id' => $trademark->id,
                'trademark_info_id' => 0,
                'id_register_trademark_choice' => $params['id_register_trademark_choice'] ?? null,
                'admin_id' => Admin::first()->id,
                'period_registration' => RegisterTrademark::PERIOD_REGISTRATION_5_YEAR,
                'period_registration_fee' => 0,
                'trademark_info_change_status' => null,
                'trademark_info_change_fee' => 0,
                'info_type_acc' => $params['info_type_acc'] ?? $registerTrademark->info_type_acc ?? null,
                'trademark_info_nation_id' => $trademarkInfoNationId,
                'trademark_info_address_first' => $trademarkInfoAddressFirst,
                'trademark_info_address_second' => $trademarkInfoAddressSecond,
                'trademark_info_address_three' => $trademarkInfoAddressThree,
                'is_change_address_free' => isset($params['is_change_address_free']),
                'trademark_info_name' => $trademarkInfoName,
                'option' => '',
                'is_payment' => false,
                'is_register' => RegisterTrademark::IS_NOT_REGISTER,
                'agent_group_id' => $registerTrademark->agent_group_id,
                'display_info_status' => '',
                'date_register' => now(),
                'register_number' => $registerTrademark->register_number,
                'deadline_update' => now()->addYear(5),
                'representative_name' => $params['representative_name'] ?? null,
                'type' => $type,
                'type_page' => RegisterTrademark::TYPE_PAGE_2,
            ];

            $newRegisterTrademarkId = $params['new_register_trademark'] ?? null;
            if (isset($newRegisterTrademarkId)) {
                $newRegisterTrademark = $this->registerTrademarkService->find($newRegisterTrademarkId);
                $newRegisterTrademark->update($dataRegisterTrademark);
            } else {
                $newRegisterTrademark = $this->registerTrademarkService->create($dataRegisterTrademark);
            }

            // Payment
            $payment = $this->paymentService->findByCondition([
                'type' => Payment::TYPE_LATE_PAYMENT,
                'target_id' => $newRegisterTrademark->id ?? 0,
            ])->with(['payerInfo'])->first();

            $dataPayerInfo = [
                'target_id' => $newRegisterTrademark->id,
                'payment_type' => $params['payment_type'] ?? null,
                'payer_type' => $params['payer_type'] ?? 0,
                'm_nation_id' => $params['m_nation_id'] ?? 0,
                'payer_name' => $params['payer_name'] ?? '',
                'payer_name_furigana' => $params['payer_name_furigana'] ?? '',
                'postal_code' => $params['postal_code'] ?? null,
                'm_prefecture_id' => $params['m_prefecture_id'] ?? null,
                'address_second' => $params['address_second'] ?? '',
                'address_three' => $params['address_three'] ?? '',
                'type' => TYPE_NOTIFY_DEADLINE_PAYMENT,
            ];
            $paymentData = $params['payment'] ?? [];
            $paymentData['trademark_id'] = $trademark->id;

            if ($fromPage == U302_402TSUINO_5YR_KOUKI) {
                $paymentData['from_page'] = U302_402TSUINO_5YR_KOUKI . '_' . count($registerTrademarkIsRegister);
            } else {
                $paymentData['from_page'] = U302_402_5YR_KOUKI . '_' . count($registerTrademarkIsRegister);
            }

            $setting = $this->mPriceListService->getSetting();
            $paymentData['tax_incidence'] = $setting->value;

            $nationId = $params['m_nation_id'] ?? null;
            if ($nationId != NATION_JAPAN_ID) {
                $paymentData['tax_incidence'] = 0;
                $paymentData['tax'] = 0;
                $paymentData['commission'] = 0;
            }

            if (empty($payment)) {
                $payerInfo = $this->payerInfoService->create($dataPayerInfo);

                $paymentData['payer_info_id'] = $payerInfo->id;
                $paymentData['type'] = Payment::TYPE_LATE_PAYMENT;
                $paymentData['target_id'] = $newRegisterTrademark->id;

                $paymentData['quote_number'] = $this->paymentService->generateQIR($trademark->trademark_number, 'quote');
                $paymentData['invoice_number'] = $this->paymentService->generateQIR('', 'invoice');
                $paymentData['receipt_number'] = $this->paymentService->generateQIR('', 'receipt');

                $paymentData['tax_withholding'] = $this->paymentService->getTaxWithHolding($payerInfo, $paymentData['total_amount']);
                $paymentData['payment_amount'] = $paymentData['total_amount'] - $paymentData['tax_withholding'];

                $payment = $this->paymentService->create($paymentData);
            } else {
                $payerInfo = $payment->payerInfo;
                $payerInfo->update($dataPayerInfo);

                $paymentData['tax_withholding'] = $this->paymentService->getTaxWithHolding($payerInfo, $paymentData['total_amount']);
                $paymentData['payment_amount'] = $paymentData['total_amount'] - $paymentData['tax_withholding'];

                $payment->update($paymentData);
            }

            // Register Trademark Prods/Payment Prods
            $registerTrademarkProds = $registerTrademark->registerTrademarkProds ?? collect();
            $registerTrademarkProds = $registerTrademarkProds->load('appTrademarkProd.mProduct');
            $productIds = [];
            foreach ($registerTrademarkProds as $registerTrademarkProd) {
                // Create/Update Payment Prods
                $productIds[] = $registerTrademarkProd->appTrademarkProd->m_product_id;

                $registerTrademarkProdData['register_trademark_id'] = $newRegisterTrademark->id;
                $registerTrademarkProdData['app_trademark_prod_id'] = $registerTrademarkProd->app_trademark_prod_id;
                $this->registerTrademarkProdService->updateOrCreate($registerTrademarkProdData, [
                    'is_apply' => RegisterTrademarkProd::IS_APPLY,
                    'm_product_id' => $registerTrademarkProd->appTrademarkProd->m_product_id,
                ]);
            }
            $dataPaymentPro['payment_id'] = $payment->id;
            $dataPaymentPro['productIds'] = $productIds;
            $this->paymentService->createPaymentProds($dataPaymentPro);

            DB::commit();

            $submitType = $params['submit_type'] ?? null;
            switch ($submitType) {
                case REDIRECT_TO_COMMON_PAYMENT:
                    $key = Str::random(11);
                    $paymentData['payment_id'] = $payment->id;
                    $paymentData['payment_type'] = $params['payment_type'] ?? Payment::CREDIT_CARD;
                    $paymentData['type_change'] = $params['type_change'] ?? null;
                    $paymentData['old_register_trademark'] = $registerTrademark->id;
                    Session::put($key, $paymentData);

                    return redirect()->route('user.payment.index', ['s' => $key]);
                    break;
                case REDIRECT_TO_COMMON_QUOTE:
                    return redirect()->route('user.quote', ['id' => $payment->id]);
                    break;
                case REDIRECT_TO_ANKEN_TOP:
                    CommonHelper::setMessage($request, MESSAGE_SUCCESS, __('messages.general.Common_E047'));

                    return redirect()->route('user.application-detail.index', ['id' => $trademark->id]);
                    break;
                case REDIRECT_TO_KAKUNIN:
                    $key = Str::random(11);
                    $paymentData['payment_id'] = $payment->id;
                    $paymentData['payment_type'] = $params['payment_type'] ?? Payment::CREDIT_CARD;
                    $paymentData['type_change'] = $params['type_change'] ?? null;
                    $paymentData['old_register_trademark'] = $registerTrademark->id;
                    Session::put($key, $paymentData);

                    return redirect()->route('user.registration.attorney-letter-confirm', [
                        'id' => $newRegisterTrademark->id,
                        's' => $key,
                    ]);
                    break;
                default:
                    return redirect()->back()->with('messages', __('messages.common.errors.Common_E025'));
                    break;
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Showing u302_402kakunin_ininjo screen.
     */
    public function showU302402KakuninIninjo(Request $request, int $id)
    {
        if (!$request->has('s') && !$request->s) {
            abort(CODE_ERROR_404);
        }

        $sessionKey = $request->s;
        $registerTrademark = $this->registerTrademarkService->find($id);
        if (!$registerTrademark) {
            abort(CODE_ERROR_404);
        }
        $registerTrademark->load('trademark');
        $trademark = $registerTrademark->trademark;

        if ($trademark->user_id != auth()->user()->id) {
            abort(CODE_ERROR_404);
        }

        $agentGroups = $this->agentGroupService->findByCondition(['status_choice' => true], ['agents'])->first();
        $agents = $agentGroups->agents;

        $sessionData = Session::get($sessionKey);

        // Get Back Url
        $backUrl = null;
        if (!empty($sessionData)) {
            $fromPage = $sessionData['from_page'] ?? null;
            if (str_contains($fromPage, U302_402_5YR_KOUKI)) {
                $backUrl = route('user.registration.notice-latter-period', $sessionData['old_register_trademark']);
            }
            if (str_contains($fromPage, U302_402TSUINO_5YR_KOUKI)) {
                $backUrl = route('user.registration.notice-later-period.overdue', $sessionData['old_register_trademark']);
            }
            if (str_contains($fromPage, U402TSUINO)) {
                $backUrl = route('user.update.notify-procedure', $sessionData['old_register_trademark']);
            } elseif (str_contains($fromPage, U402)) {
                $backUrl = route('user.update.notify-procedure.overdue', $sessionData['old_register_trademark']);
            }
        }
        $prefectures = $this->mPrefectureService->listPrefectureOptions();

        return view('user.modules.registration.u302_402kakunin_ininjo', compact(
            'agents',
            'prefectures',
            'registerTrademark',
            'sessionKey',
            'backUrl',
        ));
    }

    /**
     * Redirect to Common_payment page
     */
    public function redirectPayment(Request $request, int $id)
    {
        if (!$request->has('s') && !$request->s) {
            abort(CODE_ERROR_404);
        }

        $sessionKey = $request->s;

        $sessionData = Session::get($sessionKey);
        $sessionData['back_url'] = route('user.registration.attorney-letter-confirm', [
            'id' => $id,
            's' => $sessionKey,
        ]);
        Session::put($sessionKey, $sessionData);

        return redirect()->route('user.payment.index', ['s' => $sessionKey]);
    }

    /**
     * View page u302_402tsuino_5yr_kouki
     *
     * @param  int $id
     * @param  Request $request
     * @return View
     */
    public function notifyLatterPeriodOverdue(Request $request, int $id)
    {
        $registerTrademark = $this->registerTrademarkService->find($id);
        if (empty($registerTrademark)
            || $registerTrademark->is_register != RegisterTrademark::IS_REGISTER
            && $registerTrademark->period_registration != RegisterTrademark::PERIOD_REGISTRATION_5_YEAR) {
            abort(CODE_ERROR_404);
        }

        $currentUser = Auth::guard('web')->user();
        $trademark = $registerTrademark->trademark ?? null;
        if (empty($trademark) || $trademark->user_id != $currentUser->id) {
            abort(CODE_ERROR_404);
        }
        $trademark->load('registerTrademarks');

        // Check access page with deadline_update
        $dateUpdate = $registerTrademark->deadline_update;
        $dateUpdateCurrent = Carbon::parse($dateUpdate);
        $dateUpdateBefore6Month = Carbon::parse($dateUpdate)->subMonth(6)->startOfDay();
        $dateUpdateNext6Month = Carbon::parse($dateUpdate)->addMonth(6)->subDay(1)->endOfDay();

        if (now() < $dateUpdateBefore6Month) {
            abort(CODE_ERROR_404);
        }

        // Is Block
        $isBlock = false;

        // And register_trademarks.deadline_update + 6 months - 5 day then block only payment_type bank_transfer
        $isDisableBankTransfer = false;
        if (now() > Carbon::parse($dateUpdate)->addMonth(6)->subDay(5)
            && now() < Carbon::parse($dateUpdate)->addMonth(6)) {
            $isDisableBankTransfer = true;
        }

        // Block when has next registerTrademarks
        $registerTrademarks = $trademark->registerTrademarks ?? collect([]);
        $nextRegisterTrademark = $registerTrademarks->where('id', '>', $registerTrademark->id)->first();
        if (!empty($nextRegisterTrademark) && (
                $nextRegisterTrademark->is_register == RegisterTrademark::IS_REGISTER
                || $nextRegisterTrademark->is_cancel == RegisterTrademark::IS_CANCEL
            )) {
            $isBlock = true;
        }
        $registerTrademarks = $registerTrademarks->where('id', '<=', $registerTrademark->id)
            ->where('is_register', RegisterTrademark::IS_REGISTER);

        // Trademark Table
        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_5, $trademark->id);

        // Get RegisterTrademarkProds
        $registerTrademarkProds = $registerTrademark->registerTrademarkProds ?? collect();
        $registerTrademarkProds->load('appTrademarkProd.mProduct.mDistinction');
        $registerTrademarkProds = $registerTrademarkProds->groupBy('appTrademarkProd.mProduct.mDistinction.name')->sortKeys();

        $typeChangeName = TrademarkInfo::TYPE_CHANGE_NAME;
        $typeChangeAddress = TrademarkInfo::TYPE_CHANGE_ADDRESS;
        $typeChangeDouble = TrademarkInfo::TYPE_CHANGE_NAME_AND_ADDRESS;

        // Price Base
        $priceService = $this->mPriceListService->getPriceCommonOfPrecheck(MPriceList::LATE_PAYMENT_RENEWAL, MPriceList::PAYMENT_SERVICE_UP_3_CATEGORY);
        $priceService->base_price = $priceService->base_price * 2;
        $priceService->pof_1st_distinction_5yrs = $priceService->pof_1st_distinction_5yrs * 2;
        $priceService->pof_1st_distinction_10yrs = $priceService->pof_1st_distinction_10yrs * 2;
        $priceService->pof_2nd_distinction_5yrs = $priceService->pof_2nd_distinction_5yrs * 2;
        $priceService->pof_2nd_distinction_10yrs = $priceService->pof_2nd_distinction_10yrs * 2;
        $priceServiceFee = $this->mPriceListService->getPriceIncludesTax(MPriceList::LATE_PAYMENT_RENEWAL, MPriceList::PAYMENT_SERVICE_UP_3_CATEGORY) * 2;

        $priceServiceAddProd = $this->mPriceListService->getPriceCommonOfPrecheck(MPriceList::LATE_PAYMENT_REGISTRATION, MPriceList::PAYMENT_EACH_1_CATEGORY);
        $priceServiceAddProdFee = $this->mPriceListService->getPriceIncludesTax(MPriceList::LATE_PAYMENT_REGISTRATION, MPriceList::PAYMENT_EACH_1_CATEGORY);

        $priceServiceChangeAddress = $this->mPriceListService->getPriceCommonOfPrecheck(MPriceList::CHANGE_PROCEDURE, MPriceList::CHANGE_ADDRESS_PROCEDURE);
        $priceServiceChangeAddressFee = $this->mPriceListService->getPriceIncludesTax(MPriceList::CHANGE_PROCEDURE, MPriceList::CHANGE_ADDRESS_PROCEDURE);

        $priceServiceChangeName = $this->mPriceListService->getPriceCommonOfPrecheck(MPriceList::CHANGE_PROCEDURE, MPriceList::CHANGE_NAME_PROCEDURE);
        $priceServiceChangeNameFee = $this->mPriceListService->getPriceIncludesTax(MPriceList::CHANGE_PROCEDURE, MPriceList::CHANGE_NAME_PROCEDURE);

        $priceBankTransfer = $this->mPriceListService->getPriceCommonOfPrecheck(MPriceList::EACH_PAYMENT, MPriceList::BANK_TRANSFER_HANDLING);
        $priceBankTransferFee = $this->mPriceListService->getPriceIncludesTax(MPriceList::EACH_PAYMENT, MPriceList::BANK_TRANSFER_HANDLING);

        // Payment
        $payment = $this->paymentService->findByCondition([
            'type' => Payment::TYPE_LATE_PAYMENT,
            'target_id' => $nextRegisterTrademark->id ?? 0,
        ])->with(['payerInfo'])->first();

        $priceData = [
            'priceService' => $priceService,
            'priceServiceFee' => $priceServiceFee,
            'priceServiceAddProd' => $priceServiceAddProd,
            'priceServiceAddProdFee' => $priceServiceAddProdFee,
            'priceServiceChangeAddress' => $priceServiceChangeAddress,
            'priceServiceChangeAddressFee' => $priceServiceChangeAddressFee,
            'priceServiceChangeName' => $priceServiceChangeName,
            'priceServiceChangeNameFee' => $priceServiceChangeNameFee,
            'priceBankTransfer' => $priceBankTransfer,
            'priceBankTransferFee' => $priceBankTransferFee,
        ];

        // Get Other Data
        $setting = $this->mPriceListService->getSetting();
        $nations = $this->mNationService->listNationOptions();
        $prefectures = $this->mPrefectureService->listPrefectureOptions();

        // Check pack C
        $appTrademark = $trademark->appTrademark;
        $isPackC = false;
        if ($appTrademark->pack == AppTrademark::PACK_C) {
            $isPackC = true;
        }

        return view('user.modules.registration.u302_402tsuino_5yr_kouki', compact(
            'registerTrademark',
            'nextRegisterTrademark',
            'trademark',
            'trademarkTable',
            'registerTrademarkProds',
            'registerTrademarks',
            'isBlock',
            'isDisableBankTransfer',
            'isPackC',
            'dateUpdateCurrent',
            'dateUpdateBefore6Month',
            'dateUpdateNext6Month',
            'typeChangeName',
            'typeChangeAddress',
            'typeChangeDouble',
            'priceData',
            'setting',
            'nations',
            'prefectures',
            'payment',
        ));
    }
}
