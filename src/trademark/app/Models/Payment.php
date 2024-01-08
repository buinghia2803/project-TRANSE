<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class Payment extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'target_id',
        'payer_info_id',
        'trademark_id',
        'quote_number',
        'invoice_number',
        'receipt_number',
        'cost_service_base',
        'cost_service_add_prod',
        'cost_bank_transfer',
        'cost_registration_certificate',
        'extension_of_period_before_expiry',
        'application_discount',
        'subtotal',
        'commission',
        'tax',
        'tax_incidence',
        'print_fee',
        'cost_print_application_one_distintion',
        'cost_print_application_add_distintion',
        'costs_correspondence_of_one_prod',
        'cost_change_registration_period',
        'reduce_number_distitions',
        'cost_5_year_one_distintion',
        'cost_10_year_one_distintion',
        'cost_change_address',
        'cost_change_name',
        'cost_print_name',
        'cost_print_address',
        'total_amount',
        'tax_withholding',
        'payment_amount',
        'type',
        'is_confirm',
        'comment',
        'payment_date',
        'is_treatment',
        'payment_status',
        'payment_number',
        'treatment_date',
        'from_page',
        'is_send_notice',
        'reduce_distinctions',
        'date_of_sending_payment_email',
    ];

    /**
     * Const type
     */
    // 1: 出願
    const TYPE_TRADEMARK = 1;
    // 2: はじめからサポート お申し込み
    const TYPE_SUPPORT_FIRST_TIME = 2;
    // 3: はじめからサポートサービス：AMSからの提案
    const TYPE_SUPPORT_FIRST_TIME_AMS = 3;
    // 4: プレチェックサービス
    const TYPE_PRECHECK = 4;
    // 5: プレチェックサービス：AMSからのレポート
    const TYPE_PRECHECK_AMS = 5;
    // 6: 拒絶理由通知対応
    const TYPE_REASON_REFUSAL = 6;
    // 7: 拒絶理由通知対応：方針案選択
    const TYPE_SELECT_POLICY = 7;
    // 8: 商標登録
    const TYPE_TRADEMARK_REGIS = 8;
    // 9: 後期納付期限のお知らせ・納付手続きのお申込み
    const TYPE_LATE_PAYMENT = 9;
    // 10: 更新期限のお知らせ・更新手続きのお申込み
    const RENEWAL_DEADLINE = 10;
    // 11: 出願人名・住所変更
    const CHANG_ADDRESS = 11;
    // 12: 登録名義人名・住所変更
    const CHANG_NAME = 12;
    // 13: 期限日前期間延長のお申込み
    const BEFORE_DUE_DATE = 13;

    // Payment types
    const CREDIT_CARD = 1;
    const BANK_TRANSFER = 2;

    //is_treatment
    const IS_TREATMENT_DONE = 1;
    const IS_TREATMENT_WAIT = 0;

    // Payment status
    const STATUS_SAVE = 0; // 0: 保存, // 見積書
    const STATUS_WAITING_PAYMENT = 1; // 1: お支払待ち, // 請求書
    const STATUS_PAID = 2; // 2: お支払済み // 領収書

    const WITH_HOLDING_TAX_NUM = 1000000;
    const WITH_HOLDING_TAX_MIN = 10.21;
    const WITH_HOLDING_TAX_MAX = 20.42;

    const IS_SEND_NOTICE_FALSE = 0;
    const IS_SEND_NOTICE_TRUE = 1;

    /**
     * Payer Information
     *
     * @return BelongsTo
     */
    public function payerInfo(): BelongsTo
    {
        return $this->belongsTo(PayerInfo::class, 'payer_info_id', 'id');
    }

    /**
     * Relation payment with application trademark
     *
     * @var BelongsTo
     */
    public function appTrademark(): BelongsTo
    {
        return $this->belongsTo(AppTrademark::class, 'target_id', 'id');
    }

    /**
     * Relation payment with application trademark
     *
     * @var BelongsTo
     */
    public function sft(): BelongsTo
    {
        return $this->belongsTo(SupportFirstTime::class);
    }

    /**
     * Payer Information
     *
     * @return HasMany
     */
    public function paymentProds(): HasMany
    {
        return $this->hasMany(PaymentProd::class);
    }

    /**
     * Trademark
     *
     * @return BelongsTo
     */
    public function trademark(): BelongsTo
    {
        return $this->belongsTo(Trademark::class, 'trademark_id');
    }

    /**
     * Change Info Register
     *
     * @return HasOne
     */
    public function changeInfoRegister(): HasOne
    {
        return $this->hasOne(ChangeInfoRegister::class);
    }

    /**
     * Payer Information
     *
     * @return array
     */
    public function getProductInvoice(): array
    {
        $resultArray = [];

        $paymentProds = $this->paymentProds;
        foreach ($paymentProds as $paymentProd) {
            $product = $paymentProd->product;
            $distinction = $product->mDistinction;
            $resultArray[$distinction->id]['distinction_name'] = $distinction->name;
            $resultArray[$distinction->id]['products'][] = $product;
        }

        return $resultArray;
    }

    /**
     * Get data with TargetId
     *
     * @return self
     */
    public function loadDataTargetId(): self
    {
        switch ($this->type) {
            case Payment::TYPE_TRADEMARK:
            case Payment::TYPE_SUPPORT_FIRST_TIME_AMS:
            case Payment::TYPE_PRECHECK_AMS:
            case Payment::CHANG_ADDRESS:
            case Payment::BEFORE_DUE_DATE:
                $appTrademark = AppTrademark::where('id', $this->target_id)->with(['trademark'])->first();

                // Get Pack Name
                $appTrademark->pack_name = $appTrademark->getPackName();

                // Get Pack Detail
                $appTrademark->pack_detail = $appTrademark->getPackDetail();

                $this->trademark = $appTrademark->trademark;
                $this->app_trademark = $appTrademark;
                break;

            case Payment::TYPE_SUPPORT_FIRST_TIME:
                $supportFirstTime = SupportFirstTime::with(['trademark'])->find($this->target_id);

                $this->trademark = $supportFirstTime->trademark;
                $this->support_first_time = $supportFirstTime;
                break;

            case Payment::TYPE_PRECHECK:
                $precheck = Precheck::with(['trademark'])->find($this->target_id);

                $this->trademark = $precheck->trademark;
                $this->precheck = $precheck;
                break;

            case Payment::TYPE_REASON_REFUSAL:
                $macingResult = MatchingResult::with(['trademark', 'trademark.appTrademark'])->find($this->target_id);
                $trademark = $macingResult->trademark;

                $this->appTrademark = $trademark->appTrademark;
                $this->trademark = $trademark;
                $this->matching_result = $macingResult;
                break;
            case Payment::TYPE_SELECT_POLICY:
                $macingResult = MatchingResult::with(['trademark', 'trademark.appTrademark'])->where('trademark_id', $this->trademark_id)->first();
                $trademark = $macingResult->trademark;

                $this->appTrademark = $trademark->appTrademark;
                $this->trademark = $trademark;
                $this->matching_result = $macingResult;
                break;

            case Payment::TYPE_TRADEMARK_REGIS:
            case Payment::TYPE_LATE_PAYMENT:
            case Payment::RENEWAL_DEADLINE:
            case Payment::CHANG_NAME:
                $registerTrademark = RegisterTrademark::with(['trademark'])->find($this->target_id);

                $this->trademark = $registerTrademark->trademark;
                $this->register_trademark = $registerTrademark;
                break;

            default:
        }

        return $this;
    }

    /**
     * Get payment info
     *
     * @return self
     */
    public function withPaymentInfo(): self
    {
        $paymentProds = $this->paymentProds ?? [];

        // Get Total Product and distinction
        $productData = collect();
        $distinctionData = collect();
        foreach ($paymentProds as $paymentProd) {
            $product = $paymentProd->product;
            if ($product) {
                $distinction = $product->mDistinction;
                $productData[] = $product;
                $distinctionData[] = $distinction;
            }
        }

        $totalProd = $productData->count();
        $totalDistinction = count(array_unique($distinctionData->pluck('id')->toArray()));

        $this->total_prod = $totalProd;
        $this->total_prod_block = $totalProd > 3 ? $totalProd - 3 : 0;
        $this->total_distinction = $totalDistinction;
        $this->total_distinction_block_one = $totalDistinction > 1 ? $totalDistinction - 1 : 0;
        $this->total_distinction_block = $totalDistinction > 3 ? $totalDistinction - 3 : 0;

        // Cost Service
        $costServiceBase = $this->cost_service_base ?? 0;
        $costServiceAddProd = $this->cost_service_add_prod ?? 0;

        $block = count($productData->chunk(3)) >= 1 ? count($paymentProds->chunk(3)) - 1 : 0;

        $costServiceTotalBlock = $costServiceAddProd * $block;
        $costService = $costServiceBase + $costServiceTotalBlock;
        $costServiceAllProd = $costServiceAddProd * $totalProd;

        $this->cost_service = $costService;
        $this->cost_service_total_block = $costServiceTotalBlock;
        $this->cost_service_all_prod = $costServiceAllProd;

        // Cost print application
        $costPrintAppOneDistinction = $this->cost_print_application_one_distintion ?? 0;
        $costPrintAppAddDistinction = $this->cost_print_application_add_distintion ?? 0;

        $costPrintAppTotalBlock = $costPrintAppAddDistinction * ($totalDistinction - 1);
        $costPrintApp = $costPrintAppOneDistinction + $costPrintAppTotalBlock;

        $this->cost_print_app = $costPrintApp;
        $this->cost_print_app_total_block = $costPrintAppTotalBlock;

        // Cost 5 year Distinction
        $cost5yearOneDistinction = $this->cost_5_year_one_distintion ?? 0;
        $cost5yearAllDistinction = $totalDistinction * $cost5yearOneDistinction;
        $this->cost_5_year_all_distintion = $cost5yearAllDistinction;

        // Cost 10 year Distinction
        $cost10yearOneDistinction = $this->cost_10_year_one_distintion ?? 0;
        $cost10yearAllDistinction = $totalDistinction * $cost10yearOneDistinction;
        $this->cost_10_year_all_distintion = $cost10yearAllDistinction;

        // Cost correspondence of one prod
        $costCorrespondenceOneProduct = $this->costs_correspondence_of_one_prod;
        $costCorrespondenceAllProduct = $totalDistinction * $costCorrespondenceOneProduct;
        $this->cost_correspondence_of_all_prod = $costCorrespondenceAllProduct;

        return $this;
    }

    /**
     * Get search fields
     *
     * @return array
     */
    public function getSearchFields(): array
    {
        return [
            '' => [
                'title' => __('labels.payment_all.select_default'),
            ],
            'created_at' => [
                'title' => __('labels.payment_all.created_at'),
                'typing' => 'date',
            ],
            'payment_date' => [
                'title' => __('labels.payment_all.payment_date'),
                'typing' => 'date',
            ],
            'updated_at' => [
                'title' => __('labels.payment_all.updated_at'),
                'typing' => 'date',
            ],
            'user_info_name' => [
                'title' => __('labels.payment_all.user_info_name'),
            ],
            'payer_info_payer_name' => [
                'title' => __('labels.payment_all.payer_info_payer_name'),
            ],
            'trademark_number' => [
                'title' => __('labels.payment_all.trademark_number'),
            ]
        ];
    }

    /**
     * Get list condition all
     *
     * @return array
     */
    public function getListConditionAll(): array
    {
        return [
            'equal' => __('labels.payment_all.equal'),
            'start_from' => __('labels.payment_all.start_from'),
            'consists_of' => __('labels.payment_all.consists_of'),
            'is_greater_than' => __('labels.payment_all.is_greater_than'),
            'is_less_than' => __('labels.payment_all.is_less_than'),
        ];
    }

    /**
     * Get list condition
     *
     * @return array
     */
    public function getListCondition(): array
    {
        return [
            'equal' => __('labels.payment_all.equal'),
            'start_from' => __('labels.payment_all.start_from'),
            'consists_of' => __('labels.payment_all.consists_of'),
        ];
    }

    /**
     * Get list condition
     *
     * @return array
     */
    public function getListConditionDate(): array
    {
        return [
            'equal' => __('labels.payment_all.equal'),
            'is_greater_than' => __('labels.payment_all.is_greater_than'),
            'is_less_than' => __('labels.payment_all.is_less_than'),
        ];
    }

    /**
     * Get Created At
     *
     * @return void
     */
    public function getCreatedAt()
    {
        $now = Carbon::now()->format('Ymd');
        $createdAt = Carbon::parse($this->created_at)->format('Ymd');
        $result = $now - $createdAt;
        if ($result > DAY_EXPIRED) {
            return true;
        }

        return false;
    }

    /**
     * Check Background
     *
     * @return void
     */
    public function checkBackground7Day()
    {
        $now = Carbon::now()->format('Ymd');
        $createdAt = Carbon::parse($this->created_at)->addWeek()->format('Ymd');
        if ($now > $createdAt) {
            return true;
        }

        return false;
    }

    /**
     * Check Background
     *
     * @return void
     */
    public function checkBackground3Day()
    {
        $now = Carbon::now()->format('Ymd');
        $createdAt = Carbon::parse($this->created_at)->addDays(3)->format('Ymd');
        if ($now > $createdAt) {
            return true;
        }

        return false;
    }

    /**
     * Show buttion delete payment all
     *
     * @return bool
     */
    public function showButtonDeletePaymentAll(): bool
    {
        if ($this->payerInfo && $this->payerInfo->payment_type == PayerInfo::PAYMENT_CREATE_CARD) {
            return false;
        }
        $nowDate = Carbon::now();
        if ($this->trademark && $this->trademark->comparisonTrademarkResult) {
            //2.payment has a term to CSC && if nowDate > comparison_trademark_results.response_deadline
            if ($nowDate->greaterThan($this->trademark->comparisonTrademarkResult->response_deadline)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get value payment due date
     *
     * @return void
     */
    public function getValuePaymentDueDate()
    {
        $paymentDueDate = null;
        if (in_array($this->type, [self::TYPE_TRADEMARK, self::TYPE_SUPPORT_FIRST_TIME, self::TYPE_PRECHECK])) {
            $paymentDueDate = Carbon::parse($this->created_at)->addWeek();
        }
        if (in_array($this->type, [
            self::TYPE_REASON_REFUSAL,
            self::TYPE_SELECT_POLICY,
            self::TYPE_TRADEMARK_REGIS,
            self::TYPE_LATE_PAYMENT,
            self::RENEWAL_DEADLINE,
            self::CHANG_ADDRESS,
            self::CHANG_NAME,
            self::BEFORE_DUE_DATE,
        ])) {
            $paymentDueDate = Carbon::parse($this->created_at)->addDays(3);
        }

        return $paymentDueDate;
    }

    /**
     * Get route back.
     *
     * @param string $secret
     * @param array $data
     * @return string|null
     */
    public function getRouteBack(string $secret, array $data = []): ?string
    {
        $fromPage = $this->from_page;
        if (str_contains($this->from_page, U201SELECT02)) {
            $fromPage = U201SELECT02;
        }
        if (str_contains($this->from_page, U201_SELECT_01_N)) {
            $fromPage = U201_SELECT_01_N;
        }
        if (str_contains($this->from_page, U302_402_5YR_KOUKI)) {
            $fromPage = U302_402_5YR_KOUKI;
        }
        if (str_contains($this->from_page, U302_402TSUINO_5YR_KOUKI)) {
            $fromPage = U302_402TSUINO_5YR_KOUKI;
        }
        if (str_contains($this->from_page, U402TSUINO)) {
            $fromPage = U402TSUINO;
        } elseif (str_contains($this->from_page, U402)) {
            $fromPage = U402;
        }

        if (!empty($data['back_url'])) {
            return $data['back_url'];
        }

        switch ($fromPage) {
            case U011:
                $sft = SupportFirstTime::find($this->target_id);
                $sft->load('trademark');
                $trademark = $sft->trademark;
                $data = Session::get($secret);

                $data['sft'] = $sft;
                Session::put($secret, $data);
                if (isset($data['route_back']) && $data['route_back']) {
                    return $data['route_back'];
                }
                return route('user.sft.index', ['s' => $secret]);
            case U011B:
                $appTrademark = AppTrademark::find($this->target_id)->load('trademark.supportFirstTime');
                $sft = $appTrademark->trademark->supportFirstTime;

                return route('user.support.first.time.u011b', ['id' => $sft->id]);
            case U011B_31:
                $appTrademark = AppTrademark::find($this->target_id)->load('trademark.supportFirstTime');
                $sft = $appTrademark->trademark->supportFirstTime;

                return route('user.sft.proposal-ams', ['id' => $sft->id]);
            case U021B:
                return route('user.precheck.application-trademark', ['id' => $this->trademark_id, 'precheck_id' => $data['precheck_id']]);
            case U021B_31:
                $data = Session::get($secret);
                return route('user.precheck.application-trademark-v2', ['id' => $this->trademark_id, 'precheck_id' => $data['precheck_id'] ?? '']);
            case U031:
                return route('user.apply-trademark-register', ['id' => $this->trademark_id ?? null]);
            case U031EDIT:
                return route('user.apply-trademark-free-input', ['id' => $this->trademark_id ?? null]);
            case U031_EDIT_WITH_NUMBER:
                return route('user.precheck.apply-trademark-with-number', ['id' => $this->trademark_id]);
            case U031B:
                return route('user.register-apply-trademark-after-search', ['id' => $this->trademark_id]);
            case U031C:
                return route('user.apply-trademark-with-product-copied', ['s' => $secret]);
            case U031D:
                return route('user.apply-trademark-without-number', [ 's' => $secret]);
            case U021:
                return route('user.precheck.register-precheck', ['id' => $this->trademark_id, 's' => $secret]);
            case U021N:
                return route('user.precheck.register-time-n', ['id' => $this->trademark_id, 'precheck_id' => $data['precheck_id_old'] ?? 0, 's' => $secret]);
            case U201_SELECT_01:
                return route('user.refusal.plans.select', ['comparison_trademark_result_id' => $data['comparison_trademark_result_id']]);
            case U201_SIMPLE:
                return route('user.refusal.plans.simple', ['comparison_trademark_result_id' => $data['comparison_trademark_result_id']]);
            case U201_SELECT_01_N:
                $matchingResult = MatchingResult::find($this->target_id);
                $matchingResult->load('comparisonTrademarkResult');
                $comparisonTrademarkResult = $matchingResult->comparisonTrademarkResult;

                return route('user.refusal.plans.select-eval-report-re', [
                    'comparison_trademark_result_id' => $comparisonTrademarkResult->id,
                    'reason_no_id' => $data['reason_no_id'] ?? 0
                ]);
            case U201SELECT02:
                $planCorrespondence = PlanCorrespondence::find($this->target_id);
                $planCorrespondence->load('comparisonTrademarkResult');
                $comparisonTrademarkResult = $planCorrespondence->comparisonTrademarkResult;
                $explodeFromPage = explode('_', $this->from_page);

                return route('user.refusal.select-eval-report-show', [
                    'id' => $comparisonTrademarkResult->id ?? 0,
                    'reason_no_id' => ($explodeFromPage[1] ?? 0),
                ]);
            case U203:
            case U203N:
                $dataSession = Session::get($secret);

                return route(
                    'user.refusal.response-plan.refusal_response_plan',
                    [
                        'comparison_trademark_result_id' => $dataSession['comparison_trademark_result_id'] ?? 0,
                        'trademark_plan_id' => $dataSession['trademark_plan_id'] ?? 0,
                    ]
                );
            case U210_OVER_02:
                $appTrademark = AppTrademark::find($this->target_id);
                $appTrademark->load('trademark');
                $trademark = $appTrademark->trademark;

                return route('user.refusal.extension-period.over', ['id' => $trademark->id]);
            case U210_ALERT_02:
                $appTrademark = AppTrademark::find($this->target_id);
                $appTrademark->load('trademark');
                $trademark = $appTrademark->trademark;

                return route('user.refusal.extension-period.alert', ['id' => $trademark->id]);
            case U031D:
                return route('user.apply-trademark-without-number', ['id' => $this->trademark_id]);
            case U031C:
                return route('user.apply-trademark-with-product-copied', ['id' => $this->trademark_id]);
            case U000LIST_CHANGE_ADDRESS_02:
                return route('user.application-list.change-address.applicant', ['id' => $this->trademark_id]);
            case U000LIST_CHANGE_ADDRESS_02_KENRISHA:
                return route('user.application-list.change-address.registered', ['id' => $this->trademark_id]);
            case U302:
                return route('user.registration.procedure', ['id' => $data['maching_result_id'], 'register_trademark_id' => $data['register_trademark_id']]);
            case U302_402_5YR_KOUKI:
                $data = Session::get($secret);
                return route('user.registration.notice-latter-period', $data['old_register_trademark']);
            case U302_402TSUINO_5YR_KOUKI:
                $data = Session::get($secret);
                return route('user.registration.notice-later-period.overdue', $data['old_register_trademark']);
            case U402:
                $data = Session::get($secret);
                return route('user.update.notify-procedure', $data['old_register_trademark']);
            case U402TSUINO:
                $data = Session::get($secret);
                return route('user.update.notify-procedure.overdue', $data['old_register_trademark']);

            case U000FREE:
                $data = Session::get($secret);
                return route('user.free-history.show-create', $data['free_history_id']);
        }

        if (str_contains($this->from_page, U203 . '_')) {
            $dataSession = Session::get($secret);
            return route('user.refusal.response-plan.refusal_response_plan_re', [
                'comparison_trademark_result_id' => $dataSession['comparison_trademark_result_id'] ?? 0,
                'trademark_plan_id' => $dataSession['trademark_plan_id'] ?? 0,
            ]);
        }

        return null;
    }
}
