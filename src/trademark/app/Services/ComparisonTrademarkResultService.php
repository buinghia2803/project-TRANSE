<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\AppTrademark;
use App\Models\ComparisonTrademarkResult;
use App\Models\MCode;
use App\Models\MPriceList;
use App\Helpers\CommonHelper;
use App\Models\AppTrademarkProd;
use App\Models\ReasonComment;
use App\Models\ReasonQuestion;
use App\Models\ReasonQuestionDetail;
use App\Models\ReasonQuestionNo;
use App\Notices\PaymentNotice;
use App\Repositories\ComparisonTrademarkResultRepository;
use App\Repositories\ReasonCommentRepository;
use App\Repositories\ReasonQuestionDetailRepository;
use App\Repositories\ReasonQuestionRepository;
use App\Repositories\ReasonQuestionNoRepository;
use App\Repositories\SettingRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Ramsey\Uuid\Type\Integer;
use App\Models\MProduct;
use App\Models\Notice;
use App\Models\NoticeDetail;
use App\Models\Payment;
use App\Models\ReasonRefNumProd;
use App\Models\Setting;
use App\Models\SupportFirstTime;
use App\Models\PaymentProd;
use App\Models\PlanCorrespondence;
use App\Models\PlanCorrespondenceProd;
use App\Models\Reason;
use App\Models\ReasonNo;
use App\Models\RegisterTrademarkRenewal;
use App\Models\TrademarkDocument;
use App\Repositories\AppTrademarkProdRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\TrademarkRepository;
use App\Repositories\PayerInfoRepository;
use App\Repositories\MPriceListRepository;
use App\Repositories\MatchingResultRepository;
use App\Repositories\PaymentProductRepository;
use App\Repositories\ReasonRefNumProdRepository;
use App\Repositories\PlanCorrespondenceRepository;
use App\Repositories\PlanCorrespondenceProdRepository;
use App\Repositories\ReasonNoRepository;
use App\Repositories\ReasonRepository;
use App\Repositories\RegisterTrademarkRenewalRepository;
use App\Services\Common\NoticeService;
use App\Services\PlanCorrespondenceService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class ComparisonTrademarkResultService extends BaseService
{
    protected SettingRepository                 $settingRepository;
    protected MPriceListRepository              $mPriceListRepository;
    protected NoticeService                     $noticeService;
    protected PlanCorrespondenceRepository      $planCorrespondenceRepository;
    protected PlanCorrespondenceProdRepository  $planCorrespondenceProdRepository;
    protected MatchingResultRepository          $matchingResultRepository;
    protected PayerInfoRepository               $payerInfoRepository;
    protected PaymentRepository                 $paymentRepository;
    protected PaymentProductRepository          $paymentProductRepository;
    protected TrademarkRepository               $trademarkRepository;
    protected ReasonRefNumProdRepository        $reasonRefNumProdRepository;
    protected MCodeService                      $mCodeService;
    protected ProductCodeService                $productCodeService;
    protected AppTrademarkProdRepository        $appTrademarkProdRepository;
    protected ReasonNoRepository                $reasonNoRepository;
    protected ReasonQuestionDetailRepository    $reasonQuestionDetailRepository;
    protected ReasonQuestionRepository          $reasonQuestionRepository;
    protected ReasonQuestionNoRepository        $reasonQuestionNoRepository;
    protected ReasonCommentRepository           $reasonCommentRepository;
    protected NoticeDetailService               $noticeDetailService;
    protected PlanCorrespondenceService         $planCorrespondenceService;
    protected ReasonRepository                  $reasonRepository;
    protected RegisterTrademarkRenewalRepository $registerTrademarkRenewalRepository;
    /**
     * Initializing the instances and variables
     *
     * @param ComparisonTrademarkResultRepository $comparisonTrademarkResultRepository
     * @param SettingRepository                   $settingRepository
     * @param NoticeService                       $noticeService
     * @param PlanCorrespondenceRepository        $planCorrespondenceRepository
     * @param PlanCorrespondenceProdRepository    $planCorrespondenceProdRepository
     * @param ReasonRefNumProdRepository          $reasonRefNumProdRepository
     * @param MatchingResultRepository            $matchingResultRepository
     * @param PayerInfoRepository                 $payerInfoRepository
     * @param PaymentRepository                   $paymentRepository
     * @param MPriceListRepository                $mPriceListRepository
     * @param PaymentProductRepository            $paymentProductRepository
     * @param TrademarkRepository                 $trademarkRepository
     * @param MCodeService                        $mCodeService
     * @param ProductCodeService                  $productCodeService
     * @param ReasonQuestionDetailRepository      $reasonQuestionDetailRepository
     * @param ReasonQuestionRepository            $reasonQuestionRepository
     * @param ReasonQuestionNoRepository          $reasonQuestionNoRepository
     * @param ReasonCommentRepository             $reasonCommentRepository
     * @param NoticeDetailService                 $noticeDetailService
     */
    public function __construct(
        ComparisonTrademarkResultRepository $comparisonTrademarkResultRepository,
        RegisterTrademarkRenewalRepository  $registerTrademarkRenewalRepository,
        SettingRepository                   $settingRepository,
        NoticeService                       $noticeService,
        PlanCorrespondenceRepository        $planCorrespondenceRepository,
        PlanCorrespondenceProdRepository    $planCorrespondenceProdRepository,
        ReasonRefNumProdRepository          $reasonRefNumProdRepository,
        MatchingResultRepository            $matchingResultRepository,
        PayerInfoRepository                 $payerInfoRepository,
        PaymentRepository                   $paymentRepository,
        MPriceListRepository                $mPriceListRepository,
        PaymentProductRepository            $paymentProductRepository,
        TrademarkRepository                 $trademarkRepository,
        MCodeService                        $mCodeService,
        ProductCodeService                  $productCodeService,
        AppTrademarkProdRepository          $appTrademarkProdRepository,
        ReasonNoRepository                  $reasonNoRepository,
        ReasonQuestionDetailRepository      $reasonQuestionDetailRepository,
        ReasonQuestionRepository            $reasonQuestionRepository,
        ReasonQuestionNoRepository          $reasonQuestionNoRepository,
        ReasonCommentRepository             $reasonCommentRepository,
        NoticeDetailService                 $noticeDetailService,
        ReasonRepository                    $reasonRepository,
        PlanCorrespondenceService        $planCorrespondenceService
    )
    {
        $this->repository = $comparisonTrademarkResultRepository;
        $this->settingRepository = $settingRepository;
        $this->mPriceListRepository = $mPriceListRepository;
        $this->noticeService = $noticeService;
        $this->planCorrespondenceRepository = $planCorrespondenceRepository;
        $this->planCorrespondenceProdRepository = $planCorrespondenceProdRepository;
        $this->matchingResultRepository = $matchingResultRepository;
        $this->payerInfoRepository = $payerInfoRepository;
        $this->paymentRepository = $paymentRepository;
        $this->paymentProductRepository = $paymentProductRepository;
        $this->trademarkRepository = $trademarkRepository;
        $this->reasonRefNumProdRepository = $reasonRefNumProdRepository;
        $this->mCodeService = $mCodeService;
        $this->productCodeService = $productCodeService;
        $this->appTrademarkProdRepository = $appTrademarkProdRepository;
        $this->reasonNoRepository = $reasonNoRepository;
        $this->reasonQuestionDetailRepository = $reasonQuestionDetailRepository;
        $this->reasonQuestionRepository = $reasonQuestionRepository;
        $this->reasonQuestionNoRepository = $reasonQuestionNoRepository;
        $this->reasonCommentRepository = $reasonCommentRepository;
        $this->noticeDetailService = $noticeDetailService;
        $this->planCorrespondenceService = $planCorrespondenceService;
        $this->registerTrademarkRenewalRepository = $registerTrademarkRenewalRepository;
        $this->reasonRepository = $reasonRepository;
    }

    /**
     * Calculator Cart
     *
     * @param mixed $request
     * @return array
     */

    public function calculatorCart($request): array
    {
        $setting = $this->settingRepository->getSetting();
        $costServiceBase = floor($this->calPrice($request['cost_service_base'], $setting->value));
        $costServiceAddProdTax = floor($this->calPrice($request['cost_service_add_prod'], $setting->value));
        if ($request['flag'] && isset($request['prod_checked'])) {
            $costServiceAddProd = floor($request['prod_checked'] * $costServiceAddProdTax);
        } else {
            $costServiceAddProd = floor($request['number_distinct'] * $costServiceAddProdTax);
        }
        $extensionOfPeriodBeforeExpiry = floor($this->calPrice($request['extension_of_period_before_expiry'] ?? 0, $setting->value));
        $costBankTransfer = floor($this->calPrice($request['cost_bank_transfer'], $setting->value));
        $subTotal = floor($costServiceBase + $costServiceAddProd + $extensionOfPeriodBeforeExpiry - $request['price_discount'] + $costBankTransfer);
        $commission = $subTotal / (1 + ($setting->value / 100));

        $tax = $subTotal - $commission;
        $totalAmount = floor($request['print_fee']) + $subTotal;
        if (isset($request['numberProduct'])) {
            $moneyTakeReport = floor($request['numberProduct'] * $costServiceAddProdTax);
        }

        $data = [
            'cost_service_base' => floor($costServiceBase),
            'cost_service_add_prod' => floor($costServiceAddProd),
            'extension_of_period_before_expiry' => floor($extensionOfPeriodBeforeExpiry),
            'cost_bank_transfer' => floor($costBankTransfer),
            'sub_total' => floor($subTotal),
            'commission' => floor((float) (string) $commission),
            'tax' => floor($tax),
            'print_fee' => floor($request['print_fee']),
            'total_amount' => floor($totalAmount),
            'application_discount' => floor($request['price_discount']),
            'money_take_report' => floor($moneyTakeReport ?? 0),
            'number_distinct' => floor($request['number_distinct']),
            'prod_checked' => floor($request['prod_checked'] ?? 0),
        ];

        return $data;
    }

    /**
     * Create Comparision Result
     *
     * @param mixed $params
     * @return array
     */
    public function createComparisonResult($params)
    {
        DB::beginTransaction();
        try {
            if ($params['submit_type'] == BACK_URL) {
                return ['redirect_to' => BACK_URL];
            }

            $machingResult = $this->matchingResultRepository->findByCondition(['trademark_id' => $params['trademark_id']])->orderBy('id', SORT_TYPE_DESC)->first();
            $trademark = $this->trademarkRepository->findByCondition(['id' => $params['trademark_id']])->with('appTrademark', 'appTrademark.appTrademarkProd')->first();
            if (!isset($params['prod_ids'])) {
                $params['prod_ids'] = [];
            }

            $countProdids = count($params['prod_ids']);
            $dataPlanCorrespondences = [
                'comparison_trademark_result_id' => $params['comparison_trademark_result_id'],
                'is_ext_period' => $params['register_before_deadline'] ?? 0,
                'is_ext_period_2' => $params['is_ext_period_2'] ?? 0,
            ];
            $condition = [
                'id' => $params['plan_correspondence_id'],
            ];
            switch ($params['submit_type']) {
                case REDIRECT_TO_COMMON_PAYMENT_SIMPLE:
                case REDIRECT_TO_QUOTE_SIMPLE:
                case REDIRECT_TO_ANKEN_TOP:
                    $condition['type'] = PlanCorrespondence::TYPE_SIMPLE;
                    $dataPlanCorrespondences['type'] = PlanCorrespondence::TYPE_SIMPLE;
                    $fromPage = U201_SIMPLE;
                    break;
                case REDIRECT_TO_COMMON_PAYMENT_SELECT_01N:
                case REDIRECT_TO_QUOTE_SELECT_01N:
                case REDIRECT_TO_ANKEN_TOP_SELECT_01_N:
                    $condition['type'] = PlanCorrespondence::TYPE_SELECT;
                    $dataPlanCorrespondences['type'] = PlanCorrespondence::TYPE_SELECT;
                    break;
                default:
                    $condition['type'] = PlanCorrespondence::TYPE_SELECT;
                    $dataPlanCorrespondences['type'] = PlanCorrespondence::TYPE_SELECT;
                    $fromPage = U201_SELECT_01;
                    break;
            }
            $planCorrespondence = $this->planCorrespondenceRepository->updateOrCreate($condition, $dataPlanCorrespondences);
            $reasonNoNew = null;
            $reasonNoId = null;
            switch ($params['submit_type']) {
                case REDIRECT_TO_COMMON_PAYMENT_SELECT_01N:
                case REDIRECT_TO_QUOTE_SELECT_01N:
                case REDIRECT_TO_ANKEN_TOP_SELECT_01_N:
                    if ($params['count_reason_no'] == 0) {
                        $reasonNoNew = $this->cloneDataSelect01n($planCorrespondence, $params);
                    }
                    if (isset($reasonNoNew) && $reasonNoNew) {
                        $reasonNoId = $reasonNoNew->id;
                    } else {
                        $reasonNoId = $params['reason_no_id'];
                    }
                    if ($params['count_reason_no'] > 0) {
                        $fromPage = U201_SELECT_01_N . '_' . ($params['count_reason_no'] + 1) . '_' . $reasonNoId;
                    } else {
                        $fromPage = U201_SELECT_01_N . '_' . $reasonNoId;
                    }
                    break;
            }

            if (isset($params['is_register']) && $params['is_register']) {
                $maxReasonNo = ReasonNo::where('plan_correspondence_id', $params['plan_correspondence_id'])->count();
                foreach ($params['is_register'] as $key => $item) {
                    $dataPlanCorrespondenceProds = [
                        'is_register' => $item == PlanCorrespondenceProd::IS_REGISTER
                            ? PlanCorrespondenceProd::IS_REGISTER
                            : PlanCorrespondenceProd::IS_REGISTER_FALSE,
                        'round' => $maxReasonNo ? $maxReasonNo : 1,
                    ];
                    $appTrademarkProd = $this->appTrademarkProdRepository->findByCondition([
                        'app_trademark_id' => $trademark->appTrademark->id,
                        'm_product_id' => $key
                    ])->with([
                        'planCorrespondenceProd' => function ($query) use ($planCorrespondence) {
                            $query->where('plan_correspondence_id', $planCorrespondence->id);
                        },
                    ])->orderBy('id', SORT_TYPE_DESC)->first();

                    if (!empty($appTrademarkProd->planCorrespondenceProd)) {
                        $this->planCorrespondenceProdRepository->update($appTrademarkProd->planCorrespondenceProd, $dataPlanCorrespondenceProds);
                    } else {
                        if (empty($appTrademarkProd)) {
                            $dataAppTrademarkProd = [
                                'app_trademark_id' => $trademark->appTrademark->id,
                                'm_product_id' => $key,
                                'is_apply' => AppTrademarkProd::IS_APPLY,
                            ];
                            $appTrademarkProd = $this->appTrademarkProdRepository->create($dataAppTrademarkProd);
                        }

                        $dataPlanCorrespondenceProds['plan_correspondence_id'] = $planCorrespondence->id;
                        $dataPlanCorrespondenceProds['app_trademark_prod_id'] = $appTrademarkProd->id;
                        $this->planCorrespondenceProdRepository->create($dataPlanCorrespondenceProds);
                    }
                }
            } else {
                for ($i = 0; $i < $countProdids; $i++) {
                    $dataAppTrademarkProd = [
                        'app_trademark_id' => $trademark->appTrademark->id,
                        'm_product_id' => $params['prod_ids'][$i],
                        'is_apply' => AppTrademarkProd::IS_APPLY,

                    ];
                    $appTrademarkProd = $this->appTrademarkProdRepository->updateOrCreate($dataAppTrademarkProd, $dataAppTrademarkProd);

                    $dataPlanCorrespondenceProds = [
                        'plan_correspondence_id' => $planCorrespondence->id,
                        'app_trademark_prod_id' => $appTrademarkProd->id,
                        'is_register' => PlanCorrespondenceProd::IS_REGISTER,
                        'round' => isset($params['count_reason_no']) ? $params['count_reason_no'] + 1 : 0,
                    ];

                    $this->planCorrespondenceProdRepository->updateOrCreate($dataPlanCorrespondenceProds, $dataPlanCorrespondenceProds);
                }
            }

            if ($params['submit_type'] == U210_OVER_02) {
                DB::commit();

                return [
                    'redirect_to' => U210_OVER_02,
                    'trademark_id' => $trademark->id,
                ];
            }

            $dataPayerInfo = [
                'target_id' => $machingResult->id,
                'payment_type' => $params['payment_type'] ?? null,
                'payer_type' => $params['payer_type'] ?? 0,
                'm_nation_id' => $params['m_nation_id'] ?? 0,
                'payer_name' => $params['payer_name'] ?? '',
                'payer_name_furigana' => $params['payer_name_furigana'] ?? '',
                'postal_code' => $params['postal_code'] ?? null,
                'm_prefecture_id' => $params['m_prefecture_id'] ?? null,
                'address_second' => $params['address_second'] ?? '',
                'address_three' => $params['address_three'] ?? '',
                'type' => TYPE_MATCHING_RESULT
            ];
            if (isset($params['payer_info_id']) && $params['payer_info_id']) {
                $payerInfo = $this->payerInfoRepository->find($params['payer_info_id']);
                $this->payerInfoRepository->update($payerInfo, $dataPayerInfo);
            } else {
                $payerInfo = $this->payerInfoRepository->create($dataPayerInfo);
            }

            $paymentStatus = Payment::IS_TREATMENT_WAIT;
            $isTreatment = Payment::STATUS_SAVE;
            $taxWithHolding = floor($this->getTaxWithHolding($payerInfo, $params['total_amount']));
            $paymentAmount = $params['total_amount'] - $taxWithHolding;
            $dataPayment = [
                'target_id' => $machingResult->id,
                'trademark_id' => $params['trademark_id'],
                'payer_info_id' => $payerInfo->id,
                'payment_status' => $paymentStatus,
                'is_treatment' => $isTreatment,
                'payment_date' => now(),
                'cost_service_base' => floor($params['cost_service_base']),
                'cost_service_add_prod' => floor($params['cost_service_add_prod']),
                'cost_bank_transfer' => floor($params['cost_bank_transfer']),
                'extension_of_period_before_expiry' => $params['extension_of_period_before_expiry'],
                'application_discount' => $params['application_discount'],
                'subtotal' => floor($params['subtotal']),
                'commission' => floor($params['commission']),
                'tax' => floor($params['tax']),
                'total_amount' => $params['total_amount'],
                'tax_withholding' => floor($taxWithHolding),
                'payment_amount' => floor($paymentAmount),
                'print_fee' => floor($params['print_fee']),
                'from_page' => $fromPage,
                'type' => Payment::TYPE_REASON_REFUSAL,
            ];
            $condition = [
                'is_treatment' => $isTreatment,
                'payment_status' => $paymentStatus,
                'trademark_id' => $params['trademark_id'],
                'type' => Payment::TYPE_REASON_REFUSAL,
                'target_id' => $machingResult->id,
                'from_page' => $fromPage,
            ];
            $payment = $this->paymentRepository->updateOrCreate($condition, $dataPayment);

            $dataUpdatePayment = [];
            if (!$payment->quote_number) {
                $dataUpdatePayment['quote_number'] = $this->generateQIR($trademark->trademark_number, 'quote');
            }
            if (!$payment->invoice_number) {
                $dataUpdatePayment['invoice_number'] = $this->generateQIR('', 'invoice');
            }
            if (!$payment->invoice_number) {
                $dataUpdatePayment['receipt_number'] = $this->generateQIR('', 'receipt');
            }
            $payment->update($dataUpdatePayment);

            $dataPaymentProd = [
                'payment_id' => $payment->id,
            ];
            $productIds = [];

            if (isset($params['is_register']) && $params['is_register']) {
                $paymentProduct = $this->paymentProductRepository->findByCondition([
                    'payment_id' => 'payment_id = ' . $payment->id,
                ])->get();
                $paymentProduct->map(function ($item) {
                    $item->delete();
                });

                foreach ($params['is_register'] as $key => $item) {
                    if ($item == '1') {
                        $dataPaymentProd['m_product_id'] = $key;
                        array_push($productIds, $key);
                        $this->paymentProductRepository->updateOrCreate($dataPaymentProd, $dataPaymentProd);
                    }
                }
            } else {
                for ($i = 0; $i < $countProdids; $i++) {
                    $dataPaymentProd['m_product_id'] = $params['prod_ids'][$i];

                    $this->paymentProductRepository->updateOrCreate($dataPaymentProd, $dataPaymentProd);
                }
            }
            $redirectTo = null;
            $key = Str::random(11);
            switch ($params['submit_type']) {
                case REDIRECT_TO_COMMON_PAYMENT_SELECT_01N:
                case REDIRECT_TO_COMMON_PAYMENT_SELECT:
                case REDIRECT_TO_COMMON_PAYMENT_SIMPLE:
                    $dataPayment['payment_id'] = $payment->id;
                    $dataPayment['payment_type'] = $params['payment_type'];
                    $dataPayment['productIds'] = $productIds;
                    $dataPayment['reason_no_id'] = $reasonNoId;
                    $dataPayment['comparison_trademark_result_id'] = $params['comparison_trademark_result_id'] ?? 0;
                    $dataPayment['planCorrespondence'] = $planCorrespondence;
                    Session::put($key, $dataPayment);
                    $redirectTo = REDIRECT_TO_COMMON_PAYMENT_SIMPLE;
                    break;
                case REDIRECT_TO_QUOTE_SELECT_01N:
                case REDIRECT_TO_QUOTE_SELECT:
                case REDIRECT_TO_QUOTE_SIMPLE:
                    $redirectTo = REDIRECT_TO_QUOTE_SIMPLE;
                    break;
                case REDIRECT_TO_ANKEN_TOP:
                case REDIRECT_TO_ANKEN_TOP_SELECT:
                case REDIRECT_TO_ANKEN_TOP_SELECT_01_N:
                    $redirectTo = REDIRECT_TO_ANKEN_TOP;
                    break;
            }
            DB::commit();
            return [
                'redirect_to' => $redirectTo,
                'payment_id' => $payment->id ?? '',
                'key_session' => $key,
                'trademark_id' => $trademark->id,
            ];
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
        }
    }

    /**
     * Clone Data Select01n
     *
     * @param  mixed $planCorrespondence
     * @param  array $params
     * @return model
     */
    public function cloneDataSelect01n($planCorrespondence, array $params): Model
    {
        $planCorrespondence->load('reasonComments', 'planCorrespondenceProds.reasonRefNumProd');
        $reasonComments = $planCorrespondence->reasonComments;

        $reasonNo = $this->reasonNoRepository->find($params['reason_no_id']);
        $reasonNo->load([
            'reasons',
            'reasonRefNumProds',
        ]);
        $reasonRefNumProds = $reasonNo->reasonRefNumProds;
        $reasons = $reasonNo->reasons;
        if (isset($reasonNo) && $reasonNo) {
            $dataReasonNo = [
                'plan_correspondence_id' => $planCorrespondence->id,
                'reason_number' => $reasonNo->reason_number,
                'reason_branch_number' => $reasonNo->reason_branch_number,
                'response_deadline' => $reasonNo->response_deadline,
                'flag_role' => ReasonNo::FLAG_1,
                'round' => $reasonNo->round + 1,
                'is_confirm' => ReasonNo::IS_CONFIRM_0
            ];

            $reasonNoNew = $this->reasonNoRepository->create($dataReasonNo);
        }

        $newReasonIDs = [];
        if (isset($reasons) && $reasons) {
            foreach ($reasons as $reason) {
                $dataReason = [
                    'admin_id' => $reason->admin_id,
                    'reason_no_id' => $reasonNoNew->id,
                    'reason_name' => $reason->reason_name,
                    'm_laws_regulation_id' => $reason->m_laws_regulation_id,
                    'reference_number' => $reason->reference_number,
                ];

                $newReason = $this->reasonRepository->create($dataReason);

                $newReasonIDs[] = [
                    'old_id' => $reason->id,
                    'new_id' => $newReason->id,
                ];
            }
        }

        if (isset($reasonComments) && $reasonComments) {
            foreach ($reasonComments as $reasonComment) {
                $dataReasonComment = [
                    'plan_correspondence_id' => $planCorrespondence->id,
                    'admin_id' => $reasonComment->admin_id,
                    'content' => $reasonComment->content,
                    'type' => $reasonComment->type,
                    'type_comment_step' => $reasonComment->type_comment_step,
                ];

                $this->reasonCommentRepository->create($dataReasonComment);
            }
        }
        if (isset($reasonRefNumProds) && $reasonRefNumProds) {
            foreach ($reasonRefNumProds as $reasonRefNumProd) {
                if (isset($reasonRefNumProd)) {
                    $voteReasonID = $reasonRefNumProd->vote_reason_id;
                    foreach ($newReasonIDs as $newReasonID) {
                        $voteReasonID = str_replace($newReasonID['old_id'], $newReasonID['new_id'], $voteReasonID);
                    }

                    $dataReasonRefNumProd = [
                        'plan_correspondence_prod_id' => $reasonRefNumProd->plan_correspondence_prod_id,
                        'admin_id' => $reasonRefNumProd->admin_id,
                        'comment_patent_agent' => $reasonRefNumProd->comment_patent_agent,
                        'vote_reason_id' => $voteReasonID,
                        'reason_no_id' => $reasonNoNew->id,
                        'rank' => $reasonRefNumProd->rank,
                        'is_choice' => $reasonRefNumProd->is_choice,
                    ];

                    $this->reasonRefNumProdRepository->create($dataReasonRefNumProd);
                }
            }
        }

        return $reasonNoNew;
    }

    /**
     * Get Data Draft Plan
     *
     * @param  mixed $id
     * @param  mixed $flag
     * @return Model
     */
    public function getDataDraftPlan($id, $flag): Model
    {
        $type = '';
        $fromPage = '';
        switch ($flag) {
            case FLAG_SIMPLE:
                $type = PlanCorrespondence::TYPE_SIMPLE;
                $fromPage = U201_SIMPLE;
                break;
            case PLAG_SELECT_01_N:
                $type = PlanCorrespondence::TYPE_SELECT;
                $fromPage = U201_SELECT_01_N;
                break;
            case PLAG_SELECT_01:
                $type = PlanCorrespondence::TYPE_SELECT;
                $fromPage = U201_SELECT_01;
                break;
        }

        return $this->findByCondition(['id' => $id])->with([
            'trademark',
            'trademark.payment' => function ($query) use ($fromPage) {
                return $query->with('payerInfo', function ($query) {
                    return $query->orderBy('id', SORT_TYPE_DESC);
                })->where('is_treatment', Payment::IS_TREATMENT_WAIT)
                    ->where('payment_status', Payment::STATUS_SAVE)
                    ->where('type', Payment::TYPE_REASON_REFUSAL)
                    ->where('from_page', $fromPage);
            },
            'planCorrespondence' => function ($query) use ($id, $type) {
                return $query->where('comparison_trademark_result_id', $id)->where('type', $type);
            },
            'planCorrespondence.planCorrespondenceProds',
            'trademark.appTrademark',
            'trademark.trademarkDocuments' => function ($query) {
                return $query->where('type', TrademarkDocument::TYPE_1);
            },
            'trademark.appTrademark.appTrademarkProd',
        ])->first();
    }

    /**
     * Cal Price
     *
     * @param mixed $price
     * @param mixed $setting
     * @return Float
     */
    public function calPrice($price, $setting): float
    {
        return $price + (($price * $setting) / 100);
    }

    /**
     * Simultaneous Application Discount
     *
     * @param mixed $costRegisterBeforeDeadlineBase
     * @param mixed $costPriorDeadlineBase
     * @param mixed $setting
     * @return Float
     */
    public function simultaneousApplicationDiscount($costRegisterBeforeDeadlineBase, $costPriorDeadlineBase, $setting): float
    {
        $priceRegisterBeforeDeadlineBase = $costRegisterBeforeDeadlineBase->base_price - $costPriorDeadlineBase->base_price;
        $taxPriceRegisterBeforeDeadlineBase = (($setting->value * $priceRegisterBeforeDeadlineBase) / 100);
        $price = $priceRegisterBeforeDeadlineBase + $taxPriceRegisterBeforeDeadlineBase;

        return $price;
    }


    /**
     * Send Notice Comparison
     *
     * @param mixed $id
     * @param mixed $route
     * @return void
     */
    public function sendNoticeComparison($id, $route)
    {
        $comparisonTrademarkResult = $this->findOrFail($id);
        $comparisonTrademarkResult->load('trademark');
        $trademark = $comparisonTrademarkResult->trademark;

        // Update Notice at a201a (No 21: H I)
        $stepBeforeNotice = $this->noticeDetailService->findByCondition([
            'is_answer' => NoticeDetail::IS_NOT_ANSWER,
            'type_acc' => NoticeDetail::TYPE_USER,
        ])->with('notice')->get()
            ->where('notice.trademark_id', $trademark->id)
            ->where('notice.user_id', $trademark->user_id)
            ->whereIn('notice.flow', [Notice::FLOW_RESPONSE_REASON, Notice::FLOW_RENEWAL_BEFORE_DEADLINE])
            ->filter(function ($item) {
                if ($item->notice->flow == Notice::FLOW_RESPONSE_REASON && $item->notice->step == Notice::STEP_1) {
                    return true;
                } elseif ($item->notice->flow == Notice::FLOW_RENEWAL_BEFORE_DEADLINE && $item->notice->step == null) {
                    return true;
                }

                return false;
            });
        $stepBeforeNotice->map(function ($item) {
            $item->update([
                'is_answer' => NoticeDetail::IS_ANSWER,
            ]);
        });

        $this->noticeService->sendNotice([
            'notices' => [
                'trademark_id' => $trademark->id,
                'user_id' => Auth::user()->id,
                'flow' => Notice::FLOW_RESPONSE_REASON,
                'step' => Notice::STEP_1,
            ],
            'notice_details' => [
                // Send Notice Jimu
                [
                    'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                    'target_page' => $route,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'content' => '拒絶理由通知対応：対応不要指示受領',
                    'attribute' => 'お客様から',
                    'completion_date' => now(),
                ],
                // Send Notice User
                [
                    'target_id' => Auth::user()->id,
                    'type_acc' => NoticeDetail::TYPE_USER,
                    'target_page' => $route,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_DEFAULT,
                    'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                    'content' => '拒絶理由通知書：対応不要指示済',
                ],
                [
                    'target_id' => Auth::user()->id,
                    'type_acc' => NoticeDetail::TYPE_USER,
                    'target_page' => $route,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'content' => '拒絶理由通知書：対応不要指示済',
                ],
            ],
        ]);
    }

    /**
     * Insert product code of u021b
     *
     * @param array $param
     * @return void
     */
    public function insertProductAndCode(array $params)
    {
        $mCodes = $params['code_name'];
        if (isset($params['m_code_id'])) {
            foreach ($params['m_code_id'] as $keyCode => $mCodeId) {
                foreach ($mCodeId as $value) {
                    $this->productCodeService->findByCondition([
                        'm_product_id' => $params['product_id'][$keyCode],
                        'm_code_id' => $value
                    ])->delete();
                }
            }
        }
        foreach ($mCodes as $key => $mCode) {
            foreach ($mCode as $keyValue => $value) {
                $resultCode = $this->mCodeService->updateOrCreate(
                    [
                        'name' => $value,
                    ],
                    [
                        'name' => $value,
                        'type' => (int) $params['product_type'][$key][$keyValue],
                        'admin_id' => Auth::user()->id,
                    ]
                );
                $this->productCodeService->create([
                    'm_product_id' => $params['product_id'][$key],
                    'm_code_id' => $resultCode->id
                ]);
            }
        }
    }

    /**
     * Get select plan price with service type.
     *
     * @param int $serviceType
     * @param string $packageType
     * @return MPriceList
     */
    public function getSelectPlanPrice(int $serviceType, string $packageType): ?MPriceList
    {
        return $this->repository->getSelectPlanPrice($serviceType, $packageType);
    }

    /**
     * Save data of select plan.
     *
     * @param array $param
     * @return array
     */
    public function saveDataSelectPlan($params): array
    {
        try {
            DB::beginTransaction();
            $paymentFee = $this->getSystemFee(MPriceList::EACH_PAYMENT, MPriceList::BANK_TRANSFER_HANDLING);
            $trademark = $this->trademarkRepository->find($params['trademark_id']);
            $comparisonTrademarkResult = $this->find($params['id']);

            $comparisonTrademarkResult->update([
                'step' => ComparisonTrademarkResult::STEP_2,
            ]);
            $comparisonTrademarkResult->load('planCorrespondence');

            $productIds = [];
            foreach ($params['is_choice'] as $PCPid => $value) {
                $reasonRefNumProd = $this->reasonRefNumProdRepository->updateOrCreate(['id' => $PCPid], [
                    'is_choice' => (int) $value,
                ]);
                $reasonRefNumProd->load('planCorrespondenceProd.appTrademarkProd.mProduct');
                if ((int) $value) {
                    array_push($productIds, $reasonRefNumProd->planCorrespondenceProd->appTrademarkProd->mProduct->id);
                }
            }

            $payerInfo = $this->payerInfoRepository->updateOrCreate([
                'target_id' => $comparisonTrademarkResult->planCorrespondence->id,
                'type' => TYPE_SELECT_PLAN_02
            ], [
                'target_id' => $comparisonTrademarkResult->planCorrespondence->id,
                'payment_type' => $params['payment_type'] ?? Payment::CREDIT_CARD,
                'payer_type' => $params['payer_type'] ?? 0,
                'm_nation_id' => $params['m_nation_id'] ?? 1,
                'payer_name' => $params['payer_name'] ?? '',
                'payer_name_furigana' => $params['payer_name_furigana'] ?? '',
                'postal_code' => $params['postal_code'] ?? null,
                'm_prefecture_id' => $params['m_prefecture_id'] ?? null,
                'address_second' => $params['address_second'] ?? '',
                'address_three' => $params['address_three'] ?? '',
                'type' => TYPE_SELECT_PLAN_02,
            ]);

            $bankTransferFee = 0;
            if (isset($params['payment_type']) && $params['payment_type'] == Payment::BANK_TRANSFER) {
                $bankTransferFee = $paymentFee['cost_service_base'];
            }

            $dataPayment = [
                'target_id' => $comparisonTrademarkResult->planCorrespondence->id,
                'trademark_id' => $trademark->id,
                'payer_info_id' => $payerInfo->id,
                'cost_service_base' => floor($params['cost_service_base']),
                'cost_service_add_prod' => floor($params['cost_service_add_prod']),
                'cost_bank_transfer' => floor($bankTransferFee),
                'subtotal' => floor($params['subtotal']) ?? 0,
                'commission' => floor($params['commission']) ?? 0,
                'tax' => floor($params['tax']) ?? 0,
                'payment_status' => $params['payment_status'] ?? Payment::STATUS_SAVE,
                'total_amount' => floor($params['subtotal']) ?? 0,
                'tax_withholding' => 0,
                'payment_amount' => 0,
                'type' => TYPE_SELECT_PLAN_02,
                'from_page' => $params['from_page'] ?? '',
                'is_treatment' => Payment::IS_TREATMENT_WAIT,
                'payment_status' => Payment::STATUS_SAVE
            ];

            $dataPayment['tax_withholding'] = floor($this->getTaxWithHolding($payerInfo, $params['subtotal']));

            $payment = $this->paymentRepository->updateOrCreate([
                'target_id' => $comparisonTrademarkResult->planCorrespondence->id,
                'from_page' => $params['from_page'],
            ], $dataPayment);

            $dataUpdatePayment = [];
            if (!$payment->quote_number) {
                $dataUpdatePayment['quote_number'] = $this->generateQIR($trademark->trademark_number, 'quote');
            }
            if (!$payment->invoice_number) {
                $dataUpdatePayment['invoice_number'] = $this->generateQIR('', 'invoice');
            }
            if (!$payment->invoice_number) {
                $dataUpdatePayment['receipt_number'] = $this->generateQIR('', 'receipt');
            }
            $paymentAmount = floor($dataPayment['total_amount'] - $dataPayment['tax_withholding']);
            $dataPayment['payment_amount'] = $paymentAmount;

            $payment->update($dataUpdatePayment);

            $dataPaymentPro = [];
            $dataPaymentPro['payment_id'] = $payment->id;
            $dataPaymentPro['productIds'] = $productIds;

            $this->paymentRepository->createPaymentProds($dataPaymentPro);

            $key = Str::random(11);

            switch ($params['redirect_to']) {
                case AppTrademark::REDIRECT_TO_QUOTE:
                    DB::commit();

                    return [
                        'redirect_to' => AppTrademark::REDIRECT_TO_QUOTE,
                        'key_session' => '',
                        'payment_id' => $payment->id,
                    ];
                case AppTrademark::REDIRECT_TO_COMMON_PAYMENT:
                    if (count($productIds) == 0) {
                        return [
                            'redirect_to' => 'false',
                            'key_session' => '',
                        ];
                    }
                    $params['productIds'] = $productIds;
                    $params['payment_id'] = $payment->id;
                    Session::put($key, $params);

                    $redirectTo = AppTrademark::REDIRECT_TO_COMMON_PAYMENT;
                    if ($payment->total_amount == 0) {
                        $payment->update([
                            'is_treatment' => Payment::IS_TREATMENT_DONE,
                            'payment_status' => Payment::STATUS_PAID,
                        ]);

                        $paymentNotice = App::make(PaymentNotice::class);
                        $sessionData = Session::get($key);
                        $paymentNotice->setData($sessionData);
                        $paymentNotice->notice($payment, Payment::CREDIT_CARD);

                        $redirectTo = AppTrademark::REDIRECT_TO_GMO_THANK_YOU;
                    }

                    DB::commit();

                    return [
                        'redirect_to' => $redirectTo,
                        'key_session' => $key,
                        'payment_id' => $payment->id,
                    ];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Get comparsion tradeMark result authenticate
     *
     * @param $comparisonTrademarkResultId
     * @return Collection
     */
    public function getComparisonTradeMarkResultAuthenticate($comparisonTrademarkResultId)
    {
        return $this->repository->getComparisonTradeMarkResultAuthenticate($comparisonTrademarkResultId);
    }

    /**
     * Save prequestion re show admin
     *
     * @param array $inputs
     * @return void
     * @throws \Exception
     */
    public function savePreQuestionReShowAdmin(array $inputs)
    {
        DB::beginTransaction();
        try {
            $comparisonTrademarkResult = $this->repository->find($inputs['comparison_trademark_result_id']);

            $dataUpdate = [];
            //1. reason question
            $reasonQuestionNo = $this->reasonQuestionNoRepository->find($inputs['reason_question_no_draft']);
            if (!$reasonQuestionNo) {
                $reasonQuestionNo = $this->reasonQuestionNoRepository->create([
                    'reason_question_id' => $inputs['reason_question_id'],
                    'admin_id' => auth()->guard(ADMIN_ROLE)->user()->id
                ]);
            }
            $userResponseDeadline = Carbon::createFromFormat('Y年m月d日', $inputs['user_response_deadline'])->format('Y-m-d H:m:s');
            $dataUpdate['user_response_deadline'] = $userResponseDeadline;
            $dataUpdate['question_status'] = $inputs['question_status'] ?? ReasonQuestionNo::QUESTION_STATUS_DONT;

            if (in_array($inputs['code'], [SAVE_TO_END_USER, SAVE_COMPLATE_QUESTION])) {
                $dataUpdate['is_confirm'] = ReasonQuestionNo::IS_CONFIRM;
            }
            if ($inputs['code'] == SAVE_TO_END_USER) {
                $dataUpdate['is_confirm'] = ReasonQuestionNo::IS_CONFIRM;
                //send to end user
                $dataUpdate['question_status'] = ReasonQuestion::QUESTION_STATUS_DONT;
            } elseif ($inputs['code'] == SAVE_COMPLATE_QUESTION) {
                $dataUpdate['is_confirm'] = ReasonQuestionNo::IS_CONFIRM;
                $dataUpdate['question_status'] = ReasonQuestionNo::QUESTION_STATUS_NECESSARY;
            }

            //update reason_questions
            $this->reasonQuestionNoRepository->update($reasonQuestionNo, $dataUpdate);

            //2. reason_comments
            $this->reasonCommentRepository->updateOrCreate([
                'plan_correspondence_id' => $inputs['plan_correspondence_id'],
                'type' => ReasonComment::TYPE_1,
                'type_comment_step' => ReasonComment::STEP_2,
            ], [
                'admin_id' => Admin::getAdminIdByRole(Admin::ROLE_ADMIN_TANTO),
                'plan_correspondence_id' => $inputs['plan_correspondence_id'],
                'type' => ReasonComment::TYPE_1,
                'type_comment_step' => ReasonComment::STEP_2,
                'content' => $inputs['content']
            ]);

            //3. create or update data reason_question_details
            if (!empty($inputs['data'])) {
                foreach ($inputs['data'] as $reasonQuestionDetail) {
                    //delete old data
                    if (isset($reasonQuestionDetail['delete_status']) && $reasonQuestionDetail['delete_status'] == 'on') {
                        if (isset($reasonQuestionDetail['reason_question_detail_id'])) {
                            $modelDetail = $this->reasonQuestionDetailRepository->find($reasonQuestionDetail['reason_question_detail_id']);
                            $this->reasonQuestionDetailRepository->delete($modelDetail);
                        }
                    } else {
                        $dataUpdateQuesDetail = [
                            'question' => $reasonQuestionDetail['question'],
                            'reason_question_id' => $inputs['reason_question_id'],
                            'reason_question_no_id' => $reasonQuestionNo->id,
                        ];
                        //create or update data
                        $this->reasonQuestionDetailRepository->updateOrCreate([
                            'id' => $reasonQuestionDetail['reason_question_detail_id'] ?? null,
                        ], $dataUpdateQuesDetail);
                    }
                }
            }

            // Set response deadline
            $responseDeadlineA000AnkenTop = null;
            $responseDeadlineU000Top = null;
            $responseDeadlineU000AnkenTop = null;
            $responseDeadlineA000Top = null;

            $planCorrespondence = $comparisonTrademarkResult->planCorrespondence;
            $machingResult = $comparisonTrademarkResult->machingResult;

            $responseDeadlineA000AnkenTop = $machingResult->calculateResponseDeadline(-15);
            $responseDeadlineU000Top = $machingResult->calculateResponseDeadline(-11);
            $responseDeadlineU000AnkenTop = $machingResult->calculateResponseDeadline(-11);
            $responseDeadlineA000Top = $machingResult->calculateResponseDeadline(-15);
            if (!empty($machingResult) && $planCorrespondence->type == PlanCorrespondence::TYPE_2) {
                $responseDeadlineA000AnkenTop = $machingResult->calculateResponseDeadline(-18);
                $responseDeadlineU000Top = $machingResult->calculateResponseDeadline(-16);
                $responseDeadlineU000AnkenTop = $machingResult->calculateResponseDeadline(-16);
                $responseDeadlineA000Top = $machingResult->calculateResponseDeadline(-21);
            }

            //send notices
            if ($inputs['code'] == SAVE_TO_END_USER) {
                //update comment notices
                $this->noticeService->updateComment(
                    Notice::FLOW_RESPONSE_REASON,
                    $inputs['content'] ?? null,
                    $comparisonTrademarkResult->trademark_id
                );

                // Update Notice at u202 (No 54: F G)
                $stepBeforeNotice = $this->noticeDetailService->findByCondition([
                    'completion_date' => null,
                ])->with('notice')->get()
                    ->where('type_acc', '<>', NoticeDetail::TYPE_USER)
                    ->where('notice.trademark_id', $inputs['trademark_id'])
                    ->where('notice.user_id', $inputs['user_id'])
                    ->whereIn('notice.flow', [Notice::FLOW_RESPONSE_REASON, Notice::FLOW_RENEWAL_BEFORE_DEADLINE])
                    ->filter(function ($item) {
                        if ($item->notice->flow == Notice::FLOW_RESPONSE_REASON && $item->notice->step == Notice::STEP_2) {
                            return true;
                        } elseif ($item->notice->flow == Notice::FLOW_RENEWAL_BEFORE_DEADLINE && $item->notice->step == null) {
                            return true;
                        }
                        return false;
                    });
                $stepBeforeNotice->map(function ($item) {
                    $item->update([
                        'completion_date' => Carbon::now(),
                    ]);
                });

                $this->noticeService->sendNotice([
                    'notices' => [
                        'trademark_id' => $inputs['trademark_id'],
                        'trademark_info_id' => null,
                        'flow' => Notice::FLOW_RESPONSE_REASON,
                        'step' => Notice::STEP_2,
                        'user_id' => $inputs['user_id']
                    ],
                    'notice_details' => [
                        // Send Notice Seki: A-000anken_top
                        [
                            'target_id' => null,
                            'type_acc' => NoticeDetail::TYPE_SUPERVISOR,
                            'target_page' => route('admin.refusal.pre-question-re.supervisor.show', [
                                'id' => $inputs['comparison_trademark_result_id'],
                                'reason_question_no' => $inputs['reason_question_no_id'],
                            ]),
                            'redirect_page' => null,
                            'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                            'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                            'response_deadline' => $responseDeadlineA000AnkenTop,
                            'content' => '責任者　拒絶理由通知対応：事前質問連絡済',
                            'attribute' => 'お客様へ',
                            'completion_date' => now(),
                        ],
                        //Send Notice to user: U-000top
                        [
                            'target_id' => $inputs['user_id'],
                            'type_acc' => NoticeDetail::TYPE_USER,
                            'target_page' => route('admin.refusal.pre-question-re.supervisor.show', [
                                'id' => $inputs['comparison_trademark_result_id'],
                                'reason_question_no' => $inputs['reason_question_no_id'],
                            ]),
                            'redirect_page' => route('user.refusal.pre-question.re-reply', [
                                'id' => $inputs['comparison_trademark_result_id'],
                                'reason_question_no' => $reasonQuestionNo->id,
                            ]),
                            'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                            'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                            'is_action' => NoticeDetail::IS_ACTION_TRUE,
                            'content' => '拒絶理由通知対応：事前質問',
                            'response_deadline' => $responseDeadlineU000Top,
                            'response_deadline_ams' => $reasonQuestionNo->user_response_deadline ?? null,
                        ],

                        //Send Notice to user: U-000anken_top
                        [
                            'target_id' => $inputs['user_id'],
                            'type_acc' => NoticeDetail::TYPE_USER,
                            'target_page' => route('admin.refusal.pre-question-re.supervisor.show', [
                                'id' => $inputs['comparison_trademark_result_id'],
                                'reason_question_no' => $inputs['reason_question_no_id'],
                            ]),
                            'redirect_page' => route('user.refusal.pre-question.re-reply', [
                                'id' => $inputs['comparison_trademark_result_id'],
                                'reason_question_no' => $reasonQuestionNo->id,
                            ]),
                            'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                            'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                            'content' => '拒絶理由通知対応：事前質問',
                            'response_deadline' => $responseDeadlineU000AnkenTop,
                            'response_deadline_ams' => $reasonQuestionNo->user_response_deadline ?? null,
                        ],
                    ],
                ]);
            } elseif ($inputs['code'] == SAVE_COMPLATE_QUESTION) {
                //update comment notices
                $this->noticeService->updateComment(
                    Notice::FLOW_RESPONSE_REASON,
                    $inputs['content'] ?? null,
                    $comparisonTrademarkResult->trademark_id
                );

                // Update Notice at u202 (No 54: F G)
                //u202
                $targetPage = route('user.refusal.pre-question.reply', ['id' => $inputs['comparison_trademark_result_id'], 'reason_question_no' => $inputs['reason_question_no_id']]);
                $targetPage = str_replace(request()->root(), '', $targetPage);
                $routeA203 = route('admin.refusal.response-plan.index', $inputs['comparison_trademark_result_id']);

                $stepBeforeNotice = $this->noticeDetailService->findByCondition([
                    'completion_date' => null,
                ])->with('notice')->get()
                    ->where('type_acc', '<>', NoticeDetail::TYPE_USER)
                    ->where('notice.trademark_id', $inputs['trademark_id'])
                    ->where('notice.user_id', $inputs['user_id'])
                    ->whereIn('notice.flow', [Notice::FLOW_RESPONSE_REASON, Notice::FLOW_RENEWAL_BEFORE_DEADLINE])
                    ->filter(function ($item) {
                        if ($item->notice->flow == Notice::FLOW_RESPONSE_REASON && $item->notice->step == Notice::STEP_2) {
                            return true;
                        } elseif ($item->notice->flow == Notice::FLOW_RENEWAL_BEFORE_DEADLINE && $item->notice->step == null) {
                            return true;
                        }
                        return false;
                    });
                $stepBeforeNotice->map(function ($item) {
                    $item->update([
                        'completion_date' => Carbon::now(),
                    ]);
                });

                $this->noticeService->sendNotice([
                    'notices' => [
                        'trademark_id' => $inputs['trademark_id'],
                        'trademark_info_id' => null,
                        'flow' => Notice::FLOW_RESPONSE_REASON,
                        'step' => Notice::STEP_3,
                        'user_id' => $inputs['user_id']
                    ],
                    'notice_details' => [
                        //A000 top
                        [
                            'target_id' => null,
                            'type_acc' => NoticeDetail::TYPE_MANAGER,
                            'target_page' => route('admin.refusal.pre-question-re.supervisor.show', [
                                'id' => $inputs['comparison_trademark_result_id'],
                                'reason_question_no' => $inputs['reason_question_no_id'],
                            ]),
                            'redirect_page' => $routeA203,
                            'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                            'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                            'is_action' => NoticeDetail::IS_ACTION_TRUE,
                            'content' => '担当者　拒絶理由通知対応：方針案作成',
                            'response_deadline' => $responseDeadlineA000Top,
                        ],
                        // Send Notice Seki: A-000anken_top
                        [
                            'target_id' => null,
                            'type_acc' => NoticeDetail::TYPE_MANAGER,
                            'target_page' => route('admin.refusal.pre-question-re.supervisor.show', [
                                'id' => $inputs['comparison_trademark_result_id'],
                                'reason_question_no' => $inputs['reason_question_no_id'],
                            ]),
                            'redirect_page' => $routeA203,
                            'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                            'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                            'response_deadline' => $responseDeadlineA000Top,
                            'content' => '担当者　拒絶理由通知対応：方針案作成'
                        ],
                    ],
                ]);
            }
            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            throw new \Exception($e->getMessage());
        }
        return false;
    }

    /**
     * It sends a notification to the user.
     *
     * @param Model comparisonTrademarkResult
     */
    public function noticeCreateExamineForLawyer(Model $comparisonTrademarkResult, $reasonNo)
    {
        $comparisonTrademarkResult = $comparisonTrademarkResult->load([
            'trademark',
            'machingResult',
            'planCorrespondence',
        ]);

        $trademark = $comparisonTrademarkResult->trademark;
        $stepBeforeNotice = $this->noticeDetailService->findByCondition([
            'completion_date' => null,
        ])->with('notice')->get()
            ->where('type_acc', '<>', NoticeDetail::TYPE_USER)
            ->where('notice.trademark_id', $trademark->id)
            ->where('notice.user_id', $trademark->user_id)
            ->whereIn('notice.flow', [Notice::FLOW_RESPONSE_REASON, Notice::FLOW_RENEWAL_BEFORE_DEADLINE])
            ->filter(function ($item) {
                if ($item->notice->flow == Notice::FLOW_RESPONSE_REASON && $item->notice->step == Notice::STEP_1) {
                    return true;
                } elseif ($item->notice->flow == Notice::FLOW_RENEWAL_BEFORE_DEADLINE && $item->notice->step == null) {
                    return true;
                }
                return false;
            });
        $stepBeforeNotice->map(function ($item) {
            $item->update([
                'completion_date' => \Carbon\Carbon::now(),
            ]);
        });

        $tantou = Admin::where('role', ROLE_MANAGER)->first();

        $subDays = -7;
        $matchingResult = $comparisonTrademarkResult->machingResult;
        $planCorrespondence = $comparisonTrademarkResult->planCorrespondence;
        if ($planCorrespondence->type == PlanCorrespondence::TYPE_SELECT) {
            $subDays = -11;
        }

        $responseDeadline = $matchingResult->calculateResponseDeadline($subDays);

        $this->noticeService->sendNotice([
            'notices' => [
                'trademark_id' => $trademark->id,
                'trademark_info_id' => null,
                'user_id' => $trademark->user_id,
                'flow' => Notice::FLOW_RESPONSE_REASON,
                'step' => Notice::STEP_2,
                'created_at' => now(),
            ],
            'notice_details' => [
                [
                    'target_id' => $tantou->id,
                    'type_acc' => ROLE_SUPERVISOR,
                    'target_page' => route('admin.refusal.eval-report.create-examine.supervisor', [
                        'id' => $comparisonTrademarkResult->id,
                        'reason_no_id' => $reasonNo->id,
                    ]),
                    'redirect_page' => route('admin.refusal.pre-question.index', [
                        'id' => $comparisonTrademarkResult->id,
                        'reason_no_id' => $reasonNo->id,
                    ]),
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                    'is_action' => NoticeDetail::IS_ACTION_TRUE,
                    'content' => '担当者　拒絶理由通知対応：事前質問作成',
                    'response_deadline' => $responseDeadline,
                    'created_at' => now()
                ],
                [
                    'target_id' => $tantou->id,
                    'type_acc' => ROLE_SUPERVISOR,
                    'target_page' => route('admin.refusal.eval-report.create-examine.supervisor', [
                        'id' => $comparisonTrademarkResult->id,
                        'reason_no_id' => $reasonNo->id,
                    ]),
                    'redirect_page' => route('admin.refusal.pre-question.index', [
                        'id' => $comparisonTrademarkResult->id,
                        'reason_no_id' => $reasonNo->id,
                    ]),
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'content' => '担当者　拒絶理由通知対応：事前質問作成',
                    'attribute' => '所内処理',
                    'response_deadline' => $responseDeadline,
                    'created_at' => now()
                ],
            ],
        ]);
    }
}
