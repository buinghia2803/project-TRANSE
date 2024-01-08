<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\A203S\PostRefusalResponsePlaneSupervisorRequest;
use App\Models\Plan;
use App\Models\PlanComment;
use App\Models\PlanDetail;
use App\Models\PlanDetailProduct;
use App\Models\TrademarkDocument;
use App\Models\TrademarkPlan;
use App\Notices\CommonNotice;
use App\Services\PlanCommentService;
use App\Services\TrademarkDocumentService;
use App\Helpers\CommonHelper;
use App\Models\MailTemplate;
use App\Models\MProduct;
use App\Models\PlanDetailDistinct;
use App\Models\Notice;
use App\Models\NoticeDetail;
use App\Models\PlanCorrespondence;
use App\Models\Reason;
use App\Services\Common\NoticeService;
use App\Services\Common\TrademarkTableService;
use App\Services\ComparisonTrademarkResultService;
use App\Services\MailTemplateService;
use App\Services\MCodeService;
use App\Services\MDistinctionService;
use App\Services\MProductService;
use App\Services\NoticeDetailService;
use App\Services\PlanDetailDistinctService;
use App\Services\PlanDetailProductCodeService;
use App\Services\PlanDetailProductService;
use App\Services\MTypePlanDocService;
use App\Services\MTypePlanService;
use App\Services\PlanDetailDocService;
use App\Services\PlanDetailService;
use App\Services\PlanReasonService;
use App\Services\TrademarkPlanService;
use App\Services\PlanCorrespondenceService;
use App\Services\PlanService;
use App\Services\TrademarkService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;

class PlanController extends BaseController
{
    private TrademarkTableService $trademarkTableService;
    private ComparisonTrademarkResultService $comparisonTrademarkResultService;
    private TrademarkPlanService $trademarkPlanService;
    private PlanService $planService;
    private TrademarkDocumentService $trademarkDocumentService;
    private PlanCommentService $planCommentService;
    private PlanDetailService $planDetailService;
    private PlanDetailDocService $planDetailDocService;
    private PlanReasonService $planReasonService;
    private MTypePlanService $mTypePlanService;
    private MTypePlanDocService $mTypePlanDocService;
    private MDistinctionService $mDistinctionService;
    private PlanDetailProductService $planDetailProductService;
    private MCodeService $mCodeService;
    private MProductService $mProductService;
    private PlanDetailProductCodeService $planDetailProductCodeService;
    private PlanDetailDistinctService $planDetailDistinctService;
    private NoticeService $noticeService;
    private NoticeDetailService $noticeDetailService;
    private PlanCorrespondenceService $planCorrespondenceService;
    private MailTemplateService $mailTemplateService;
    private TrademarkService $trademarkService;

    /**
     * Constructor
     *
     * @return  void
     */
    public function __construct(
        TrademarkTableService            $trademarkTableService,
        ComparisonTrademarkResultService $comparisonTrademarkResultService,
        TrademarkPlanService             $trademarkPlanService,
        PlanService                      $planService,
        PlanReasonService                $planReasonService,
        MTypePlanService                 $mTypePlanService,
        MTypePlanDocService              $mTypePlanDocService,
        PlanDetailService                $planDetailService,
        PlanDetailDocService             $planDetailDocService,
        PlanDetailProductService         $planDetailProductService,
        PlanDetailProductCodeService     $planDetailProductCodeService,
        PlanDetailDistinctService        $planDetailDistinctService,
        MDistinctionService              $mDistinctionService,
        PlanCommentService               $planCommentService,
        MCodeService                     $mCodeService,
        MProductService                  $mProductService,
        TrademarkDocumentService         $trademarkDocumentService,
        NoticeService                    $noticeService,
        NoticeDetailService              $noticeDetailService,
        PlanCorrespondenceService        $planCorrespondenceService,
        MailTemplateService              $mailTemplateService,
        TrademarkService                 $trademarkService
    )
    {
        parent::__construct();
        $this->comparisonTrademarkResultService = $comparisonTrademarkResultService;
        $this->trademarkTableService = $trademarkTableService;
        $this->trademarkDocumentService = $trademarkDocumentService;
        $this->planService = $planService;
        $this->trademarkPlanService = $trademarkPlanService;
        $this->planCommentService = $planCommentService;
        $this->planReasonService = $planReasonService;
        $this->mTypePlanService = $mTypePlanService;
        $this->mTypePlanDocService = $mTypePlanDocService;
        $this->planDetailService = $planDetailService;
        $this->planDetailDocService = $planDetailDocService;
        $this->mDistinctionService = $mDistinctionService;
        $this->planDetailProductService = $planDetailProductService;
        $this->planDetailProductCodeService = $planDetailProductCodeService;
        $this->planDetailDistinctService = $planDetailDistinctService;
        $this->mCodeService = $mCodeService;
        $this->mProductService = $mProductService;
        $this->noticeService = $noticeService;
        $this->noticeDetailService = $noticeDetailService;
        $this->planCorrespondenceService = $planCorrespondenceService;
        $this->mailTemplateService = $mailTemplateService;
        $this->trademarkService = $trademarkService;

        // Check permission
        $this->middleware('permission:plans.store')->only(['store']);
        $this->middleware('permission:plans.productCreate')->only(['productCreate']);
        $this->middleware('permission:plans.postProductCreate')->only(['postProductCreate']);
        $this->middleware('permission:plans.productEditSupervisor')->only(['productEditSupervisor']);
        $this->middleware('permission:plans.postProductEditSupervisor')->only(['postProductEditSupervisor']);
        $this->middleware('permission:plans.postProductReCreateSupervisor')->only(['postProductReCreateSupervisor']);
        $this->middleware('permission:plans.postEditSupervisor')->only(['postEditSupervisor']);
        $this->middleware('permission:plans.postRefusalResponsePlaneSupervisor')->only(['postRefusalResponsePlaneSupervisor']);
        $this->middleware('permission:plans.postRefusalResponsePlaneSupervisorReject')->only(['postRefusalResponsePlaneSupervisorReject']);
        $this->middleware('permission:plans.showSimilarGroupCode')->only(['showSimilarGroupCode']);
        $this->middleware('permission:plans.showSimilarGroupCodeEdit')->only(['showSimilarGroupCodeEdit']);
        $this->middleware('permission:plans.redirectSimilarGroupCodeEditConfirm')->only(['redirectSimilarGroupCodeEditConfirm']);
        $this->middleware('permission:plans.confirmSimilarGroupCodeEdit')->only(['confirmSimilarGroupCodeEdit']);
        $this->middleware('permission:plans.updateSimilarGroupCodeEditConfirm')->only(['updateSimilarGroupCodeEditConfirm']);
        $this->middleware('permission:plans.showModalA203check')->only(['showModalA203check']);
        $this->middleware('permission:plans.postRefusalResponsePlanReSupervisor')->only(['postRefusalResponsePlanReSupervisor']);
    }

    /**
     * Get Refusal Response Plane Supervisor - a203s
     *
     * @param Request $request
     * @param $id - comparison_trademark_result_id ?trademark_plan_id
     * @return View
     */
    public function getRefusalResponsePlaneSupervisor(Request $request, $id): View
    {
        $inputs = $request->all();
        $comparisonTrademarkResult = $this->comparisonTrademarkResultService->find($id)->load('planCorrespondence.trademarkPlans');
        if (empty($inputs['trademark_plan_id']) || !$comparisonTrademarkResult) {
            abort(404);
        }
        $trademarkPlanId = $inputs['trademark_plan_id'];
        $planCorrespondence = $comparisonTrademarkResult->planCorrespondence;

        $trademarkPlan = $this->trademarkPlanService->findByCondition([
            'id' => $trademarkPlanId,
            'plan_correspondence_id' => $planCorrespondence ? $planCorrespondence->id : 0,
        ])->first();

        if (!$trademarkPlan) {
            abort(404);
        }
        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_ADMIN_1, $comparisonTrademarkResult->trademark_id, [
            SHOW_LINK_ANKEN_TOP => true,
            A203S => true,
        ]);

        $listPossibilityResolution = PlanDetail::listPossibilityResolution();
        $listLeaveStatus = PlanDetailProduct::getListLeaveStatus();

        // if have a trademark plan is reject then disable button 差し戻し(button reject)
        $isHideButtonReject = $planCorrespondence->trademarkPlans->where('is_reject', TrademarkPlan::IS_REJECT_TRUE)->count() ?? 0;

        // Get Plan
        $trademarkPlan = $trademarkPlan->load([
            'planComments',
            'plans.planDetails.mTypePlan',
            'plans.planReasons.reason',
        ]);

        //history plan_comments
        $listPlanComments = $this->planCommentService->findByCondition([
            'trademark_id' => $comparisonTrademarkResult->trademark_id,
            'type' => PlanComment::TYPE_1,
        ])->where('trademark_plan_id', '<=', $trademarkPlan->id ?? '')
            ->whereIn('type_comment_step', [
                PlanComment::STEP_1,
                PlanComment::STEP_2,
                PlanComment::STEP_3,
                PlanComment::STEP_4,
                PlanComment::STEP_5,
            ])
            ->whereHas('trademarkPlan.planCorrespondence.comparisonTrademarkResult', function ($query) use ($comparisonTrademarkResult) {
                $query->where('id', $comparisonTrademarkResult->id);
            })
            ->orderBy('id', 'desc')->get();
        $listPlanComments = $listPlanComments->filter(function ($item) {
            return !empty($item->content);
        });

        //plan_comments old data
        $planComment = $this->planCommentService->findByCondition([
            'trademark_plan_id' => $trademarkPlanId,
            'type' => PlanComment::TYPE_INTERNAL,
            'type_comment_step' => PlanComment::STEP_2,
        ])->first();

        //list ids trademark_plan_detail all
        $trademarkPlanDetailIds = [];
        foreach ($trademarkPlan->plans as $plan) {
            $products = $this->planService->getDataProductPlanSupervisor($plan->planDetails->pluck('id')->toArray());
            $array = [];
            foreach ($products as $product) {
                $array[$product->id]['m_distinction_id'] = $product->m_distinction_id;
                $array[$product->id]['id'] = $product->id;
                $array[$product->id]['products_number'] = $product->products_number;
                $array[$product->id]['rank'] = $product->rank;
                $array[$product->id]['name'] = $product->name;
                $array[$product->id]['type'] = $product->type;
                $array[$product->id]['block'] = $product->block;
                $array[$product->id]['m_code_names'] = $product->m_code_names;
                $array[$product->id]['mDistinction'] = $product->mDistinction;
                $array[$product->id]['is_choice'] = $product->is_choice;
                $array[$product->id]['role_add'] = $product->role_add;

                if (!isset($array[$product->id]['plan_details'])) {
                    $array[$product->id]['plan_details'] = [];
                }

                $array[$product->id]['plan_details'][] = [
                    'm_product_id' => $product->id,
                    'leave_status' => $product->leave_status,
                    'leave_status_other' => $product->leave_status_other,
                    'is_choice' => $product->is_choice,
                    'plan_detail_id' => $product->plan_detail_id,
                    'role_add' => $product->role_add
                ];
                $array[$product->id]['plan_details'] = array_unique($array[$product->id]['plan_details'], SORT_REGULAR);
            }
            $plan->productsByProduct = $array;
        }

        //common
        $dataCommon = $this->planService->deadlineAndQuestionToRefuse($id);

        //is_choice
        $isChoiceTrue = PlanDetailProduct::IS_CHOICE;
        $isChoiceFalse = PlanDetailProduct::IS_NOT_CHOICE;

        //is_confirm
        $isConfirmTrue = TrademarkPlan::IS_CONFIRM_TRUE;

        //is_reject
        $trademarkPlanIsRejectTrue = TrademarkPlan::IS_REJECT_TRUE;

        //role_add
        $roleAddOther = PlanDetailProduct::ROLE_ADD_OTHER; //1
        $roleAddPersonCharge = PlanDetailProduct::ROLE_ADD_PERSON_CHARGE; //2
        $roleAddResponsiblePerson = PlanDetailProduct::ROLE_ADD_RESPONSIBLE_PERSON; //3

        $isModal = (int) $request->is_modal ?? 0;

        $data = [
            'comparisonTrademarkResultId' => $id,
            'trademarkPlanId' => $trademarkPlanId,
            'trademarkPlan' => $trademarkPlan,
            'trademarkTable' => $trademarkTable,
            'comparisonTrademarkResult' => $comparisonTrademarkResult,
            'listPossibilityResolution' => $listPossibilityResolution,
            'listLeaveStatus' => $listLeaveStatus,
            'planComment' => $planComment,
            'dataCommon' => $dataCommon,
            'isChoiceTrue' => $isChoiceTrue,
            'isChoiceFalse' => $isChoiceFalse,
            'roleAddOther' => $roleAddOther,
            'roleAddPersonCharge' => $roleAddPersonCharge,
            'roleAddResponsiblePerson' => $roleAddResponsiblePerson,
            'listPlanComments' => $listPlanComments,
            'isConfirmTrue' => $isConfirmTrue,
            'trademarkPlanIsRejectTrue' => $trademarkPlanIsRejectTrue,
            'isModal' => $isModal,
            'isHideButtonReject' => $isHideButtonReject
        ];

