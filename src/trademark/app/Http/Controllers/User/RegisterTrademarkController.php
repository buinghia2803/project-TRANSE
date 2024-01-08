<?php

namespace App\Http\Controllers\User;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AppTrademark;
use App\Models\MailTemplate;
use App\Models\MPriceList;
use App\Models\Payment;
use App\Models\RegisterTrademark;
use App\Models\RegisterTrademarkProd;
use App\Models\TrademarkInfo;
use App\Services\Common\TrademarkTableService;
use App\Services\MNationService;
use App\Services\MPrefectureService;
use App\Services\MPriceListService;
use App\Services\PayerInfoService;
use App\Services\PaymentService;
use App\Services\MailTemplateService;
use App\Services\RegisterTrademarkProdService;
use App\Services\RegisterTrademarkService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class RegisterTrademarkController extends Controller
{
    protected $registerTrademarkService;
    private TrademarkTableService $trademarkTableService;
    private MPriceListService $mPriceListService;
    private PaymentService $paymentService;
    private MNationService $mNationService;
    private MPrefectureService $mPrefectureService;
    private PayerInfoService $payerInfoService;
    private RegisterTrademarkProdService $registerTrademarkProdService;
    private MailTemplateService $mailTemplateService;

    /**
     * Constructor
     *
     * @return  void
     */
    public function __construct(
        RegisterTrademarkService $registerTrademarkService,
        TrademarkTableService $trademarkTableService,
        MPriceListService $mPriceListService,
        PaymentService $paymentService,
        PayerInfoService $payerInfoService,
        MNationService $mNationService,
        MPrefectureService $mPrefectureService,
        RegisterTrademarkProdService $registerTrademarkProdService,
        MailTemplateService $mailTemplateService
    )
    {
        $this->registerTrademarkService = $registerTrademarkService;
        $this->trademarkTableService = $trademarkTableService;
        $this->mPriceListService = $mPriceListService;
        $this->paymentService = $paymentService;
        $this->payerInfoService = $payerInfoService;
        $this->mNationService = $mNationService;
        $this->mPrefectureService = $mPrefectureService;
        $this->registerTrademarkProdService = $registerTrademarkProdService;
        $this->mailTemplateService = $mailTemplateService;
    }

    /**
     * Cancel Trademark
     *
     * @param int $id
     * @return View
     */
    public function cancelTrademark(int $id): View
    {
        $registerTrademark = $this->registerTrademarkService->getRegisterTrademarkOfUser($id);
        if (!$registerTrademark) {
            abort(404);
        }
        $isCancel = RegisterTrademark::IS_CANCEL;

        return view('user.modules.register-trademark.u402_cancel', compact('registerTrademark', 'isCancel'));
    }

    /**
     * Cancel Trademark Post
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function cancelTrademarkPost(int $id): RedirectResponse
    {
        $registerTrademark = $this->registerTrademarkService->getRegisterTrademarkOfUser($id);
        if (!$registerTrademark && $registerTrademark->is_cancel == RegisterTrademark::IS_CANCEL) {
            abort(404);
        }
        $result = $this->registerTrademarkService->cancelTrademarkPost($registerTrademark);

        if ($result) {
            $user = Auth::user();
            $dataSendMail = [
                'user' => $user,
                'from_page' => U402CANCEL,
            ];
            $this->mailTemplateService->sendMailRequest($dataSendMail, MailTemplate::TYPE_OTHER);

            return redirect()->route('user.top')->with('message', __('messages.update_success'));
        }

        return redirect()->back()->with('error', __('messages.update_fail'));
    }

    /**
     * View page u402
     *
     * @param  int $id
     * @param  Request $request
     * @return View
     */
    public function getUpdateNotifyProcedure(Request $request, int $id): View
    {
        $registerTrademark = $this->registerTrademarkService->find($id);
        if (empty($registerTrademark) || $registerTrademark->is_register != RegisterTrademark::IS_REGISTER) {
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

        if (!($dateUpdateBefore6Month <= now() || now() <= $dateUpdateNext6Month)) {
            abort(CODE_ERROR_404);
        }

        // Is Block Page
        $isBlock = false;
        if (now() > $dateUpdateNext6Month) {
            $isBlock = true;
        }

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

        // Get NextRegisterTrademarkProd
        $nextRegisterTrademarkProds = collect([]);
        if (!empty($nextRegisterTrademark)) {
            $nextRegisterTrademarkProds = $nextRegisterTrademark->registerTrademarkProds ?? collect();
        }
        $nextProdIsApply = $nextRegisterTrademarkProds->pluck('is_apply', 'app_trademark_prod_id')->toArray();

        $typeChangeName = TrademarkInfo::TYPE_CHANGE_NAME;
        $typeChangeAddress = TrademarkInfo::TYPE_CHANGE_ADDRESS;
        $typeChangeDouble = TrademarkInfo::TYPE_CHANGE_NAME_AND_ADDRESS;

        // Price Base
        $priceService = $this->mPriceListService->getPriceCommonOfPrecheck(MPriceList::UPDATE, MPriceList::UPDATE_SERVICE_UP_3_CATEGORY);
        $priceServiceFee = $this->mPriceListService->getPriceIncludesTax(MPriceList::UPDATE, MPriceList::UPDATE_SERVICE_UP_3_CATEGORY);

        $priceServiceAddProd = $this->mPriceListService->getPriceCommonOfPrecheck(MPriceList::UPDATE, MPriceList::UPDATE_SERVICE_EACH_CATEGORY);
        $priceServiceAddProdFee = $this->mPriceListService->getPriceIncludesTax(MPriceList::UPDATE, MPriceList::UPDATE_SERVICE_EACH_CATEGORY);

        $priceServiceChangeAddress = $this->mPriceListService->getPriceCommonOfPrecheck(MPriceList::CHANGE_PROCEDURE, MPriceList::CHANGE_ADDRESS_PROCEDURE);
        $priceServiceChangeAddressFee = $this->mPriceListService->getPriceIncludesTax(MPriceList::CHANGE_PROCEDURE, MPriceList::CHANGE_ADDRESS_PROCEDURE);

        $priceServiceChangeName = $this->mPriceListService->getPriceCommonOfPrecheck(MPriceList::CHANGE_PROCEDURE, MPriceList::CHANGE_NAME_PROCEDURE);
        $priceServiceChangeNameFee = $this->mPriceListService->getPriceIncludesTax(MPriceList::CHANGE_PROCEDURE, MPriceList::CHANGE_NAME_PROCEDURE);

        $priceBankTransfer = $this->mPriceListService->getPriceCommonOfPrecheck(MPriceList::EACH_PAYMENT, MPriceList::BANK_TRANSFER_HANDLING);
        $priceBankTransferFee = $this->mPriceListService->getPriceIncludesTax(MPriceList::EACH_PAYMENT, MPriceList::BANK_TRANSFER_HANDLING);

        $priceReduceDistinction  = $this->mPriceListService->getPriceCommonOfPrecheck(MPriceList::REGISTRATION, MPriceList::REGISTRATION_REDUCTION_PROCEDURE);
        $priceReduceDistinctionFee = $this->mPriceListService->getPriceIncludesTax(MPriceList::REGISTRATION, MPriceList::REGISTRATION_REDUCTION_PROCEDURE);

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
            'priceReduceDistinction' => $priceReduceDistinction,
            'priceReduceDistinctionFee' => $priceReduceDistinctionFee,
        ];

        // Payment
        $payment = $this->paymentService->findByCondition([
            'type' => Payment::RENEWAL_DEADLINE,
            'target_id' => $nextRegisterTrademark->id ?? 0,
        ])->with(['payerInfo'])->first();

        // Get Other Data
        $setting = $this->mPriceListService->getSetting();
        $nations = $this->mNationService->listNationOptions();
        $prefectures = $this->mPrefectureService->listPrefectureOptions();
        $periodRegistrations = RegisterTrademark::getOptionPeriodRegistration();

        // Check pack C
        $appTrademark = $trademark->appTrademark;
        $isPackC = false;
        if ($appTrademark->pack == AppTrademark::PACK_C) {
            $isPackC = true;
        }

        return view('user.modules.register-trademark.u402', compact(
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
            'periodRegistrations',
            'payment',
            'nextProdIsApply',
        ));
    }

    /**
     * Post u402
     *
     * @param  Request $request
     * @param  int $id
     * @return RedirectResponse
     */
    public function postUpdateNotifyProcedure(Request $request, int $id): RedirectResponse
    {
        $registerTrademark = $this->registerTrademarkService->find($id);
        if (empty($registerTrademark) || $registerTrademark->is_register != RegisterTrademark::IS_REGISTER) {
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
            $round = count($registerTrademarkIsRegister);

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

            if ($fromPage == U402TSUINO) {
                $type = U402TSUINO . '_' . $round;
            } else {
                $type = U402 . '_' . $round;
            }

            $dataRegisterTrademark = [
                'trademark_id' => $trademark->id,
                'trademark_info_id' => 0,
                'id_register_trademark_choice' => $params['id_register_trademark_choice'] ?? null,
                'admin_id' => Admin::first()->id,
                'period_registration' => $params['period_registration'] ?? null,
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
                'type_page' => RegisterTrademark::TYPE_PAGE_3,
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
                'type' => Payment::RENEWAL_DEADLINE,
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
                'type' => Payment::RENEWAL_DEADLINE,
            ];

            $paymentData = $params['payment'] ?? [];
            $paymentData['trademark_id'] = $trademark->id;

            if ($fromPage == U402TSUINO) {
                $paymentData['from_page'] = U402TSUINO . '_' . $round;
            } else {
                $paymentData['from_page'] = U402 . '_' . $round;
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
                $paymentData['type'] = Payment::RENEWAL_DEADLINE;
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
            $distinctionSelected = array_keys($params['distinctions'] ?? []);
            $registerTrademarkProds = $registerTrademark->registerTrademarkProds ?? collect();
            $registerTrademarkProds = $registerTrademarkProds->load('appTrademarkProd.mProduct');
            $productIds = [];
            foreach ($registerTrademarkProds as $registerTrademarkProd) {
                // Create/Update Payment Prods
                $productIds[] = $registerTrademarkProd->appTrademarkProd->m_product_id;

                // Set IsApply
                $mDistinctionId = $registerTrademarkProd->appTrademarkProd->mProduct->m_distinction_id ?? 0;
                $isApply = RegisterTrademarkProd::IS_APPLY;
                if (!in_array($mDistinctionId, $distinctionSelected)) {
                    $isApply = RegisterTrademarkProd::IS_NOT_APPLY;
                }

                $registerTrademarkProdData['register_trademark_id'] = $newRegisterTrademark->id;
                $registerTrademarkProdData['app_trademark_prod_id'] = $registerTrademarkProd->app_trademark_prod_id;
                $this->registerTrademarkProdService->updateOrCreate($registerTrademarkProdData, [
                    'is_apply' => $isApply,
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
     * View page u402tsuino
     *
     * @param  int $id
     * @param  Request $request
     * @return View
     */
    public function getUpdateNotifyProcedureOverdue(Request $request, int $id): View
    {
        $registerTrademark = $this->registerTrademarkService->find($id);
        if (empty($registerTrademark) || $registerTrademark->is_register != RegisterTrademark::IS_REGISTER) {
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

        if (now() <= $dateUpdateBefore6Month) {
            abort(CODE_ERROR_404);
        }

        // Is Block Page
        $isBlock = false;

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

        // Get NextRegisterTrademarkProd
        $nextRegisterTrademarkProds = collect([]);
        if (!empty($nextRegisterTrademark)) {
            $nextRegisterTrademarkProds = $nextRegisterTrademark->registerTrademarkProds ?? collect();
        }
        $nextProdIsApply = $nextRegisterTrademarkProds->pluck('is_apply', 'app_trademark_prod_id')->toArray();

        $typeChangeName = TrademarkInfo::TYPE_CHANGE_NAME;
        $typeChangeAddress = TrademarkInfo::TYPE_CHANGE_ADDRESS;
        $typeChangeDouble = TrademarkInfo::TYPE_CHANGE_NAME_AND_ADDRESS;

        // Price Base
        $priceService = $this->mPriceListService->getPriceCommonOfPrecheck(MPriceList::UPDATE, MPriceList::UPDATE_SERVICE_UP_3_CATEGORY);
        $priceService->base_price = $priceService->base_price * 2;
        $priceService->pof_1st_distinction_5yrs = $priceService->pof_1st_distinction_5yrs * 2;
        $priceService->pof_1st_distinction_10yrs = $priceService->pof_1st_distinction_10yrs * 2;
        $priceService->pof_2nd_distinction_5yrs = $priceService->pof_2nd_distinction_5yrs * 2;
        $priceService->pof_2nd_distinction_10yrs = $priceService->pof_2nd_distinction_10yrs * 2;
        $priceServiceFee = $this->mPriceListService->getPriceIncludesTax(MPriceList::UPDATE, MPriceList::UPDATE_SERVICE_UP_3_CATEGORY) * 2;

        $priceServiceAddProd = $this->mPriceListService->getPriceCommonOfPrecheck(MPriceList::UPDATE, MPriceList::UPDATE_SERVICE_EACH_CATEGORY);
        $priceServiceAddProdFee = $this->mPriceListService->getPriceIncludesTax(MPriceList::UPDATE, MPriceList::UPDATE_SERVICE_EACH_CATEGORY);

        $priceServiceChangeAddress = $this->mPriceListService->getPriceCommonOfPrecheck(MPriceList::CHANGE_PROCEDURE, MPriceList::CHANGE_ADDRESS_PROCEDURE);
        $priceServiceChangeAddressFee = $this->mPriceListService->getPriceIncludesTax(MPriceList::CHANGE_PROCEDURE, MPriceList::CHANGE_ADDRESS_PROCEDURE);

        $priceServiceChangeName = $this->mPriceListService->getPriceCommonOfPrecheck(MPriceList::CHANGE_PROCEDURE, MPriceList::CHANGE_NAME_PROCEDURE);
        $priceServiceChangeNameFee = $this->mPriceListService->getPriceIncludesTax(MPriceList::CHANGE_PROCEDURE, MPriceList::CHANGE_NAME_PROCEDURE);

        $priceBankTransfer = $this->mPriceListService->getPriceCommonOfPrecheck(MPriceList::EACH_PAYMENT, MPriceList::BANK_TRANSFER_HANDLING);
        $priceBankTransferFee = $this->mPriceListService->getPriceIncludesTax(MPriceList::EACH_PAYMENT, MPriceList::BANK_TRANSFER_HANDLING);

        $priceReduceDistinction  = $this->mPriceListService->getPriceCommonOfPrecheck(MPriceList::REGISTRATION, MPriceList::REGISTRATION_REDUCTION_PROCEDURE);
        $priceReduceDistinctionFee = $this->mPriceListService->getPriceIncludesTax(MPriceList::REGISTRATION, MPriceList::REGISTRATION_REDUCTION_PROCEDURE);

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
            'priceReduceDistinction' => $priceReduceDistinction,
            'priceReduceDistinctionFee' => $priceReduceDistinctionFee,
        ];

        // Payment
        $payment = $this->paymentService->findByCondition([
            'type' => Payment::RENEWAL_DEADLINE,
            'target_id' => $nextRegisterTrademark->id ?? 0,
        ])->with(['payerInfo'])->first();

        // Get Other Data
        $setting = $this->mPriceListService->getSetting();
        $nations = $this->mNationService->listNationOptions();
        $prefectures = $this->mPrefectureService->listPrefectureOptions();
        $periodRegistrations = RegisterTrademark::getOptionPeriodRegistration();

        // Check pack C
        $appTrademark = $trademark->appTrademark;
        $isPackC = false;
        if ($appTrademark->pack == AppTrademark::PACK_C) {
            $isPackC = true;
        }

        return view('user.modules.register-trademark.u402tsuino', compact(
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
            'periodRegistrations',
            'payment',
            'nextProdIsApply',
        ));
    }
}
