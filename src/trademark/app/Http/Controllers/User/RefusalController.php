<?php

namespace App\Http\Controllers\User;

use App\Models\MLawsRegulation;
use Exception;
use App\Helpers\CommonHelper;
use App\Helpers\FileHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Refusal\ChoosePlan\SaveDataChoosePlanRequest;
use App\Models\MPriceList;
use App\Models\PlanDetail;
use App\Models\PlanDetailProduct;
use App\Models\Notice;
use App\Models\NoticeDetail;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\PlanCorrespondence;
use App\Models\Setting;
use App\Notices\PaymentNotice;
use App\Models\PlanDetailDoc;
use App\Models\PlanDocCmt;
use App\Models\Reason;
use App\Models\RequiredDocument;
use App\Models\RequiredDocumentDetail;
use App\Models\RequiredDocumentMiss;
use App\Models\RequiredDocumentPlan;
use App\Models\Trademark;
use App\Models\TrademarkDocument;
use App\Models\TrademarkPlan;
use App\Services\Common\TrademarkTableService;
use App\Services\ComparisonTrademarkResultService;
use App\Services\MNationService;
use App\Services\MPrefectureService;
use App\Services\MPriceListService;
use App\Services\MProductService;
use App\Services\PaymentService;
use App\Services\PlanCorrespondenceService;
use App\Services\TrademarkPlanService;
use App\Services\PlanService;
use App\Services\NoticeDetailService;
use App\Services\AdminService;
use App\Services\Common\NoticeService;
use App\Services\PlanDetailDocService;
use App\Services\PlanDetailService;
use App\Services\PlanDocCmtService;
use Illuminate\Database\Eloquent\Model;
use App\Services\TrademarkDocumentService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

class RefusalController extends Controller
{
    protected ComparisonTrademarkResultService $comparisonTrademarkResultService;
    protected PlanCorrespondenceService $planCorrespondenceService;
    protected PaymentService $paymentService;
    protected $trademarkPlanService;
    protected $planService;
    protected $noticeDetailService;
    protected $adminService;
    protected $noticeService;
    protected $trademarkDocumentService;
    protected $planDetailDocService;
    protected $planDocCmtService;
    protected $trademarkTableService;
    protected $planDetailService;
    protected $mNationService;
    protected $mPrefectureService;
    protected $mPriceListService;
    protected $mProductService;

    /**
     * Constructor
     *
     * @return  void
     */
    public function __construct(
        ComparisonTrademarkResultService $comparisonTrademarkResultService,
        TrademarkTableService $trademarkTableService,
        PlanCorrespondenceService $planCorrespondenceService,
        MNationService $mNationService,
        MPrefectureService $mPrefectureService,
        MPriceListService $mPriceListService,
        MProductService $mProductService,
        PaymentService $paymentService,
        TrademarkPlanService $trademarkPlanService,
        PlanService $planService,
        NoticeDetailService $noticeDetailService,
        AdminService $adminService,
        NoticeService $noticeService,
        TrademarkDocumentService $trademarkDocumentService,
        PlanDetailDocService $planDetailDocService,
        PlanDocCmtService $planDocCmtService,
        PlanDetailService $planDetailService
    )
    {
        $this->comparisonTrademarkResultService = $comparisonTrademarkResultService;
        $this->trademarkTableService = $trademarkTableService;
        $this->planCorrespondenceService = $planCorrespondenceService;
        $this->mNationService = $mNationService;
        $this->mPrefectureService = $mPrefectureService;
        $this->mPriceListService = $mPriceListService;
        $this->mProductService = $mProductService;
        $this->paymentService = $paymentService;
        $this->trademarkPlanService = $trademarkPlanService;
        $this->planService = $planService;
        $this->noticeDetailService = $noticeDetailService;
        $this->adminService = $adminService;
        $this->noticeService = $noticeService;
        $this->trademarkDocumentService = $trademarkDocumentService;
        $this->planDetailDocService = $planDetailDocService;
        $this->planDocCmtService = $planDocCmtService;
        $this->planDetailService = $planDetailService;
    }

    /**
     * Show Choose Plan
     *
     * @param  mixed $request
     * @param  mixed $id - comparison_trademark_result_id ? trademark_plan_id
     * @return void
     */
    public function showChoosePlan(Request $request, $id)
    {
        $comparisonTrademarkResult = $this->comparisonTrademarkResultService->find($id);
        if (!$comparisonTrademarkResult) {
            abort(CODE_ERROR_404);
        }
        $comparisonTrademarkResult->load([
            'PlanCorrespondence',
            'trademark',
        ]);
        $trademark = $comparisonTrademarkResult->trademark;
        if (!$trademark) {
            abort(CODE_ERROR_404);
        }

        if ($request->show == ADMIN_ROLE && auth()->guard('admin')->check()) {
            Auth::onceUsingID($trademark->user_id);
        }

        if (!Auth::check() || $trademark->user_id != Auth::user()->id) {
            abort(403);
        }
        $planCorrespondence = $comparisonTrademarkResult->PlanCorrespondences->last();
        if (!$planCorrespondence) {
            abort(CODE_ERROR_404);
        }
        $trademarkPlan = $this->trademarkPlanService->findByCondition([
            'id' => $request->trademark_plan_id,
            'plan_correspondence_id' => $planCorrespondence->id,
        ])->first();
        if (!$trademarkPlan) {
            abort(404);
        }
        // Get Product Not Is Choice
        $planDetailProductNotRegister = $trademarkPlan->load([
            'plans.planDetails.planDetailProducts' => function ($query) {
                return $query->with('mProduct')->where('is_choice', PlanDetailProduct::IS_NOT_CHOICE);
            },
        ]);
        $dataPlanDetailProdNotRegister = $this->planCorrespondenceService->getMProductIdByPlanDetail($planDetailProductNotRegister);
        $mProductIdsNotRegister = $dataPlanDetailProdNotRegister['mProductId'];
        $mDistinctsNotRegister = $this->mProductService->getDataMProductChoosePlan($mProductIdsNotRegister);
        // Get Number Plan Detail
        $trademarkPlanAllPlanDetailProducts = $trademarkPlan->load([
            'plans.planDetails.planDetailProducts',
        ]);

        // Get Plan
        $trademarkPlan->load([
            'plans.planDetails.planDetailProducts' => function ($query) {
                return $query->with('mProduct')->where('is_choice', PlanDetailProduct::IS_CHOICE)->where('is_deleted', false);
            },
            'plans.planDetails.planDetailDistincts.planDetailProducts',
            'plans.planDetails.mTypePlan.mTypePlanDocs',
            'plans.planReasons.reasons' => function ($query) {
                $query->where('reason_name', '!=', Reason::NO_REASON);
            },
        ]);

        $paymentDraft = $this->paymentService->findByCondition([
            'type' => TYPE_MATCHING_RESULT_SELECTION,
            'target_id' => $trademarkPlan->id,
            'from_page' => U203,
        ])->get()->last();

        // Get Product Is Choice
        $planDetailProductIds = $trademarkPlanAllPlanDetailProducts->plans
            ->pluck('planDetails')
            ->flatten()
            ->pluck('planDetailProducts')
            ->flatten()
            ->pluck('m_product_id')
            ->unique()
            ->toArray();
        $mDistinctCart = $this->mProductService->getDataMProductChoosePlan($planDetailProductIds);
        // Address
        $nations = $this->mNationService->listNationOptions();
        $prefectures = $this->mPrefectureService->listPrefectureOptions();
        $costBankTransfer = $this->mPriceListService->getPriceIncludesTax(MPriceList::EACH_PAYMENT, MPriceList::BANK_TRANSFER_HANDLING);
        $costAdditional = $this->mPriceListService->getPriceCommonOfPrecheck(MPriceList::REASONS_REFUSAL, MPriceList::ADD_OPTION_EACH_PROD);

        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_4, $planCorrespondence->comparisonTrademarkResult->trademark->id, [
            U203 => true,
        ]);
        $routeU201bCancel = route('user.refusal.notification.cancel', ['comparison_trademark_result_id' => $comparisonTrademarkResult->id]);

        $data = [
            'cost_additional' => $costAdditional,
            'cost_bank_transfer' => $costBankTransfer,
            'period_registration' => $trademark->appTrademark->period_registration,
        ];

        $trademarkDocuments = $this->trademarkDocumentService->getByCondition([
            'trademark_id' => $trademark->id,
            'type' => TrademarkDocument::TYPE_1,
            'flow' => Notice::FLOW_RESPONSE_REASON,
            'step' => Notice::STEP_1,
        ]);
        $mLawRegulationContentDefault = MLawsRegulation::ARRAY_CONTENT_DEFAULT;