        return view('admin.modules.plans.a203s.index', $data);
    }

    /**
     * Post Refusal Response Plane Supervisor - a203s
     *
     * @param PostRefusalResponsePlaneSupervisorRequest $request
     * @param $id - comparison_trademark_result_id ?trademark_plan_id
     *
     * @return RedirectResponse
     */
    public function postRefusalResponsePlaneSupervisor(PostRefusalResponsePlaneSupervisorRequest $request, $id)
    {
        $inputs = $request->all();

        $comparisonTrademarkResult = $this->comparisonTrademarkResultService->find($id);
        if (!$comparisonTrademarkResult || empty($inputs['trademark_plan_id'])) {
            abort(404);
        }

        $trademarkPlan = $this->trademarkPlanService->find($inputs['trademark_plan_id']);
        if (!$trademarkPlan) {
            abort(404);
        }

        try {
            DB::beginTransaction();

            if ($inputs['redirect_to'] == A203SASHI) {
                $trademarkPlan->update([
                    'is_redirect' => true,
                ]);

                $commonNotice = app(CommonNotice::class);
                $commonNotice->noticeA203StoA203Sashi($comparisonTrademarkResult, $trademarkPlan);

                DB::commit();

                return redirect()->route('admin.refusal.response-plan.supervisor-reject', [
                    'id' => $comparisonTrademarkResult->id,
                    'trademark_plan_id' => $trademarkPlan->id,
                ]);
            } elseif ($inputs['redirect_to'] == A203SHU) {
                $trademarkPlan->update([
                    'is_redirect' => true,
                ]);

                $commonNotice = app(CommonNotice::class);
                $commonNotice->noticeA203StoA203Shu($comparisonTrademarkResult, $trademarkPlan);

                DB::commit();

                return redirect()->route('admin.refusal.response-plan.edit.supervisor', [
                    'id' => $comparisonTrademarkResult->id,
                    'trademark_plan_id' => $trademarkPlan->id,
                ]);
            } else { // U203
                $inputs['comparison_trademark_result_id'] = $id;
                $inputs['trademark_id'] = $comparisonTrademarkResult->trademark_id;
                $inputs['user_id'] = $comparisonTrademarkResult->trademark->user->id;
                $result = $this->planService->postRefusalResponsePlaneSupervisor($inputs);

                if ($result) {
                    // send mail a203s
                    $dataMail = [
                        'from_page' => A203S,
                        'user' => $comparisonTrademarkResult->trademark->user
                    ];
                    $this->mailTemplateService->sendMailRequest($dataMail, MailTemplate::TYPE_OTHER, MailTemplate::GUARD_TYPE_ADMIN);

                    DB::commit();

                    return redirect()->route('admin.home')->with('message', __('messages.general.Hoshin_A203_S001'));
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
        }

        return redirect()->back()->with('message', __('messages.error'));
    }

    /**
     * Get Refusal Response Plane Supervisor Reject - a203sashi
     *
     * @param Request $request
     * @param integer $id - comparison_trademark_result_id ?trademark_plan_id
     * @return void
     */
    public function getRefusalResponsePlaneSupervisorReject(Request $request, int $id)
    {
        $inputs = $request->all();
        if (empty($inputs['trademark_plan_id'])) {
            abort(404);
        }
        $trademarkPlanId = $inputs['trademark_plan_id'];

        $comparisonTrademarkResult = $this->comparisonTrademarkResultService->find($id);
        $planCorrespondence = $comparisonTrademarkResult->planCorrespondence;

        //trademark_plans: flug_role = 2, is_reject = 0
        $trademarkPlan = $this->trademarkPlanService->findByCondition([
            'id' => $trademarkPlanId,
            'flag_role' => TrademarkPlan::FLAG_ROLE_2,
            'plan_correspondence_id' => $planCorrespondence ? $planCorrespondence->id : 0,
        ])->first();

        $trademarkPlanDataIsRejectTrue = $this->trademarkPlanService->findByCondition([
            'is_reject' => TrademarkPlan::IS_REJECT_TRUE,
            'plan_correspondence_id' => $planCorrespondence->id ?? 0,
        ])->first();

        if (!$comparisonTrademarkResult || !$trademarkPlan) {
            abort(404);
        }

        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_ADMIN_1, $comparisonTrademarkResult->trademark_id, [
            SHOW_LINK_ANKEN_TOP => true,
            A203SASHI => true,
        ]);

        $listPossibilityResolution = PlanDetail::listPossibilityResolution();
        $listLeaveStatus = PlanDetailProduct::getListLeaveStatus();

        // Get Plan
        $isBlockScreen = false;
        if ($trademarkPlan) {
            $trademarkPlan = $trademarkPlan->load([
                'planComments',
                'plans.planDetails.mTypePlan',
                'plans.planReasons.reason',
            ]);

            if ($trademarkPlan->is_reject == TrademarkPlan::IS_REJECT_TRUE) {
                $isBlockScreen = true;
            }
        }
        if (!empty($trademarkPlanDataIsRejectTrue)) {
            $isBlockScreen = true;
        }

        //plan_comments old data: type = 1, type_comment_step = 3
        $planComment = $this->planCommentService->findByCondition([
            'trademark_plan_id' => $trademarkPlanId,
            'type' => PlanComment::TYPE_INTERNAL,
            'type_comment_step' => PlanComment::STEP_3,
        ])->first();

        //history plan_comments
        $listPlanComments = $this->planCommentService->findByCondition([
            'trademark_id' => $comparisonTrademarkResult->trademark_id,
            'type' => PlanComment::TYPE_1,
        ])->where('trademark_plan_id', '<=', $trademarkPlan->id ?? '')
            ->whereIn('type_comment_step', [
                PlanComment::STEP_1,
                PlanComment::STEP_2,
                PlanComment::STEP_3,
                PlanComment::STEP_4,
                PlanComment::STEP_5,
            ])
            ->whereHas('trademarkPlan.planCorrespondence.comparisonTrademarkResult', function ($query) use ($comparisonTrademarkResult) {
                $query->where('id', $comparisonTrademarkResult->id);
            });
        if ($planComment) {
            $listPlanComments = $listPlanComments->where('id', '!=', $planComment->id);
        }
        $listPlanComments = $listPlanComments->orderBy('id', 'desc')->get();
        $listPlanComments = $listPlanComments->filter(function ($item) {
            return !empty($item->content);
        });

        //list ids trademark_plan_detail all
        $trademarkPlanDetailIds = [];
        if ($trademarkPlan) {
            foreach ($trademarkPlan->plans as $plan) {
                $products = $this->planService->getDataProductPlanSupervisor($plan->planDetails->pluck('id')->toArray());
                $array = [];
                foreach ($products as $product) {
                    $array[$product->id]['m_distinction_id'] = $product->m_distinction_id;
                    $array[$product->id]['id'] = $product->id;
                    $array[$product->id]['products_number'] = $product->products_number;
                    $array[$product->id]['rank'] = $product->rank;
                    $array[$product->id]['name'] = $product->name;
                    $array[$product->id]['type'] = $product->type;
                    $array[$product->id]['block'] = $product->block;
                    $array[$product->id]['m_code_names'] = $product->m_code_names;
                    $array[$product->id]['mDistinction'] = $product->mDistinction;
                    $array[$product->id]['is_choice'] = $product->is_choice;
                    $array[$product->id]['role_add'] = $product->role_add;

                    if (!isset($array[$product->id]['plan_details'])) {
                        $array[$product->id]['plan_detail_products'] = [];
                    }

                    $array[$product->id]['plan_details'][] = [
                        'm_product_id' => $product->id,
                        'leave_status' => $product->leave_status,
                        'leave_status_other' => $product->leave_status_other,
                        'is_choice' => $product->is_choice,
                        'plan_detail_id' => $product->plan_detail_id,
                    ];
                    $array[$product->id]['plan_details'] = array_unique($array[$product->id]['plan_details'], SORT_REGULAR);
                }
                $plan->productsByProduct = $array;
            }
        }

        //common
        $dataCommon = $this->planService->deadlineAndQuestionToRefuse($id);

        //is_choice
        $isChoiceTrue = PlanDetailProduct::IS_CHOICE;
        $isChoiceFalse = PlanDetailProduct::IS_NOT_CHOICE;

        //is_confirm
        $isConfirmTrue = TrademarkPlan::IS_CONFIRM_TRUE;

        //is_reject
        $trademarkPlanIsRejectFalse = TrademarkPlan::IS_REJECT_FALSE;
        $trademarkPlanIsRejectTrue = TrademarkPlan::IS_REJECT_TRUE;

        //role_add
        $roleAddOther = PlanDetailProduct::ROLE_ADD_OTHER; //1
        $roleAddPersonCharge = PlanDetailProduct::ROLE_ADD_PERSON_CHARGE; //2
        $roleAddResponsiblePerson = PlanDetailProduct::ROLE_ADD_RESPONSIBLE_PERSON; //3

        $data = [
            'comparisonTrademarkResultId' => $id,
            'trademarkPlanId' => $trademarkPlanId,
            'trademarkPlan' => $trademarkPlan,
            'trademarkTable' => $trademarkTable,
            'comparisonTrademarkResult' => $comparisonTrademarkResult,
            'listPossibilityResolution' => $listPossibilityResolution,
            'listLeaveStatus' => $listLeaveStatus,
            'planComment' => $planComment,
            'dataCommon' => $dataCommon,
            'isChoiceTrue' => $isChoiceTrue,
            'isChoiceFalse' => $isChoiceFalse,
            'roleAddOther' => $roleAddOther,
            'roleAddPersonCharge' => $roleAddPersonCharge,
            'roleAddResponsiblePerson' => $roleAddResponsiblePerson,
            'listPlanComments' => $listPlanComments,
            'isConfirmTrue' => $isConfirmTrue,
            'trademarkPlanIsRejectFalse' => $trademarkPlanIsRejectFalse,
            'trademarkPlanIsRejectTrue' => $trademarkPlanIsRejectTrue,
            'isBlockScreen' => $isBlockScreen,
        ];

        return view('admin.modules.plans.a203sashi.index', $data);
    }

    /**
     * Post Refusal Response Plane Supervisor Reject - a203sashi
     *
     * @param Request $request
     * @param integer $id - comparison_trademark_result_id ?trademark_plan_id
     * @return void
     */
    public function postRefusalResponsePlaneSupervisorReject(PostRefusalResponsePlaneSupervisorRequest $request, $id)
    {
        $inputs = $request->all();
        $comparisonTrademarkResult = $this->comparisonTrademarkResultService->find($id);
        if (!$comparisonTrademarkResult || empty($inputs['trademark_plan_id'])) {
            abort(404);
        }
        $trademarkPlane = $this->trademarkPlanService->find($inputs['trademark_plan_id']);
        if (!$trademarkPlane) {
            abort(404);
        }

        $inputs['comparison_trademark_result_id'] = $id;
        $inputs['trademark_id'] = $comparisonTrademarkResult->trademark_id;
        $inputs['user_id'] = $comparisonTrademarkResult->trademark->user->id;
        $result = $this->planService->postRefusalResponsePlaneSupervisorReject($inputs);

        if ($result) {
            return redirect()->route('admin.home')->with('message', __('messages.general.Common_S041'));
        }

        return redirect()->back()->with('message', __('messages.error'));
    }

    /**
     * Index a203
     *
     * @param Request $request
     * @param int $id
     * @return View
     */
    public function index(Request $request, int $id): View
    {
        $comparisonTrademarkResult = $this->comparisonTrademarkResultService->find($id);
        if (!$comparisonTrademarkResult) {
            abort(404);
        }

        $comparisonTrademarkResult->load('planCorrespondences.reasonNos', 'trademark', 'trademark.appTrademark');
        $trademark = $comparisonTrademarkResult->trademark;
        if (!$trademark) {
            abort(404);
        }

        $planCorrespondence = $comparisonTrademarkResult->planCorrespondence;
        if (!$planCorrespondence) {
            abort(404);
        }
        $reasonNo = $planCorrespondence->reasonNos->last() ?? null;
        if (!$reasonNo) {
            abort(404);
        }
        $reasons = [];
        if ($reasonNo) {
            $reasonNo->load([
                'reasons' => function ($query) {
                    $query->where('reason_name', '!=', Reason::NO_REASON);
                },
            ]);
            $reasons = $reasonNo->reasons;
        }
        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_ADMIN_1, $trademark->id);
        $trademarkPlan = $planCorrespondence->trademarkPlans->sortBy([
            ['id', 'desc'],
        ])->load('plans')->first();
        if ($trademarkPlan) {
            $plans = $trademarkPlan->plans;
            $plans->load(
                'planDetails.planDetailDocs',
                'reason',
                'planDetails.distinctsIsAdd',
                'planDetails.distinctsIsDistinctSettement',
                'planDetails.planDetailDistincts.planDetailProducts',
            );
            foreach ($plans as &$plan) {
                $reasonIds = [];
                foreach ($plan->reason as $reason) {
                    $reasonIds[] = $reason->id;
                }
                foreach ($plan->planDetails as &$planDetail) {
                    $planDetailDistincts = $planDetail->planDetailDistincts->where('is_add', PlanDetailDistinct::IS_ADD);
                    $planDetailDistinctNotAdd = $planDetailDistincts->filter(function ($planDetailDistincts) {
                        $planDetailProducts = $planDetailDistincts->planDetailProducts;

                        $planDetailProductNotAdd = $planDetailProducts->whereIn('leave_status', [
                            PlanDetailProduct::LEAVE_STATUS_7,
                            PlanDetailProduct::LEAVE_STATUS_3,
                        ]);

                        $planDetailProductIsDeleted = $planDetailProducts->where('is_deleted', true);

                        return $planDetailProductIsDeleted->count() > 0 || $planDetailProductNotAdd->count() > 0;
                    });
                    $planDetailDistinctNotAddIDs = $planDetailDistinctNotAdd->pluck('m_distinction_id')->toArray();

                    $planDetail->distinctsIsAdd = $planDetail->distinctsIsAdd
                        ->whereNotIn('id', $planDetailDistinctNotAddIDs)
                        ->unique('id')->flatten();
                    $planDetail->distinctsIsDistinctSettement = $planDetail->distinctsIsDistinctSettement
                        ->whereNotIn('id', $planDetailDistinctNotAddIDs)
                        ->unique('id')->flatten();
                }
                $plan->reasonIds = $reasonIds;
            }
        } else {
            $plans = [];
        }

        $possibilityResolutions = [
            '◎' => PlanDetail::RESOLUTION_1,
            '○' => PlanDetail::RESOLUTION_2,
            '△' => PlanDetail::RESOLUTION_3,
            '×' => PlanDetail::RESOLUTION_4,
        ];
        $planComments = $this->planCommentService->findByCondition([
            'trademark_id' => $comparisonTrademarkResult->trademark_id,
            'type' => PlanComment::TYPE_1,
        ])->where('trademark_plan_id', '<=', $trademarkPlan->id ?? '')
            ->whereIn('type_comment_step', [
                PlanComment::STEP_1,
                PlanComment::STEP_2,
                PlanComment::STEP_3,
                PlanComment::STEP_4,
                PlanComment::STEP_5,
            ])
            ->whereHas('trademarkPlan.planCorrespondence.comparisonTrademarkResult', function ($query) use ($comparisonTrademarkResult) {
                $query->where('id', $comparisonTrademarkResult->id);
            })
            ->orderBy('id', 'desc')->get();
        $planComments = $planComments->filter(function ($item) {
            return !empty($item->content);
        });

        $flagDisabled = false;
        if ($trademarkPlan) {
            if ($trademarkPlan->flag_role == TrademarkPlan::FLAG_ROLE_2 && $trademarkPlan->is_reject == TrademarkPlan::IS_REJECT_FALSE) {
                $flagDisabled = true;
            }
        }
        $mTypePlans = $this->mTypePlanService->getAllTypePlan();
        $mTypePlanDocs = $this->mTypePlanDocService->getAllTypePlanDoc();
        $redirectBack = route('admin.refusal.response-plan.index', [
            'id' => $comparisonTrademarkResult->id,
        ]);
        $dataCommon = $this->planService->deadlineAndQuestionToRefuse($id, A203);

        return view('admin.modules.plans.index', compact(
            'comparisonTrademarkResult',
            'trademarkPlan',
            'id',
            'reasons',
            'trademarkTable',
            'mTypePlans',
            'mTypePlanDocs',
            'plans',
            'dataCommon',
            'possibilityResolutions',
            'planComments',
            'flagDisabled',
            'redirectBack'
        ));
    }

    /**
     * Store a203
     *
     * @param Request $request
     */
    public function store(Request $request)
    {
        $params = $request->all();
        $comparisonTrademarkResult = $this->comparisonTrademarkResultService->find($params['comparison_trademark_result_id']);
        if (!$comparisonTrademarkResult) {
            abort(404);
        }
        $comparisonTrademarkResult->load('planCorrespondences.reasonNo', 'trademark', 'trademark.appTrademark');
        $trademark = $comparisonTrademarkResult->trademark;
        if (!$trademark) {
            abort(404);
        }
        try {
            DB::beginTransaction();
            $planCorrespondence = $comparisonTrademarkResult->planCorrespondence;
            $planCorrespondenceProds = $planCorrespondence->planCorrespondenceProds;
            $planCorrespondenceProds->load('appTrademarkProd.mProduct.mCode', 'reasonRefNumProd');

            $trademarkPlan = $planCorrespondence->trademarkPlans->sortBy(['id', 'desc'])->first();
            if ($params['submit'] != 'create_reason') {
                $responseDeadline = Carbon::createFromFormat('Y年m月d日', $params['response_deadline'] ?? null)->endOfDay()->toDateString();
            }
            $params['data'] = json_decode($params['plan_reason'][0], true);

            if (isset($params['is_distinct_settlements'])) {
                $params['distinct_settement'] = json_decode($params['is_distinct_settlements'][0], true);
            }
            if (isset($params['is_leave_all'])) {
                $isLeaveAll = $params['is_leave_all'];
                foreach ($params['plan_id'] as $keyPlan => $plan) {
                    foreach ($params['plan_detail_id'][$keyPlan] as $keyPlanDetail => $planDetail) {
                        if (!empty($isLeaveAll[$keyPlan . '_' . $keyPlanDetail])) {
                            $isLeaveAll[$keyPlan][$keyPlanDetail] = 1;
                        } else {
                            $isLeaveAll[$keyPlan][$keyPlanDetail] = 0;
                        }
                    }
                }
                $params['is_leave_all'] = $isLeaveAll;
            }

            if (!$trademarkPlan) {
                $trademarkPlan = $this->trademarkPlanService->create([
                    'plan_correspondence_id' => $planCorrespondence->id,
                    'is_cancel' => false,
                    'is_reject' => TrademarkPlan::IS_REJECT_FALSE,
                    'is_register' => false,
                    'flag_role' => TrademarkPlan::FLAG_ROLE_1,
                    'is_confirm' => TrademarkPlan::IS_CONFIRM_FALSE
                ]);
                foreach ($planCorrespondenceProds as $key => $value) {
                    if ($value->appTrademarkProd) {
                        $mProduct = $value->appTrademarkProd->mProduct;
                        if (!empty($value->reasonRefNumProd)) {
                            $mProduct->is_choice = $value->reasonRefNumProd->is_choice;
                            $params['products'][] = $mProduct;
                            $params['codes'][$key] = $mProduct->mCode;
                        }
                    }
                }
                $this->planService->createPlan($params, $trademarkPlan);
            } else {
                if ($trademarkPlan->is_reject == TrademarkPlan::IS_REJECT_TRUE && $trademarkPlan->flag_role == TrademarkPlan::FLAG_ROLE_2) {
                    $trademarkPlan = $this->trademarkPlanService->create([
                        'plan_correspondence_id' => $planCorrespondence->id,
                        'is_cancel' => false,
                        'is_reject' => TrademarkPlan::IS_REJECT_FALSE,
                        'is_register' => false,
                        'flag_role' => TrademarkPlan::FLAG_ROLE_1,
                        'is_confirm' => TrademarkPlan::IS_CONFIRM_FALSE
                    ]);
                    foreach ($planCorrespondenceProds as $key => $value) {
                        if ($value->appTrademarkProd) {
                            $mProduct = $value->appTrademarkProd->mProduct;
                            if (!empty($value->reasonRefNumProd)) {
                                $mProduct->is_choice = $value->reasonRefNumProd->is_choice;
                                $params['products'][] = $mProduct;
                                $params['codes'][$key] = $mProduct->mCode;
                            }
                        }
                    }
                    $this->planService->createPlan($params, $trademarkPlan);
                } else {
                    $this->planService->updateOrCreatePlan($params, $trademarkPlan);
                }
            }

            $trademarkPlan->update([
                'response_deadline' => $responseDeadline ?? null,
            ]);
            DB::commit();
            if (isset($params['name_page']) && $params['submit'] == 'draft') {
                return redirect()->route('admin.refusal.response-plan.index', [
                    'id' => $comparisonTrademarkResult->id,
                    'modal' => $params['name_page'],
                ])->with('message', __('messages.general.Common_S008'))->withInput();
            } elseif (isset($params['link_redirect'])) {
                $params['link_redirect'] = str_replace('0000', $trademarkPlan->id, $params['link_redirect']);
                return redirect($params['link_redirect']);
            } elseif ($params['submit'] == 'save' || $params['submit'] == 'create_reason') {
                return redirect()->route('admin.refusal.response-plan.index', $comparisonTrademarkResult->id)->with('message', __('messages.precheck.success'))->withInput();
            } elseif ($params['submit'] == 'submit') {
                if (isset($params['type_save'])) {
                    return [
                        'route' => route('admin.refusal.response-plan.product.create', [
                            'id' => $comparisonTrademarkResult->id,
                            'trademark_plan_id' => $trademarkPlan->id,
                        ]),
                    ];
                }
                return redirect()->route('admin.refusal.response-plan.product.create', [
                    'id' => $comparisonTrademarkResult->id,
                    'trademark_plan_id' => $trademarkPlan->id,
                ])->withInput();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            CommonHelper::setMessage($request, MESSAGE_ERROR, __('messages.error'));

            return redirect()->back();
        }
    }

    /**
     * Delete plan detail a203
     *
     * @param Request $request
     */
    public function deletePlanDetail(Request $request)
    {
        try {
            DB::beginTransaction();

            $planDetail = $this->planDetailService->find($request->plan_detail_id)
                ->load(
                    'plan.planReasons',
                    'plan.planDetails',
                    'planDetailDocs',
                    'planDetailProducts.planDetailProductCodes',
                    'planDetailDistincts',
                );
            $plan = $planDetail->plan;

            $planDetails = $plan->planDetails;

            $planDetailDocs = $planDetail->planDetailDocs()->delete();
            $planDetailDistricts = $planDetail->planDetailDistincts()->delete();
            $planDetailProducts = $planDetail->planDetailProducts;
            foreach ($planDetailProducts as $planDetailProduct) {
                $planDetailProductCodes = $planDetailProduct->planDetailProductCodes();
                $planDetailProduct->delete();
            }

            if (count($planDetails) == 1) {
                $planReason = $plan->planReasons()->delete();
                $plan->delete();
            }
            $planDetail->delete();
            DB::commit();

            return ['message' => 'das'];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            CommonHelper::setMessage($request, MESSAGE_ERROR, __('messages.error'));

            return redirect()->back();
        }
    }

    /**
     * Delete plan a203
     *
     * @param Request $request
     */
    public function deletePlan(Request $request)
    {
        try {
            DB::beginTransaction();
            $plan = $this->planService->find($request->plan_id)->load(
                'planReasons',
                'planDetails.planDetailDocs',
                'planDetails.planDetailDistincts',
                'planDetails.planDetailProducts.planDetailProductCodes'
            );
            $plan->planReasons()->delete();
            $planDetails = $plan->planDetails;

            foreach ($planDetails as $planDetail) {
                $planDetailDoc = $planDetail->planDetailDocs()->delete();
                $planDetailDistricts = $planDetail->planDetailDistincts()->delete();

                $planDetailProducts = $planDetail->planDetailProducts;
                foreach ($planDetailProducts as $planDetailProduct) {
                    $planDetailProductCode = $planDetailProduct->planDetailProductCodes()->delete();
                    $planDetailProduct->delete();
                }
                $planDetail->delete();
            }
            $plan->delete();

            DB::commit();

            return ['message' => 'das'];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            CommonHelper::setMessage($request, MESSAGE_ERROR, __('messages.error'));

            return redirect()->back();
        }
    }

    /**
     * Product Create a203c
     *
     * @param Request $request
     * @param int $id
     * @return View
     */
    public function productCreate(Request $request, int $id): View
    {
        $trademarkPlan = $this->trademarkPlanService->find($request->trademark_plan_id ?? 0);
        if (empty($trademarkPlan)) {
            abort(CODE_ERROR_404);
        }

        $comparisonTrademarkResult = $this->comparisonTrademarkResultService->find($id);
        if (empty($comparisonTrademarkResult)) {
            abort(CODE_ERROR_404);
        }

        $comparisonTrademarkResult->load(['trademark', 'planCorrespondence']);
        $planCorrespondence = $comparisonTrademarkResult->planCorrespondence;
        if ($trademarkPlan->plan_correspondence_id != $planCorrespondence->id) {
            abort(CODE_ERROR_404);
        }

        $trademark = $comparisonTrademarkResult->trademark;
        if (empty($trademark)) {
            abort(CODE_ERROR_404);
        }

        // Get Plan
        $trademarkPlan = $trademarkPlan->load([
            'plans.planDetails.mTypePlan',
            'plans.planDetails.planDetailProducts.mProduct',
            'plans.planDetails.planDetailProducts.planDetailDistinct.mDistinction',
            'plans.planDetails.planDetailProducts.planDetailProductCodes.mCode',
            'plans.planReasons.reason',
        ]);
        $plans = $trademarkPlan->getPlans();
        $products = $trademarkPlan->getProducts()->sortBy('distinction.id');

        // Get Plan Comment
        $planComment = $this->planCommentService->findByCondition([
            'trademark_plan_id' => $trademarkPlan->id,
            'type' => PlanComment::TYPE_1,
            'type_comment_step' => PlanComment::STEP_1,
        ])->first();

        // Trademark Table
        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_ADMIN_1, $trademark->id, [
            SHOW_LINK_ANKEN_TOP => true,
        ]);

        // Get $mDistinctionService
        $disableDistinctionID = $products->where('plan_detail_distinction.is_add', false)->pluck('plan_detail_distinction.m_distinction_id');
        $distinctions = $this->mDistinctionService->findByCondition([])->get()->whereNotIn('id', $disableDistinctionID);

        // Url Back
        $backUrl = route('admin.refusal.response-plan.index', [
            'id' => $comparisonTrademarkResult->id,
            'trademark_plan_id' => $trademarkPlan->id,
        ]);

        // Common
        $dataCommon = $this->planService->deadlineAndQuestionToRefuse($id);

        $isBlockScreen = false;
        if ($trademarkPlan->flag_role == TrademarkPlan::FLAG_ROLE_2 && $trademarkPlan->is_reject == TrademarkPlan::IS_REJECT_FALSE) {
            $isBlockScreen = true;
        }

        return view('admin.modules.plans.product-create', compact(
            'comparisonTrademarkResult',
            'trademarkPlan',
            'planComment',
            'trademarkTable',
            'backUrl',
            'plans',
            'products',
            'distinctions',
            'dataCommon',
            'isBlockScreen',
        ));
    }

    /**
     * Post Product Create a203c
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function postProductCreate(Request $request, int $id): RedirectResponse
    {
        $trademarkPlan = $this->trademarkPlanService->find($request->trademark_plan_id ?? 0);
        if (empty($trademarkPlan)) {
            abort(CODE_ERROR_404);
        }

        $comparisonTrademarkResult = $this->comparisonTrademarkResultService->find($id);
        if (empty($comparisonTrademarkResult)) {
            abort(CODE_ERROR_404);
        }

        $comparisonTrademarkResult->load(['trademark', 'planCorrespondence']);
        $planCorrespondence = $comparisonTrademarkResult->planCorrespondence;
        if ($trademarkPlan->plan_correspondence_id != $planCorrespondence->id) {
            abort(CODE_ERROR_404);
        }

        $trademark = $comparisonTrademarkResult->trademark;
        if (empty($trademark)) {
            abort(CODE_ERROR_404);
        }

        try {
            DB::beginTransaction();

            $params = $request->all();
            $currentAdmin = Auth::guard('admin')->user();

            // Update Trademark Plan
            $responseDeadline = Carbon::createFromFormat('Y年m月d日', $params['response_deadline'] ?? null)->endOfDay();
            $trademarkPlan->update([
                'response_deadline' => $responseDeadline ?? null,
            ]);

            // Create/Update Plan Comment
            $this->planCommentService->updateOrCreate([
                'trademark_plan_id' => $trademarkPlan->id,
                'type' => PlanComment::TYPE_1,
                'type_comment_step' => PlanComment::STEP_1,
            ], [
                'admin_id' => $currentAdmin->id,
                'content' => $params['content'] ?? '',
                'trademark_id' => $comparisonTrademarkResult->trademark_id ?? null,
                'trademark_plan_id' => $trademarkPlan->id,
                'target_id' => $trademarkPlan->id
            ]);

            // Delete Plan Detail Product
            $deletePlanDetailProductIds = $request->delete_plan_detail_product_ids ?? '';
            if (!empty($deletePlanDetailProductIds)) {
                $deletePlanDetailProductIds = explode(',', $deletePlanDetailProductIds);
                $deletePlanDetailProductIds = array_unique($deletePlanDetailProductIds);

                foreach ($deletePlanDetailProductIds as $planDetailProductIds) {
                    $planDetailProduct = $this->planDetailProductService->find($planDetailProductIds);

                    $planDetailProduct->planDetailProductCodes()->delete();
                    $planDetailProduct->planDetailDistinct()->delete();
                    $planDetailProduct->delete();
                }
            }

            // Update Plan Detail Product
            $planDetailProducts = $params['plan_detail_products'] ?? [];
            foreach ($planDetailProducts as $planDetailProductID => $value) {
                $planDetailProduct = $this->planDetailProductService->find($planDetailProductID);

                $leaveStatusOther = [];
                if (!empty($value['leave_status_other'])) {
                    foreach ($value['leave_status_other'] as $key => $leaveStatusOtherValue) {
                        $leaveStatusOther[] = [
                            'plan_product_detail_id' => $key,
                            'value' => $leaveStatusOtherValue,
                        ];
                    }
                }
                $value['leave_status_other'] = json_encode($leaveStatusOther);

                $planDetailProduct->update($value);
            }

            // Update Distinct/Product/ProductCode of PlanDetailProduct
            $updateProduct = $params['update_products'] ?? [];
            foreach ($updateProduct as $planDetailProductID => $value) {
                $planDetailProduct = $this->planDetailProductService->find($planDetailProductID);
                $planDetailProductIds = $value['plan_detail_product_ids'] ?? [];

                // Update Distinct
                $mDistinctionId = $value['m_distinction_id'] ?? 0;
                if (!empty($mDistinctionId)) {
                    $planDetailProducts = $this->planDetailProductService->findByCondition([
                        'ids' => $planDetailProductIds,
                    ])->get();

                    $planDetailDistinctIDs = $planDetailProducts->pluck('plan_detail_distinct_id')->toArray();
                    $this->planDetailDistinctService->findByCondition([
                        'ids' => $planDetailDistinctIDs,
                    ])->get()->map(function ($item) use ($mDistinctionId) {
                        $item->update([
                            'm_distinction_id' => $mDistinctionId,
                        ]);
                    });
                }

                // Update Product
                $productName = $value['product_name'] ?? null;
                $mProduct = null;
                if (!empty($productName)) {
                    $mProduct = $this->mProductService->find($planDetailProduct->m_product_id);
                    $mProduct->update([
                        'name' => $productName,
                    ]);
                }

                // Update/Sync Product Code
                $codeFix = explode(' ', $value['product_code_fix'] ?? null);
                $codes = explode(' ', $value['product_code'] ?? null);
                $codes = array_unique(array_merge($codeFix, $codes));

                $productCode = [];
                $codeIds = [];
                foreach ($codes as $code) {
                    if (!empty($code)) {
                        $codeData = $this->mCodeService->createOrUpdateName($code);
                        $codeIds[] = $codeData->id;

                        foreach ($planDetailProductIds as $detailProductId) {
                            $productCodeData = $this->planDetailProductCodeService->updateOrCreate([
                                'plan_detail_product_id' => $detailProductId,
                                'm_code_id' => $codeData->id,
                            ], []);
                            $productCode[] = $productCodeData;
                        }
                    }
                }
                $planDetailProductCodeIds = collect($productCode)->pluck('id');

                if ($mProduct) {
                    $mProduct->mCode()->sync($codeIds);
                }

                // Delete not match product_code
                $this->planDetailProductCodeService->findByCondition([
                    'plan_detail_product_ids' => $planDetailProductIds,
                ])->whereNotIn('id', $planDetailProductCodeIds)->delete();
            }

            // Create addition product/distinct
            $products = $request->products ?? [];
            foreach ($products as $product) {
                $codes = explode(' ', $product['product_code'] ?? null);

                // Create/Update Code
                $productCode = [];
                foreach ($codes as $code) {
                    if (!empty($code)) {
                        $productCode[] = $this->mCodeService->createOrUpdateName($code);
                    }
                }

                // Create/Update Product
                $dataCreateProduct = [
                    'm_distinction_id' => $product['m_distinction_id'],
                    'admin_id' => $currentAdmin->id,
                    'name' => $product['product_name'],
                    'type' => MProduct::TYPE_CREATIVE_CLEAN,
                ];
                $dataCreateProduct['products_number'] = $this->mProductService->generateProductCode(
                    $dataCreateProduct['type'],
                    $dataCreateProduct['m_distinction_id'],
                );
                $newProduct = $this->mProductService->create($dataCreateProduct);

                // Sync Product Code
                $newProduct->mCode()->sync(collect($productCode)->pluck('id')->toArray());

                // Create Plan Detail Product
                $planDetails = $product['plan_details'] ?? [];
                foreach ($planDetails as $planDetailID => $value) {
                    // Get old Plan Detail Distinct
                    $isDistinctSettlement = PlanDetailDistinct::IS_DISTINCT_SETTLEMENT_TRUE;
                    $planDetailDistinctID = $product['plan_detail_distinct_id'] ?? null;
                    if (!empty($planDetailDistinctID)) {
                        $isDistinctSettlement = PlanDetailDistinct::IS_DISTINCT_SETTLEMENT_FALSE;
                    }

                    $additionDistinct = $this->planDetailDistinctService->create([
                        'plan_detail_id' => $planDetailID,
                        'm_distinction_id' => $product['m_distinction_id'],
                        'is_decision' => PlanDetailDistinct::IS_DECISION_NOT_CHOOSE,
                        'is_add' => $product['is_add'] ?? 0,
                        'is_distinct_settlement' => $isDistinctSettlement,
                    ]);

                    $leaveStatusOther = [];
                    if (!empty($value['leave_status_other'])) {
                        foreach ($value['leave_status_other'] as $key => $value) {
                            $leaveStatusOther[] = [
                                'plan_product_detail_id' => $key,
                                'value' => $value,
                            ];
                        }
                    }

                    $planDetailProductData = [
                        'plan_detail_id' => $planDetailID,
                        'plan_detail_distinct_id' => $additionDistinct->id,
                        'm_product_id' => $newProduct->id,
                        'leave_status' => $value['leave_status'] ?? null,
                        'leave_status_other' => json_encode($leaveStatusOther ?? []),
                        'role_add' => PlanDetailProduct::ROLL_ADD_MANAGER,
                        'is_choice' => true,
                    ];

                    $planDetailProduct = $this->planDetailProductService->create($planDetailProductData);

                    foreach ($productCode as $code) {
                        $this->planDetailProductCodeService->create([
                            'plan_detail_product_id' => $planDetailProduct->id,
                            'm_code_id' => $code->id,
                        ]);
                    }
                }
            }

            if (!empty($params[SUBMIT]) && $params[SUBMIT] != DRAFT) {
                // Update Flag Role
                $trademarkPlan->update([
                    'flag_role' => TrademarkPlan::FLAG_ROLE_2,
                ]);

                $this->noticeService->updateComment(
                    Notice::FLOW_RESPONSE_REASON,
                    $request->content ?? '',
                    $comparisonTrademarkResult->trademark_id
                );

                // Send Notice
                $this->noticeProductCreate($comparisonTrademarkResult, $trademarkPlan);

                CommonHelper::setMessage($request, MESSAGE_SUCCESS, __('messages.general.Common_E034'));
                $redirect = route('admin.home');
            } else {
                if (!empty($params['name_page'])) {
                    CommonHelper::setMessage($request, MESSAGE_SUCCESS, __('messages.general.Common_S008'));
                    $redirect = route('admin.refusal.response-plan.product.create', [
                        'id' => $comparisonTrademarkResult->id,
                        'trademark_plan_id' => $trademarkPlan->id,
                        'modal' => $params['name_page'],
                    ]);
                } elseif (!empty($params['link_redirect'])) {
                    $redirect = $params['link_redirect'];
                } else {
                    CommonHelper::setMessage($request, MESSAGE_SUCCESS, __('messages.update_success'));
                    $redirect = route('admin.refusal.response-plan.product.create', [
                        'id' => $comparisonTrademarkResult->id,
                        'trademark_plan_id' => $trademarkPlan->id,
                    ]);
                }
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
     * Notice Product Create a203c
     *
     * @param Model $comparisonTrademarkResult
     * @param Model $trademarkPlan
     * @return void
     */
    public function noticeProductCreate(Model $comparisonTrademarkResult, Model $trademarkPlan)
    {
        $trademark = $comparisonTrademarkResult->trademark;
        $targetPage = route('admin.refusal.response-plan.product.create', [
            'id' => $comparisonTrademarkResult->id,
            'trademark_plan_id' => $trademarkPlan->id,
        ]);
        $redirectPage = route('admin.refusal.response-plan.supervisor', [
            'id' => $comparisonTrademarkResult->id,
            'trademark_plan_id' => $trademarkPlan->id,
        ]);

        // Update Notice at no 53,56
        $stepBeforeNotice = $this->noticeDetailService->findByCondition([
            'completion_date' => null,
        ])->with('notice')->get()
            ->where('type_acc', '<>', NoticeDetail::TYPE_USER)
            ->where('notice.trademark_id', $trademark->id)
            ->where('notice.user_id', $trademark->user_id)
            ->whereIn('notice.flow', [Notice::FLOW_RESPONSE_REASON, Notice::FLOW_RENEWAL_BEFORE_DEADLINE])
            ->filter(function ($item) {
                if ($item->notice->flow == Notice::FLOW_RESPONSE_REASON && in_array($item->notice->step, [Notice::STEP_2, Notice::STEP_3])) {
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

        // Set response deadline
        $trademarkPlan->load(['planCorrespondence']);
        $planCorrespondence = $trademarkPlan->planCorrespondence;

        $responseDeadline = null;
        $machingResult = $comparisonTrademarkResult->machingResult;
        if (!empty($machingResult) && !empty($planCorrespondence)) {
            if ($planCorrespondence->type == PlanCorrespondence::TYPE_2) {
                $responseDeadline = $machingResult->calculateResponseDeadline(-24);
            } else {
                $responseDeadline = $machingResult->calculateResponseDeadline(-18);
            }
        }

        $notice = [
            'flow' => Notice::FLOW_RESPONSE_REASON,
            'step' => Notice::STEP_3,
            'trademark_id' => $trademark->id,
            'user_id' => $trademark->user_id,
            'trademark_info_id' => null,
        ];

        $noticeDetails = [
            // Send Notice Seki
            [
                'target_id' => null,
                'type_acc' => NoticeDetail::TYPE_SUPERVISOR,
                'target_page' => $targetPage,
                'redirect_page' => $redirectPage,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                'is_action' => true,
                'content' => '拒絶理由通知対応：方針案承認・差し戻し・修正',
                'attribute' => null,
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
                'content' => '責任者　拒絶理由通知対応：方針案承認・差し戻し・修正',
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
     * Modal a203check
     *
     * @param Request $request
     * @param int $id
     * @return View
     */
    public function showModalA203check(Request $request, int $id): View
    {
        $comparisonTrademarkResult = $this->comparisonTrademarkResultService->find($id);
        if (!$comparisonTrademarkResult) {
            abort(404);
        }
        $comparisonTrademarkResult->load([
            'PlanCorrespondence',
            'trademark',
        ]);
        $planCorrespondence = $comparisonTrademarkResult->PlanCorrespondence;

        $trademarkPlan = $this->trademarkPlanService->findByCondition([
            'id' => $request->trademark_plan_id,
            'plan_correspondence_id' => $planCorrespondence->id,
        ])->first();
        if (!$trademarkPlan) {
            abort(404);
        }

        // Get Plan
        $trademarkPlan = $trademarkPlan->load([
            'plans.planDetails.mTypePlan',
            'plans.planDetails.planDetailProducts.mProduct',
            'plans.planDetails.planDetailProducts.planDetailDistinct.mDistinction',
        ]);
        $plans = $trademarkPlan->getPlans();
        $products = $trademarkPlan->getProducts()->sortBy('distinction.id');

        $products = $products->map(function ($product) {
            $planDetailProduct = $product['plan_detail_product'] ?? null;
            $product['is_choice'] = false;
            if (!empty($planDetailProduct) && $planDetailProduct->is_choice == true) {
                $product['is_choice'] = true;
            }

            return $product;
        })->where('is_choice', true);

        return view('admin.modules.plans.a203check', compact(
            'plans',
            'products',
        ));
    }

    /**
     * Edit Supervisor a203shu
     *
     * @param Request $request
     * @param int $id
     * @return View
     */
    public function editSupervisor(Request $request, int $id): View
    {
        $comparisonTrademarkResult = $this->comparisonTrademarkResultService->find($id);
        if (!$comparisonTrademarkResult) {
            abort(404);
        }

        $trademarkPlan = $this->trademarkPlanService->find($request->trademark_plan_id ?? 0);
        if (empty($trademarkPlan)) {
            abort(CODE_ERROR_404);
        }
        $comparisonTrademarkResult->load('planCorrespondences.reasonNos', 'trademark', 'trademark.appTrademark');
        $trademark = $comparisonTrademarkResult->trademark;
        if (!$trademark) {
            abort(404);
        }

        $planCorrespondence = $comparisonTrademarkResult->planCorrespondence;
        if (!$planCorrespondence) {
            abort(404);
        }
        if ($trademarkPlan->plan_correspondence_id != $planCorrespondence->id) {
            abort(404);
        }
        $reasonNo = $planCorrespondence->reasonNos->last();
        $reasons = [];
        if ($reasonNo) {
            $reasonNo->load([
                'reasons' => function ($query) {
                    $query->where('reason_name', '!=', Reason::NO_REASON);
                },
            ]);
            $reasons = $reasonNo->reasons;
        }
        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_ADMIN_1, $trademark->id);
        $planComment = $this->planCommentService->findByCondition([
            'trademark_id' => $comparisonTrademarkResult->trademark_id,
            'trademark_plan_id' => $trademarkPlan->id,
            'type_comment_step' => PlanComment::STEP_4,
        ])->first();

        $planComments = $this->planCommentService->findByCondition([
            'trademark_id' => $comparisonTrademarkResult->trademark_id,
            'type' => PlanComment::TYPE_1,
        ])->where('trademark_plan_id', '<=', $trademarkPlan->id ?? '')
            ->whereIn('type_comment_step', [
                PlanComment::STEP_1,
                PlanComment::STEP_2,
                PlanComment::STEP_3,
                PlanComment::STEP_4,
                PlanComment::STEP_5,
            ])
            ->whereHas('trademarkPlan.planCorrespondence.comparisonTrademarkResult', function ($query) use ($comparisonTrademarkResult) {
                $query->where('id', $comparisonTrademarkResult->id);
            })
            ->orderBy('id', 'desc')->get();
        if ($planComment) {
            $planComments = $planComments->where('id', '!=', $planComment->id);
        }
        $planComments = $planComments->filter(function ($item) {
            return !empty($item->content);
        });

        $plans = $trademarkPlan->plans;
        $plans->load([
            'planDetails.planDetailDocs.MTypePlanDocEdit',
            'reason' => function ($query) {
                $query->where('reason_name', '!=', Reason::NO_REASON);
            },
            'planDetails.distinctsIsAdd',
            'planDetails.distinctsIsDistinctSettement',
            'planDetails.isDistinctSettmentEdit',
            'planDetails.planDetailDistincts',
            'planDetails.mTypePlan'
        ]);
        foreach ($plans as &$plan) {
            $reasonIds = [];
            foreach ($plan->reason as $reason) {
                $reasonIds[] = $reason->id;
                $reasonIds[] = $reason->id;
            }
            foreach ($plan->planDetails as &$planDetail) {
                $planDetailDistincts = $planDetail->planDetailDistincts->where('is_add', PlanDetailDistinct::IS_ADD);
                $planDetailDistinctNotAdd = $planDetailDistincts->filter(function ($planDetailDistincts) {
                    $planDetailProducts = $planDetailDistincts->planDetailProducts;

                    $planDetailProductNotAdd = $planDetailProducts->whereIn('leave_status', [
                        PlanDetailProduct::LEAVE_STATUS_7,
                        PlanDetailProduct::LEAVE_STATUS_3,
                    ]);

                    $planDetailProductIsDeleted = $planDetailProducts->where('is_deleted', true);

                    return $planDetailProductIsDeleted->count() > 0 || $planDetailProductNotAdd->count() > 0;
                });
                $planDetailDistinctNotAddIDs = $planDetailDistinctNotAdd->pluck('m_distinction_id')->toArray();

                $planDetail->distinctsIsAdd = $planDetail->distinctsIsAdd
                    ->whereNotIn('id', $planDetailDistinctNotAddIDs)
                    ->unique('id')->flatten();
                $planDetail->distinctsIsDistinctSettement = $planDetail->distinctsIsDistinctSettement
                    ->whereNotIn('id', $planDetailDistinctNotAddIDs)
                    ->unique('id')->flatten();
                $planDetail->isDistinctSettmentEdit = $planDetail->isDistinctSettmentEdit->unique('id')->flatten();
            }
            $plan->reasonIds = $reasonIds;
        }
        $possibilityResolutions = [
            '◎' => PlanDetail::RESOLUTION_1,
            '○' => PlanDetail::RESOLUTION_2,
            '△' => PlanDetail::RESOLUTION_3,
            '×' => PlanDetail::RESOLUTION_4,
        ];

        $textPosibilitiResolution = [
            PlanDetail::RESOLUTION_1 => '◎',
            PlanDetail::RESOLUTION_2 => '○',
            PlanDetail::RESOLUTION_3 => '△',
            PlanDetail::RESOLUTION_4 => '×',
        ];

        $isBlockScreen = false;
        if ($trademarkPlan->is_reject == TrademarkPlan::IS_REJECT_TRUE || $trademarkPlan->is_confirm == TrademarkPlan::IS_CONFIRM_TRUE) {
            $isBlockScreen = true;
        }

        $redirectBack = route('admin.refusal.response-plan.edit.supervisor', [
            'id' => $comparisonTrademarkResult->id,
            'trademark_plan_id' => $trademarkPlan->id,
        ]);
        $mTypePlans = $this->mTypePlanService->getAllTypePlan();
        $mTypePlanDocs = $this->mTypePlanDocService->getAllTypePlanDoc();

        $dataCommon = $this->planService->deadlineAndQuestionToRefuse($id);

        return view('admin.modules.plans.edit-supervisor', compact(
            'comparisonTrademarkResult',
            'id',
            'reasons',
            'trademarkTable',
            'plans',
            'dataCommon',
            'possibilityResolutions',
            'mTypePlans',
            'mTypePlanDocs',
            'trademarkPlan',
            'planComment',
            'planComments',
            'textPosibilitiResolution',
            'redirectBack',
            'isBlockScreen'
        ));
    }

    /**
     * Post Edit Supervisor a203shu
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function postEditSupervisor(Request $request, int $id): RedirectResponse
    {
        $params = $request->all();
        $trademarkPlan = $this->trademarkPlanService->find($params['trademark_plan_id'] ?? 0);
        if (empty($trademarkPlan)) {
            abort(CODE_ERROR_404);
        }

        $comparisonTrademarkResult = $this->comparisonTrademarkResultService->find($id);
        if (empty($comparisonTrademarkResult)) {
            abort(CODE_ERROR_404);
        }
        try {
            DB::beginTransaction();
            $responseDeadline = Carbon::createFromFormat('Y年m月d日', $params['response_deadline'] ?? null)->endOfDay()->toDateString();
            $params['data'] = json_decode($params['plan_reason'][0], true);
            if (isset($params['is_distinct_settlement_edit'])) {
                $params['is_distinct_settlement_edit'] = json_decode($params['is_distinct_settlement_edit'][0], true);
            }

            $trademarkPlan->update([
                'response_deadline' => $responseDeadline ?? null,
            ]);
            $this->planCommentService->updateOrCreate(
                [
                    'trademark_plan_id' => $trademarkPlan->id,
                    'type' => PlanComment::TYPE_1,
                    'type_comment_step' => PlanComment::STEP_4,
                ],
                [
                    'admin_id' => Auth::user()->id,
                    'trademark_id' => $comparisonTrademarkResult->trademark_id,
                    'trademark_plan_id' => $trademarkPlan->id,
                    'target_id' => $trademarkPlan->id,
                    'content' => isset($params['plan_comment']) ? $params['plan_comment'] : '',
                    'type' => PlanComment::TYPE_1,
                    'type_comment_step' => PlanComment::STEP_4,
                ]
            );
            $this->planService->updatePlanA203shuOrA203n($params, $trademarkPlan);
            DB::commit();
            if ($params['submit'] == 'draft' && isset($params['name_page'])) {
                return redirect()->route('admin.refusal.response-plan.edit.supervisor', [
                    'id' => $comparisonTrademarkResult->id,
                    'trademark_plan_id' => $trademarkPlan->id,
                    'modal' => $params['name_page'],
                ])->with('message', __('messages.general.Common_S008'))->withInput();
            } elseif (isset($params['link_redirect'])) {
                return redirect($params['link_redirect']);
            } elseif ($params['submit'] == 'save') {
                return redirect()->route('admin.refusal.response-plan.edit.supervisor', [
                    'id' => $comparisonTrademarkResult->id,
                    'trademark_plan_id' => $trademarkPlan->id,
                ])->with('message', __('messages.precheck.success'))->withInput();
            } elseif ($params['submit'] == 'submit') {
                return redirect()->route('admin.refusal.response-plan.product.edit.supervisor', [
                    'id' => $comparisonTrademarkResult->id,
                    'trademark_plan_id' => $trademarkPlan->id,
                ])->withInput();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            CommonHelper::setMessage($request, MESSAGE_ERROR, __('messages.error'));

            return redirect()->back();
        }
    }

    /**
     * Product Edit Supervisor a203c_shu
     *
     * @param Request $request
     * @param int $id
     * @return View
     */
    public function productEditSupervisor(Request $request, int $id): View
    {
        $comparisonTrademarkResult = $this->comparisonTrademarkResultService->find($id);
        if (empty($comparisonTrademarkResult)) {
            abort(CODE_ERROR_404);
        }
        $comparisonTrademarkResult->load(['trademark', 'planCorrespondence']);
        $planCorrespondence = $comparisonTrademarkResult->planCorrespondence;

        $trademarkPlan = $this->trademarkPlanService->find($request->trademark_plan_id ?? 0);

        if (empty($trademarkPlan)) {
            abort(CODE_ERROR_404);
        }
        if ($trademarkPlan->plan_correspondence_id != $planCorrespondence->id && $trademarkPlan->flag_role == TrademarkPlan::FLAG_ROLE_1) {
            abort(CODE_ERROR_404);
        }

        $trademark = $comparisonTrademarkResult->trademark;
        if (empty($trademark)) {
            abort(CODE_ERROR_404);
        }

        // Get Plan
        $trademarkPlan = $trademarkPlan->load([
            'plans.planDetails.mTypePlan',
            'plans.planDetails.planDetailProducts.mProduct',
            'plans.planDetails.planDetailProducts.planDetailDistinct.mDistinction',
            'plans.planDetails.planDetailProducts.planDetailProductCodes.mCode',
            'plans.planReasons.reason',
        ]);
        $plans = $trademarkPlan->getPlans();
        $products = $trademarkPlan->getProducts()->sortBy('distinction.id');

        $isConfirmA203shu = false;
        foreach ($products as $product) {
            $planDetails = $product['plan_details'] ?? [];
            foreach ($planDetails as $planDetail) {
                if (isset($planDetail['is_confirm']) && $planDetail['is_confirm'] == false
                    || isset($planDetail['is_decision']) && $planDetail['is_decision'] == false) {
                    $isConfirmA203shu = true;
                }
            }
        }

        // Get Plan Comment
        $planComments = $this->planCommentService->findByCondition([
            'trademark_id' => $comparisonTrademarkResult->trademark_id,
            'type' => PlanComment::TYPE_1,
        ])->where('trademark_plan_id', '<=', $trademarkPlan->id ?? '')
            ->whereIn('type_comment_step', [
                PlanComment::STEP_1,
                PlanComment::STEP_2,
                PlanComment::STEP_3,
                PlanComment::STEP_4,
                PlanComment::STEP_5,
            ])
            ->whereHas('trademarkPlan.planCorrespondence.comparisonTrademarkResult', function ($query) use ($comparisonTrademarkResult) {
                $query->where('id', $comparisonTrademarkResult->id);
            })
            ->orderBy('id', 'desc')->get();
        $planComment = $planComments->where('type_comment_step', PlanComment::STEP_4)->last();
        $planComments = $planComments->where('id', '<>', $planComment->id ?? 0)->sortByDesc('created_at');
        $planComments = $planComments->filter(function ($item) {
            return !empty($item->content);
        });

        // Trademark Table
        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_ADMIN_1, $trademark->id, [
            SHOW_LINK_ANKEN_TOP => true,
        ]);

        // Get $mDistinctionService
        $disableDistinctionID = $products->where('plan_detail_distinction.is_add', false)->pluck('plan_detail_distinction.m_distinction_id');
        $allDistinctions = $this->mDistinctionService->findByCondition([])->get();

        $distinctions = $allDistinctions->whereNotIn('id', $disableDistinctionID);
        $allDistinctionData = $allDistinctions->pluck('name', 'id')->toArray();

        // Url Back
        $backUrl = route('admin.refusal.response-plan.edit.supervisor', [
            'id' => $comparisonTrademarkResult->id,
            'trademark_plan_id' => $trademarkPlan->id,
        ]);

        // Common
        $dataCommon = $this->planService->deadlineAndQuestionToRefuse($id);

        $isBlockScreen = false;
        if ($trademarkPlan->is_reject == true || $trademarkPlan->is_confirm == true) {
            $isBlockScreen = true;
        }

        return view('admin.modules.plans.product-edit-supervisor', compact(
            'comparisonTrademarkResult',
            'trademarkPlan',
            'planComment',
            'planComments',
            'trademarkTable',
            'backUrl',
            'plans',
            'products',
            'allDistinctionData',
            'distinctions',
            'dataCommon',
            'isConfirmA203shu',
            'isBlockScreen',
        ));
    }

    /**
     * Post Product Edit Supervisor a203c_shu
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function postProductEditSupervisor(Request $request, int $id): RedirectResponse
    {
        $trademarkPlan = $this->trademarkPlanService->find($request->trademark_plan_id ?? 0);
        if (empty($trademarkPlan)) {
            abort(CODE_ERROR_404);
        }

        $comparisonTrademarkResult = $this->comparisonTrademarkResultService->find($id);
        if (empty($comparisonTrademarkResult)) {
            abort(CODE_ERROR_404);
        }

        $comparisonTrademarkResult->load(['trademark', 'planCorrespondence', 'trademark.user']);
        $planCorrespondence = $comparisonTrademarkResult->planCorrespondence;
        if ($trademarkPlan->plan_correspondence_id != $planCorrespondence->id && $trademarkPlan->flag_role == TrademarkPlan::FLAG_ROLE_1) {
            abort(CODE_ERROR_404);
        }

        $trademark = $comparisonTrademarkResult->trademark;
        if (empty($trademark)) {
            abort(CODE_ERROR_404);
        }

        try {
            DB::beginTransaction();

            $params = $request->all();
            $currentAdmin = Auth::guard('admin')->user();

            $planDetailProductIds = $params['plan_detail_product_ids'] ?? [];

            // Get All Plan Detail Product ID
            $allPlanDetailProductIds = [];
            foreach ($planDetailProductIds as $value) {
                foreach ($value as $v) {
                    $allPlanDetailProductIds[] = $v;
                }
            }
            $allPlanDetailProductIds = array_unique($allPlanDetailProductIds);

            // Update Trademark Plan
            $responseDeadline = Carbon::createFromFormat('Y年m月d日', $params['response_deadline'] ?? null)->endOfDay();
            $trademarkPlan->update([
                'response_deadline' => $responseDeadline ?? null,
                'is_edit_plan' => $params['is_edit_plan'] ?? false,
                'is_decision' => $params['is_decision'] ?? 0,
            ]);

            // Create/Update Plan Comment
            $this->planCommentService->updateOrCreate([
                'trademark_plan_id' => $trademarkPlan->id,
                'type' => PlanComment::TYPE_1,
                'type_comment_step' => PlanComment::STEP_4,
            ], [
                'admin_id' => $currentAdmin->id,
                'content' => $params['content'] ?? '',
                'trademark_id' => $comparisonTrademarkResult->trademark_id ?? null,
                'trademark_plan_id' => $trademarkPlan->id,
                'target_id' => $trademarkPlan->id,
            ]);

            // Update Plan
            $planIds = $params['plan_ids'] ?? [];
            $plans = $params['plans'] ?? [];
            foreach ($planIds as $planID) {
                $planData = $plans[$planID] ?? [];
                if (empty($planData)) {
                    $planData = ['is_confirm' => false];
                }
                $this->planService->updateById($planID, $planData);
            }

            // Delete Plan Detail Product
            $deletePlanDetailProductIds = $request->delete_plan_detail_product_ids ?? '';
            if (!empty($deletePlanDetailProductIds)) {
                $deletePlanDetailProductIds = explode(',', $deletePlanDetailProductIds);
                $deletePlanDetailProductIds = array_unique($deletePlanDetailProductIds);

                foreach ($deletePlanDetailProductIds as $planDetailProductIds) {
                    $planDetailProduct = $this->planDetailProductService->find($planDetailProductIds);

                    if ($planDetailProduct->role_add == PlanDetailProduct::ROLL_ADD_MANAGER) {
                        $planDetailProduct->update([
                            'is_deleted' => true,
                        ]);
                    } else {
                        $planDetailProduct->planDetailProductCodes()->delete();
                        $planDetailProduct->planDetailDistinct()->delete();
                        $planDetailProduct->delete();
                    }
                }
            }

            // Restore Plan Detail Product
            $restorePlanDetailProductIds = $request->restore_plan_detail_product_ids ?? '';
            if (!empty($restorePlanDetailProductIds)) {
                $restorePlanDetailProductIds = explode(',', $restorePlanDetailProductIds);
                $restorePlanDetailProductIds = array_unique($restorePlanDetailProductIds);

                foreach ($restorePlanDetailProductIds as $planDetailProductIds) {
                    $planDetailProduct = $this->planDetailProductService->find($planDetailProductIds);

                    if ($planDetailProduct->role_add == PlanDetailProduct::ROLL_ADD_MANAGER) {
                        $planDetailProduct->update([
                            'is_deleted' => false,
                        ]);
                    }
                }
            }

            // Update Plan Detail Product
            $planDetailProducts = $params['plan_detail_products'] ?? [];
            foreach ($planDetailProducts as $planDetailProductID => $value) {
                $planDetailProduct = $this->planDetailProductService->find($planDetailProductID);

                $value['leave_status_edit'] = $value['leave_status_edit'] ?? null;
                $value['leave_status_edit'] = (int) $value['leave_status_edit'] > 0 ? $value['leave_status_edit'] : null;

                $value['leave_status_decision'] = $value['leave_status_decision'] ?? null;
                $value['leave_status_decision'] = (int) $value['leave_status_decision'] > 0 ? $value['leave_status_decision'] : null;

                $leaveStatusOtherEdit = [];
                if (!empty($value['leave_status_other_edit'])) {
                    foreach ($value['leave_status_other_edit'] as $key => $leaveStatusOtherEditValue) {
                        $leaveStatusOtherEdit[] = [
                            'plan_product_detail_id' => $key,
                            'value' => $leaveStatusOtherEditValue,
                        ];
                    }
                }
                $value['leave_status_other_edit'] = json_encode($leaveStatusOtherEdit);

                $leaveStatusOtherDecision = [];
                if (!empty($value['leave_status_other_decision'])) {
                    foreach ($value['leave_status_other_decision'] as $key => $leaveStatusOtherDecisionValue) {
                        $leaveStatusOtherDecision[] = [
                            'plan_product_detail_id' => $key,
                            'value' => $leaveStatusOtherDecisionValue,
                        ];
                    }
                }
                $value['leave_status_other_decision'] = json_encode($leaveStatusOtherDecision);

                $planDetailProduct->update($value);
            }

            // Update Product
            $updateProducts = $request->update_products ?? [];
            foreach ($updateProducts as $planDetailProductID => $data) {
                $detailProductIds = $planDetailProductIds[$planDetailProductID] ?? [];
                $detailProductIds = array_unique($detailProductIds);

                // Get Code name edit
                $codeNameEditFix = $data['code_name_edit_fix'] ?? null;
                $codeNameEditFix = !empty($codeNameEditFix) ? explode(' ', $codeNameEditFix) : [];
                $codeNameEdit = $data['code_name_edit'] ?? null;
                $codeNameEdit = !empty($codeNameEdit) ? explode(' ', $codeNameEdit) : [];
                $codeNameEdit = array_unique(array_merge($codeNameEditFix, $codeNameEdit));

                // Get Code name Decision
                $codeNameDecision = $data['code_name_decision'] ?? null;
                $codeNameDecision = (!empty($codeNameDecision)) ? explode(' ', $codeNameDecision) : [];
                $codeNameDecision = array_unique($codeNameDecision);

                $allCodeName = array_unique(array_merge($codeNameEdit, $codeNameDecision));
                foreach ($allCodeName as $code) {
                    if (!empty($code)) {
                        $this->mCodeService->createOrUpdateName($code);
                    }
                }

                foreach ($detailProductIds as $detailProductId) {
                    $this->planDetailProductService->updateById($detailProductId, [
                        'm_distinction_id_edit' => $data['m_distinction_id_edit'] ?? null,
                        'm_distinction_id_decision' => $data['m_distinction_id_decision'] ?? null,
                        'product_name_edit' => $data['product_name_edit'] ?? null,
                        'product_name_decision' => $data['product_name_decision'] ?? null,
                        'code_name_edit' => json_encode($codeNameEdit),
                        'code_name_decision' => json_encode($codeNameDecision),
                    ]);
                }
            }

            // Create addition product/distinct
            $products = $params['products'] ?? [];
            $newPlanDetailProduct = collect();
            foreach ($products as $product) {
                $codeNameEdit = array_unique(explode(' ', $product['code_name_edit'] ?? null));
                $codeNameDecision = array_unique(explode(' ', $product['code_name_decision'] ?? null));

                // Create/Update Code
                $productCode = [];
                foreach ($codeNameEdit as $code) {
                    if (!empty($code)) {
                        $productCode[] = $this->mCodeService->createOrUpdateName($code);
                    }
                }

                // Create/Update Product
                $dataCreateProduct = [
                    'm_distinction_id' => $product['m_distinction_id_edit'],
                    'admin_id' => $currentAdmin->id,
                    'name' => $product['product_name_edit'],
                    'type' => MProduct::TYPE_CREATIVE_CLEAN,
                ];
                $dataCreateProduct['products_number'] = $this->mProductService->generateProductCode(
                    $dataCreateProduct['type'],
                    $dataCreateProduct['m_distinction_id'],
                );
                $newProduct = $this->mProductService->create($dataCreateProduct);

                // Sync Product Code
                $newProduct->mCode()->sync(collect($productCode)->pluck('id')->toArray());

                // Create Plan Detail Product
                $planDetails = $product['plan_details'] ?? [];
                foreach ($planDetails as $planDetailID => $value) {
                    // Get old Plan Detail Distinct
                    $isDistinctSettlement = PlanDetailDistinct::IS_DISTINCT_SETTLEMENT_FALSE;
                    $planDetailDistinctID = $product['plan_detail_distinct_id'] ?? null;
                    if (!empty($planDetailDistinctID)) {
                        $planDetailDistinct = $this->planDetailDistinctService->find($planDetailDistinctID);
                        $isDistinctSettlement = $planDetailDistinct->is_distinct_settlement;
                    }

                    $additionDistinct = $this->planDetailDistinctService->create([
                        'plan_detail_id' => $planDetailID,
                        'm_distinction_id' => $product['m_distinction_id_edit'],
                        'is_decision' => PlanDetailDistinct::IS_DECISION_NOT_CHOOSE,
                        'is_add' => $product['is_add'] ?? 0,
                        'is_distinct_settlement' => $isDistinctSettlement,
                    ]);

                    $leaveStatusOtherEdit = [];
                    if (!empty($value['leave_status_other_edit'])) {
                        foreach ($value['leave_status_other_edit'] as $key => $v) {
                            $leaveStatusOtherEdit[] = [
                                'plan_product_detail_id' => $key,
                                'value' => $v,
                            ];
                        }
                    }

                    $leaveStatusOtherDecision = [];
                    if (!empty($value['leave_status_other_decision'])) {
                        foreach ($value['leave_status_other_decision'] as $key => $v) {
                            $leaveStatusOtherDecision[] = [
                                'plan_product_detail_id' => $key,
                                'value' => $v,
                            ];
                        }
                    }

                    $planDetailProductData = [
                        'plan_detail_id' => $planDetailID,
                        'plan_detail_distinct_id' => $additionDistinct->id,
                        'm_distinction_id_edit' => $product['m_distinction_id_edit'] ?? null,
                        'm_distinction_id_decision' => $product['m_distinction_id_decision'] ?? null,
                        'm_product_id' => $newProduct->id,
                        'product_name_edit' => $product['product_name_edit'] ?? null,
                        'product_name_decision' => $product['product_name_decision'] ?? null,
                        'leave_status' => $value['leave_status_edit'] ?? null,
                        'leave_status_edit' => $value['leave_status_edit'] ?? null,
                        'leave_status_decision' => $value['leave_status_decision'] ?? null,
                        'leave_status_other' => json_encode($leaveStatusOtherEdit ?? []),
                        'leave_status_other_edit' => json_encode($leaveStatusOtherEdit ?? []),
                        'leave_status_other_decision' => json_encode($leaveStatusOtherDecision ?? []),
                        'code_name_edit' => json_encode($codeNameEdit ?? []),
                        'code_name_decision' => json_encode($codeNameDecision ?? []),
                        'role_add' => PlanDetailProduct::ROLL_ADD_SUPERVISOR,
                        'is_choice' => true,
                    ];

                    $planDetailProduct = $this->planDetailProductService->create($planDetailProductData);

                    foreach ($productCode as $code) {
                        $this->planDetailProductCodeService->create([
                            'plan_detail_product_id' => $planDetailProduct->id,
                            'm_code_id' => $code->id,
                        ]);
                    }

                    $newPlanDetailProduct->push($planDetailProduct);
                }
            }
            $newPlanDetailProductArrayIDs = $newPlanDetailProduct->pluck('id')->toArray();
            $allPlanDetailProductIds = array_unique(array_merge($allPlanDetailProductIds, $newPlanDetailProductArrayIDs));

            if (!empty($params[SUBMIT]) && $params[SUBMIT] != DRAFT) {
                // Update Trademark Plan
                $trademarkPlan->update([
                    'is_confirm' => true,
                    'is_edit_plan' => false,
                    'is_decision' => 0,
                    'is_cancel' => false,
                ]);

                // Update Plan/PlanDetail/PlanDetailDoc/PlanDetailDistinct
                $plans = $params['plans'] ?? [];
                foreach ($plans as $planID => $planData) {
                    $plan = $this->planService->updateById($planID, [
                        'is_confirm' => false,
                    ]);

                    $plan = $plan->load([
                        'planDetails.planDetailDistincts',
                        'planDetails.planDetailDocs',
                    ]);
                    $planDetails = $plan->planDetails ?? collect([]);
                    foreach ($planDetails as $planDetail) {
                        $this->planDetailService->updateDecision($planDetail);

                        $planDetailDistincts = $planDetail->planDetailDistincts ?? collect([]);
                        foreach ($planDetailDistincts as $planDetailDistinct) {
                            $this->planDetailDistinctService->updateDecision($planDetailDistinct);
                        }

                        $planDetailDocs = $planDetail->planDetailDocs ?? collect([]);
                        foreach ($planDetailDocs as $planDetailDoc) {
                            $this->planDetailDocService->updateDecision($planDetailDoc);
                        }
                    }
                }

                // Update Decision
                foreach ($allPlanDetailProductIds as $planDetailProductId) {
                    $planDetailProduct = $this->planDetailProductService->find($planDetailProductId);

                    $leaveStatusDecision = $planDetailProduct->leave_status_decision ?? null;
                    $leaveStatusOtherDecision = $planDetailProduct->leave_status_other_decision ?? json_encode([]);

                    // Update Distinct
                    $mDistinctionIdDecision = $planDetailProduct->m_distinction_id_decision ?? null;
                    if (!empty($mDistinctionIdDecision)) {
                        $this->planDetailDistinctService->updateById($planDetailProduct->plan_detail_distinct_id, [
                            'm_distinction_id' => $mDistinctionIdDecision,
                        ]);
                    }

                    // Update Product Name
                    $productNameDecision = $planDetailProduct->product_name_decision ?? null;
                    $mProduct = null;
                    if (!empty($productNameDecision)) {
                        $mProduct = $this->mProductService->updateById($planDetailProduct->m_product_id, [
                            'name' => $productNameDecision,
                        ]);
                    }

                    // Sync Plan Detail Code
                    $codeNameDecision = $planDetailProduct->code_name_decision ?? '[]';
                    $codeNameDecision = json_decode($codeNameDecision);

                    $productCode = [];
                    $codeIds = [];
                    foreach ($codeNameDecision as $code) {
                        if (!empty($code)) {
                            $codeData = $this->mCodeService->createOrUpdateName($code);
                            $codeIds[] = $codeData->id;

                            $productCodeData = $this->planDetailProductCodeService->updateOrCreate([
                                'plan_detail_product_id' => $planDetailProduct->id,
                                'm_code_id' => $codeData->id,
                            ], []);
                            $productCode[] = $productCodeData;
                        }
                    }
                    $planDetailProductCodeIds = collect($productCode)->pluck('id');

                    if ($mProduct) {
                        $mProduct->mCode()->sync($codeIds);
                    }

                    // Delete not match product_code
                    $this->planDetailProductCodeService->findByCondition([
                        'plan_detail_product_id' => $planDetailProduct->id,
                    ])->whereNotIn('id', $planDetailProductCodeIds)->delete();

                    // Update Plan Detail Product
                    $planDetailProduct->update([
                        'm_distinction_id_edit' => null,
                        'm_distinction_id_decision' => null,
                        'product_name_edit' => null,
                        'product_name_decision' => null,
                        'leave_status' => $leaveStatusDecision ?? null,
                        'leave_status_edit' => null,
                        'leave_status_decision' => null,
                        'leave_status_other' => $leaveStatusOtherDecision ?? json_encode([]),
                        'leave_status_other_edit' => null,
                        'leave_status_other_decision' => null,
                        'code_name_edit' => null,
                        'code_name_decision' => null,
                    ]);
                }

                $this->noticeService->updateComment(
                    Notice::FLOW_RESPONSE_REASON,
                    $params['content'] ?? '',
                    $comparisonTrademarkResult->trademark_id
                );

                //Send Notice
                $this->noticeProductEditSupervisor($comparisonTrademarkResult, $trademarkPlan);
                // send mail a203c_shu
                $dataMail = [
                    'from_page' => A203C_SHU,
                    'user' => $comparisonTrademarkResult->trademark->user
                ];

                $this->mailTemplateService->sendMailRequest($dataMail, MailTemplate::TYPE_OTHER, MailTemplate::GUARD_TYPE_ADMIN);
                CommonHelper::setMessage($request, MESSAGE_SUCCESS, __('messages.general.Common_E035'));
                $redirect = route('admin.home');
            } else {
                if (!empty($params['name_page'])) {
                    CommonHelper::setMessage($request, MESSAGE_SUCCESS, __('messages.general.Common_S008'));
                    $redirect = route('admin.refusal.response-plan.product.edit.supervisor', [
                        'id' => $comparisonTrademarkResult->id,
                        'trademark_plan_id' => $trademarkPlan->id,
                        'modal' => $params['name_page'],
                    ]);
                } elseif (!empty($params['link_redirect'])) {
                    $redirect = $params['link_redirect'];
                } else {
                    CommonHelper::setMessage($request, MESSAGE_SUCCESS, __('messages.update_success'));
                    $redirect = route('admin.refusal.response-plan.product.edit.supervisor', [
                        'id' => $comparisonTrademarkResult->id,
                        'trademark_plan_id' => $trademarkPlan->id,
                    ]);
                }
            }

            DB::commit();

            return redirect($redirect);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            CommonHelper::setMessage($request, MESSAGE_ERROR, __('messages.error'));
            return redirect()->back();
        }
    }

    /**
     * Notice Product Edit Supervisor a203c_shu
     *
     * @param Model $comparisonTrademarkResult
     * @param Model $trademarkPlan
     * @return void
     */
    public function noticeProductEditSupervisor(Model $comparisonTrademarkResult, Model $trademarkPlan)
    {
        $trademark = $comparisonTrademarkResult->trademark;

        $targetPage = route('admin.refusal.response-plan.product.edit.supervisor', [
            'id' => $comparisonTrademarkResult->id,
            'trademark_plan_id' => $trademarkPlan->id,
        ]);
        $redirectPage = route('user.refusal.response-plan.refusal_response_plan', [
            'comparison_trademark_result_id' => $comparisonTrademarkResult->id,
            'trademark_plan_id' => $trademarkPlan->id,
        ]);

        // Update Notice at no 60
        $stepBeforeNotice = $this->noticeDetailService->findByCondition([
            'completion_date' => null,
        ])->with('notice')->get()
            ->where('type_acc', '<>', NoticeDetail::TYPE_USER)
            ->where('notice.trademark_id', $trademark->id)
            ->where('notice.user_id', $trademark->user_id)
            ->whereIn('notice.flow', [Notice::FLOW_RESPONSE_REASON, Notice::FLOW_RENEWAL_BEFORE_DEADLINE])
            ->filter(function ($item) {
                if ($item->notice->flow == Notice::FLOW_RESPONSE_REASON && in_array($item->notice->step, [Notice::STEP_2, Notice::STEP_3, Notice::STEP_4])) {
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

        // Set response deadline
        $planCorrespondence = $comparisonTrademarkResult->planCorrespondence;

        $responseDeadlineSupervisor = null;
        $responseDeadlineUser = null;
        $machingResult = $comparisonTrademarkResult->machingResult;
        if (!empty($machingResult) && !empty($planCorrespondence)) {
            if ($planCorrespondence->type == PlanCorrespondence::TYPE_2) {
                $responseDeadlineSupervisor = $machingResult->calculateResponseDeadline(-24);
                $responseDeadlineUser = $machingResult->calculateResponseDeadline(-25);
            } else {
                $responseDeadlineSupervisor = $machingResult->calculateResponseDeadline(-18);
                $responseDeadlineUser = $machingResult->calculateResponseDeadline(-21);
            }
        }

        $notice = [
            'flow' => Notice::FLOW_RESPONSE_REASON,
            'step' => Notice::STEP_3,
            'trademark_id' => $trademark->id,
            'user_id' => $trademark->user_id,
            'trademark_info_id' => null,
        ];

        $noticeDetails = [
            // Send Notice Seki
            [
                'target_id' => null,
                'type_acc' => NoticeDetail::TYPE_SUPERVISOR,
                'target_page' => $targetPage,
                'redirect_page' => null,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                'is_action' => false,
                'content' => '拒絶理由通知対応：方針案連絡済',
                'attribute' => 'お客様へ',
                'response_deadline' => $responseDeadlineSupervisor,
                'completion_date' => now(),
            ],
            // Send Notice User
            [
                'target_id' => $trademark->user_id,
                'type_acc' => NoticeDetail::TYPE_USER,
                'target_page' => $targetPage,
                'redirect_page' => $redirectPage,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                'is_action' => true,
                'content' => '拒絶理由通知対応：方針案選択',
                'attribute' => null,
                'response_deadline' => $responseDeadlineUser,
                'response_deadline_ams' => $trademarkPlan->response_deadline ?? null,
            ],
            [
                'target_id' => $trademark->user_id,
                'type_acc' => NoticeDetail::TYPE_USER,
                'target_page' => $targetPage,
                'redirect_page' => $redirectPage,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                'is_action' => false,
                'content' => '拒絶理由通知対応：方針案選択',
                'attribute' => null,
                'response_deadline' => $responseDeadlineUser,
                'response_deadline_ams' => $trademarkPlan->response_deadline ?? null,
            ],
        ];

        $this->noticeService->sendNotice([
            'notices' => $notice,
            'notice_details' => $noticeDetails,
        ]);
    }

    /**
     * Product Re-create Supervisor a203c_n
     *
     * @param Request $request
     * @param int $id
     * @return View
     */
    public function productReCreateSupervisor(Request $request, int $id): View
    {
        $trademarkPlan = $this->trademarkPlanService->find($request->trademark_plan_id ?? 0);
        if (empty($trademarkPlan)) {
            abort(CODE_ERROR_404);
        }
        $comparisonTrademarkResult = $this->comparisonTrademarkResultService->find($id);
        if (empty($comparisonTrademarkResult)) {
            abort(CODE_ERROR_404);
        }

        $comparisonTrademarkResult->load('trademark');

        $trademark = $comparisonTrademarkResult->trademark;
        if (empty($trademark)) {
            abort(CODE_ERROR_404);
        }

        // Get Plan
        $trademarkPlan = $trademarkPlan->load([
            'plans.planDetails.mTypePlan',
            'plans.planDetails.planDetailProducts.mProduct',
            'plans.planDetails.planDetailProducts.planDetailDistinct.mDistinction',
            'plans.planDetails.planDetailProducts.planDetailProductCodes.mCode',
            'plans.planReasons.reason',
        ]);
        $plans = $trademarkPlan->getPlans();
        $products = $trademarkPlan->getProducts()->sortBy('distinction.id');

        // Get Plan Comment
        $planComment = $this->planCommentService->findByCondition([
            'trademark_plan_id' => $trademarkPlan->id,
            'type' => PlanComment::TYPE_1,
            'type_comment_step' => PlanComment::STEP_5,
        ])->first();

        // Trademark Table
        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_ADMIN_1, $trademark->id, [
            SHOW_LINK_ANKEN_TOP => true,
        ]);

        // Get $mDistinctionService
        $disableDistinctionID = $products->where('plan_detail_distinction.is_add', false)->pluck('plan_detail_distinction.m_distinction_id');
        $distinctions = $this->mDistinctionService->findByCondition([])->get()->whereNotIn('id', $disableDistinctionID);

        // Url Back
        $urlBackDefault = route('admin.home');
        $checkUrl = route('admin.refusal.response-plan.product.create', $comparisonTrademarkResult->id);
        $backUrl = CommonHelper::setBackUrl($urlBackDefault, $checkUrl);

        // Common
        $dataCommon = $this->planService->deadlineAndQuestionToRefuse($id);

        $isBlockScreen = false;
        if ($trademarkPlan->is_confirm == true) {
            $isBlockScreen = true;
        }

        return view('admin.modules.plans.product-re-create-supervisor', compact(
            'comparisonTrademarkResult',
            'trademarkPlan',
            'planComment',
            'trademarkTable',
            'backUrl',
            'plans',
            'products',
            'distinctions',
            'dataCommon',
            'isBlockScreen',
        ));
    }

    /**
     * Post Product Re-create Supervisor a203c_n
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function postProductReCreateSupervisor(Request $request, int $id): RedirectResponse
    {
        $trademarkPlan = $this->trademarkPlanService->find($request->trademark_plan_id ?? 0);
        if (empty($trademarkPlan)) {
            abort(CODE_ERROR_404);
        }

        $comparisonTrademarkResult = $this->comparisonTrademarkResultService->find($id);
        if (empty($comparisonTrademarkResult)) {
            abort(CODE_ERROR_404);
        }

        $comparisonTrademarkResult->load(['trademark', 'planCorrespondence']);
        $planCorrespondence = $comparisonTrademarkResult->planCorrespondence;
        if ($trademarkPlan->plan_correspondence_id != $planCorrespondence->id && $trademarkPlan->is_confirm == true) {
            abort(CODE_ERROR_404);
        }

        $trademark = $comparisonTrademarkResult->trademark;
        if (empty($trademark)) {
            abort(CODE_ERROR_404);
        }

        try {
            DB::beginTransaction();

            $params = $request->all();
            $currentAdmin = Auth::guard('admin')->user();

            // Update Trademark Plan
            $responseDeadline = Carbon::createFromFormat('Y年m月d日', $params['response_deadline'] ?? null)->endOfDay();
            $trademarkPlan->update([
                'response_deadline' => $responseDeadline ?? null,
            ]);

            // Create/Update Plan Comment
            $this->planCommentService->updateOrCreate([
                'trademark_plan_id' => $trademarkPlan->id,
                'type' => PlanComment::TYPE_1,
                'type_comment_step' => PlanComment::STEP_5,
            ], [
                'admin_id' => $currentAdmin->id,
                'content' => $params['content'] ?? '',
                'trademark_id' => $comparisonTrademarkResult->trademark_id ?? null,
                'trademark_plan_id' => $trademarkPlan->id,
                'target_id' => $trademarkPlan->id,
            ]);

            // Delete Plan Detail Product
            $deletePlanDetailProductIds = $request->delete_plan_detail_product_ids ?? '';
            if (!empty($deletePlanDetailProductIds)) {
                $deletePlanDetailProductIds = explode(',', $deletePlanDetailProductIds);
                $deletePlanDetailProductIds = array_unique($deletePlanDetailProductIds);

                foreach ($deletePlanDetailProductIds as $planDetailProductIds) {
                    $planDetailProduct = $this->planDetailProductService->find($planDetailProductIds);

                    if ($planDetailProduct->role_add == PlanDetailProduct::ROLL_ADD_MANAGER) {
                        $planDetailProduct->update([
                            'is_deleted' => true,
                        ]);
                    } else {
                        $planDetailProduct->planDetailProductCodes()->delete();
                        $planDetailProduct->planDetailDistinct()->delete();
                        $planDetailProduct->delete();
                    }
                }
            }

            // Restore Plan Detail Product
            $restorePlanDetailProductIds = $request->restore_plan_detail_product_ids ?? '';
            if (!empty($restorePlanDetailProductIds)) {
                $restorePlanDetailProductIds = explode(',', $restorePlanDetailProductIds);
                $restorePlanDetailProductIds = array_unique($restorePlanDetailProductIds);

                foreach ($restorePlanDetailProductIds as $planDetailProductIds) {
                    $planDetailProduct = $this->planDetailProductService->find($planDetailProductIds);

                    if ($planDetailProduct->role_add == PlanDetailProduct::ROLL_ADD_MANAGER) {
                        $planDetailProduct->update([
                            'is_deleted' => false,
                        ]);
                    }
                }
            }

            // Update Plan Detail Product
            $planDetailProducts = $params['plan_detail_products'] ?? [];
            foreach ($planDetailProducts as $planDetailProductID => $value) {
                $planDetailProduct = $this->planDetailProductService->find($planDetailProductID);

                $leaveStatusOther = [];
                if (!empty($value['leave_status_other'])) {
                    foreach ($value['leave_status_other'] as $key => $leaveStatusOtherValue) {
                        $leaveStatusOther[] = [
                            'plan_product_detail_id' => $key,
                            'value' => $leaveStatusOtherValue,
                        ];
                    }
                }
                $value['leave_status_other'] = json_encode($leaveStatusOther);

                $planDetailProduct->update($value);
            }

            // Update Distinct/Product/ProductCode of PlanDetailProduct
            $updateProduct = $params['update_products'] ?? [];
            foreach ($updateProduct as $planDetailProductID => $value) {
                $planDetailProduct = $this->planDetailProductService->find($planDetailProductID);
                $planDetailProductIds = $value['plan_detail_product_ids'] ?? [];

                // Update Distinct
                $mDistinctionId = $value['m_distinction_id'] ?? 0;
                if (!empty($mDistinctionId)) {
                    $planDetailProducts = $this->planDetailProductService->findByCondition([
                        'ids' => $planDetailProductIds,
                    ])->get();

                    $planDetailDistinctIDs = $planDetailProducts->pluck('plan_detail_distinct_id')->toArray();
                    $this->planDetailDistinctService->findByCondition([
                        'ids' => $planDetailDistinctIDs,
                    ])->get()->map(function ($item) use ($mDistinctionId) {
                        $item->update([
                            'm_distinction_id' => $mDistinctionId,
                        ]);
                    });
                }

                // Update Product
                $productName = $value['product_name'] ?? null;
                $mProduct = null;
                if (!empty($productName)) {
                    $mProduct = $this->mProductService->find($planDetailProduct->m_product_id);
                    $mProduct->update([
                        'name' => $productName,
                    ]);
                }

                // Update/Sync Product Code
                $codeFix = explode(' ', $value['product_code_fix'] ?? null);
                $codes = explode(' ', $value['product_code'] ?? null);
                $codes = array_unique(array_merge($codeFix, $codes));

                $productCode = [];
                $codeIds = [];
                foreach ($codes as $code) {
                    if (!empty($code)) {
                        $codeData = $this->mCodeService->createOrUpdateName($code);
                        $codeIds[] = $codeData->id;

                        foreach ($planDetailProductIds as $detailProductId) {
                            $productCodeData = $this->planDetailProductCodeService->updateOrCreate([
                                'plan_detail_product_id' => $detailProductId,
                                'm_code_id' => $codeData->id,
                            ], []);
                            $productCode[] = $productCodeData;
                        }
                    }
                }
                $planDetailProductCodeIds = collect($productCode)->pluck('id');

                if ($mProduct) {
                    $mProduct->mCode()->sync($codeIds);
                }

                // Delete not match product_code
                $this->planDetailProductCodeService->findByCondition([
                    'plan_detail_product_ids' => $planDetailProductIds,
                ])->whereNotIn('id', $planDetailProductCodeIds)->delete();
            }

            // Create addition product/distinct
            $products = $request->products ?? [];
            foreach ($products as $product) {
                $codes = explode(' ', $product['product_code'] ?? null);

                // Create/Update Code
                $productCode = [];
                foreach ($codes as $code) {
                    if (!empty($code)) {
                        $productCode[] = $this->mCodeService->createOrUpdateName($code);
                    }
                }

                // Create/Update Product
                $dataCreateProduct = [
                    'm_distinction_id' => $product['m_distinction_id'],
                    'admin_id' => $currentAdmin->id,
                    'name' => $product['product_name'],
                    'type' => MProduct::TYPE_CREATIVE_CLEAN,
                ];
                $dataCreateProduct['products_number'] = $this->mProductService->generateProductCode(
                    $dataCreateProduct['type'],
                    $dataCreateProduct['m_distinction_id'],
                );
                $newProduct = $this->mProductService->create($dataCreateProduct);

                // Sync Product Code
                $newProduct->mCode()->sync(collect($productCode)->pluck('id')->toArray());

                // Create Plan Detail Product
                $planDetails = $product['plan_details'] ?? [];
                foreach ($planDetails as $planDetailID => $value) {
                    // Get old Plan Detail Distinct
                    $isDistinctSettlement = PlanDetailDistinct::IS_DISTINCT_SETTLEMENT_FALSE;
                    $planDetailDistinctID = $product['plan_detail_distinct_id'] ?? null;
                    if (!empty($planDetailDistinctID)) {
                        $planDetailDistinct = $this->planDetailDistinctService->find($planDetailDistinctID);
                        $isDistinctSettlement = $planDetailDistinct->is_distinct_settlement;
                    }

                    $additionDistinct = $this->planDetailDistinctService->create([
                        'plan_detail_id' => $planDetailID,
                        'm_distinction_id' => $product['m_distinction_id'],
                        'is_decision' => PlanDetailDistinct::IS_DECISION_NOT_CHOOSE,
                        'is_add' => $product['is_add'] ?? 0,
                        'is_distinct_settlement' => $isDistinctSettlement,
                    ]);

                    $leaveStatusOther = [];
                    if (!empty($value['leave_status_other'])) {
                        foreach ($value['leave_status_other'] as $key => $value) {
                            $leaveStatusOther[] = [
                                'plan_product_detail_id' => $key,
                                'value' => $value,
                            ];
                        }
                    }

                    $planDetailProductData = [
                        'plan_detail_id' => $planDetailID,
                        'plan_detail_distinct_id' => $additionDistinct->id,
                        'm_product_id' => $newProduct->id,
                        'leave_status' => $value['leave_status'] ?? null,
                        'leave_status_other' => json_encode($leaveStatusOther ?? []),
                        'role_add' => PlanDetailProduct::ROLL_ADD_SUPERVISOR,
                        'is_choice' => true,
                    ];

                    $planDetailProduct = $this->planDetailProductService->create($planDetailProductData);

                    foreach ($productCode as $code) {
                        $this->planDetailProductCodeService->create([
                            'plan_detail_product_id' => $planDetailProduct->id,
                            'm_code_id' => $code->id,
                        ]);
                    }
                }
            }

            if (!empty($params[SUBMIT]) && $params[SUBMIT] != DRAFT) {
                // Update Trademark Plan
                $trademarkPlan->update([
                    'flag_role' => TrademarkPlan::FLAG_ROLE_2,
                    'is_confirm' => true,
                ]);
                $trademarkPlan = $trademarkPlan->load('plans');

                // Update PlanDetail/PlanDetailDoc/PlanDetailDistinct
                $plans = $trademarkPlan->plans ?? collect([]);
                foreach ($plans as $plan) {
                    $plan = $plan->load([
                        'planDetails.planDetailDistincts',
                        'planDetails.planDetailDocs',
                    ]);
                    $planDetails = $plan->planDetails ?? collect([]);
                    foreach ($planDetails as $planDetail) {
                        $this->planDetailService->updateDecision($planDetail);

                        $planDetailDistincts = $planDetail->planDetailDistincts ?? collect([]);
                        foreach ($planDetailDistincts as $planDetailDistinct) {
                            $this->planDetailDistinctService->updateDecision($planDetailDistinct);
                        }

                        $planDetailDocs = $planDetail->planDetailDocs ?? collect([]);
                        foreach ($planDetailDocs as $planDetailDoc) {
                            $this->planDetailDocService->updateDecision($planDetailDoc);
                        }
                    }
                }

                // Send Notice
                $this->noticeProductReCreateSupervisor($comparisonTrademarkResult, $trademarkPlan);

                CommonHelper::setMessage($request, MESSAGE_SUCCESS, __('messages.general.Common_E035'));
                $redirect = route('admin.home');
            } else {
                if (!empty($params['name_page'])) {
                    CommonHelper::setMessage($request, MESSAGE_SUCCESS, __('messages.general.Common_S008'));
                    $redirect = route('admin.refusal.response-plan.product.edit.supervisor', [
                        'id' => $comparisonTrademarkResult->id,
                        'trademark_plan_id' => $trademarkPlan->id,
                        'modal' => $params['name_page'],
                    ]);
                } elseif (!empty($params['link_redirect'])) {
                    $redirect = $params['link_redirect'];
                } else {
                    CommonHelper::setMessage($request, MESSAGE_SUCCESS, __('messages.update_success'));
                    $redirect = redirect()->back()->getTargetUrl();
                }
            }

            DB::commit();

            return redirect($redirect);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            CommonHelper::setMessage($request, MESSAGE_ERROR, __('messages.error'));
            return redirect()->back();
        }
    }

    /**
     * Notice Product Edit Supervisor a203c_n
     *
     * @param Model $comparisonTrademarkResult
     * @param Model $trademarkPlan
     * @return void
     */
    public function noticeProductReCreateSupervisor(Model $comparisonTrademarkResult, Model $trademarkPlan)
    {
        $trademark = $comparisonTrademarkResult->trademark;

        $targetPage = route('admin.refusal.response-plan.product.re-create.supervisor', [
            'id' => $comparisonTrademarkResult->id,
            'trademark_plan_id' => $trademarkPlan->id,
        ]);
        $redirectPage = route('user.refusal.response-plan.refusal_response_plan_re', [
            'comparison_trademark_result_id' => $comparisonTrademarkResult->id,
            'trademark_plan_id' => $trademarkPlan->id,
        ]);

        // Update Notice at no 70
        $stepBeforeNotice = $this->noticeDetailService->findByCondition([
            'completion_date' => null,
        ])->with('notice')->get()
            ->where('type_acc', '<>', NoticeDetail::TYPE_USER)
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
                'completion_date' => Carbon::now(),
            ]);
        });

        // Set response deadline
        $planCorrespondence = $comparisonTrademarkResult->planCorrespondence;

        $responseDeadlineSupervisor = null;
        $responseDeadlineUser = null;
        $machingResult = $comparisonTrademarkResult->machingResult;
        if (!empty($machingResult) && !empty($planCorrespondence)) {
            if ($planCorrespondence->type == PlanCorrespondence::TYPE_2) {
                $responseDeadlineSupervisor = $machingResult->calculateResponseDeadline(-20);
                $responseDeadlineUser = $machingResult->calculateResponseDeadline(-30);
            } else {
                $responseDeadlineSupervisor = $machingResult->calculateResponseDeadline(-24);
                $responseDeadlineUser = $machingResult->calculateResponseDeadline(-25);
            }
        }

        $notice = [
            'flow' => Notice::FLOW_RESPONSE_REASON,
            'step' => Notice::STEP_3,
            'trademark_id' => $trademark->id,
            'user_id' => $trademark->user_id,
            'trademark_info_id' => null,
        ];

        $noticeDetails = [
            // Send Notice Seki
            [
                'target_id' => null,
                'type_acc' => NoticeDetail::TYPE_SUPERVISOR,
                'target_page' => $targetPage,
                'redirect_page' => null,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                'is_action' => false,
                'content' => '責任者　拒絶理由通知対応：方針案連絡済',
                'attribute' => 'お客様へ',
                'response_deadline' => $responseDeadlineSupervisor,
                'completion_date' => now(),
            ],
            // Send Notice User
            [
                'target_id' => $trademark->user_id,
                'type_acc' => NoticeDetail::TYPE_USER,
                'target_page' => $targetPage,
                'redirect_page' => $redirectPage,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                'is_action' => true,
                'content' => '拒絶理由通知対応：方針案選択',
                'attribute' => null,
                'response_deadline' => $responseDeadlineUser,
                'response_deadline_ams' => $trademarkPlan->response_deadline ?? null,
            ],
            [
                'target_id' => $trademark->user_id,
                'type_acc' => NoticeDetail::TYPE_USER,
                'target_page' => $targetPage,
                'redirect_page' => $redirectPage,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                'is_action' => false,
                'content' => '拒絶理由通知対応：方針案選択',
                'attribute' => null,
                'response_deadline' => $responseDeadlineUser,
                'response_deadline_ams' => $trademarkPlan->response_deadline ?? null,
            ],
        ];

        $this->noticeService->sendNotice([
            'notices' => $notice,
            'notice_details' => $noticeDetails,
        ]);
    }

    /**
     * Show Similar Group Code
     *
     * @param Request $request
     * @return View
     */
    public function showSimilarGroupCode(Request $request, $id): View
    {
        $trademarkPlan = $this->trademarkPlanService->find($id);

        // Get Plan
        $trademarkPlan = $trademarkPlan->load([
            'planCorrespondence.comparisonTrademarkResult',
            'plans.planDetails.mTypePlan',
            'plans.planDetails.planDetailProducts.mProduct',
            'plans.planDetails.planDetailProducts.planDetailDistinct.mDistinction',
            'plans.planDetails.planDetailProducts.planDetailProductCodes.mCode',
            'plans.planReasons.reason',
        ]);
        $comparisonTrademarkResult = $trademarkPlan->planCorrespondence->comparisonTrademarkResult;

        $plans = $trademarkPlan->getPlans();
        $products = $trademarkPlan->getProducts();

        $productTable = [];
        foreach ($products as $product) {
            foreach ($product['codes'] ?? [] as $code) {
                $productTable[$code->name][] = $product;
            }
        }
        $productTable = collect($productTable)->sortKeys();

        $isModal = (int) $request->is_modal ?? 0;

        return view('admin.modules.plans.common.a203c_rui', compact(
            'comparisonTrademarkResult',
            'trademarkPlan',
            'plans',
            'productTable',
            'isModal'
        ));
    }

    /**
     * Showing similar group code edit
     *
     * @param Request $requestF
     * @param int $id - trademark_plan_id
     * @return View
     */
    public function showSimilarGroupCodeEdit(Request $request, $id): View
    {
        $dataSession = null;
        if ($request->has('s') && $request->s) {
            $dataSession = Session::get($request->s);
        }

        if ($request->has('m_code_id') && $request->m_code_id) {
            $trademarkPlan = $this->trademarkPlanService->find($id);

            // Get Plan
            $trademarkPlan = $trademarkPlan->load([
                'planCorrespondence.comparisonTrademarkResult',
                'plans.planDetails.mTypePlan',
                'plans.planDetails.planDetailProducts.mProduct',
                'plans.planDetails.planDetailProducts.planDetailDistinct.mDistinction',
                'plans.planDetails.planDetailProducts.planDetailProductCodes.mCode',
                'plans.planReasons.reason',
            ]);

            $plans = $trademarkPlan->getPlans();
            $products = $trademarkPlan->getProducts();

            if (!empty($dataSession)) {
                $products = $products->map(function ($product) use ($dataSession) {
                    // Set new leave_status data
                    $planDetails = $product['plan_details'];
                    $sessionDataPlanDetailProducts = $dataSession['plan_detail_products'] ?? [];
                    foreach ($planDetails as $planDetail) {
                        $planDetailProduct = $planDetail['plan_detail_product'];

                        foreach ($sessionDataPlanDetailProducts as $key => $data) {
                            if ($planDetailProduct->id == $key) {
                                if (!empty($data['leave_status'])) {
                                    $planDetailProduct->leave_status = $data['leave_status'] ?? null;
                                }

                                if (!empty($data['leave_status_other'])) {
                                    $statusLeaveOther = [];
                                    foreach ($data['leave_status_other'] as $planProductDetailID => $item) {
                                        $statusLeaveOther[] = [
                                            'plan_product_detail_id' => $planProductDetailID,
                                            'value' => $item,
                                        ];
                                    }
                                    $planDetailProduct->leave_status_other = json_encode($statusLeaveOther ?? []);
                                }
                            }
                        }

                        $planDetail['plan_detail_product'] = $planDetailProduct;
                    }

                    // Set new product nane for old data
                    $planDetailProduct = $product['plan_detail_product'] ?? null;
                    $productData = $product['product'] ?? null;
                    $sessionDataProductName = $dataSession['update_products'][$planDetailProduct->id]['product_name'] ?? null;
                    if (!empty($sessionDataProductName)) {
                        $productData->name = $sessionDataProductName;
                    }

                    // Check is delete
                    $product['isDeleted'] = false;
                    $sessionDataPlanDetailDeleteID = explode(',', $dataSession['delete_plan_detail_product_ids']);
                    if (in_array($planDetailProduct->id, $sessionDataPlanDetailDeleteID)) {
                        $product['isDeleted'] = true;
                    }

                    return $product;
                })->filter(function ($product) {
                    return $product['isDeleted'] == false;
                });
            }

            $productTable = [];
            foreach ($products as $product) {
                foreach ($product['codes'] ?? [] as $code) {
                    if ($request->m_code_id == $code->id) {
                        $productTable[$code->name][] = $product;
                    }
                }
            }
            $productTable = collect($productTable)->sortKeys();

            // Get $mDistinctionService
            $disableDistinctionID = $products->where('plan_detail_distinction.is_add', false)->pluck('plan_detail_distinction.m_distinction_id');
            $distinctionsExclude = $this->mDistinctionService->findByCondition([])->get()->whereNotIn('id', $disableDistinctionID);
            $allDistinction = $this->mDistinctionService->findByCondition([])->get();

            $isModal = isset($request->is_modal) ? $request->is_modal : false;

            return view('admin.modules.plans.common.a203c_rui_edit', compact(
                'isModal',
                'plans',
                'distinctionsExclude',
                'allDistinction',
                'trademarkPlan',
                'productTable',
                'dataSession',
            ));
        } else {
            abort(404);
        }
    }

    /**
     * Get data and redirect to A203c_rui_edit02
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function redirectSimilarGroupCodeEditConfirm(Request $request, int $id): RedirectResponse
    {
        $key = Str::random(11);
        $params = $request->all();
        Session::put($key, $params);

        return redirect()->route('admin.refusal.response-plan.product-group-edit.confirm', ['id' => $id, 'm_code_id' => $request->m_code_id, 's' => $key]);
    }

    /**
     * Showing confirm similar group code edit.
     *
     * @param Request $request
     * @param int $id
     * @return View
     */
    public function confirmSimilarGroupCodeEdit(Request $request, int $id): View
    {
        $dataSession = null;
        $secret = null;
        if ($request->has('s') && $request->s && Session::has($request->s)) {
            $dataSession = Session::get($request->s);
            $secret = $request->s;
        }

        $trademarkPlan = $this->trademarkPlanService->find($id);

        // Get Plan
        $trademarkPlan = $trademarkPlan->load([
            'planCorrespondence.comparisonTrademarkResult',
            'plans.planDetails.mTypePlan',
            'plans.planDetails.planDetailProducts.mProduct',
            'plans.planDetails.planDetailProducts.planDetailDistinct.mDistinction',
            'plans.planDetails.planDetailProducts.planDetailProductCodes.mCode',
            'plans.planReasons.reason',
        ]);

        $plans = $trademarkPlan->getPlans();
        $products = $trademarkPlan->getProducts();

        if (!empty($dataSession)) {
            $products = $products->map(function ($product) use ($dataSession) {
                // Set new leave_status data
                $planDetails = $product['plan_details'];
                $sessionDataPlanDetailProducts = $dataSession['plan_detail_products'] ?? [];
                foreach ($planDetails as $planDetail) {
                    $planDetailProduct = $planDetail['plan_detail_product'];

                    foreach ($sessionDataPlanDetailProducts as $key => $data) {
                        if ($planDetailProduct->id == $key) {
                            if (!empty($data['leave_status'])) {
                                $planDetailProduct->leave_status = $data['leave_status'] ?? null;
                            }

                            if (!empty($data['leave_status_other'])) {
                                $statusLeaveOther = [];
                                foreach ($data['leave_status_other'] as $planProductDetailID => $item) {
                                    $statusLeaveOther[] = [
                                        'plan_product_detail_id' => $planProductDetailID,
                                        'value' => $item,
                                    ];
                                }
                                $planDetailProduct->leave_status_other = json_encode($statusLeaveOther ?? []);
                            }
                        }
                    }

                    $planDetail['plan_detail_product'] = $planDetailProduct;
                }

                // Set new product nane for old data
                $planDetailProduct = $product['plan_detail_product'] ?? null;
                $productData = $product['product'] ?? null;
                $sessionDataProductName = $dataSession['update_products'][$planDetailProduct->id]['product_name'] ?? null;
                if (!empty($sessionDataProductName)) {
                    $productData->name = $sessionDataProductName;
                }

                // Check is delete
                $product['isDeleted'] = false;
                $sessionDataPlanDetailDeleteID = explode(',', $dataSession['delete_plan_detail_product_ids']);
                if (in_array($planDetailProduct->id, $sessionDataPlanDetailDeleteID)) {
                    $product['isDeleted'] = true;
                }

                return $product;
            })->filter(function ($product) {
                return $product['isDeleted'] == false;
            });
        }

        $productTable = [];
        foreach ($products as $product) {
            foreach ($product['codes'] ?? [] as $code) {
                $productTable[$code->name][] = $product;
            }
        }

        $distinctions = $this->mDistinctionService->findByCondition([])->get();
        if (!empty($dataSession)) {
            $newProducts = $dataSession['products'] ?? [];

            foreach ($newProducts as $newProduct) {
                $distinction = $distinctions->where('id', $newProduct['m_distinction_id'])->first();
                $newProduct['distinction'] = $distinction;

                $classRow = '';
                switch ($newProduct['role_add'] ?? null) {
                    case ROLE_MANAGER:
                        $classRow = 'bg_yellow';
                        break;
                    case ROLE_SUPERVISOR:
                        $classRow = 'bg_purple2';
                        break;
                }
                $newProduct['classRow'] = $classRow;

                $planDetails = [];
                foreach ($newProduct['plan_details'] as $key => $planDetail) {
                    $planDetailData = $planDetail ?? [];

                    $planDetails[$key] = $planDetail;
                }
                $newProduct['plan_details'] = collect($planDetails)->sortKeys()->toArray();

                $productTable[$newProduct['m_code_name']][] = $newProduct;
            }
        }

        $productTable = collect($productTable)->sortKeys();

        $isModal = isset($request->is_modal) ? $request->is_modal : false;

        return view('admin.modules.plans.common.a203c_rui_edit02', compact(
            'trademarkPlan',
            'productTable',
            'plans',
            'products',
            'secret',
            'isModal',
            'distinctions',
        ));
    }

    /**
     * Update or create product and distinction for plans.
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function updateSimilarGroupCodeEditConfirm(Request $request, int $id): RedirectResponse
    {
        try {
            $user = \Auth::user();
            $trademarkPlan = $this->trademarkPlanService->find($id)->load(['planCorrespondence.comparisonTrademarkResult']);
            $comparisonTrademarkResult = $trademarkPlan->planCorrespondence->comparisonTrademarkResult;

            $dataSession = null;
            if ($request->has('s') && $request->s && Session::has($request->s)) {
                $dataSession = Session::get($request->s);
            }

            $this->planService->saveDistinctProduct($dataSession);

            Session::forget($request->s);

            if ($user->role == ROLE_MANAGER) {
                return redirect()->route('admin.refusal.response-plan.product.create', [
                    'id' => $comparisonTrademarkResult->id,
                    'trademark_plan_id' => $id,
                ]);
            } elseif ($user->role == ROLE_SUPERVISOR) {
                if ($trademarkPlan->is_redirect == false) {
                    $trademarkPlan->update([
                        'is_redirect' => true,
                    ]);

                    // Send notice a203shu
                    $commonNotice = app(CommonNotice::class);
                    $commonNotice->noticeA203StoA203Shu($comparisonTrademarkResult, $trademarkPlan);
                }

                return redirect()->route('admin.refusal.response-plan.product.edit.supervisor', [
                    'id' => $comparisonTrademarkResult->id,
                    'trademark_plan_id' => $id,
                ]);
            }
        } catch (\Exception $e) {
            Log::error($e);

            return redirect()->back()->with('error', __('messages.import_xml.system_error'));
        }
    }

    /**
     * Get Refusal Response Plan Re Supervisor.
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function getRefusalResponsePlanReSupervisor(Request $request, $id)
    {
        $comparisonTrademarkResult = $this->comparisonTrademarkResultService->find($id);
        if (!$comparisonTrademarkResult) {
            abort(CODE_ERROR_404);
        }

        $trademarkPlan = $this->trademarkPlanService->find($request->trademark_plan_id ?? 0);
        if (empty($trademarkPlan)) {
            abort(CODE_ERROR_404);
        }
        $comparisonTrademarkResult->load('planCorrespondences.reasonNos', 'trademark', 'trademark.appTrademark');
        $trademark = $comparisonTrademarkResult->trademark;
        if (!$trademark) {
            abort(CODE_ERROR_404);
        }

        $planCorrespondence = $comparisonTrademarkResult->planCorrespondence;
        if (!$planCorrespondence) {
            abort(CODE_ERROR_404);
        }
        if ($trademarkPlan->plan_correspondence_id != $planCorrespondence->id) {
            abort(CODE_ERROR_404);
        }
        $reasonNo = $planCorrespondence->reasonNos->last();
        if (!$reasonNo) {
            abort(CODE_ERROR_404);
        }
        $reasonNo->load([
            'reasons' => function ($query) {
                $query->where('reason_name', '!=', Reason::NO_REASON);
            },
        ]);
        $reasons = $reasonNo->reasons;
        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_ADMIN_1, $trademark->id);
        $planComment = $this->planCommentService->findByCondition([
            'trademark_id' => $comparisonTrademarkResult->trademark_id,
            'trademark_plan_id' => $trademarkPlan->id,
            'type' => PlanComment::TYPE_1,
            'type_comment_step' => PlanComment::STEP_5,
        ])->first();

        $planComments = $this->planCommentService->findByCondition([
            'trademark_id' => $comparisonTrademarkResult->trademark_id,
            'type' => PlanComment::TYPE_1,
        ])->where('trademark_plan_id', '<=', $trademarkPlan->id ?? '')
            ->whereIn('type_comment_step', [
                PlanComment::STEP_1,
                PlanComment::STEP_2,
                PlanComment::STEP_3,
                PlanComment::STEP_4,
                PlanComment::STEP_5,
            ])
            ->whereHas('trademarkPlan.planCorrespondence.comparisonTrademarkResult', function ($query) use ($comparisonTrademarkResult) {
                $query->where('id', $comparisonTrademarkResult->id);
            })
            ->orderBy('id', 'desc')->get();
        if ($planComment) {
            $planComments = $planComments->where('id', '!=', $planComment->id);
        }
        $planComments = $planComments->filter(function ($item) {
            return !empty($item->content);
        });

        $plans = $trademarkPlan->plans;
        $plans->load([
            'planDetails.planDetailDocs.MTypePlanDocEdit',
            'reason' => function ($query) {
                $query->where('reason_name', '!=', Reason::NO_REASON);
            },
            'planDetails.distinctsIsAdd',
            'planDetails.distinctsIsDistinctSettement',
            'planDetails.isDistinctSettmentEdit',
            'planDetails.planDetailDistincts',
            'planDetails.mTypePlan'
        ]);
        foreach ($plans as &$plan) {
            $reasonIds = [];
            foreach ($plan->reason as $reason) {
                $reasonIds[] = $reason->id;
                $reasonIds[] = $reason->id;
            }
            foreach ($plan->planDetails as &$planDetail) {
                $planDetailDistincts = $planDetail->planDetailDistincts->where('is_add', PlanDetailDistinct::IS_ADD);
                $planDetailDistinctNotAdd = $planDetailDistincts->filter(function ($planDetailDistincts) {
                    $planDetailProducts = $planDetailDistincts->planDetailProducts;

                    $planDetailProductNotAdd = $planDetailProducts->whereIn('leave_status', [
                        PlanDetailProduct::LEAVE_STATUS_7,
                        PlanDetailProduct::LEAVE_STATUS_3,
                    ]);

                    $planDetailProductIsDeleted = $planDetailProducts->where('is_deleted', true);

                    return $planDetailProductIsDeleted->count() > 0 || $planDetailProductNotAdd->count() > 0;
                });
                $planDetailDistinctNotAddIDs = $planDetailDistinctNotAdd->pluck('m_distinction_id')->toArray();

                $planDetail->distinctsIsAdd = $planDetail->distinctsIsAdd
                    ->whereNotIn('id', $planDetailDistinctNotAddIDs)
                    ->unique('id')->flatten();
                $planDetail->distinctsIsDistinctSettement = $planDetail->distinctsIsDistinctSettement
                    ->whereNotIn('id', $planDetailDistinctNotAddIDs)
                    ->unique('id')->flatten();
                $planDetail->isDistinctSettmentEdit = $planDetail->isDistinctSettmentEdit->unique('id')->flatten();
            }
            $plan->reasonIds = $reasonIds;
        }
        $possibilityResolutions = [
            '◎' => PlanDetail::RESOLUTION_1,
            '○' => PlanDetail::RESOLUTION_2,
            '△' => PlanDetail::RESOLUTION_3,
            '×' => PlanDetail::RESOLUTION_4,
        ];

        $textPosibilitiResolution = [
            PlanDetail::RESOLUTION_1 => '◎',
            PlanDetail::RESOLUTION_2 => '○',
            PlanDetail::RESOLUTION_3 => '△',
            PlanDetail::RESOLUTION_4 => '×',
        ];

        $hasDisabled = false;
        if ($trademarkPlan->is_confirm == TrademarkPlan::IS_CONFIRM_TRUE) {
            $hasDisabled = true;
        }

        $mTypePlans = $this->mTypePlanService->getAllTypePlan();
        $mTypePlanDocs = $this->mTypePlanDocService->getAllTypePlanDoc();
        $redirectBack = route('admin.refusal.response-plan-re.supervisor', [
            'id' => $comparisonTrademarkResult->id,
            'trademark_plan_id' => $trademarkPlan->id,
        ]);
        $dataCommon = $this->planService->deadlineAndQuestionToRefuse($id);
        return view('admin.modules.plans.a203n.index', compact(
            'comparisonTrademarkResult',
            'id',
            'reasons',
            'trademarkTable',
            'plans',
            'dataCommon',
            'possibilityResolutions',
            'mTypePlans',
            'mTypePlanDocs',
            'trademarkPlan',
            'planComment',
            'planComments',
            'textPosibilitiResolution',
            'hasDisabled',
            'redirectBack'
        ));
    }

    /**
     * Post Edit Supervisor a203n
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function postRefusalResponsePlanReSupervisor(Request $request, int $id): RedirectResponse
    {
        $params = $request->all();
        $trademarkPlan = $this->trademarkPlanService->find($params['trademark_plan_id'] ?? 0);
        if (empty($trademarkPlan)) {
            abort(CODE_ERROR_404);
        }

        $comparisonTrademarkResult = $this->comparisonTrademarkResultService->find($id);
        if (empty($comparisonTrademarkResult)) {
            abort(CODE_ERROR_404);
        }
        try {
            DB::beginTransaction();
            $responseDeadline = Carbon::createFromFormat('Y年m月d日', $params['response_deadline'] ?? null)->endOfDay()->toDateString();
            $params['data'] = json_decode($params['plan_reason'][0], true);
            if (isset($params['is_distinct_settlement_edit'])) {
                $params['is_distinct_settlement_edit'] = json_decode($params['is_distinct_settlement_edit'][0], true);
            }

            if (isset($params['submit']) && $params['submit'] == 'submit') {
                foreach ($params['plan_id'] as $keyPlan => $planId) {
                    if (!isset($params['is_confirm'])) {
                        return redirect()->back()->with('error', __('messages.general.Common_E053'))->withInput();
                    } else {
                        foreach ($params['plan_detail_id'] as $keyPlanDetail => $planDetailId) {
                            if (!isset($params['is_confirm'][$keyPlan]) && !isset($params['is_confirm'][$keyPlan][$keyPlanDetail])
                                && $params['is_confirm'][$keyPlan][$keyPlanDetail] == 0) {
                                return redirect()->back()->with('error', __('messages.general.Common_E053'))->withInput();
                            }
                            if (isset($params['is_decision'][$keyPlan]) && isset($params['is_decision'][$keyPlan][$keyPlanDetail])
                                && $params['is_decision'][$keyPlan][$keyPlanDetail] == 0) {
                                return redirect()->back()->with('error', __('messages.general.Hoshin_A203_E001'))->withInput();
                            }

                            if (isset($params['is_decision'][$keyPlan]) && isset($params['is_decision'][$keyPlan][$keyPlanDetail])
                                && empty($params['is_decision'][$keyPlan][$keyPlanDetail])) {
                                return redirect()->back()->with('error', __('messages.general.Hoshin_A203_E001'))->withInput();
                            }

                            if (isset($params['type_plan_id_edit'][$keyPlan]) && isset($params['type_plan_id_edit'][$keyPlan][$keyPlanDetail])
                                && $params['type_plan_id_edit'][$keyPlan][$keyPlanDetail] == 0) {
                                return redirect()->back()->with('error', __('messages.general.Common_E001'))->withInput();
                            }


                            if (isset($params['possibility_resolution_edit'][$keyPlan]) && isset($params['possibility_resolution_edit'][$keyPlan][$keyPlanDetail])
                                && $params['possibility_resolution_edit'][$keyPlan][$keyPlanDetail] == 0) {
                                return redirect()->back()->with('error', __('messages.general.Common_E001'))->withInput();
                            }
                        }
                    }
                }
            }
            $trademarkPlan->update([
                'response_deadline' => $responseDeadline ?? null,
            ]);
            $this->planCommentService->updateOrCreate(
                [
                    'trademark_plan_id' => $trademarkPlan->id,
                    'type' => PlanComment::TYPE_1,
                    'type_comment_step' => PlanComment::STEP_5,
                ],
                [
                    'admin_id' => Auth::user()->id,
                    'trademark_id' => $comparisonTrademarkResult->trademark_id,
                    'trademark_plan_id' => $trademarkPlan->id,
                    'target_id' => $trademarkPlan->id,
                    'content' => isset($params['plan_comment']) ? $params['plan_comment'] : '',
                    'type' => PlanComment::TYPE_1,
                    'type_comment_step' => PlanComment::STEP_5,
                ]
            );
            $this->planService->updatePlanA203shuOrA203n($params, $trademarkPlan);
            DB::commit();
            if ($params['submit'] == 'draft' && isset($params['name_page'])) {
                return redirect()->route('admin.refusal.response-plan-re.supervisor', [
                    'id' => $comparisonTrademarkResult->id,
                    'trademark_plan_id' => $trademarkPlan->id,
                    'modal' => $params['name_page'],
                ])->with('message', __('messages.general.Common_S008'))->withInput();
            } elseif (isset($params['link_redirect'])) {
                return redirect($params['link_redirect']);
            } elseif ($params['submit'] == 'save') {
                return redirect()->route('admin.refusal.response-plan-re.supervisor', [
                    'id' => $comparisonTrademarkResult->id,
                    'trademark_plan_id' => $trademarkPlan->id,
                ])->with('message', __('messages.precheck.success'))->withInput();
            } elseif ($params['submit'] == 'submit') {
                return redirect()->route('admin.refusal.response-plan.product.re-create.supervisor', [
                    'id' => $comparisonTrademarkResult->id,
                    'trademark_plan_id' => $trademarkPlan->id,
                ])->withInput();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            CommonHelper::setMessage($request, MESSAGE_ERROR, __('messages.error'));
            return redirect()->back();
        }
    }
}
