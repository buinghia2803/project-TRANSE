<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\AppTrademarkProd;
use App\Models\MPriceList;
use App\Models\Notice;
use App\Models\Payment;
use App\Models\TrademarkDocument;
use App\Services\AppTrademarkProdService;
use App\Services\Common\TrademarkTableService;
use App\Services\ComparisonTrademarkResultService;
use App\Services\MNationService;
use App\Services\MPrefectureService;
use App\Services\MPriceListService;
use App\Services\MProductService;
use App\Services\PaymentService;
use App\Services\ReasonNoService;
use App\Services\TrademarkDocumentService;
use App\Services\TrademarkService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class PlanController extends Controller
{
    protected $comparisonTrademarkResultService;
    protected $mProductService;
    protected $mNationService;
    protected $mPrefectureService;
    protected $mPriceListService;
    protected $trademarkTableService;
    protected $appTrademarkProdService;
    protected $trademarkService;
    protected $reasonNoService;
    protected $paymentService;
    protected $trademarkDocumentService;

    /**
     * Constructor
     *
     * @return  void
     */
    public function __construct(
        ComparisonTrademarkResultService $comparisonTrademarkResultService,
        MNationService $mNationService,
        MPrefectureService $mPrefectureService,
        MPriceListService $mPriceListService,
        TrademarkTableService $trademarkTableService,
        MProductService $mProductService,
        AppTrademarkProdService $appTrademarkProdService,
        TrademarkService $trademarkService,
        ReasonNoService $reasonNoService,
        PaymentService $paymentService,
        TrademarkDocumentService $trademarkDocumentService
    )
    {
        $this->comparisonTrademarkResultService = $comparisonTrademarkResultService;
        $this->mNationService = $mNationService;
        $this->mPrefectureService = $mPrefectureService;
        $this->mPriceListService = $mPriceListService;
        $this->trademarkTableService = $trademarkTableService;
        $this->mProductService = $mProductService;
        $this->appTrademarkProdService = $appTrademarkProdService;
        $this->trademarkService = $trademarkService;
        $this->reasonNoService = $reasonNoService;
        $this->paymentService = $paymentService;
        $this->trademarkDocumentService = $trademarkDocumentService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * @param $id - comparison_trademark_result_id
     */
    public function showSimplePlan($id)
    {
        $comparisonTrademarkResult = $this->comparisonTrademarkResultService->findByCondition(['id' => $id])->with([
            'trademark',
            'trademark.payment' => function ($query) {
                return $query->where('type', Payment::TYPE_REASON_REFUSAL)->where('payment_status', Payment::STATUS_SAVE)->where('from_page', U201_SIMPLE);
            },
            'trademark.appTrademark',
            'trademark.trademarkDocuments' => function ($query) {
                return $query->where('type', TYPE_1);
            },
            'trademark.appTrademark.appTrademarkProd',
            'trademark.payment.payerInfo' => function ($query) {
                return $query->where('type', TYPE_MATCHING_RESULT);
            },
            'planCorrespondence' => function ($query) {
                return $query->where('type', TYPE_1);
            }
        ])->whereHas('trademark', function ($query) {
            return $query->where('user_id', Auth::user()->id);
        })->first();
        $flag = FLAG_SIMPLE;
        if (!$comparisonTrademarkResult) {
            abort(404);
        }

        if (isset($comparisonTrademarkResult->trademark->payment) && $comparisonTrademarkResult->trademark->payment->payment_status != Payment::STATUS_SAVE) {
            abort(404);
        }
        // $planCorrespondence = null;
        // if ($comparisonTrademarkResult->planCorrespondence) {
            // $comparisonTrademarkResult->planCorrespondence->where('type', TYPE_1)->first();
        // }
        $planCorrespondence = $comparisonTrademarkResult->planCorrespondence ?? null;

        if ($planCorrespondence && isset($planCorrespondence->register_date) && $planCorrespondence->register_date) {
            abort(404);
        }

        $comparisonTrademarkResult->trademark->trademarkDocuments = $this->trademarkDocumentService->getByCondition([
            'trademark_id' => $comparisonTrademarkResult->trademark_id,
            'type' => TrademarkDocument::TYPE_1,
            'flow' => Notice::FLOW_RESPONSE_REASON,
            'step' => Notice::STEP_1,
        ]);

        $comparisonTrademarkResultDraft = $this->comparisonTrademarkResultService->getDataDraftPlan($id, $flag);
        $productIds = $comparisonTrademarkResult->trademark->appTrademark->appTrademarkProd->where('is_apply', AppTrademarkProd::IS_APPLY)->pluck('m_product_id')->toArray();
        $dataProdAndDistinct = $this->mProductService->getDataMproduct(array_unique($productIds));
        // Address
        $nations = $this->mNationService->listNationOptions();
        $prefectures = $this->mPrefectureService->listPrefectureOptions();
        // Get Price Include Tax
        $costRegisterBeforeDeadline = $this->mPriceListService->getPriceIncludesTax(MPriceList::REASONS_REFUSAL, MPriceList::REGISTER_BEFORE_DEADLINE);
        $costBankTransfer = $this->mPriceListService->getPriceIncludesTax(MPriceList::EACH_PAYMENT, MPriceList::BANK_TRANSFER_HANDLING);
        $costServiceAddProd = $this->mPriceListService->getPriceIncludesTax(MPriceList::REASONS_REFUSAL, MPriceList::SIMPLE_PLAN_ADD_3_PRODS);
        // Get Price Base
        $costRegisterBeforeDeadlineBase = $this->mPriceListService->getPriceCommonOfPrecheck(MPriceList::REASONS_REFUSAL, MPriceList::REGISTER_BEFORE_DEADLINE);
        $costPriorDeadlineBase = $this->mPriceListService->getPriceCommonOfPrecheck(MPriceList::REASONS_REFUSAL, MPriceList::PRIOR_DEADLINE);
        $costBankTransferBase = $this->mPriceListService->getPriceCommonOfPrecheck(MPriceList::EACH_PAYMENT, MPriceList::BANK_TRANSFER_HANDLING);
        $setting = $this->mPriceListService->getSetting();
        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_4, $comparisonTrademarkResult->trademark->id, [
            U201 => true,
        ]);
        // Calculator Price Discount
        $priceDiscount = $this->comparisonTrademarkResultService->simultaneousApplicationDiscount($costRegisterBeforeDeadlineBase, $costPriorDeadlineBase, $setting);
        $costServiceBase = $this->mPriceListService->getPriceCommonOfPrecheck(MPriceList::REASONS_REFUSAL, MPriceList::SIMPLE_PLAN_BASIC)->base_price;
        // Data Cart
        $data = [
            'cost_service_base' => $costServiceBase,
            'cost_service_add_prod' => $this->mPriceListService->getPriceCommonOfPrecheck(MPriceList::REASONS_REFUSAL, MPriceList::SIMPLE_PLAN_ADD_3_PRODS)->base_price,
            'extension_of_period_before_expiry' => $costRegisterBeforeDeadlineBase->base_price,
            'cost_prior_deadline_base' => $costPriorDeadlineBase->base_price,
            'cost_bank_transfer' => $costBankTransferBase->base_price,
            'number_distinct' => count(array_unique($productIds)),
            'print_fee' => $costRegisterBeforeDeadlineBase->pof_1st_distinction_5yrs,
            'price_discount' => $priceDiscount,
        ];
        $checkRoute = null;
        switch (URL::current()) {
            case route('user.refusal.plans.simple_over', ['comparison_trademark_result_id' => $id]):
                $checkRoute = U201_SIMPLE01_OVER;
                if ($comparisonTrademarkResult->checkResponseDeadlineAlert()) {
                    return redirect()->route('user.refusal.plans.simple_alert', ['comparison_trademark_result_id' => $id]);
                }
                break;
            case route('user.refusal.plans.simple_alert', ['comparison_trademark_result_id' => $id]):
                $checkRoute = U201_SIMPLE01_ALERT;
                if ($comparisonTrademarkResult->checkResponseDeadlineOver()) {
                    return redirect()->route('user.refusal.plans.simple_over', ['comparison_trademark_result_id' => $id]);
                }
                break;
            case route('user.refusal.plans.simple', ['comparison_trademark_result_id' => $id]):
                if ($comparisonTrademarkResult->checkResponseDeadlineOver()) {
                    return redirect()->route('user.refusal.plans.simple_over', ['comparison_trademark_result_id' => $id]);
                } elseif ($comparisonTrademarkResult->checkResponseDeadlineAlert()) {
                    return redirect()->route('user.refusal.plans.simple_alert', ['comparison_trademark_result_id' => $id]);
                }
                $checkRoute = U201_SIMPLE;
                break;
        }

        return view('user.modules.plan.simple', compact(
            'comparisonTrademarkResult',
            'nations',
            'prefectures',
            'costBankTransfer',
            'trademarkTable',
            'dataProdAndDistinct',
            'productIds',
            'costRegisterBeforeDeadline',
            'costRegisterBeforeDeadlineBase',
            'priceDiscount',
            'data',
            'costServiceAddProd',
            'setting',
            'id',
            'costServiceBase',
            'comparisonTrademarkResultDraft',
            'checkRoute',
            'planCorrespondence'
        ));
    }

    /**
     * Show Select Plan
     *
     * @param  mixed $id
     * @return void
     */
    public function showSelectPlan($id)
    {
        $comparisonTrademarkResult = $this->comparisonTrademarkResultService->findByCondition(['id' => $id])->with([
            'trademark' => function ($query) {
                return $query->where('user_id', Auth::user()->id);
            },
            'planCorrespondences',
            'trademark.payment' => function ($query) {
                return $query->where('type', Payment::TYPE_REASON_REFUSAL)->where('payment_status', Payment::STATUS_SAVE)->where('from_page', U201_SIMPLE);
            },
            'trademark.appTrademark',
            'trademark.trademarkDocuments' => function ($query) {
                return $query->where('type', TYPE_1);
            },
            'trademark.appTrademark.appTrademarkProd',
            'trademark.payment.payerInfo' => function ($query) {
                return $query->where('type', TYPE_MATCHING_RESULT);
            }
        ])->whereHas('trademark', function ($query) {
            return $query->where('user_id', Auth::user()->id);
        })->first();
        if (!$comparisonTrademarkResult) {
            abort(404);
        }
        $planCorrespondence = null;
        if ($comparisonTrademarkResult->planCorrespondences) {
            $planCorrespondence = $comparisonTrademarkResult->planCorrespondences->where('type', TYPE_2)->last();
        }

        if (isset($planCorrespondence->register_date)) {
            abort(404);
        }

        if (isset($comparisonTrademarkResult->trademark->payment) && $comparisonTrademarkResult->trademark->payment->payment_status != Payment::STATUS_SAVE) {
            abort(404);
        }

        $comparisonTrademarkResult->trademark->trademarkDocuments = $this->trademarkDocumentService->getByCondition([
            'trademark_id' => $comparisonTrademarkResult->trademark_id,
            'type' => TrademarkDocument::TYPE_1,
            'flow' => Notice::FLOW_RESPONSE_REASON,
            'step' => Notice::STEP_1,
        ]);

        $flag = PLAG_SELECT_01;
        $comparisonTrademarkResultDraft = $this->comparisonTrademarkResultService->getDataDraftPlan($id, $flag);

        $productIds = $comparisonTrademarkResult->trademark->appTrademark->appTrademarkProd->where('is_apply', AppTrademarkProd::IS_APPLY)->pluck('m_product_id')->toArray();
        $dataProdAndDistinct = $comparisonTrademarkResult->trademark
            ->appTrademark
            ->appTrademarkProd
            ->where('is_apply', AppTrademarkProd::IS_APPLY)
            ->load([
                'planCorrespondenceProd' => function ($query) use ($planCorrespondence) {
                    $query->where('plan_correspondence_id', $planCorrespondence->id ?? 0);
                },
                'mProduct',
            ])
            ->groupBy('mProduct.m_distinction_id')
            ->sortKeys();

        // Address
        $nations = $this->mNationService->listNationOptions();
        $prefectures = $this->mPrefectureService->listPrefectureOptions();
        // Get Price Include Tax
        $costRegisterBeforeDeadline = $this->mPriceListService->getPriceIncludesTax(MPriceList::REASONS_REFUSAL, MPriceList::REGISTER_BEFORE_DEADLINE);
        $costBankTransfer = $this->mPriceListService->getPriceIncludesTax(MPriceList::EACH_PAYMENT, MPriceList::BANK_TRANSFER_HANDLING);
        $costServiceAddProd = $this->mPriceListService->getPriceIncludesTax(MPriceList::REASONS_REFUSAL, MPriceList::SELECT_PLAN_REGISTRATION_REPORT_EACH_PROD);
        // Get Price Base
        $costRegisterBeforeDeadlineBase = $this->mPriceListService->getPriceCommonOfPrecheck(MPriceList::REASONS_REFUSAL, MPriceList::REGISTER_BEFORE_DEADLINE);
        $costPriorDeadlineBase = $this->mPriceListService->getPriceCommonOfPrecheck(MPriceList::REASONS_REFUSAL, MPriceList::PRIOR_DEADLINE);
        $costBankTransferBase = $this->mPriceListService->getPriceCommonOfPrecheck(MPriceList::EACH_PAYMENT, MPriceList::BANK_TRANSFER_HANDLING);
        $setting = $this->mPriceListService->getSetting();
        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_4, $comparisonTrademarkResult->trademark->id, [
            U201 => true,
        ]);
        // Calculator Price Discount
        $priceDiscount = $this->comparisonTrademarkResultService->simultaneousApplicationDiscount($costRegisterBeforeDeadlineBase, $costPriorDeadlineBase, $setting);
        $costServiceBase = $this->mPriceListService->getPriceCommonOfPrecheck(MPriceList::REASONS_REFUSAL, MPriceList::SELECT_PLAN_REGISTRATION_REPORT_BASIC)->base_price;
        // Data Cart
        $data = [
            'cost_service_base' => $costServiceBase,
            'cost_service_add_prod' => $this->mPriceListService->getPriceCommonOfPrecheck(MPriceList::REASONS_REFUSAL, MPriceList::SELECT_PLAN_REGISTRATION_REPORT_EACH_PROD)->base_price,
            'extension_of_period_before_expiry' => $costRegisterBeforeDeadlineBase->base_price,
            'cost_prior_deadline_base' => $costPriorDeadlineBase->base_price,
            'cost_bank_transfer' => $costBankTransferBase->base_price,
            'number_distinct' => count(array_unique($productIds)),
            'print_fee' => $costRegisterBeforeDeadlineBase->pof_1st_distinction_5yrs,
            'price_discount' => $priceDiscount,
            'flag' => FLAG_PROD_ADD
        ];
        $checkRoute = null;

        switch (URL::current()) {
            case route('user.refusal.plans.select_01_over', ['comparison_trademark_result_id' => $id]):
                $checkRoute = U201_SELECT01_OVER;
                if ($comparisonTrademarkResult->checkResponseDeadlineAlert()) {
                    return redirect()->route('user.refusal.plans.select_01_alert', ['comparison_trademark_result_id' => $id]);
                }
                break;
            case route('user.refusal.plans.select_01_alert', ['comparison_trademark_result_id' => $id]):
                $checkRoute = U201_SELECT01_ALERT;
                if ($comparisonTrademarkResult->checkResponseDeadlineOver()) {
                    return redirect()->route('user.refusal.plans.select_01_over', ['comparison_trademark_result_id' => $id]);
                }
                break;
            case route('user.refusal.plans.select', ['comparison_trademark_result_id' => $id]):
                if ($comparisonTrademarkResult->checkResponseDeadlineOver()) {
                    return redirect()->route('user.refusal.plans.select_01_over', ['comparison_trademark_result_id' => $id]);
                } elseif ($comparisonTrademarkResult->checkResponseDeadlineAlert()) {
                    return redirect()->route('user.refusal.plans.select_01_alert', ['comparison_trademark_result_id' => $id]);
                }
                $checkRoute = U201_SELECT_01;
                break;
        }

        return view('user.modules.plan.select', compact(
            'comparisonTrademarkResult',
            'nations',
            'prefectures',
            'costBankTransfer',
            'trademarkTable',
            'dataProdAndDistinct',
            'productIds',
            'costRegisterBeforeDeadline',
            'costRegisterBeforeDeadlineBase',
            'priceDiscount',
            'data',
            'costServiceAddProd',
            'setting',
            'id',
            'comparisonTrademarkResultDraft',
            'costServiceBase',
            'flag',
            'checkRoute',
            'planCorrespondence'
        ));
    }

    /**
     * Show Select Plan 01 n
     *
     * @param  int $id - comparison_trademark_result_id ? reason_no_id
     * @return void
     */
    public function showSelectPlan01n(Request $request, $id)
    {
        if (!$request->has('reason_no_id') || !$request->reason_no_id || !is_numeric($request->reason_no_id)) {
            abort(404);
        }
        $comparisonTrademarkResult = $this->comparisonTrademarkResultService->findByCondition(['id' => $id])->with([
            'trademark' => function ($query) {
                return $query->where('user_id', Auth::user()->id);
            },
            'trademark.payment',
            'trademark.appTrademark',
            'trademark.trademarkDocuments' => function ($query) {
                return $query->where('type', TYPE_1);
            },
            'trademark.appTrademark.appTrademarkProd.planCorrespondenceProd',
            'trademark.payment.payerInfo',
            'planCorrespondence'
        ])->whereHas('trademark', function ($query) {
            return $query->where('user_id', Auth::user()->id);
        })->first();

        if (!$comparisonTrademarkResult) {
            abort(404);
        }

        $comparisonTrademarkResult->trademark->trademarkDocuments = $this->trademarkDocumentService->getByCondition([
            'trademark_id' => $comparisonTrademarkResult->trademark_id,
            'type' => TrademarkDocument::TYPE_1,
            'flow' => Notice::FLOW_RESPONSE_REASON,
            'step' => Notice::STEP_1,
        ]);

        $planCorrespondence = $comparisonTrademarkResult->planCorrespondence;
        $reasonNo = null;
        $countReasonNo = 0;
        if (isset($planCorrespondence)) {
            $planCorrespondence->load('reasonNos');
            $countReasonNo = $planCorrespondence->reasonNos->where('id', '>', $request['reason_no_id'])->count();
            if ($countReasonNo) {
                $reasonNo = $planCorrespondence->reasonNos->where('id', '>', $request['reason_no_id'])->sortByDesc('id')->first();
            } else {
                $reasonNo = $this->reasonNoService->findByCondition([
                    'id' => $request['reason_no_id'],
                    'plan_correspondence_id' => $planCorrespondence->id,
                ])->first();
            }
        }
        $trademark = $comparisonTrademarkResult->trademark;
        $flag = PLAG_SELECT_01_N;
        $comparisonTrademarkResultDraft = $this->comparisonTrademarkResultService->getDataDraftPlan($id, $flag);
        $paymentDraft = $this->paymentService->getPaymentDraft($trademark->id, Payment::TYPE_REASON_REFUSAL, U201_SELECT_01_N);
        $appTrademarkProdIds = $comparisonTrademarkResult->trademark->appTrademark->appTrademarkProd->where('is_apply', AppTrademarkProd::IS_APPLY)->pluck('id')->toArray();
        $productIds = $comparisonTrademarkResult->trademark->appTrademark->appTrademarkProd
            ->where('planCorrespondenceProd', '!=', null)
            ->where('is_apply', AppTrademarkProd::IS_APPLY)->pluck('m_product_id')->toArray();
        $dataProdAndDistinct = $this->mProductService->getDataMproduct(array_unique($productIds));

        // Address
        $nations = $this->mNationService->listNationOptions();
        $prefectures = $this->mPrefectureService->listPrefectureOptions();
        // Get Price Include Tax
        $costRegisterBeforeDeadline = $this->mPriceListService->getPriceIncludesTax(MPriceList::REASONS_REFUSAL, MPriceList::REGISTER_BEFORE_DEADLINE);
        $costBankTransfer = $this->mPriceListService->getPriceIncludesTax(MPriceList::EACH_PAYMENT, MPriceList::BANK_TRANSFER_HANDLING);
        $costServiceAddProd = $this->mPriceListService->getPriceIncludesTax(MPriceList::REASONS_REFUSAL, MPriceList::SELECT_PLAN_B_C_D_E);
        // Get Price Base
        $costRegisterBeforeDeadlineBase = $this->mPriceListService->getPriceCommonOfPrecheck(MPriceList::REASONS_REFUSAL, MPriceList::REGISTER_BEFORE_DEADLINE);
        $costServiceProductAdd = $this->mPriceListService->getPriceCommonOfPrecheck(MPriceList::REASONS_REFUSAL, MPriceList::PRIOR_DEADLINE);
        $costBankTransferBase = $this->mPriceListService->getPriceCommonOfPrecheck(MPriceList::EACH_PAYMENT, MPriceList::BANK_TRANSFER_HANDLING);
        $setting = $this->mPriceListService->getSetting();
        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_4, $comparisonTrademarkResult->trademark->id, [
            U201 => true,
        ]);
        // Calculator Price Discount
        $priceDiscount = $this->comparisonTrademarkResultService->simultaneousApplicationDiscount($costRegisterBeforeDeadlineBase, $costServiceProductAdd, $setting);
        // Possibility Of Registration
        $mPriceListPossibilityRegistrationA = $this->mPriceListService->getPriceCommonOfPrecheck(MPriceList::REASONS_REFUSAL, MPriceList::SELECT_PLAN_A_RATING)->base_price;
        $possibilityRegistrationA = $mPriceListPossibilityRegistrationA + (($setting->value * $mPriceListPossibilityRegistrationA) / 100);
        $mPriceListPossibilityRegistrationOther = $this->mPriceListService->getPriceCommonOfPrecheck(MPriceList::REASONS_REFUSAL, MPriceList::SELECT_PLAN_B_C_D_E)->base_price;
        $possibilityRegistrationOther = $mPriceListPossibilityRegistrationOther + (($setting->value * $mPriceListPossibilityRegistrationOther) / 100);
        // Data Cart
        $data = [
            'cost_service_base' => 0,
            'cost_service_add_prod' => $this->mPriceListService->getPriceCommonOfPrecheck(MPriceList::REASONS_REFUSAL, MPriceList::SELECT_PLAN_B_C_D_E)->base_price,
            'extension_of_period_before_expiry' => $costRegisterBeforeDeadlineBase->base_price,
            'cost_prior_deadline_base' => $costServiceProductAdd->base_price,
            'cost_bank_transfer' => $costBankTransferBase->base_price,
            'print_fee' => $costRegisterBeforeDeadlineBase->pof_1st_distinction_5yrs,
            'price_discount' => $priceDiscount,
            'flag' => PLAG_SELECT_01_N
        ];

        return view('user.modules.plan.select01n', compact(
            'comparisonTrademarkResult',
            'nations',
            'prefectures',
            'costBankTransfer',
            'trademarkTable',
            'costRegisterBeforeDeadline',
            'costRegisterBeforeDeadlineBase',
            'priceDiscount',
            'data',
            'costServiceAddProd',
            'setting',
            'id',
            'comparisonTrademarkResultDraft',
            'possibilityRegistrationA',
            'possibilityRegistrationOther',
            'productIds',
            'flag',
            'dataProdAndDistinct',
            'countReasonNo',
            'planCorrespondence',
            'reasonNo',
            'paymentDraft',
        ));
    }

    /**
     * Ajax Calculate Cart
     *
     * @param  mixed $request
     * @return void
     */
    public function ajaxCalculateCart(Request $request)
    {
        return $this->comparisonTrademarkResultService->calculatorCart($request->all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $comparisonTrademarkResult = $this->comparisonTrademarkResultService->createComparisonResult($request->all());
        switch ($comparisonTrademarkResult['redirect_to']) {
            case REDIRECT_TO_COMMON_PAYMENT_SELECT_01N:
            case REDIRECT_TO_COMMON_PAYMENT_SELECT:
            case REDIRECT_TO_COMMON_PAYMENT_SIMPLE:
                return redirect()->route('user.payment.index', ['s' => $comparisonTrademarkResult['key_session']]);
            case REDIRECT_TO_QUOTE_SELECT_01N:
            case REDIRECT_TO_QUOTE_SELECT:
            case REDIRECT_TO_QUOTE_SIMPLE:
                return redirect()->route('user.quote', ['id' => $comparisonTrademarkResult['payment_id']]);
            case REDIRECT_TO_ANKEN_TOP:
                return redirect()->route('user.application-detail.index', ['id' => $comparisonTrademarkResult['trademark_id']])->with('message', __('messages.general.update_success'))->withInput();
            case U210_OVER_02:
                return redirect()->route('user.refusal.extension-period.over', ['id' => $comparisonTrademarkResult['trademark_id']])
                    ->with('message', __('messages.general.update_success'))->withInput();
            case BACK_URL:
                return redirect()->route('user.refusal.plans.index', ['id' => $request['comparison_trademark_result_id']]);
            case false:
                return redirect()->back()->with('message', __('message.common.errors.Common_E025'));
        }
    }
}