        return view('user.modules.refusal.choose-plan', compact(
            'trademarkTable',
            'planCorrespondence',
            'trademarkPlan',
            'nations',
            'prefectures',
            'costBankTransfer',
            'mDistinctsNotRegister',
            'costAdditional',
            'data',
            'mDistinctCart',
            'paymentDraft',
            'dataPlanDetailProdNotRegister',
            'comparisonTrademarkResult',
            'trademarkDocuments',
            'routeU201bCancel',
            'trademark',
            'mLawRegulationContentDefault'
        ));
    }

    /**
     * Show Product
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return void
     */
    public function showProduct(Request $request, $id)
    {
        $paramsU203b02 = ['comparison_trademark_result_id' => $id, 'trademark_plan_id' => $request->trademark_plan_id];
        if ($request->has('s') && $request->s) {
            $paramsU203b02['s'] = $request->s;
        }

        $routeAccesses = [
            route('user.refusal.response-plan.refusal_response_plan', ['comparison_trademark_result_id' => $id, 'trademark_plan_id' => $request->trademark_plan_id]),
            route('user.refusal.response-plan.refusal_response_plan.confirm', $paramsU203b02),
        ];

        if (!in_array(URL::previous(), $routeAccesses)) {
            abort(404);
        }

        $comparisonTrademarkResult = $this->comparisonTrademarkResultService->find($id);
        if (!$comparisonTrademarkResult) {
            abort(404);
        }
        $comparisonTrademarkResult->load([
            'PlanCorrespondence',
            'trademark' => function ($query) {
                return $query->where('user_id', Auth::user()->id);
            },
        ]);
        $trademark = $comparisonTrademarkResult->trademark;
        $planCorrespondence = $comparisonTrademarkResult->PlanCorrespondence;
        if (!$comparisonTrademarkResult->trademark) {
            abort(404);
        }
        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_4, $comparisonTrademarkResult->trademark->id, [
            U203 => true,
        ]);
        $trademarkPlan = $this->trademarkPlanService->findByCondition([
            'id' => $request->trademark_plan_id,
            'plan_correspondence_id' => $planCorrespondence->id,
        ])->first();
        if (!$trademarkPlan) {
            abort(404);
        }
        $planIds = $trademarkPlan->plans->pluck('trademark_plan_id');

        // Product Not Register
        $planDetailProductNotRegister = $trademarkPlan->load([
            'plans.planDetails' => function ($query) use ($planIds) {
                return $query->whereIn('plan_id', $planIds);
            },
            'plans.planDetails.planDetailProducts' => function ($query) {
                return $query->with('mProduct')->where('is_choice', PlanDetailProduct::IS_NOT_CHOICE);
            },
        ]);
        $dataPlanDetailProdNotRegister = $this->planCorrespondenceService->getMProductIdByPlanDetail($planDetailProductNotRegister);
        $mProductIdsNotRegister = $dataPlanDetailProdNotRegister['mProductId'];
        $mDistinctsNotRegister = $this->mProductService->getDataMProductChoosePlan($mProductIdsNotRegister);

        // Get Number Plan Detail
        $trademarkPlanAllPlanDetailProducts = $trademarkPlan->load('plans.planDetails.planDetailProducts');
        $numberPlanDetailProducts = $this->planCorrespondenceService->getNumberPlanDetailProduct($trademarkPlanAllPlanDetailProducts);

        // Get Plan
        $trademarkPlan->load([
            'plans.planDetails.mTypePlan',
            'plans.planDetails.planDetailProducts' => function ($query) {
                return $query->with('mProduct')->where('is_choice', PlanDetailProduct::IS_CHOICE)->where('is_deleted', false);
            },
            'plans.planDetails.planDetailProducts.planDetailDistinct.mDistinction',
            'plans.planDetails.mTypePlan.mTypePlanDocs',
        ]);
        // Product Register
        $planDetailIds = $trademarkPlan->plans->pluck('planDetails')->flatten()->pluck('id');

        $dataPlanDetailProd = $this->planCorrespondenceService->getMProductIdByPlanDetail($trademarkPlan);
        $mProductIds = $dataPlanDetailProd['mProductId'];
        $mDistincts = $this->mProductService->getDataMProductu203c($mProductIds, $planDetailIds->toArray());

        $costBankTransfer = $this->mPriceListService->getPriceIncludesTax(MPriceList::EACH_PAYMENT, MPriceList::BANK_TRANSFER_HANDLING);
        $costAdditional = $this->mPriceListService->getPriceCommonOfPrecheck(MPriceList::REASONS_REFUSAL, MPriceList::ADD_OPTION_EACH_PROD);

        $data = [
            'cost_additional' => $costAdditional,
            'arr_number_plan_detail_product' => $numberPlanDetailProducts ?? 0,
            'cost_bank_transfer' => $costBankTransfer,
            'period_registration' => $trademark->appTrademark->period_registration,
        ];
        $plans = $trademarkPlan->plans;

        $trademarkDocuments = $this->trademarkDocumentService->getByCondition([
            'trademark_id' => $trademark->id,
            'type' => TrademarkDocument::TYPE_1,
            'flow' => Notice::FLOW_RESPONSE_REASON,
            'step' => Notice::STEP_1,
        ]);

        // U203 Data
        $paymentDraft = $this->paymentService->findByCondition([
            'type' => TYPE_MATCHING_RESULT_SELECTION,
            'target_id' => $trademarkPlan->id,
            'from_page' => U203,
        ])->get()->last();

        // Get Product Is Choice
        $planDetailProductIds = $trademarkPlanAllPlanDetailProducts->plans
            ->pluck('planDetails')
            ->flatten()
            ->pluck('planDetailProducts')
            ->flatten()
            ->pluck('m_product_id')
            ->unique()
            ->toArray();
        $mDistinctCart = $this->mProductService->getDataMProductChoosePlan($planDetailProductIds);

        return view('user.modules.refusal.product', compact(
            'trademarkTable',
            'comparisonTrademarkResult',
            'planCorrespondence',
            'trademarkDocuments',
            'trademarkPlan',
            'costBankTransfer',
            'data',
            'mDistincts',
            'mDistinctsNotRegister',
            'plans',
            'paymentDraft',
            'mDistinctCart',
        ));
    }

    /**
     * Show Choose Plan Re
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return void
     */
    public function showChoosePlanRe(Request $request, $id)
    {
        try {
            $comparisonTrademarkResult = $this->comparisonTrademarkResultService->find($id);
            if (!$comparisonTrademarkResult) {
                abort(404);
            }
            $comparisonTrademarkResult->load([
                'PlanCorrespondence',
                'trademark' => function ($query) {
                    return $query->where('user_id', Auth::user()->id);
                },
            ]);
            $trademark = $comparisonTrademarkResult->trademark;
            $planCorrespondence = $comparisonTrademarkResult->PlanCorrespondence;
            if (!$comparisonTrademarkResult->trademark) {
                abort(404);
            }
            $trademarkPlan = $this->trademarkPlanService->findByCondition([
                'id' => $request->trademark_plan_id,
                'plan_correspondence_id' => $planCorrespondence->id,
            ])->first();
            if (!$trademarkPlan) {
                abort(404);
            }
            // Get Number Plan Detail
            $trademarkPlanAllPlanDetailProducts = $trademarkPlan->load('plans.planDetails.planDetailProducts');
            // Get Product Not Is Choice
            $planDetailProductNotRegister = $trademarkPlan->load([
                'plans.planDetails.planDetailProducts' => function ($query) {
                    return $query->with('mProduct')->where('is_choice', PlanDetailProduct::IS_NOT_CHOICE);
                },
            ]);
            $dataPlanDetailProdNotRegister = $this->planCorrespondenceService->getMProductIdByPlanDetail($planDetailProductNotRegister);
            $mProductIdsNotRegister = $dataPlanDetailProdNotRegister['mProductId'];
            $mDistinctsNotRegister = $this->mProductService->getDataMProductChoosePlan($mProductIdsNotRegister);
            // Get Plan Detail Is Past
            $trademarkPlanDetailsIsPast = $trademarkPlan->load([
                'plans.planDetails.mTypePlan',
                'plans.planDetails' => function ($query) {
                    return $query->where('is_choice_past', PlanDetail::IS_CHOICE_PAST_TRUE);
                },
                'plans.planDetails.planDetailDistincts.planDetailProducts',
                'plans.planDetails.planDetailProducts.planDetailDistinct.mDistinction',
                'plans.planDetails.mTypePlan.mTypePlanDocs',
                'plans.planReasons.reasons' => function ($query) {
                    $query->where('reason_name', '!=', Reason::NO_REASON);
                }
            ]);
            // Get Plan
            $trademarkPlan->load([
                'plans.planDetails.mTypePlan',
                'plans.planDetails.planDetailProducts' => function ($query) {
                    return $query->with('mProduct')->where('is_choice', PlanDetailProduct::IS_CHOICE)->where('is_deleted', false);
                },
                'plans.planDetails.planDetailDistincts.planDetailProducts',
                'plans.planDetails.planDetailProducts.planDetailDistinct.mDistinction',
                'plans.planDetails.mTypePlan.mTypePlanDocs',
            ]);
            $fromPage = U203N;
            $paymentDraft = $this->paymentService->getPaymentDraft($planCorrespondence->comparisonTrademarkResult->trademark->id, TYPE_MATCHING_RESULT_SELECTION, $fromPage);
            // Get Product Is Choice
            $mProductIds = $trademarkPlan->plans->pluck('planDetails')->flatten()
                ->pluck('planDetailProducts')->flatten()
                ->pluck('m_product_id');
            $mProductIdIsChoice = $mProductIds->unique()->toArray();
            $countMProductIdIsChoice = count($mProductIdIsChoice);
            $mDistinctCart = [];
            if ($countMProductIdIsChoice > 0) {
                for ($i = 0; $i < $countMProductIdIsChoice; $i++) {
                    $mDistinctCart = $this->mProductService->getDataMProductChoosePlan($mProductIdIsChoice);
                }
            }

            // Address
            $nations = $this->mNationService->listNationOptions();
            $prefectures = $this->mPrefectureService->listPrefectureOptions();
            $costBankTransfer = $this->mPriceListService->getPriceIncludesTax(MPriceList::EACH_PAYMENT, MPriceList::BANK_TRANSFER_HANDLING);
            $costAdditional = $this->mPriceListService->getPriceCommonOfPrecheck(MPriceList::REASONS_REFUSAL, MPriceList::ADD_OPTION_EACH_PROD);

            $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_4, $planCorrespondence->comparisonTrademarkResult->trademark->id, [
                U203 => true,
            ]);
            $data = [
                'cost_additional' => $costAdditional,
                'cost_bank_transfer' => $costBankTransfer,
                'period_registration' => $trademark->appTrademark->period_registration,
            ];

            $trademarkDocuments = $this->trademarkDocumentService->getByCondition([
                'trademark_id' => $trademark->id,
                'type' => TrademarkDocument::TYPE_1,
                'flow' => Notice::FLOW_RESPONSE_REASON,
                'step' => Notice::STEP_1,
            ]);
            $mLawRegulationContentDefault = MLawsRegulation::ARRAY_CONTENT_DEFAULT;
            return view('user.modules.refusal.choose-plan-re', compact(
                'trademarkTable',
                'planCorrespondence',
                'trademarkPlan',
                'nations',
                'prefectures',
                'costBankTransfer',
                'mDistinctsNotRegister',
                'costAdditional',
                'data',
                'mDistinctCart',
                'paymentDraft',
                'dataPlanDetailProdNotRegister',
                'trademarkPlanDetailsIsPast',
                'comparisonTrademarkResult',
                'trademarkDocuments',
                'mLawRegulationContentDefault'
            ));
        } catch (\Exception $e) {
            Log::error($e);

            abort(404);
        }
    }

    /**
     * Show Product
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return void
     */
    public function showProductRe(Request $request, $id)
    {
        if (URL::previous() != route('user.refusal.response-plan.refusal_response_plan_re', ['comparison_trademark_result_id' => $id, 'trademark_plan_id' => $request->trademark_plan_id])) {
            abort(404);
        }

        $sessionData = Session::get($request->s);
        if (empty($sessionData)) {
            abort(404);
        }

        $comparisonTrademarkResult = $this->comparisonTrademarkResultService->find($id);
        if (!$comparisonTrademarkResult) {
            abort(404);
        }
        $comparisonTrademarkResult->load([
            'PlanCorrespondence',
            'trademark' => function ($query) {
                return $query->where('user_id', Auth::user()->id);
            },
        ]);
        $trademark = $comparisonTrademarkResult->trademark;
        $planCorrespondence = $comparisonTrademarkResult->PlanCorrespondence;
        if (!$comparisonTrademarkResult->trademark) {
            abort(404);
        }
        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_4, $comparisonTrademarkResult->trademark->id, [
            U203 => true,
        ]);
        $trademarkPlan = $this->trademarkPlanService->findByCondition([
            'id' => $request->trademark_plan_id,
            'plan_correspondence_id' => $planCorrespondence->id,
        ])->first();
        if (!$trademarkPlan) {
            abort(404);
        }
        $planIds = $trademarkPlan->plans->pluck('trademark_plan_id');
        // Product Not Register
        $planDetailProductNotRegister = $trademarkPlan->load([
            'plans.planDetails.mTypePlan',
            'plans.planDetails.planDetailProducts' => function ($query) {
                return $query->with('mProduct')->where('is_choice', PlanDetailProduct::IS_NOT_CHOICE)->where('is_deleted', false);
            },
            'plans.planDetails.planDetailProducts.planDetailDistinct.mDistinction',
            'plans.planDetails.mTypePlan.mTypePlanDocs',
        ]);

        $dataPlanDetailProdNotRegister = $this->planCorrespondenceService->getMProductIdByPlanDetail($planDetailProductNotRegister);
        $mProductIdsNotRegister = $dataPlanDetailProdNotRegister['mProductId'];
        $mDistinctsNotRegister = $this->mProductService->getDataMProductChoosePlan($mProductIdsNotRegister);
        // Get Number Plan Detail
        $trademarkPlanAllPlanDetailProducts = $trademarkPlan->load('plans.planDetails.planDetailProducts');
        $numberPlanDetailProducts = $this->planCorrespondenceService->getNumberPlanDetailProduct($trademarkPlanAllPlanDetailProducts);
        // Get Plan
        $trademarkPlan->load([
            'plans.planDetails.mTypePlan',
            'plans.planDetails.planDetailProducts' => function ($query) {
                return $query->with('mProduct')->where('is_choice', PlanDetailProduct::IS_CHOICE)->where('is_deleted', false);
            },
            'plans.planDetails.planDetailProducts.planDetailDistinct.mDistinction',
            'plans.planDetails.mTypePlan.mTypePlanDocs',
        ]);
        // Product Register
        $planDetailIds = $trademarkPlan->plans->pluck('planDetails')->flatten()->pluck('id');

        $dataPlanDetailProd = $this->planCorrespondenceService->getMProductIdByPlanDetail($trademarkPlan);
        $mProductIds = $dataPlanDetailProd['mProductId'];
        $mDistincts = $this->mProductService->getDataMProductu203c($mProductIds, $planDetailIds->toArray());

        $costBankTransfer = $this->mPriceListService->getPriceIncludesTax(MPriceList::EACH_PAYMENT, MPriceList::BANK_TRANSFER_HANDLING);
        $costAdditional = $this->mPriceListService->getPriceCommonOfPrecheck(MPriceList::REASONS_REFUSAL, MPriceList::ADD_OPTION_EACH_PROD);
        $data = [
            'cost_additional' => $costAdditional,
            'arr_number_plan_detail_product' => $numberPlanDetailProducts ?? 0,
            'cost_bank_transfer' => $costBankTransfer,
            'period_registration' => $trademark->appTrademark->period_registration,
        ];
        $plans = $trademarkPlan->plans;

        $trademarkDocuments = $this->trademarkDocumentService->getByCondition([
            'trademark_id' => $trademark->id,
            'type' => TrademarkDocument::TYPE_1,
            'flow' => Notice::FLOW_RESPONSE_REASON,
            'step' => Notice::STEP_1,
        ]);

        // u203n data
        $paymentDraft = $this->paymentService->getPaymentDraft($planCorrespondence->comparisonTrademarkResult->trademark->id, TYPE_MATCHING_RESULT_SELECTION, U203N);

        $mDistinctCart = $this->mProductService->getDataMProductChoosePlan($mProductIds);

        return view('user.modules.refusal.product-re', compact(
            'trademarkTable',
            'comparisonTrademarkResult',
            'planCorrespondence',
            'trademarkPlan',
            'costBankTransfer',
            'data',
            'mDistincts',
            'mDistinctCart',
            'mDistinctsNotRegister',
            'plans',
            'trademarkDocuments',
            'paymentDraft',
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function ajaxCalculatorChoosePlan(Request $request)
    {
        return $this->planCorrespondenceService->calculatorChoosePlan($request->all());
    }

    /**
     * Save Data Choose Plan
     *
     * @param  mixed $request
     * @return void
     */
    public function saveDataChoosePlan(SaveDataChoosePlanRequest $request)
    {
        try {
            $planCorrespondence = $this->planCorrespondenceService->saveDataChoosePlan($request->all());
            $urlU203c = route(
                'user.refusal.response-plan.refusal_product',
                [
                    'comparison_trademark_result_id' => $request->comparison_trademark_result_id,
                    'trademark_plan_id' => $request->trademark_plan_id,
                    's' => $planCorrespondence['key_session'] ?? ''
                ]
            );
            $urlU203c_n = route(
                'user.refusal.response-plan.refusal_product_re',
                [
                    'comparison_trademark_result_id' => $request->comparison_trademark_result_id,
                    'trademark_plan_id' => $request->trademark_plan_id,
                    's' => $planCorrespondence['key_session'] ?? ''
                ]
            );
            $urlU203b02 = route(
                'user.refusal.response-plan.refusal_response_plan.confirm',
                [
                    'comparison_trademark_result_id' => $request->comparison_trademark_result_id,
                    'trademark_plan_id' => $request->trademark_plan_id,
                    's' => $planCorrespondence['key_session'] ?? ''
                ]
            );

            switch ($planCorrespondence['redirect_to']) {
                case U203C:
                    return redirect($urlU203c);
                case U203C_N:
                    return redirect($urlU203c_n);
                case U203B02:
                    return redirect($urlU203b02);
                case U201B_CANCEL:
                    return redirect()->route('user.refusal.notification.cancel', ['comparison_trademark_result_id' => $request['comparison_trademark_result_id']]);
                case _QUOTES:
                    return redirect()->route('user.quote', ['id' => $planCorrespondence['payment_id'], 'trademark_plan_id' => $request['trademark_plan_id']]);
                case U000ANKEN_TOP:
                    return redirect()->route('user.application-detail.index', ['id' => $planCorrespondence['trademark_id']])->with('message', __('messages.general.Common_E047'))->withInput();
                case false:
                    return redirect()->back()->with('error', __('messages.general.Common_E025'));
            }
        } catch (Exception $e) {
            Log::error($e);
            abort(404);
        }
    }

    /**
     * Post Product
     *
     * @param  mixed $request
     * @return void
     */
    public function postProduct(SaveDataChoosePlanRequest $request)
    {
        $product = $this->planDetailService->updatePlanDetails($request->all());
        $urlU203b02 = route(
            'user.refusal.response-plan.refusal_response_plan.confirm',
            [
                'comparison_trademark_result_id' => $request->comparison_trademark_result_id,
                'trademark_plan_id' => $request->trademark_plan_id,
                's' => $product['key_session'],
            ]
        );
        $urlU203 = route(
            'user.refusal.response-plan.refusal_response_plan',
            [
                'comparison_trademark_result_id' => $request->comparison_trademark_result_id,
                'trademark_plan_id' => $request->trademark_plan_id,
            ]
        );
        $urlU203n = route(
            'user.refusal.response-plan.refusal_response_plan_re',
            [
                'comparison_trademark_result_id' => $request->comparison_trademark_result_id,
                'trademark_plan_id' => $request->trademark_plan_id,
            ]
        );

        switch ($product['redirect_to']) {
            case U203B02:
                return redirect($urlU203b02);
            case U203:
                return redirect($urlU203);
            case U203N:
                return redirect($urlU203n);
            case U210ALERT02:
                return redirect()->route('user.refusal.extension-period.alert', ['id' => $request['plan_corresspondence_id']]);
            default:
                return redirect()->back()->with('error', __('messages.general.Common_E025'));
        }
    }

    /**
     * Stop
     *
     * @param  Request $request
     * @param  int $id
     * @return View
     */
    public function stop(Request $request, int $id): View
    {
        if (empty($request->trademark_plan_id)) {
            abort(404);
        }

        $comparisonTrademarkResult = $this->comparisonTrademarkResultService->find($id);
        if (!$comparisonTrademarkResult) {
            abort(404);
        }
        $comparisonTrademarkResult->load([
            'PlanCorrespondence',
            'trademark',
        ]);
        $planCorrespondence = $comparisonTrademarkResult->PlanCorrespondence;
        $trademark = $comparisonTrademarkResult->trademark;
        if ($trademark->user_id != auth()->user()->id) {
            abort(403);
        }

        $trademarkPlan = $this->trademarkPlanService->findByCondition([
            'id' => $request->trademark_plan_id,
            'plan_correspondence_id' => $planCorrespondence->id,
        ])->first();
        if (!$trademarkPlan) {
            abort(404);
        }

        if (!empty($trademarkPlan) && ($trademarkPlan->is_cancel)) {
            $request->session()->put('message_confirm', [
                'content' => __('messages.general.Hoshin_U203_S002'),
                'btn' => __('labels.back'),
                'url' => route('user.top'),
            ]);
        }

        return view('user.modules.refusal.stop', compact(
            'trademark',
            'trademarkPlan',
            'comparisonTrademarkResult',
        ));
    }

    /**
     * Post stop
     *
     * @param  Request $request
     * @return RedirectResponse
     */
    public function postStop(Request $request)
    {
        try {
            DB::beginTransaction();

            $trademarkPlan = $this->trademarkPlanService->find($request->id);
            if (!$trademarkPlan) {
                abort(404);
            }

            $relation = $trademarkPlan->load([
                'planCorrespondence.comparisonTrademarkResult.trademark',
                'planCorrespondence.comparisonTrademarkResult.machingResult',
            ]);
            $planCorrespondence = $relation->planCorrespondence;
            $comparisonTrademarkResult = $planCorrespondence->comparisonTrademarkResult;
            $trademark = $comparisonTrademarkResult->trademark;

            if (isset($request->draft)) {
                // Update trademark plan
                $trademarkPlan->update([
                    'reason_cancel' => $request->reason_cancel,
                ]);

                CommonHelper::setMessage($request, MESSAGE_SUCCESS, __('messages.general.Common_E034'));
                $redirect = route('user.top');
            } elseif (isset($request->submit)) {
                // Update trademark plan
                $trademarkPlan->update([
                    'reason_cancel' => $request->reason_cancel,
                    'is_cancel' => true,
                ]);

                // Duplicate Trademark Plan
                $newTrademarkPlan = $this->trademarkPlanService->duplicate($trademarkPlan);

                // Send notice
                $this->noticeStop($comparisonTrademarkResult, $trademarkPlan, $newTrademarkPlan);

                CommonHelper::setMessage($request, MESSAGE_SUCCESS, __('messages.general.Hoshin_U203_S002'));
                $redirect = route('user.application-detail.index', ['id' => $trademark->id]);
            }

            DB::commit();

            return redirect($redirect)->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            CommonHelper::setMessage($request, MESSAGE_ERROR, __('messages.error'));
            return redirect()->back();
        }
    }

    /**
     * Notice Stop u203stop
     *
     * @param Model $comparisonTrademarkResult
     * @param Model $trademarkPlan
     * @param Model $newTrademarkPlan
     * @return void
     */
    public function noticeStop(Model $comparisonTrademarkResult, Model $trademarkPlan, Model $newTrademarkPlan)
    {
        $trademark = $comparisonTrademarkResult->trademark;

        // Update Notice at a203s a203c_shu (No 64, 67: H I)
        $stepBeforeNotice = $this->noticeDetailService->findByCondition([
            'is_answer' => NoticeDetail::IS_NOT_ANSWER,
            'type_acc' => NoticeDetail::TYPE_USER,
        ])->with('notice')->get()
            ->where('notice.trademark_id', $trademark->id)
            ->where('notice.user_id', $trademark->user_id)
            ->whereIn('notice.flow', [Notice::FLOW_RESPONSE_REASON, Notice::FLOW_RENEWAL_BEFORE_DEADLINE])
            ->filter(function ($item) {
                if ($item->notice->flow == Notice::FLOW_RESPONSE_REASON && $item->notice->step == Notice::STEP_3) {
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

        $targetPage = route('user.refusal.response-plan.stop', [
            'id' => $comparisonTrademarkResult->id,
            'trademark_plan_id' => $trademarkPlan->id,
        ]);
        $redirectPage = route('admin.refusal.response-plan-re.supervisor', [
            'id' => $comparisonTrademarkResult->id,
            'trademark_plan_id' => $newTrademarkPlan->id,
        ]);

        // Set response deadline
        $planCorrespondence = $newTrademarkPlan->planCorrespondence;
        $machingResult = $comparisonTrademarkResult->machingResult;
        $responseDeadline = $machingResult->calculateResponseDeadline(-24);
        if (!empty($machingResult) && $planCorrespondence->type == PlanCorrespondence::TYPE_2) {
            $responseDeadline = $machingResult->calculateResponseDeadline(-29);
        }

        $notice = [
            'trademark_id' => $trademark->id,
            'trademark_info_id' => null,
            'user_id' => $trademark->user_id,
            'flow' => Notice::FLOW_RESPONSE_REASON,
            'step' => Notice::STEP_3,
        ];

        $noticeDetails = [
            [
                'target_id' => null,
                'type_acc' => NoticeDetail::TYPE_SUPERVISOR,
                'target_page' => $targetPage,
                'redirect_page' => $redirectPage,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                'is_action' => true,
                'content' => '拒絶理由通知対応：方針案再作成',
                'response_deadline' => $responseDeadline,
            ],
            [
                'target_id' => null,
                'type_acc' => NoticeDetail::TYPE_SUPERVISOR,
                'target_page' => $targetPage,
                'redirect_page' => $redirectPage,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                'is_action' => false,
                'content' => '責任者　拒絶理由通知対応：方針案再作成',
                'attribute' => '所内処理',
                'response_deadline' => $responseDeadline,
            ],
        ];

        $this->noticeService->sendNotice([
            'notices' => $notice,
            'notice_details' => $noticeDetails,
        ]);
    }

    /**
     * Showing select motto.
     *
     * @param Request $request.
     * @param int $id
     * @return View
     */
    public function showU203b02(Request $request, int $id)
    {
        $user = Auth::guard('web')->user();
        $dataSession = null;
        if ($request->has('s') && $request->s) {
            $dataSession = Session::get($request->s);
        };

        if (!$dataSession) {
            abort(404);
        }

        if ((!isset($dataSession['from_page'])) || (isset($dataSession['from_page']) && !in_array($dataSession['from_page'], [U203C, U203, U203N, U203C_N]))) {
            abort(404);
        }

        $planCorrespondence = $this->planCorrespondenceService->getPlanCorrespondence($id);
        if (!$planCorrespondence) {
            abort(404);
        }
        $comparisonTrademarkResult = $planCorrespondence->comparisonTrademarkResult;
        $comparisonTrademarkResult->load('trademark');

        $trademark = isset($planCorrespondence->comparisonTrademarkResult) && isset($planCorrespondence->comparisonTrademarkResult->trademark)
            ? $planCorrespondence->comparisonTrademarkResult->trademark
            : null;

        if ($trademark->user_id != $user->id) {
            abort(404);
        }
        $hasFee = $dataSession['has_fee'] ?? 1;

        $planCorrespondence->load('trademarkPlans');
        $trademarkPlan = $planCorrespondence->trademarkPlans->where('id', $request->trademark_plan_id)->first();

        if (!$trademarkPlan) {
            abort(404);
        }

        $trademarkPlanAllPlanDetailProducts = $trademarkPlan->load('plans.planDetails.planDetailProducts');
        $payment = null;
        $payerInfo = null;
        if ($dataSession && isset($dataSession['payment_id']) && $dataSession['payment_id']) {
            $payment = $this->paymentService->find($dataSession['payment_id']);
            if ($payment) {
                $payment->load('payerInfo');
                $payerInfo = $payment->payerInfo;
            }
        }

        // Get Number Plan Detail
        $trademarkPlanAllPlanDetailProducts = $trademarkPlan->load('plans.planDetails.planDetailProducts');

        // Get Plan
        $trademarkPlan->load([
            'plans.reasons',
            'plans.planDetails.mTypePlan',
            'plans.planDetails.mTypePlan.mTypePlanDocs',
        ]);

        // Get Product Is Choice
        $dataPlanDetailProdIsChoice = $this->planCorrespondenceService->getMProductIdByPlanDetail($trademarkPlan);
        $setting = Setting::where('key', Setting::KEY_TAX)->first();
        $mProductIdIsChoice = $dataPlanDetailProdIsChoice['mProductId'];
        $countMProductIdIsChoice = count($mProductIdIsChoice);
        $mDistinctCart = [];
        if ($countMProductIdIsChoice > 0) {
            for ($i = 0; $i < $countMProductIdIsChoice; $i++) {
                $mDistinctCart = $this->mProductService->getDataMProductChoosePlan($mProductIdIsChoice);
            }
        }

        // Address
        $nations = $this->mNationService->listNationOptions();
        $prefectures = $this->mPrefectureService->listPrefectureOptions();
        $costBankTransfer = $this->mPriceListService->getPriceIncludesTax(MPriceList::EACH_PAYMENT, MPriceList::BANK_TRANSFER_HANDLING);
        $costAdditional = $this->mPriceListService->getPriceCommonOfPrecheck(MPriceList::REASONS_REFUSAL, MPriceList::ADD_OPTION_EACH_PROD);

        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_4, $planCorrespondence->comparisonTrademarkResult->trademark->id, [
            U203 => true,
        ]);
        $planDetailProducts = collect([]);
        if (isset($dataSession['productIds']) && !empty($dataSession['productIds'])) {
            $planDetailProducts = PlanDetailProduct::with('mProduct.mDistinction')->whereIn('m_product_id', $dataSession['productIds'])->get()->groupBy('m_product_id');
        }

        $firstPlan = $trademarkPlan->plans->first();
        $totalDistinction = 0;
        if ($firstPlan) {
            $firstPlanDetailChoice = $firstPlan->planDetails->where('is_choice', IS_CHOICE)->first();
            if ($firstPlanDetailChoice && isset($firstPlanDetailChoice->planDetailDistincts)) {
                $firstPlanDetailDistincts = $firstPlanDetailChoice->planDetailDistincts->filter(function ($item) {
                    $planDetailProducts = $item->planDetailProducts;
                    $planDetailProductNotAdd = $planDetailProducts->whereIn('leave_status', [
                        \App\Models\PlanDetailProduct::LEAVE_STATUS_7,
                        \App\Models\PlanDetailProduct::LEAVE_STATUS_3,
                    ]);
                    $planDetailProductIsDeleted = $planDetailProducts->where('is_deleted', true);
                    return $planDetailProductIsDeleted->count() == 0 && $planDetailProductNotAdd->count() == 0;
                });

                $totalDistinction = $firstPlanDetailDistincts
                    ->where('is_distinct_settlement', IS_DISTINCT_SETTLEMENT)
                    ->pluck('m_distinction_id')
                    ->unique()->count();
            }
        }
        $planDetailChoice = $firstPlan->planDetails->where('is_choice', PlanDetail::IS_CHOICE)->first();

        $numberPlanDetailProducts = count($this->planCorrespondenceService->getNumberProduct($planDetailChoice));

        return view('user.modules.refusal.u203b02', compact(
            'trademarkPlan',
            'trademarkTable',
            'trademark',
            'hasFee',
            'planCorrespondence',
            'nations',
            'prefectures',
            'costBankTransfer',
            'costAdditional',
            'setting',
            'dataSession',
            'payerInfo',
            'comparisonTrademarkResult',
            'numberPlanDetailProducts',
            'planDetailProducts',
            'totalDistinction'
        ));
    }

    /**
     * Save Select Motto
     *
     * @param Request $request
     * @param int $id
     */
    public function saveU203b02(Request $request, int $id)
    {
        try {
            DB::beginTransaction();
            $params = [];
            $dataSession = [];
            if ($request->has('s') && $request->s) {
                $dataSession = Session::get($request->s) ?? [];
            }
            $params = array_merge($dataSession, $request->all());
            $payment = null;

            if (isset($params['payment_id']) && $params['payment_id']) {
                $payment = $this->paymentService->find($params['payment_id']);
            }
            $planCorrespondence = $this->planCorrespondenceService->getPlanCorrespondence($id);

            $plans = $this->planService->findByCondition(['trademark_plan_id' => $dataSession['trademark_plan_id']], ['planDetails.mTypePlan'])->get();
            $mTypePlans = $plans->pluck('planDetails')->flatten()->where('is_choice', PlanDetail::IS_CHOICE)->whereIn('mTypePlan.id', [2, 4, 5, 7, 8]);

            // No fee
            if ($params['redirect_to'] == COMMON_PAYMENT && isset($dataSession['has_fee']) && !$dataSession['has_fee']) {
                if ($plans->count()) {
                    $trademarkPlan = $this->trademarkPlanService->find($dataSession['trademark_plan_id']);
                    $trademarkPlan->update([
                        'is_register' => true,
                        'sending_docs_deadline' => Carbon::now()->addDays(2),
                    ]);

                    $paymentNotice = App::make(PaymentNotice::class);
                    $paymentNotice->setTrademark(Trademark::find($planCorrespondence->comparisonTrademarkResult->trademark_id));
                    $paymentNotice->setCurrentUser(Auth::user());
                    $paymentNotice->setData($dataSession);
                    // Has request document
                    if (!$params['has_fee'] && $mTypePlans->count()) {
                        $paymentNotice->noticeU203B02NoFee(true);
                        DB::commit();

                        // Redirect to u204
                        return redirect()->route('user.refusal.materials.index', [
                            'id' => $id,
                            'trademark_plan_id' => $dataSession['trademark_plan_id'],
                        ]);
                    } elseif (!$params['has_fee'] && !$mTypePlans->count()) {
                        $paymentNotice->noticeU203B02NoFee(false);
                        DB::commit();

                        // Redirect to top
                        return redirect()->route('user.application-detail.index', ['id' => $dataSession['trademark_id']])->with(['message' => __('messages.precheck.success_u203b02')]);
                    }
                }
            }

            switch ($params['redirect_to']) {
                case U000ANKEN_TOP:
                    if (!empty($payment)) {
                        $payment->update([
                            'is_treatment' => Payment::IS_TREATMENT_WAIT,
                            'payment_status' => Payment::STATUS_SAVE,
                        ]);
                    }
                    DB::commit();

                    return redirect()->route('user.application-detail.index', ['id' => $planCorrespondence->comparisonTrademarkResult->trademark_id])
                        ->with('message', __('messages.general.Common_E047'))->withInput();
                case QUOTE:
                    if (!empty($payment)) {
                        $payment->update([
                            'is_treatment' => Payment::IS_TREATMENT_WAIT,
                            'payment_status' => Payment::STATUS_SAVE,
                        ]);
                    }
                    DB::commit();

                    return redirect()->route('user.quote', ['id' => $params['payment_id']]);
                case COMMON_PAYMENT:
                    if (!empty($payment)) {
                        $payment->update([
                            'is_treatment' => Payment::IS_TREATMENT_WAIT,
                            'payment_status' => Payment::STATUS_SAVE,
                        ]);
                    }

                    $secret = Str::random(11);
                    $redirectPage = '';
                    // Required docs.
                    if ($mTypePlans->count() && $payment->payerInfo->payment_type == Payment::CREDIT_CARD) {
                        $redirectPage = route('user.refusal.response-plan.notice_next', [
                            'comparison_trademark_result_id' => $params['comparison_trademark_result_id'],
                            'trademark_plan_id' => $params['trademark_plan_id']
                        ]);
                    } else {
                        $redirectPage = route('user.application-detail.index', ['id' => $dataSession['trademark_id']]);
                    }

                    Session::put($secret, array_merge($params, [
                        'payment_id' => $params['payment_id'],
                        'payment_type' => $payment->payerInfo->payment_type ?? 0,
                        'from_page' => $payment->from_page ?? null,
                        'redirect' => $redirectPage,
                        'required_document' => $mTypePlans->count(),
                    ]));

                    DB::commit();

                    return redirect()->route('user.payment.index', ['s' => $secret]);
                case U203C:
                    return redirect()->route('user.refusal.response-plan.refusal_product', [
                        'comparison_trademark_result_id' => $params['comparison_trademark_result_id'] ?? '',
                        'trademark_plan_id' => $params['trademark_plan_id'] ?? '',
                        's' => $params['s'] ?? ''
                    ]);
                default:
                    return redirect()->back()->with('error', __('messages.import_xml.system_error'));
            }
        } catch (\Exception $e) {
            Log::error($e);
            DB::rollBack();

            return redirect()->back()->with('error', __('messages.import_xml.system_error'));
        }
    }

    /**
     * Payment completed
     *
     * @param Request $request;
     */
    public function paymentCompleted(Request $request, int $id): View
    {
        $comparisonTrademarkResult = $this->comparisonTrademarkResultService->find($id);

        if (!$comparisonTrademarkResult || !$request->has('trademark_plan_id')) {
            abort(CODE_ERROR_404);
        }

        $sessionData = [
            'comparison_trademark_result_id' => $id,
            'trademark_plan_id' => $request->trademark_plan_id
        ];

        return view('user.modules.refusal.u203b02paid', compact('sessionData'));
    }

    /**
     * Material index.
     *
     * @param  mixed $request
     * @param  int $id
     * @return View
     */
    public function materialIndex($id, Request $request): View
    {
        $isBlockScreen = false;
        $requiredDocumentDetails = [];
        $requiredDoc = null;
        if (empty($request->trademark_plan_id)) {
            abort(404);
        }
        $comparisonTrademarkResult = $this->comparisonTrademarkResultService->find($id);
        if (!$comparisonTrademarkResult) {
            abort(404);
        }
        $comparisonTrademarkResult->load([
            'PlanCorrespondence',
            'trademark',
        ]);

        $planCorrespondence = $comparisonTrademarkResult->PlanCorrespondence;
        $trademark = $comparisonTrademarkResult->trademark;
        if ($trademark->user_id != auth()->user()->id) {
            abort(403);
        }
        $trademarkPlan = $this->trademarkPlanService->findByCondition([
            'id' => $request->trademark_plan_id,
            'plan_correspondence_id' => $planCorrespondence->id,
        ])->first();
        if (!$trademarkPlan) {
            abort(404);
        }

        $relation = $trademarkPlan->load(
            'plans.planDetails.mTypePlan.mTypePlanDocs',
            'plans.planDetails.planDetailDocs.MTypePlanDoc',
            'plans.planReasons.reason',
            'plans.planDocCmts',
        );

        $plans = $relation->getPlans();

        foreach ($plans as $key => $item) {
            $item->content_plan_doc_cmt = $item->planDocCmts->where('type', PlanDocCmt::TYPE_U204)->first() ?? null;
        }

        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_4, $trademark->id, [
            U204 => true,
        ]);

        $trademarkDocuments = $this->trademarkDocumentService->getByCondition([
            'trademark_id' => $trademark->id,
            'type' => TrademarkDocument::TYPE_1,
            'flow' => Notice::FLOW_RESPONSE_REASON,
            'step' => Notice::STEP_1,
        ])->pluck('url');

        $hasRequireDoc = $plans->pluck('planDetails')->flatten()->whereNotIn('type_plan_id', [1, 3, 6])->count();
        // Has require document then run code below
        if ($hasRequireDoc) {
            // Checking if the required document is send or not.
            $requiredDoc = RequiredDocument::where([
                'trademark_plan_id' => $request->trademark_plan_id,
            ])->first();
            // Checking if the user has been confirmed. If the user has been confirmed, it will block the screen
            if ($requiredDoc) {
                $isBlockScreen = $requiredDoc->is_send;
                $requiredDocumentDetails = RequiredDocumentDetail::where([
                    'required_document_id' => $requiredDoc->id,
                    'from_send_doc' => U204
                ])->get()->groupBy('plan_detail_doc_id');
            }
        }

        return view('user.modules.refusal.materials.index', compact(
            'plans',
            'requiredDoc',
            'trademark',
            'trademarkPlan',
            'isBlockScreen',
            'trademarkTable',
            'trademarkDocuments',
            'comparisonTrademarkResult',
            'requiredDocumentDetails'
        ));
    }

    /**
     * Post material.
     *
     * @param  Request $request
     * @param  int $id
     * @return RedirectResponse
     */
    public function postMaterial(Request $request, $id)
    {
        // Get the required document for given trademark plan id
        $requiredDoc = RequiredDocument::where([
            'trademark_plan_id' => $request->trademark_plan_id,
            'is_send' => RequiredDocument::IS_SEND
        ])->first();

        // Check if the required document is available and return the error message accordingly
        if ($requiredDoc) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => CODE_ERROR_400,
                    'message' => __('messages.error'),
                ]);
            } else {
                abort(404);
            }
        }

        $filepath = [];

        if (empty($request->trademark_plan_id)) {
            abort(404);
        }

        $comparisonTrademarkResult = $this->comparisonTrademarkResultService->find($id);
        if (!$comparisonTrademarkResult) {
            abort(404);
        }
        $comparisonTrademarkResult->load([
            'PlanCorrespondence',
            'trademark',
        ]);
        $planCorrespondence = $comparisonTrademarkResult->PlanCorrespondence;
        $trademark = $comparisonTrademarkResult->trademark;
        if ($trademark->user_id != auth()->user()->id) {
            abort(403);
        }

        $trademarkPlan = $this->trademarkPlanService->findByCondition([
            'id' => $request->trademark_plan_id,
            'plan_correspondence_id' => $planCorrespondence->id,
        ])->first();
        if (!$trademarkPlan) {
            abort(404);
        }

        try {
            DB::beginTransaction();

            $storage = FileHelper::getStorage();

            foreach ($request->data ?? [] as $planId => $value) {
                // is sended and is not confirm
                $requiredDoc = RequiredDocument::updateOrCreate([
                    'trademark_plan_id' => $request->trademark_plan_id,
                ], [
                    'is_send' => isset($request->submit_confirm) ? RequiredDocument::IS_SEND : RequiredDocument::IS_NOT_SEND,
                ]);

                RequiredDocumentPlan::updateOrCreate([
                    'plan_id' => $planId,
                    'required_document_id' => $requiredDoc->id,
                ]);

                foreach ($value['plan_detail_doc'] ?? [] as $planDetailDocId => $valuePlanDetailDoc) {
                    $attachmentUser = [];
                    if (!empty($valuePlanDetailDoc['attach'])) {
                        foreach ($valuePlanDetailDoc['attach'] as $file) {
                            if (str_contains($file, FOLDER_TEMP)) {
                                $newFile = Str::replace('temp', 'materials', $file);

                                $explodeFile = explode('/', $newFile);
                                $explodeFileName = explode('.', $explodeFile[3]);

                                // Check namesake
                                $i = 0;
                                while ($storage->exists($newFile)) {
                                    $i++;
                                    $newFile = LOCAL_PUBLIC_FOLDER . '/materials/' . $explodeFileName[0] . '-' . $i . '.' . $explodeFileName[1];
                                }

                                Storage::move($file, $newFile);
                                $attachmentUser[] = [
                                    'sending_date' => now()->format('Y-m-d'),
                                    'value' => $newFile,
                                    'type' => ATTACH,
                                ];
                                $filepath[] = $newFile;
                            } else {
                                $attachmentUser[] = [
                                    'sending_date' => now()->format('Y-m-d'),
                                    'value' => $file,
                                    'type' => ATTACH,
                                ];
                            };
                        }
                    }

                    if (!empty($valuePlanDetailDoc['url'])) {
                        foreach ($valuePlanDetailDoc['url'] as $url) {
                            $attachmentUser[] = [
                                'sending_date' => now()->format('Y-m-d'),
                                'value' => $url,
                                'type' => URL,
                            ];
                        }
                    }

                    $stringAttachmentUser = json_encode($attachmentUser);
                    // $planDetailDoc = $this->planDetailDocService->find($planDetailDocId);
                    // $planDetailDoc->update([
                    //     'attachment_user' => $stringAttachmentUser,
                    // ]);

                    RequiredDocumentDetail::updateOrCreate([
                        'required_document_id' => $requiredDoc->id,
                        'plan_detail_doc_id' => $planDetailDocId,
                        'from_send_doc' => U204,
                    ], [
                        'attachment_user' => $stringAttachmentUser,
                    ]);
                }

                $dataUpdate = [];

                if (isset($request->submit)) {
                    $dataUpdate = [
                        'content' => $value['content'],
                        'type' => PlanDocCmt::TYPE_U204,
                        'from_send_doc' => U204,
                    ];
                } elseif (isset($request->draft)) {
                    $dataUpdate = [
                        'content' => $value['content'],
                        'type' => PlanDocCmt::TYPE_U204,
                        'from_send_doc' => U204,
                    ];
                } elseif (isset($request->submit_confirm)) {
                    $dataUpdate = [
                        'content' => $value['content'],
                        'type' => PlanDocCmt::TYPE_U204,
                        'date_send' => now(),
                    ];
                    RequiredDocument::updateOrCreate([
                        'trademark_plan_id' => $request->trademark_plan_id,
                    ], [
                        'is_send' => RequiredDocument::IS_SEND,
                    ]);
                }

                if (!empty($dataUpdate['content'])) {
                    $this->planDocCmtService->updateOrCreate([
                        'plan_id' => $planId,
                        'type' => PlanDocCmt::TYPE_U204
                    ], $dataUpdate);
                }
            }

            if (isset($request->submit)) {
                // CommonHelper::setMessage($request, MESSAGE_SUCCESS, __('messages.general.Common_E034'));
                $redirect = route('user.refusal.materials.confirm.index', ['id' => $comparisonTrademarkResult->id, 'trademark_plan_id' => $trademarkPlan->id]);
            } elseif (isset($request->draft)) {
                CommonHelper::setMessage($request, MESSAGE_SUCCESS, __('messages.save_draft'));
                $redirect = route('user.application-detail.index', ['id' => $comparisonTrademarkResult->trademark_id]);
            } elseif (isset($request->submit_confirm)) {
                $trademarkPlan->update([
                    'from_send_doc' => U204,
                ]);

                // Send notice
                $requiredDoc = RequiredDocument::where([
                    'trademark_plan_id' => $request->trademark_plan_id,
                ])->first();

                $this->noticeMaterial($comparisonTrademarkResult, $trademarkPlan, $requiredDoc);

                CommonHelper::setMessage($request, MESSAGE_SUCCESS, __('messages.general.Common_E034'));
                $redirect = route('user.top');
            } elseif (isset($request->draft_confirm)) {
                $redirect = route('user.refusal.materials.index', ['id' => $comparisonTrademarkResult->id, 'trademark_plan_id' => $trademarkPlan->id]);
            }

            DB::commit();

            return redirect($redirect)->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            foreach ($filepath as $path) {
                FileHelper::unlink($path);
            }

            CommonHelper::setMessage($request, MESSAGE_ERROR, __('messages.general.Common_E027'));
            return redirect()->back();
        }
    }

    /**
     * Notice Material u204
     *
     * @param Model $comparisonTrademarkResult
     * @param Model $trademarkPlan
     * @return void
     */
    public function noticeMaterial(Model $comparisonTrademarkResult, Model $trademarkPlan, ?Model $requiredDoc)
    {
        $trademark = $comparisonTrademarkResult->trademark;

        // Update Notice (No 77, 78: H I)
        $stepBeforeNotice = $this->noticeDetailService->findByCondition([
            'type_acc' => NoticeDetail::TYPE_USER,
            'is_answer' => NoticeDetail::IS_NOT_ANSWER,
        ])->with('notice')->get()
            ->where('notice.trademark_id', $trademark->id)
            ->where('notice.user_id', $trademark->user_id)
            ->whereIn('notice.flow', [Notice::FLOW_RESPONSE_REASON, Notice::FLOW_RENEWAL_BEFORE_DEADLINE])
            ->filter(function ($item) {
                if ($item->notice->flow == Notice::FLOW_RESPONSE_REASON && in_array($item->notice->step, [Notice::STEP_3, Notice::STEP_4])) {
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

        $targetPage = route('user.refusal.materials.index', [
            'id' => $comparisonTrademarkResult->id,
            'trademark_plan_id' => $trademarkPlan->id,
        ]);
        $redirectPage = route('admin.refusal.materials.supervisor', [
            'id' => $comparisonTrademarkResult->id,
            'trademark_plan_id' => $trademarkPlan->id,
        ]);

        // Set response deadline
        $planCorrespondence = $trademarkPlan->planCorrespondence;
        $machingResult = $comparisonTrademarkResult->machingResult;
        $responseDeadline = $machingResult->calculateResponseDeadline(-28);
        if (!empty($machingResult) && $planCorrespondence->type == PlanCorrespondence::TYPE_2) {
            $responseDeadline = $machingResult->calculateResponseDeadline(-31);
        }

        $notice = [
            'trademark_id' => $trademark->id,
            'trademark_info_id' => null,
            'user_id' => $trademark->user_id,
            'flow' => Notice::FLOW_RESPONSE_REASON,
            'step' => Notice::STEP_4,
        ];
        $paramRedirect = [
            'id' => $comparisonTrademarkResult->id,
            'trademark_plan_id' => $trademarkPlan->id,
        ];
        if ($requiredDoc) {
            $paramRedirect['required_document_id'] = $requiredDoc->id;
        }
        $noticeDetails = [
            [
                'target_id' => null,
                'type_acc' => NoticeDetail::TYPE_SUPERVISOR,
                'target_page' => $targetPage,
                'redirect_page' => route('admin.refusal.materials.supervisor', $paramRedirect),
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                'is_action' => true,
                'content' => '拒絶理由通知対応：必要資料依頼',
                'response_deadline' => $responseDeadline,
            ],
            [
                'target_id' => null,
                'type_acc' => NoticeDetail::TYPE_SUPERVISOR,
                'target_page' => $targetPage,
                'redirect_page' => null,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                'is_action' => true,
                'content' => '責任者　拒絶理由通知対応：必要資料 お客様からの回答',
                'attribute' => 'お客様から',
                'response_deadline' => $responseDeadline,
            ],
            [
                'target_id' => null,
                'type_acc' => NoticeDetail::TYPE_SUPERVISOR,
                'target_page' => $targetPage,
                'redirect_page' => route('admin.refusal.materials.supervisor', $paramRedirect),
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                'is_action' => true,
                'content' => '責任者　拒絶理由通知対応：必要資料依頼',
                'attribute' => '所内処理',
                'response_deadline' => $responseDeadline,
            ],
            [
                'target_id' => $trademark->user_id,
                'type_acc' => NoticeDetail::TYPE_USER,
                'target_page' => $targetPage,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_DEFAULT,
                'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                'content' => '拒絶理由通知対応：必要資料提出済・返信待ち',
                'response_deadline' => $responseDeadline,
            ],
            [
                'target_id' => $trademark->user_id,
                'type_acc' => NoticeDetail::TYPE_USER,
                'target_page' => $targetPage,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                'content' => '拒絶理由通知対応：必要資料提出済・返信待ち',
                'response_deadline' => $responseDeadline,
            ],
        ];

        $this->noticeService->sendNotice([
            'notices' => $notice,
            'notice_details' => $noticeDetails,
        ]);
    }

    /**
     * Ajax Material
     *
     * @param   Request $request
     * @return  JsonResponse
     */
    public function ajaxMaterial(Request $request)
    {
        $filepath = [];

        try {
            if ($request->images) {
                foreach ($request->images as $value) {
                    if (count($request->images) > 0) {
                        $file = $value;
                        $image = FileHelper::uploads($file, [], FOLDER_TEMP);
                        $filepath[] = $image[0]['filepath'] ?? null;
                    };
                }
            }

            return response()->json([
                'status' => CODE_SUCCESS_200,
                'filepath' => $filepath,
            ]);
        } catch (\Exception $e) {
            Log::error($e);

            foreach ($filepath as $path) {
                FileHelper::unlink($path);
            }

            return response()->json([
                'status' => CODE_SUCCESS_200,
                'message' => __('messages.error'),
            ]);
        }
    }

    /**
     * Ajax Material Delete
     *
     * @param   Request $request
     * @return  JsonResponse
     */
    public function ajaxMaterialDelete(Request $request)
    {
        if (!$request->has('trademark_plan_id') || !$request->trademark_plan_id) {
            return response()->json([
                'status' => CODE_ERROR_400,
                'message' => __('messages.error'),
            ]);
        }

        // $requiredDoc = RequiredDocument::where([
        //     'trademark_plan_id' => $request->trademark_plan_id,
        //     'is_send' => RequiredDocument::IS_SEND
        // ])->first();
        //
        // if ($requiredDoc) {
        //     return response()->json([
        //         'status' => CODE_ERROR_400,
        //         'message' => __('messages.error'),
        //     ]);
        // }

        try {
            $attachmentUser = RequiredDocumentDetail::where([
                'plan_detail_doc_id' => $request->plan_detail_doc_id,
                'required_document_id' => $request->required_document_id,
                'from_send_doc' => $request->from_send_doc,
            ])->first();

            if ($attachmentUser) {
                $attachmentUserDecode = collect(json_decode($attachmentUser->attachment_user, true));
                foreach ($attachmentUserDecode as $k => $item) {
                    if ($item['value'] == $request->file) {
                        unset($attachmentUserDecode[$k]);
                        FileHelper::unlink($item['value']);
                    }
                }
                $attachmentUser->update([
                    'attachment_user' => count($attachmentUserDecode) ? json_encode($attachmentUserDecode) : null,
                ]);
            } else {
                FileHelper::unlink($request->file);
            }

            return response()->json([
                'status' => CODE_SUCCESS_200,
            ]);
        } catch (\Exception $e) {
            Log::error($e);

            return response()->json([
                'status' => CODE_ERROR_400,
                'message' => __('messages.error'),
            ]);
        }
    }

    /**
     * Material re index.
     *
     * @param  Request $request
     * @param  int $id
     * @return View
     */
    public function materialReIndex($id, Request $request): View
    {
        /* Dumping the request data to the screen. */
        if (empty($request->trademark_plan_id)) {
            abort(CODE_ERROR_404);
        }

        if (empty($request->round) && !$request->round) {
            abort(CODE_ERROR_404);
        }

        if (!$request->has('required_document_id') || !$request->required_document_id) {
            abort(CODE_ERROR_404);
        }

        // Get required document of
        $requiredDocument = RequiredDocument::find($request->required_document_id);

        if ($requiredDocument && $requiredDocument->trademark_plan_id != $request->trademark_plan_id) {
            abort(CODE_ERROR_404);
        }

        $isBlockScreen = false;
        $round = $request->round;
        $comparisonTrademarkResult = $this->comparisonTrademarkResultService->find($id);
        if (!$comparisonTrademarkResult) {
            abort(CODE_ERROR_404);
        }
        $comparisonTrademarkResult->load([
            'PlanCorrespondence',
            'trademark',
        ]);

        $planCorrespondence = $comparisonTrademarkResult->PlanCorrespondence;
        $trademark = $comparisonTrademarkResult->trademark;
        if ($trademark->user_id != auth()->user()->id) {
            abort(CODE_ERROR_403);
        }

        $trademarkPlan = $this->trademarkPlanService->findByCondition([
            'id' => $request->trademark_plan_id,
            'plan_correspondence_id' => $planCorrespondence->id,
        ])->first();

        if (!$trademarkPlan) {
            abort(CODE_ERROR_404);
        }

        $explodeFromSendDoc = explode('_', $trademarkPlan->from_send_doc);
        $step = $explodeFromSendDoc[1] ?? 0;

        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_4, $trademark->id, [
            U204N => true,
        ]);

        $trademarkDocuments = $this->trademarkDocumentService->getByCondition([
            'trademark_id' => $trademark->id,
            'type' => TrademarkDocument::TYPE_1,
            'flow' => Notice::FLOW_RESPONSE_REASON,
            'step' => Notice::STEP_1,
        ])->pluck('url');

        $relation = $trademarkPlan->load(
            'plans.planDetails.mTypePlan.mTypePlanDocs',
            'plans.planDetails.planDetailDocs.MTypePlanDoc',
            'plans.planReasons.reason',
            'plans.planDocCmts',
            'plans.planDetails.planDetailDocs',
            'plans.requiredDocumentPlans'
        );

        $plansIsNotCompleted = $relation->getPlans()->where('is_completed', Plan::IS_COMPLETED_FALSE);

        $requiredDocMiss = RequiredDocumentMiss::whereIn('plan_id', $plansIsNotCompleted->pluck('id')->toArray());
        if ($round == 1) {
            $requiredDocMiss = $requiredDocMiss->where('from_send_doc', U204N);
        } else {
            // Get require of last time.
            $requiredDocMiss = $requiredDocMiss->where('from_send_doc', U204N . '_' . $round);
        }
        $requiredDocMiss = $requiredDocMiss->get();

        $requiredDocumentDetailData = RequiredDocumentDetail::where([
            'required_document_id' => $requiredDocument->id,
        ])->get();
        $requiredDocumentDetails = $requiredDocumentDetailData->groupBy('plan_detail_doc_id');

        $planDetailDocIds = $plansIsNotCompleted->pluck('planDetails')->flatten()->pluck('planDetailDocs')->flatten()->pluck('id');
        $plansIsNotCompleted->map(function ($item) use ($round, $requiredDocumentDetailData) {
            $item->plan_detail_is_choice = $item->planDetails->where('is_choice', PlanDetail::IS_CHOICE)->first();

            if ($item->plan_detail_is_choice) {
                $item->plan_detail_is_choice->type_plan_name = $item->plan_detail_is_choice->getTypePlanName();
                $item->plan_detail_is_choice->is_type_plan_name = $item->plan_detail_is_choice->isRequiredTypePlan();
            }
            $item->content_plan_doc_cmt = $item->planDocCmts->where('type', PlanDocCmt::TYPE_U204N)->where('from_send_doc', 'u204_' . $round)->first();

            $planDetailDocs = $item->plan_detail_is_choice ? $item->plan_detail_is_choice->planDetailDocs : null;
            $planDetailDocs = $planDetailDocs->map(function ($item) use ($requiredDocumentDetailData) {
                $requiredDocumentDetail = $requiredDocumentDetailData->where('plan_detail_doc_id', $item->id)->first();

                $item->require_document_detail_completed_status = $requiredDocumentDetail->is_completed ?? 0;
                $item->requiredDocumentDetail = $requiredDocumentDetail;

                return $item;
            });

            $item->plan_detail_doc_is_not_completed = $planDetailDocs->where('require_document_detail_completed_status', 0);
            $item->plan_detail_doc_is_completed = $planDetailDocs->where('require_document_detail_completed_status', 1);
        });
        $plansIsNotCompleted = $plansIsNotCompleted->filter(function ($item) {
            return isset($item->plan_detail_is_choice->is_type_plan_name)
                && $item->plan_detail_is_choice->is_type_plan_name == true
                && count($item->plan_detail_doc_is_not_completed) > 0;
        });

        $plansIsCompleted = $relation->getPlans();
        $plansIsCompleted->map(function ($item) use ($request, $requiredDocumentDetailData) {
            $item->plan_detail_is_choice = $item->planDetails->where('is_choice', PlanDetail::IS_CHOICE)->first();

            if ($item->plan_detail_is_choice) {
                $item->plan_detail_is_choice->type_plan_name = $item->plan_detail_is_choice->getTypePlanName();
                $item->plan_detail_is_choice->is_type_plan_name = $item->plan_detail_is_choice->isRequiredTypePlan();
            }

            $planDetailDocs = $item->plan_detail_is_choice ? $item->plan_detail_is_choice->planDetailDocs : null;
            $planDetailDocs = $planDetailDocs->map(function ($item) use ($requiredDocumentDetailData) {
                $requiredDocumentDetail = $requiredDocumentDetailData->where('plan_detail_doc_id', $item->id)->first();

                $item->require_document_detail_completed_status = $requiredDocumentDetail->is_completed ?? 0;
                $item->requiredDocumentDetail = $requiredDocumentDetail;

                return $item;
            });

            $item->plan_detail_doc_is_not_completed = $planDetailDocs->where('require_document_detail_completed_status', 0);
            $item->plan_detail_doc_is_completed = $planDetailDocs->where('require_document_detail_completed_status', 1);
        });
        $plansIsCompleted = $plansIsCompleted->filter(function ($item) {
            return isset($item->plan_detail_is_choice->is_type_plan_name)
                && $item->plan_detail_is_choice->is_type_plan_name == false
                || count($item->plan_detail_doc_is_not_completed) == 0;
        });

        // Checking if the user has been confirmed. If the user has been confirmed, it will block the screen
        $countDocDetail = RequiredDocumentDetail::where('required_document_id', $requiredDocument->id)
            ->whereIn('plan_detail_doc_id', $planDetailDocIds)->where('from_send_doc', U204 . '_' . $round)->count();

        // Block screen when has data in required document detail and required document has is_send = 1 and current page is u204n
        $isBlockScreen = ($countDocDetail && $requiredDocument->is_send == RequiredDocument::IS_SEND) || Route::is(['user.refusal.materials-re.confirm.index']);
        return view('user.modules.refusal.materials-re.index', compact(
            'round',
            'requiredDocument',
            'trademarkPlan',
            'trademarkTable',
            'plansIsCompleted',
            'requiredDocMiss',
            'isBlockScreen',
            'trademarkDocuments',
            'plansIsNotCompleted',
            'comparisonTrademarkResult',
            'requiredDocumentDetails'
        ));
    }

    /**
     * Post material re.
     *
     * @param  Request $request
     * @param  int $id
     * @return RedirectResponse
     */
    public function postMaterialRe(Request $request, $id)
    {
        $filepath = [];

        if (empty($request->trademark_plan_id)) {
            abort(404);
        }

        if (empty($request->round)) {
            abort(404);
        }

        $comparisonTrademarkResult = $this->comparisonTrademarkResultService->find($id);
        if (!$comparisonTrademarkResult) {
            abort(404);
        }
        $comparisonTrademarkResult->load([
            'PlanCorrespondence',
            'trademark',
        ]);
        $planCorrespondence = $comparisonTrademarkResult->PlanCorrespondence;
        $trademark = $comparisonTrademarkResult->trademark;
        if ($trademark->user_id != auth()->user()->id) {
            abort(403);
        }

        $trademarkPlan = $this->trademarkPlanService->findByCondition([
            'id' => $request->trademark_plan_id,
            'plan_correspondence_id' => $planCorrespondence->id,
        ])->first();
        if (!$trademarkPlan) {
            abort(404);
        }
        try {
            DB::beginTransaction();

            $storage = FileHelper::getStorage();

            // is sended and is not confirm
            $requiredDoc = RequiredDocument::updateOrCreate([
                'id' => $request->required_document_id,
                'trademark_plan_id' => $request->trademark_plan_id,
            ], [
                'is_send' => isset($request->submit_confirm) ? RequiredDocument::IS_SEND : RequiredDocument::IS_NOT_SEND,
            ]);

            foreach ($request->data ?? [] as $planId => $value) {
                $plan = $this->planService->find($planId)->load(['requiredDocuments']);
                if (isset($plan->requiredDocuments) && count($plan->requiredDocuments) > 0) {
                    RequiredDocumentPlan::updateOrCreate([
                        'plan_id' => $plan->id,
                        'required_document_id' => $requiredDoc->id,
                    ]);
                }

                foreach ($value['plan_detail_doc'] ?? [] as $planDetailDocId => $valuePlanDetailDoc) {
                    $oldDetail = RequiredDocumentDetail::where('required_document_id', $requiredDoc->id)
                        ->where('plan_detail_doc_id', $planDetailDocId)
                        ->where('from_send_doc', U204 . '_' . $request->round)
                        ->first();

                    $attachmentUser = [];

                    if (!empty($oldDetail)) {
                        $attachmentUser = json_decode($oldDetail->attachment_user, true);
                    }

                    if (!empty($valuePlanDetailDoc['attach'])) {
                        foreach ($valuePlanDetailDoc['attach'] as $file) {
                            if (str_contains($file, FOLDER_TEMP)) {
                                $newFile = Str::replace('temp', 'materials', $file);

                                $explodeFile = explode('/', $newFile);
                                $explodeFileName = explode('.', $explodeFile[3]);

                                // Check namesake
                                $i = 0;
                                while ($storage->exists($newFile)) {
                                    $i++;
                                    $newFile = LOCAL_PUBLIC_FOLDER . '/materials/' . $explodeFileName[0] . '-' . $i . '.' . $explodeFileName[1];
                                }

                                Storage::move($file, $newFile);
                                $attachmentUser[] = [
                                    'sending_date' => now()->format('Y-m-d'),
                                    'value' => $newFile,
                                    'type' => ATTACH,
                                ];
                                $filepath[] = $newFile;
                            } else {
                                $attachmentUser[] = [
                                    'sending_date' => now()->format('Y-m-d'),
                                    'value' => $file,
                                    'type' => ATTACH,
                                ];
                            };
                        }
                    }

                    if (!empty($valuePlanDetailDoc['url'])) {
                        foreach ($valuePlanDetailDoc['url'] as $url) {
                            $attachmentUser[] = [
                                'sending_date' => now()->format('Y-m-d'),
                                'value' => $url,
                                'type' => URL,
                            ];
                        }
                    }

                    $stringAttachmentUser = json_encode($attachmentUser);

                    RequiredDocumentDetail::updateOrCreate([
                        'required_document_id' => $requiredDoc->id,
                        'plan_detail_doc_id' => $planDetailDocId,
                        'from_send_doc' => U204 . '_' . $request->round,
                    ], [
                        'attachment_user' => $stringAttachmentUser,
                    ]);
                }

                if (!empty($value['content'])) {
                    $dataUpdate = [
                        'content' => $value['content'],
                        'type' => PlanDocCmt::TYPE_U204N,
                        'from_send_doc' => isset($request->round) ? "u204_" . $request->round : 'u204',
                        'date_send' => now(),
                    ];

                    $this->planDocCmtService->updateOrCreate([
                        'plan_id' => $planId,
                        'type' => PlanDocCmt::TYPE_U204N,
                        'from_send_doc' => U204 . '_' . $request->round,
                    ], $dataUpdate);
                }
            }

            if (isset($request->submit)) {
                // CommonHelper::setMessage($request, MESSAGE_SUCCESS, __('messages.general.Common_E034'));
                $redirect = route('user.refusal.materials-re.confirm.index', [
                    'id' => $comparisonTrademarkResult->id,
                    'required_document_id' => $request->required_document_id,
                    'trademark_plan_id' => $trademarkPlan->id,
                    'round' => $request->round
                ]);
            } elseif (isset($request->draft)) {
                CommonHelper::setMessage($request, MESSAGE_SUCCESS, __('messages.save_draft'));
                $redirect = route('user.application-detail.index', ['id' => $comparisonTrademarkResult->trademark_id]);
            } elseif (isset($request->submit_confirm)) {
                $trademarkPlan->update([
                    'from_send_doc' => isset($request->round) ? "u204_" . $request->round : 'u204_2',
                    'is_confirm_docs' => TrademarkPlan::IS_CONFIRM_DOCS_FALSE,
                ]);

                // Send notice
                $this->noticeMaterialRe($comparisonTrademarkResult, $trademarkPlan, $request->required_document_id);

                CommonHelper::setMessage($request, MESSAGE_SUCCESS, __('messages.general.Common_E034'));
                $redirect = route('user.top');
            } elseif (isset($request->draft_confirm)) {
                // CommonHelper::setMessage($request, MESSAGE_SUCCESS, __('messages.general.Common_E034'));
                $redirect = route('user.refusal.materials-re.index', [
                    'id' => $comparisonTrademarkResult->id,
                    'trademark_plan_id' => $trademarkPlan->id,
                    'required_document_id' => $request->required_document_id,
                    'round' => $request->round
                ]);
            }
            DB::commit();
            return redirect($redirect)->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            foreach ($filepath as $path) {
                FileHelper::unlink($path);
            }

            CommonHelper::setMessage($request, MESSAGE_ERROR, __('messages.general.Common_E027'));
            return redirect()->back();
        }
    }

    /**
     * Notice Material Re u204n
     *
     * @param Model $comparisonTrademarkResult
     * @param Model $trademarkPlan
     * @return void
     */
    public function noticeMaterialRe(Model $comparisonTrademarkResult, Model $trademarkPlan, int $requiredDocumentId)
    {
        $trademark = $comparisonTrademarkResult->trademark;

        $explodeFromSendDoc = explode('_', $trademarkPlan->from_send_doc);
        $step = $explodeFromSendDoc[1] ?? 0;

        // Update Notice at A-204n (No 83: H I)
        $stepBeforeNotice = $this->noticeDetailService->findByCondition([
            'type_acc' => NoticeDetail::TYPE_USER,
            'is_answer' => NoticeDetail::IS_NOT_ANSWER,
        ])->with('notice')->get()
            ->where('notice.trademark_id', $trademark->id)
            ->where('notice.user_id', $trademark->user_id)
            ->whereIn('notice.flow', [Notice::FLOW_RESPONSE_REASON, Notice::FLOW_RENEWAL_BEFORE_DEADLINE])
            ->filter(function ($item) {
                if ($item->notice->flow == Notice::FLOW_RESPONSE_REASON && in_array($item->notice->step, [Notice::STEP_3, Notice::STEP_4])) {
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

        $targetPage = route('admin.refusal.materials-re.supervisor', [
            'id' => $comparisonTrademarkResult->id,
            'trademark_plan_id' => $trademarkPlan->id,
            'required_document_id' => $requiredDocumentId,
            'round' => $step,
        ]);
        $redirectPage = route('admin.refusal.materials-re.check.supervisor', [
            'id' => $comparisonTrademarkResult->id,
            'trademark_plan_id' => $trademarkPlan->id,
            'required_document_id' => $requiredDocumentId,
            'round' => $step,
        ]);

        // Set response deadline
        $planCorrespondence = $trademarkPlan->planCorrespondence;
        $machingResult = $comparisonTrademarkResult->machingResult;
        $responseDeadline = $machingResult->calculateResponseDeadline(-30);
        if (!empty($machingResult) && $planCorrespondence->type == PlanCorrespondence::TYPE_2) {
            $responseDeadline = $machingResult->calculateResponseDeadline(-35);
        }

        $notice = [
            'trademark_id' => $trademark->id,
            'trademark_info_id' => null,
            'user_id' => $trademark->user_id,
            'flow' => Notice::FLOW_RESPONSE_REASON,
            'step' => Notice::STEP_4,
        ];

        $noticeDetails = [
            [
                'target_id' => null,
                'type_acc' => NoticeDetail::TYPE_SUPERVISOR,
                'target_page' => $targetPage,
                'redirect_page' => $redirectPage,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                'is_action' => true,
                'content' => '拒絶理由通知対応：必要資料受領',
                'response_deadline' => $responseDeadline,
            ],
            [
                'target_id' => null,
                'type_acc' => NoticeDetail::TYPE_SUPERVISOR,
                'target_page' => $targetPage,
                'redirect_page' => $redirectPage,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                'content' => '責任者　拒絶理由通知対応：必要資料受領',
                'attribute' => 'お客様から',
                'response_deadline' => $responseDeadline,
            ],
            [
                'target_id' => $trademark->user_id,
                'type_acc' => NoticeDetail::TYPE_USER,
                'target_page' => $targetPage,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_DEFAULT,
                'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                'content' => '拒絶理由通知対応：必要資料提出済・返信待ち',
                'attribute' => 'お客様から',
                'response_deadline' => $responseDeadline,
            ],
            [
                'target_id' => $trademark->user_id,
                'type_acc' => NoticeDetail::TYPE_USER,
                'target_page' => $targetPage,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                'content' => '拒絶理由通知対応：必要資料提出済・返信待ち',
                'attribute' => 'お客様から',
                'response_deadline' => $responseDeadline,
            ],
        ];

        $this->noticeService->sendNotice([
            'notices' => $notice,
            'notice_details' => $noticeDetails,
        ]);
    }
}
