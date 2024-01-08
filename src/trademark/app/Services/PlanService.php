<?php

namespace App\Services;

use App\Models\MLawsRegulation;
use Carbon\Carbon;
use App\Models\Admin;
use App\Models\Notice;
use App\Models\NoticeDetail;
use App\Models\Plan;
use App\Models\MProduct;
use App\Models\PlanComment;
use App\Models\PlanDetail;
use App\Models\PlanDetailDoc;
use App\Models\PlanDetailProduct;
use App\Models\TrademarkPlan;
use App\Models\PlanCorrespondence;
use App\Models\TrademarkDocument;
use App\Models\PlanDetailDistinct;
use App\Models\Reason;
use App\Repositories\ComparisonTrademarkResultRepository;
use App\Repositories\PlanCommentRepository;
use App\Repositories\TrademarkPlanRepository;
use App\Repositories\PlanDetailProductRepository;
use App\Repositories\PlanDetailDistinctRepository;
use App\Repositories\MProductRepository;
use App\Repositories\PlanRepository;
use App\Services\Common\NoticeService;
use App\Services\ComparisonTrademarkResultService;
use App\Services\TrademarkDocumentService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class PlanService extends BaseService
{
    private ComparisonTrademarkResultService            $comparisonTrademarkResultService;
    protected TrademarkDocumentService                  $trademarkDocumentService;
    protected PlanCommentRepository                     $planCommentRepository;
    protected MProductRepository                        $mProductRepository;
    protected TrademarkPlanRepository                   $trademarkPlanRepository;
    protected PlanDetailProductRepository               $planDetailProductRepository;
    protected PlanDetailDistinctRepository              $planDetailDistinctRepository;
    protected NoticeService                             $noticeService;
    protected NoticeDetailService                       $noticeDetailService;
    protected ComparisonTrademarkResultRepository       $comparisonTrademarkResultRepository;
    protected PlanReasonService                         $planReasonService;
    protected PlanDetailService                         $planDetailService;
    protected PlanDetailDocService                      $planDetailDocService;
    protected PlanDetailProductService                  $planDetailProductService;
    protected PlanDetailProductCodeService              $planDetailProductCodeService;
    protected PlanDetailDistinctService                 $planDetailDistinctService;
    protected PlanCommentService                        $planCommentService;

    /**
     * Initializing the instances and variables
     *
     * @param PlanRepository $planRepository
     * @param ComparisonTrademarkResultService $comparisonTrademarkResultService
     * @param TrademarkDocumentService $trademarkDocumentService
     * @param PlanDetailService $planDetailService ;
     * @param PlanDetailDocService $planDetailDocService ;
     * @param PlanReasonService $planReasonService ;
     * @param PlanDetailProductService $planDetailProductService ;
     * @param PlanDetailProductCodeService $planDetailProductCodeService ;
     * @param PlanDetailDistinctService $planDetailDistinctService ;
     * @param PlanCommentService $planCommentService ;
     * @param PlanCommentRepository $planCommentRepository
     * @param TrademarkPlanRepository $trademarkPlanRepository
     * @param ComparisonTrademarkResultRepository $comparisonTrademarkResultRepository
     * @param PlanDetailProductRepository $planDetailProductRepository
     * @param PlanDetailDistinctRepository $planDetailDistinctRepository
     * @param MProductRepository $mProductRepository
     * @param NoticeService $noticeService
     * @param NoticeDetailService $noticeDetailService
     */
    public function __construct(
        PlanRepository                      $planRepository,
        ComparisonTrademarkResultService    $comparisonTrademarkResultService,
        TrademarkDocumentService            $trademarkDocumentService,
        PlanCommentRepository               $planCommentRepository,
        TrademarkPlanRepository             $trademarkPlanRepository,
        NoticeService                       $noticeService,
        NoticeDetailService                 $noticeDetailService,
        ComparisonTrademarkResultRepository $comparisonTrademarkResultRepository,
        PlanDetailProductRepository         $planDetailProductRepository,
        PlanDetailDistinctRepository        $planDetailDistinctRepository,
        MProductRepository                  $mProductRepository,
        PlanReasonService                   $planReasonService,
        PlanDetailService                   $planDetailService,
        PlanDetailDocService                $planDetailDocService,
        PlanDetailProductService            $planDetailProductService,
        PlanDetailProductCodeService        $planDetailProductCodeService,
        PlanDetailDistinctService           $planDetailDistinctService,
        PlanCommentService                  $planCommentService
    )
    {
        $this->repository = $planRepository;
        $this->comparisonTrademarkResultService = $comparisonTrademarkResultService;
        $this->trademarkDocumentService = $trademarkDocumentService;
        $this->planCommentRepository = $planCommentRepository;
        $this->mProductRepository = $mProductRepository;
        $this->trademarkPlanRepository = $trademarkPlanRepository;
        $this->planDetailProductRepository = $planDetailProductRepository;
        $this->planDetailDistinctRepository = $planDetailDistinctRepository;
        $this->noticeService = $noticeService;
        $this->noticeDetailService = $noticeDetailService;
        $this->comparisonTrademarkResultRepository = $comparisonTrademarkResultRepository;
        $this->planReasonService = $planReasonService;
        $this->planDetailService = $planDetailService;
        $this->planDetailDocService = $planDetailDocService;
        $this->planDetailProductService = $planDetailProductService;
        $this->planDetailProductCodeService = $planDetailProductCodeService;
        $this->planDetailDistinctService = $planDetailDistinctService;
        $this->planCommentService = $planCommentService;
    }

    /**
     * Get data product plan supervisor
     *
     * @param array $trademarkPlanDetailIds
     * @return Collection
     */
    public function getDataProductPlanSupervisor(array $trademarkPlanDetailIds): Collection
    {
        return $this->repository->getDataProductPlanSupervisor($trademarkPlanDetailIds);
    }

    /**
     * Post data product plan supervisor - a203s
     *
     * @param array $inputs
     *
     * @return boolean
     */
    public function postRefusalResponsePlaneSupervisor(array $inputs)
    {
        try {
            $comparisonTrademarkResult = $this->comparisonTrademarkResultRepository->find($inputs['comparison_trademark_result_id']);
            $userResponseDeadline = \Illuminate\Support\Carbon::createFromFormat('Y年m月d日', $inputs['response_deadline'])->format('Y-m-d');

            //update trademark_plans: is_confirm = 1
            $trademarkPlan = $this->trademarkPlanRepository->find($inputs['trademark_plan_id']);
            $trademarkPlan->update([
                'is_confirm' => TrademarkPlan::IS_CONFIRM_TRUE,
                'response_deadline' => $userResponseDeadline,
            ]);

            //update plan_comments
            $this->planCommentRepository->updateOrCreate([
                'trademark_plan_id' => $inputs['trademark_plan_id'],
                'type' => PlanComment::TYPE_INTERNAL,
                'type_comment_step' => PlanComment::STEP_2,
            ], [
                'admin_id' => auth('admin')->user()->id,
                'trademark_id' => $inputs['trademark_id'],
                'trademark_plan_id' => $inputs['trademark_plan_id'],
                'target_id' => $inputs['trademark_plan_id'],
                'type' => PlanComment::TYPE_INTERNAL,
                'type_comment_step' => PlanComment::STEP_2,
                'content' => $inputs['content'],
            ]);
            $routerA203s = route('admin.refusal.response-plan.supervisor', [
                'id' => $inputs['comparison_trademark_result_id'],
                'trademark_plan_id' => $inputs['trademark_plan_id'],
            ]);
            $routeU203 = route('user.refusal.response-plan.refusal_response_plan', [
                'comparison_trademark_result_id' => $inputs['comparison_trademark_result_id'],
                'trademark_plan_id' => $inputs['trademark_plan_id'],
            ]);

            // Set response deadline
            $responseDeadlineA000AnkenTop = null;
            $responseDeadlineU000Top = null;
            $responseDeadlineU000AnkenTop = null;
            $responseDeadlineA000Top = null;

            $planCorrespondence = $comparisonTrademarkResult->planCorrespondence;
            $machingResult = $comparisonTrademarkResult->machingResult;

            $responseDeadlineU000AnkenTop = $machingResult->calculateResponseDeadline(-18);
            $responseDeadlineU000Top = $machingResult->calculateResponseDeadline(-21);
            $responseDeadlineU000AkenTop = $machingResult->calculateResponseDeadline(-21);
            if (!empty($machingResult) && $planCorrespondence->type == PlanCorrespondence::TYPE_2) {
                $responseDeadlineU000AnkenTop = $machingResult->calculateResponseDeadline(-24);
                $responseDeadlineU000Top = $machingResult->calculateResponseDeadline(-25);
                $responseDeadlineU000AkenTop = $machingResult->calculateResponseDeadline(-25);
            }

            //send notices

            // Update Notice at u202 (No 60: F G)
            //u202
            $this->noticeService->updateComment(
                Notice::FLOW_RESPONSE_REASON,
                $inputs['content'] ?? '',
                $comparisonTrademarkResult->trademark_id
            );

            $stepBeforeNotice = $this->noticeDetailService->findByCondition([
                'completion_date' => null,
            ])->with('notice')->get()
                ->where('type_acc', '<>', NoticeDetail::TYPE_USER)
                ->where('notice.trademark_id', $inputs['trademark_id'])
                ->where('notice.user_id', $inputs['user_id'])
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

            $this->noticeService->sendNotice([
                'notices' => [
                    'trademark_id' => $inputs['trademark_id'],
                    'trademark_info_id' => null,
                    'user_id' => $inputs['user_id'],
                    'flow' => Notice::FLOW_RESPONSE_REASON,
                    'step' => Notice::STEP_3,
                    'created_at' => Carbon::now()
                ],
                'notice_details' => [
                    // Send Notice Seki: A-000anken_top
                    [
                        'target_id' => Admin::getAdminIdOfSeki(),
                        'type_acc' => NoticeDetail::TYPE_SUPERVISOR,
                        'target_page' => $routerA203s,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                        'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                        'content' => '責任者　拒絶理由通知対応：方針案連絡済',
                        'attribute' => 'お客様へ',
                        'response_deadline' => $responseDeadlineU000AnkenTop,
                        'created_at' => Carbon::now(),
                        'completion_date' => now(),
                    ],
                    //Send Notice to user: U-000top
                    [
                        'target_id' => $inputs['user_id'],
                        'type_acc' => NoticeDetail::TYPE_USER,
                        'target_page' => $routerA203s,
                        'redirect_page' => $routeU203,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                        'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                        'is_action' => NoticeDetail::IS_ACTION_TRUE,
                        'content' => '拒絶理由通知対応：方針案選択',
                        'response_deadline' => $responseDeadlineU000Top,
                        'created_at' => Carbon::now(),
                        'response_deadline_ams' => $trademarkPlan->response_deadline ?? null,
                    ],
                    //Send Notice to user: U-000anken_top
                    [
                        'target_id' => $inputs['user_id'],
                        'type_acc' => NoticeDetail::TYPE_USER,
                        'target_page' => $routerA203s,
                        'redirect_page' => $routeU203,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                        'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                        'content' => '拒絶理由通知対応：方針案選択',
                        'response_deadline' => $responseDeadlineU000AkenTop,
                        'created_at' => Carbon::now(),
                        'response_deadline_ams' => $trademarkPlan->response_deadline ?? null,
                    ],
                ],
            ]);

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
     * Post Refusal Response Plane Supervisor Reject - a203sashi
     *
     * @param array $inputs
     * @return void
     */
    public function postRefusalResponsePlaneSupervisorReject(array $inputs)
    {
        try {
            DB::beginTransaction();

            $comparisonTrademarkResult = $this->comparisonTrademarkResultRepository->find($inputs['comparison_trademark_result_id']);

            //update trademark_plans: is_reject = 1
            $responseDeadline = Carbon::createFromFormat('Y年m月d日', $inputs['response_deadline'] ?? null)->endOfDay();

            $trademarkPlan = $this->trademarkPlanRepository->find($inputs['trademark_plan_id']);
            $trademarkPlan->update([
                'is_reject' => TrademarkPlan::IS_REJECT_TRUE,
                'response_deadline' => $responseDeadline ?? null,
            ]);

            //update plan_comments
            $this->planCommentRepository->updateOrCreate([
                'trademark_plan_id' => $inputs['trademark_plan_id'],
                'type' => PlanComment::TYPE_INTERNAL,
                'type_comment_step' => PlanComment::STEP_3,
            ], [
                'admin_id' => auth('admin')->user()->id,
                'trademark_id' => $inputs['trademark_id'],
                'trademark_plan_id' => $inputs['trademark_plan_id'],
                'target_id' => $inputs['trademark_plan_id'],
                'type' => PlanComment::TYPE_INTERNAL,
                'type_comment_step' => PlanComment::STEP_3,
                'content' => $inputs['content'],
            ]);

            $trademarkPlan->duplicateA203Group();

            // Set response deadline
            $planCorrespondence = $comparisonTrademarkResult->planCorrespondence;
            $machingResult = $comparisonTrademarkResult->machingResult;

            $responseDeadlineA000Top = $machingResult->calculateResponseDeadline(-18);
            $responseDeadlineA000AnkenTop = $machingResult->calculateResponseDeadline(-18);
            if (!empty($machingResult) && $planCorrespondence->type == PlanCorrespondence::TYPE_2) {
                $responseDeadlineA000Top = $machingResult->calculateResponseDeadline(-24);
                $responseDeadlineA000AnkenTop = $machingResult->calculateResponseDeadline(-24);
            }

            $this->noticeService->updateComment(
                Notice::FLOW_RESPONSE_REASON,
                $inputs['content'] ?? '',
                $comparisonTrademarkResult->trademark_id
            );

            // Update Notice at u202 (No 60: F G)
            $stepBeforeNotice = $this->noticeDetailService->findByCondition([
                'completion_date' => null,
            ])->with('notice')->get()
                ->where('type_acc', '<>', NoticeDetail::TYPE_USER)
                ->where('notice.trademark_id', $inputs['trademark_id'])
                ->where('notice.user_id', $inputs['user_id'])
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

            //send notices
            $routerA203sashi = route('admin.refusal.response-plan.supervisor-reject', [
                'id' => $inputs['comparison_trademark_result_id'],
                'trademark_plan_id' => $inputs['trademark_plan_id'],
            ]);
            $routerA203 = route('admin.refusal.response-plan.index', $inputs['comparison_trademark_result_id']);
            $this->noticeService->sendNotice([
                'notices' => [
                    'trademark_id' => $inputs['trademark_id'],
                    'trademark_info_id' => null,
                    'user_id' => $inputs['user_id'],
                    'flow' => Notice::FLOW_RESPONSE_REASON,
                    'step' => Notice::STEP_3,
                    'created_at' => Carbon::now()
                ],
                'notice_details' => [
                    // Send Notice Seki: A-000top
                    [
                        'target_id' => Admin::getAdminIdByRole(Admin::ROLE_ADMIN_TANTO),
                        'type_acc' => NoticeDetail::TYPE_MANAGER,
                        'target_page' => $routerA203sashi,
                        'redirect_page' => $routerA203,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                        'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                        'is_action' => NoticeDetail::IS_ACTION_TRUE,
                        'content' => '拒絶理由通知対応：方針案作成(差し戻し)',
                        'response_deadline' => $responseDeadlineA000Top,
                        'created_at' => Carbon::now()
                    ],
                    // Send Notice Seki: A-000anken_top
                    [
                        'target_id' => Admin::getAdminIdByRole(Admin::ROLE_ADMIN_TANTO),
                        'type_acc' => NoticeDetail::TYPE_MANAGER,
                        'target_page' => $routerA203sashi,
                        'redirect_page' => $routerA203,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                        'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                        'content' => '担当者　拒絶理由通知対応：方針案作成(差し戻し)',
                        'response_deadline' => $responseDeadlineA000AnkenTop,
                        'created_at' => Carbon::now()
                    ],
                ],
            ]);

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
     * Get data of a203
     *
     * @param int $trademarkPlanId
     */
    public function getData($trademarkPlanId)
    {
        return $this->repository->getData($trademarkPlanId);
    }

    /**
     * Deadline And Question To Refuse
     *
     * @param int $id
     * @param string $fromPage
     */
    public function deadlineAndQuestionToRefuse($id, $fromPage = null)
    {
        $comparisonTrademarkResult = $this->comparisonTrademarkResultService->find($id);
        if (!$comparisonTrademarkResult) {
            abort(404);
        }

        $relation = $comparisonTrademarkResult->load([
            'trademark',
            'planCorrespondence.reasonNos.reasons.mLawsRegulation',
            'planCorrespondence.trademarkPlans.docSubmissions',
        ]);

        if (!$relation->planCorrespondence) {
            abort(404);
        }
        $trademark = $relation->trademark;
        $planCorrespondence = $relation->planCorrespondence;
        $reasonNo = $planCorrespondence->reasonNos->last() ?? null;
        if ($reasonNo) {
            $reasonNo->load([
            'reasons' => function ($query) {
                $query->where('reason_name', '!=', Reason::NO_REASON);
            },
            ]);

            $reasons = $reasonNo->reasons ?? [];
        } else {
            $reasons = [];
        }

        // Get response deadline
        $trademarkPlanId = request()->trademark_plan_id;
        if (!empty($trademarkPlanId)) {
            $trademarkPlans = $planCorrespondence->trademarkPlans->where('id', $trademarkPlanId)->first();
        } else {
            $trademarkPlans = $planCorrespondence->trademarkPlans->last();
        }

        $responseDeadline = null;
        switch ($fromPage) {
            case A203:
                $responseDeadline = Carbon::now()->addDay(13);
                break;
            default:
                $responseDeadline = Carbon::parse($comparisonTrademarkResult->response_deadline)->addDay(-21);
                if ($planCorrespondence->type == PlanCorrespondence::TYPE_3) {
                    $responseDeadline = Carbon::parse($comparisonTrademarkResult->response_deadline)->addDay(-25);
                }
        }
        if ($trademarkPlans && !empty($trademarkPlans->response_deadline)) {
            $responseDeadline = Carbon::parse($trademarkPlans->response_deadline)->endOfDay();
        }

        $trademarkDocuments = $this->trademarkDocumentService->getByCondition([
            'trademark_id' => $trademark->id,
            'type' => TrademarkDocument::TYPE_1,
            'flow' => Notice::FLOW_RESPONSE_REASON,
            'step' => Notice::STEP_1,
        ])->pluck('url');

        $comparisonTrademarkResults = $this->comparisonTrademarkResultService->findByCondition(['trademark_id' => $trademark->id]);
        $countComparisonTrademarkResults = $comparisonTrademarkResults->count();
        $comparisonTrademarkResultMinusOne = $comparisonTrademarkResults->where('id', '<', $comparisonTrademarkResult->id)->orderBy('id', 'DESC')->first();

        $btnReasonForRefusalN = false;
        $routeA205kakunin = null;
        if ($comparisonTrademarkResultMinusOne != null) {
            $comparisonTrademarkResultMinusOne->load('planCorrespondence.trademarkPlans');

            $btnReasonForRefusalN = true;
            $docSubmissions = $trademarkPlans ? $trademarkPlans->docSubmissions->last() : null;

            $routeA205kakunin = route('admin.refusal.documents.confirm', [
                'id' => $comparisonTrademarkResultMinusOne->id ?? 0,
                'trademark_plan_id' => $comparisonTrademarkResultMinusOne->planCorrespondence->trademarkPlans->last() ?? null,
                'doc_submission_id' => $docSubmissions->id ?? null,
            ]);
        }

        $isSeki = false;
        $routeA203OrA203shu = route('admin.refusal.response-plan.index', ['id' => $id]);
        $routeA203cOrA203c_shu = route('admin.refusal.response-plan.product.create', ['id' => $id, 'trademark_plan_id' => $trademarkPlans->id ?? null]);
        if (auth()->guard(ADMIN_ROLE)->user()->role == ROLE_SUPERVISOR) {
            $isSeki = true;
            $routeA203OrA203shu = route('admin.refusal.response-plan.edit.supervisor', ['id' => $id, 'trademark_plan_id' => $trademarkPlans->id ?? null]);
            $routeA203cOrA203c_shu = route('admin.refusal.response-plan.product.edit.supervisor', ['id' => $id, 'trademark_plan_id' => $trademarkPlans->id ?? null]);
        }

        return [
            'isSeki' => $isSeki,
            'reasons' => $reasons,
            'trademarkPlans' => $trademarkPlans,
            'routeA205kakunin' => $routeA205kakunin,
            'responseDeadline' => $responseDeadline,
            'routeA203OrA203shu' => $routeA203OrA203shu,
            'trademarkDocuments' => $trademarkDocuments,
            'btnReasonForRefusalN' => $btnReasonForRefusalN,
            'routeA203cOrA203c_shu' => $routeA203cOrA203c_shu,
            'comparisonTrademarkResult' => $comparisonTrademarkResult,
            'comparisonTrademarkResultMinusOne' => $comparisonTrademarkResultMinusOne,
            'mLawRegulationContentDefault' => MLawsRegulation::ARRAY_CONTENT_DEFAULT
        ];
    }

    /**
     * Update or Create new data Plan.
     *
     * @param array $params
     * @param object $trademarkPlan
     */
    public function updateOrCreatePlan(array $params, object $trademarkPlan)
    {
        if (isset($params['data']) && count($params['data']) > 0) {
            $this->planReasonService->findByCondition(['plan_id', $params['plan_id']])->delete();
        }
        foreach ($params['plan_id'] as $keyPlan => $planId) {
            if ($params['submit'] == 'draft' || $params['submit'] == 'save' || $params['submit'] == 'submit') {
                if (isset($params['plan_detail_id'][$keyPlan])) {
                    $planDetailDocs = PlanDetailDoc::whereIn('plan_detail_id', $params['plan_detail_id'][$keyPlan])->delete();
                }
            }
            $plan = $this->repository->updateOrCreate([
                'id' => $planId,
            ], [
                'admin_id' => Auth::user()->id,
                'trademark_plan_id' => $trademarkPlan->id,
                'plan_no' => 1,
                'is_completed' => Plan::IS_COMPLETED_FALSE
            ]);
            if (isset($params['data'][$keyPlan])) {
                foreach ($params['data'][$keyPlan] as $reasonId) {
                    $planReason = $this->planReasonService->create(
                        [
                            'plan_id' => $plan->id,
                            'reason_id' => $reasonId,
                        ]
                    );
                }
            }
            if ($params['submit'] == 'draft' || $params['submit'] == 'save' || $params['submit'] == 'submit') {
                foreach ($params['plan_detail_id'][$keyPlan] as $keyTypePlan => $typePlanId) {
                    $dataUpdate = [];
                    if (isset($params['type_plan_id'])
                        && isset($params['type_plan_id'][$keyPlan])
                        && isset($params['type_plan_id'][$keyPlan][$keyTypePlan])
                    ) {
                        $dataUpdate['type_plan_id'] = $params['type_plan_id'][$keyPlan][$keyTypePlan];
                    }

                    if (isset($params['plan_description'])
                        && isset($params['plan_description'][$keyPlan])
                        && isset($params['plan_description'][$keyPlan][$keyTypePlan])
                    ) {
                        $dataUpdate['plan_description'] = $params['plan_description'][$keyPlan][$keyTypePlan];
                    }

                    if (isset($params['plan_content'])
                        && isset($params['plan_content'][$keyPlan])
                        && isset($params['plan_content'][$keyPlan][$keyTypePlan])
                    ) {
                        $dataUpdate['plan_content'] = $params['plan_content'][$keyPlan][$keyTypePlan];
                    }

                    if ((isset($params['possibility_resolution'])
                        && isset($params['possibility_resolution'][$keyPlan])
                        && isset($params['possibility_resolution'][$keyPlan][$keyTypePlan]))
                    ) {
                        $dataUpdate['possibility_resolution'] = $params['possibility_resolution'][$keyPlan][$keyTypePlan];
                    }

                    if ((isset($params['is_confirm'])
                        && isset($params['is_confirm'][$keyPlan])
                        && isset($params['is_confirm'][$keyPlan][$keyTypePlan])
                        && $params['is_confirm'][$keyPlan][$keyTypePlan] == 1)
                    ) {
                        $dataUpdate['is_confirm'] = 1;
                    }

                    $planDetail = $this->planDetailService->updateOrCreate(
                        [
                            'id' => (int) $params['plan_detail_id'][$keyPlan][$keyTypePlan],
                        ],
                        [
                            'admin_id' => Auth::user()->id,
                            'plan_id' => $plan->id,
                            'type_plan_id' => $dataUpdate['type_plan_id'] ?? null,
                            'plan_description' => $dataUpdate['plan_description'] ?? null,
                            'plan_content' => $dataUpdate['plan_content'] ?? null,
                            'possibility_resolution' => $dataUpdate['possibility_resolution'] ?? null,
                            'is_confirm' => $dataUpdate['is_confirm'] ?? 0,
                            'is_choice' => PlanDetail::IS_NOT_CHOICE,
                            'is_choice_past' => PlanDetail::IS_CHOICE_PAST_FALSE
                        ]
                    );

                    if (isset($params['plan_detail_doc_id'][$keyPlan][$keyTypePlan])) {
                        foreach ($params['plan_detail_doc_id'][$keyPlan][$keyTypePlan] as $keyTypeDoc => $planDetailDocId) {
                            $planDetailDoc = $this->planDetailDocService->updateOrCreate(
                                [
                                    'id' => $planDetailDocId,
                                ],
                                [
                                    'plan_detail_id' => $planDetail->id,
                                    'm_type_plan_doc_id' => (isset($params['type_plan_doc_id'])
                                        && isset($params['type_plan_doc_id'][$keyPlan])
                                        && isset($params['type_plan_doc_id'][$keyPlan][$keyTypePlan])
                                        && isset($params['type_plan_doc_id'][$keyPlan][$keyTypePlan][$keyTypeDoc]))
                                        ? $params['type_plan_doc_id'][$keyPlan][$keyTypePlan][$keyTypeDoc] : null,
                                    'doc_requirement_des' => (isset($params['doc_requirement_des'])
                                        && isset($params['doc_requirement_des'][$keyPlan])
                                        && isset($params['doc_requirement_des'][$keyPlan][$keyTypePlan])
                                        && isset($params['doc_requirement_des'][$keyPlan][$keyTypePlan][$keyTypeDoc]))
                                        ? $params['doc_requirement_des'][$keyPlan][$keyTypePlan][$keyTypeDoc] : null,
                                    'm_type_plan_doc_id_edit' => (isset($params['type_plan_doc_id_edit'])
                                        && isset($params['type_plan_doc_id_edit'][$keyPlan])
                                        && isset($params['type_plan_doc_id_edit'][$keyPlan][$keyTypePlan])
                                        && isset($params['type_plan_doc_id_edit'][$keyPlan][$keyTypePlan][$keyTypeDoc]))
                                        ? $params['type_plan_doc_id_edit'][$keyPlan][$keyTypePlan][$keyTypeDoc] : null,
                                    'doc_requirement_des_edit' => (isset($params['doc_requirement_des_edit']) &&
                                        isset($params['doc_requirement_des_edit'][$keyPlan]) &&
                                        isset($params['doc_requirement_des_edit'][$keyPlan][$keyTypePlan]) &&
                                        isset($params['doc_requirement_des_edit'][$keyPlan][$keyTypePlan][$keyTypeDoc]))
                                        ? $params['doc_requirement_des_edit'][$keyPlan][$keyTypePlan][$keyTypeDoc] : null,
                                    'is_completed' => PlanDetailDoc::IS_COMPLETED_FALSE,
                                ]
                            );
                        }
                    }

                    unset($params['products'], $params['codes']);
                    $trademarkPlan = $trademarkPlan->load([
                        'plans.planDetails.planDetailProducts.mProduct',
                        'plans.planDetails.planDetailProducts.planDetailDistinct.mDistinction',
                        'plans.planDetails.planDetailProducts.planDetailProductCodes.mCode',
                    ]);
                    $products = $trademarkPlan->getProducts();
                    foreach ($products as $key => $value) {
                        $params['products'][] = $value['plan_detail_product'];
                        $params['codes'][] = $value['codes'];
                    }
                    $products->distincts = $trademarkPlan->getProducts()->groupBy('distinction.id');
                    foreach ($products->distincts as $keyDistinct => $distinct) {
                        $params['distincts'][] = $keyDistinct;
                    }

                    $checkExistProd = $this->planDetailProductService->findByCondition([
                        'plan_detail_id' => $planDetail->id,
                    ])->get();

                    if (!isset($params['type_create'][$keyPlan][$keyTypePlan]) && $checkExistProd->count() > 0) {
                        if (isset($params['products']) && count($params['products']) > 0) {
                            foreach ($params['products'] as $keyProduct => $product) {
                                // Update planDetailDistinctService
                                $this->planDetailDistinctService->findByCondition([
                                    'plan_detail_id' => $planDetail->id,
                                    'm_distinction_id' => $product->planDetailDistinct->m_distinction_id,
                                ])->update([
                                    'is_distinct_settlement' => (isset($params['distinct_settement'])
                                        && isset($params['distinct_settement'][$keyPlan])
                                        && isset($params['distinct_settement'][$keyPlan][$keyTypePlan])
                                        && in_array($product->planDetailDistinct->m_distinction_id, $params['distinct_settement'][$keyPlan][$keyTypePlan]))
                                        ? PlanDetailDistinct::IS_DISTINCT_SETTLEMENT_TRUE : PlanDetailDistinct::IS_DISTINCT_SETTLEMENT_FALSE,
                                    'is_leave_all' => (isset($params['is_leave_all'])
                                        && isset($params['is_leave_all'][$keyPlan])
                                        && isset($params['is_leave_all'][$keyPlan][$keyTypePlan])
                                        && (int) $params['is_leave_all'][$keyPlan][$keyTypePlan] == 1) ? 1 : 0,
                                    'is_decision' => PlanDetailDistinct::IS_DECISION_NOT_CHOOSE,
                                ]);

                                if (isset($params['page']) && $params['page'] == 'a203shu') {
                                    // Update planDetailDistinctService
                                    $this->planDetailDistinctService->findByCondition([
                                        'plan_detail_id' => $planDetail->id,
                                        'm_distinction_id' => $product->planDetailDistinct->m_distinction_id,
                                    ])->update([
                                        'is_distinct_settlement_edit' => (isset($params['is_distinct_settlement_edit'])
                                            && isset($params['is_distinct_settlement_edit'][$keyPlan])
                                            && isset($params['is_distinct_settlement_edit'][$keyPlan][$keyTypePlan])
                                            && in_array($product->planDetailDistinct->m_distinction_id, $params['is_distinct_settlement_edit'][$keyPlan][$keyTypePlan]))
                                            ? PlanDetailDistinct::IS_DISTINCT_SETTLEMENT_EDIT : PlanDetailDistinct::IS_DISTINCT_SETTLEMENT_EDIT_FALSE,
                                        'is_leave_all_edit' => (isset($params['is_leave_all_edit'])
                                            && isset($params['is_leave_all_edit'][$keyPlan])
                                            && isset($params['is_leave_all_edit'][$keyPlan][$keyTypePlan])
                                            && $params['is_leave_all_edit'][$keyPlan][$keyTypePlan] == 1) ? 1 : 0
                                    ]);
                                }

                                $planDetailProduct = $this->planDetailProductService->updateOrCreate([
                                    'id' => $product->id,
                                ], [
                                    'is_choice' => $product->is_choice ?? 0,
                                ]);

                                foreach ($params['codes'][$keyProduct] as $code) {
                                    $this->planDetailProductCodeService->updateOrCreate(
                                        [
                                            'plan_detail_product_id' => $planDetailProduct->id,
                                            'm_code_id' => $code->id,
                                        ],
                                        [
                                            'plan_detail_product_id' => $planDetailProduct->id,
                                            'm_code_id' => $code->id,
                                        ]
                                    );
                                }
                            }
                        }
                    } else {
                        foreach ($params['products'] as $keyProduct => $product) {
                            $planDetailDistinct = $this->planDetailDistinctService->create(
                                [
                                    'plan_detail_id' => $planDetail->id,
                                    'm_distinction_id' => $product->planDetailDistinct->m_distinction_id,
                                    'is_distinct_settlement' => PlanDetailDistinct::IS_DISTINCT_SETTLEMENT_FALSE,
                                    'is_add' => $product->planDetailDistinct->is_add,
                                    'is_decision' => PlanDetailDistinct::IS_DECISION_NOT_CHOOSE
                                ]
                            );
                            $planDetailProduct = $this->planDetailProductService->create([
                                'plan_detail_id' => $planDetail->id,
                                'plan_detail_distinct_id' => $planDetailDistinct->id,
                                'leave_status' => null,
                                'role_add' => $product->role_add,
                                'is_choice' => $product->is_choice ?? 0,
                                'm_product_id' => $product->mProduct->id
                            ]);
                            foreach ($params['codes'][$keyProduct] as $code) {
                                $planDetailProductCode = $this->planDetailProductCodeService->create(
                                    [
                                        'plan_detail_product_id' => $planDetailProduct->id,
                                        'm_code_id' => $code->id,
                                    ]
                                );
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Create new data Plan.
     *
     * @param array $params
     * @param object $trademarkPlan
     */
    public function createPlan(array $params, object $trademarkPlan)
    {
        $currentAdmin = Auth::guard(ADMIN_ROLE)->user();
        foreach ($params['plan_id'] as $keyPlan => $planId) {
            $plan = $this->repository->create([
                'admin_id' => Auth::user()->id,
                'trademark_plan_id' => $trademarkPlan->id,
                'plan_no' => 1,
                'is_completed' => Plan::IS_COMPLETED_FALSE
            ]);
            if (isset($params['data'][$keyPlan])) {
                foreach ($params['data'][$keyPlan] as $reasonId) {
                    $planReason = $this->planReasonService->create(
                        [
                            'plan_id' => $plan->id,
                            'reason_id' => $reasonId,
                        ]
                    );
                }
            }
            if ($params['submit'] == 'draft' || $params['submit'] == 'save' || $params['submit'] == 'submit') {
                foreach ($params['plan_detail_id'][$keyPlan] as $keyTypePlan => $typePlanId) {
                    $dataUpdate = [];
                    if (isset($params['type_plan_id']) &&
                        isset($params['type_plan_id'][$keyPlan]) &&
                        isset($params['type_plan_id'][$keyPlan][$keyTypePlan])
                    ) {
                        $dataUpdate['type_plan_id'] = $params['type_plan_id'][$keyPlan][$keyTypePlan];
                    }
                    if (isset($params['plan_description']) &&
                        isset($params['plan_description'][$keyPlan]) &&
                        isset($params['plan_description'][$keyPlan][$keyTypePlan])
                    ) {
                        $dataUpdate['plan_description'] = $params['plan_description'][$keyPlan][$keyTypePlan];
                    }
                    if (isset($params['plan_content']) &&
                        isset($params['plan_content'][$keyPlan]) &&
                        isset($params['plan_content'][$keyPlan][$keyTypePlan])
                    ) {
                        $dataUpdate['plan_content'] = $params['plan_content'][$keyPlan][$keyTypePlan];
                    }

                    if ((isset($params['possibility_resolution']) &&
                        isset($params['possibility_resolution'][$keyPlan]) &&
                        isset($params['possibility_resolution'][$keyPlan][$keyTypePlan]))
                    ) {
                        $dataUpdate['possibility_resolution'] = $params['possibility_resolution'][$keyPlan][$keyTypePlan];
                    }

                    if ((isset($params['is_confirm']) &&
                        isset($params['is_confirm'][$keyPlan]) &&
                        isset($params['is_confirm'][$keyPlan][$keyTypePlan]) &&
                        $params['is_confirm'][$keyPlan][$keyTypePlan] == 1)
                    ) {
                        $dataUpdate['is_confirm'] = 1;
                    }

                    if ((isset($params['is_decision']) &&
                        isset($params['is_decision'][$keyPlan]) &&
                        isset($params['is_decision'][$keyPlan][$keyTypePlan]))
                    ) {
                        $dataUpdate['is_decision'] = (int) $params['is_decision'][$keyPlan][$keyTypePlan];
                    }

                    $planDetail = $this->planDetailService->create(
                        [
                            'admin_id' => Auth::user()->id,
                            'plan_id' => $plan->id,
                            'type_plan_id' => $dataUpdate['type_plan_id'] ?? null,
                            'plan_description' => $dataUpdate['plan_description'] ?? null,
                            'plan_content' => $dataUpdate['plan_content'] ?? null,
                            'possibility_resolution' => $dataUpdate['possibility_resolution'] ?? null,
                            'is_confirm' => $dataUpdate['is_confirm'] ?? 0,
                            'is_decision' => $dataUpdate['is_decision'] ?? 0,
                            'is_choice' => PlanDetail::IS_NOT_CHOICE,
                            'is_choice_past' => PlanDetail::IS_CHOICE_PAST_FALSE
                        ]
                    );
                    if (isset($params['plan_detail_doc_id'][$keyPlan][$keyTypePlan])) {
                        foreach ($params['plan_detail_doc_id'][$keyPlan][$keyTypePlan] as $keyTypeDoc => $planDetailDocId) {
                            $planDetailDoc = $this->planDetailDocService->create(
                                [
                                    'plan_detail_id' => $planDetail->id,
                                    'm_type_plan_doc_id' => (isset($params['type_plan_doc_id'])
                                        && isset($params['type_plan_doc_id'][$keyPlan])
                                        && isset($params['type_plan_doc_id'][$keyPlan][$keyTypePlan])
                                        && isset($params['type_plan_doc_id'][$keyPlan][$keyTypePlan][$keyTypeDoc]))
                                        ? $params['type_plan_doc_id'][$keyPlan][$keyTypePlan][$keyTypeDoc] : null,
                                    'doc_requirement_des' => $params['doc_requirement_des'][$keyPlan][$keyTypePlan][$keyTypeDoc] ?? '',
                                    'is_completed' => PlanDetailDoc::IS_COMPLETED_FALSE,
                                ]
                            );
                        }
                    }
                    foreach ($params['products'] as $keyProduct => $product) {
                        $planDetailDistinct = $this->planDetailDistinctService->create(
                            [
                                'plan_detail_id' => $planDetail->id,
                                'm_distinction_id' => $product->m_distinction_id,
                                'is_distinct_settlement' => (isset($params['distinct_settement'])
                                    && isset($params['distinct_settement'][$keyPlan])
                                    && isset($params['distinct_settement'][$keyPlan][$keyTypePlan])
                                    && in_array($product->m_distinction_id, $params['distinct_settement'][$keyPlan][$keyTypePlan]))
                                    ? PlanDetailDistinct::IS_DISTINCT_SETTLEMENT_TRUE : PlanDetailDistinct::IS_DISTINCT_SETTLEMENT_FALSE,
                                'is_leave_all' => (isset($params['is_leave_all'])
                                    && isset($params['is_leave_all'][$keyPlan])
                                    && isset($params['is_leave_all'][$keyPlan][$keyTypePlan])
                                    && $params['is_leave_all'][$keyPlan][$keyTypePlan] == 1) ? 1 : 0,
                                'is_add' => PlanDetailDistinct::IS_ADD_FALSE,
                                'is_decision' => PlanDetailDistinct::IS_DECISION_NOT_CHOOSE
                            ]
                        );
                        $planDetailProduct = $this->planDetailProductService->create([
                            'plan_detail_id' => $planDetail->id,
                            'plan_detail_distinct_id' => $planDetailDistinct->id,
                            'leave_status' => null,
                            'role_add' => PlanDetailProduct::ROLE_ADD_OTHER,
                            'is_choice' => $product->is_choice ?? 0,
                            'm_product_id' => $product->id
                        ]);
                        foreach ($params['codes'][$keyProduct] as $code) {
                            $planDetailProductCode = $this->planDetailProductCodeService->create(
                                [
                                    'plan_detail_product_id' => $planDetailProduct->id,
                                    'm_code_id' => $code->id,
                                ]
                            );
                        }
                    }
                }
            }
        }
    }

    /**
     * Update or Create page a203shu and a203n
     *
     * @param array $params
     * @param object $trademarkPlan
     */
    public function updatePlanA203shuOrA203n(array $params, object $trademarkPlan)
    {
        if (isset($params['data']) && count($params['data']) > 0) {
            $this->planReasonService->findByCondition(['plan_id', $params['plan_id']])->delete();
        }
        foreach ($params['plan_id'] as $keyPlan => $planId) {
            if (isset($params['plan_detail_id'][$keyPlan])) {
                PlanDetailDoc::whereIn('plan_detail_id', $params['plan_detail_id'][$keyPlan])->delete();
            }

            $plan = $this->repository->updateOrCreate([
                'id' => $planId,
            ], [
                'admin_id' => Auth::user()->id,
                'trademark_plan_id' => $trademarkPlan->id,
                'plan_no' => 1,
                'is_completed' => Plan::IS_COMPLETED_FALSE
            ]);
            if (isset($params['data'][$keyPlan])) {
                foreach ($params['data'][$keyPlan] as $reasonId) {
                    $this->planReasonService->create(
                        [
                            'plan_id' => $plan->id,
                            'reason_id' => $reasonId,
                        ]
                    );
                }
            }
            if ($params['submit'] == 'draft' || $params['submit'] == 'save' || $params['submit'] == 'submit') {
                foreach ($params['plan_detail_id'][$keyPlan] as $keyTypePlan => $typePlanId) {
                    $dataUpdate = [];
                    if (isset($params['type_plan_id_edit'])
                        && isset($params['type_plan_id_edit'][$keyPlan])
                        && isset($params['type_plan_id_edit'][$keyPlan][$keyTypePlan])
                    ) {
                        $dataUpdate['type_plan_id_edit'] = $params['type_plan_id_edit'][$keyPlan][$keyTypePlan];
                    }

                    if (isset($params['plan_description_edit'])
                        && isset($params['plan_description_edit'][$keyPlan])
                        && isset($params['plan_description_edit'][$keyPlan][$keyTypePlan])
                    ) {
                        $dataUpdate['plan_description_edit'] = $params['plan_description_edit'][$keyPlan][$keyTypePlan];
                    }
                    if (isset($params['plan_content_edit'])
                        && isset($params['plan_content_edit'][$keyPlan])
                        && isset($params['plan_content_edit'][$keyPlan][$keyTypePlan])
                    ) {
                        $dataUpdate['plan_content_edit'] = $params['plan_content_edit'][$keyPlan][$keyTypePlan];
                    }

                    if (isset($params['possibility_resolution_edit'])
                        && isset($params['possibility_resolution_edit'][$keyPlan])
                        && isset($params['possibility_resolution_edit'][$keyPlan][$keyTypePlan])
                    ) {
                        $dataUpdate['possibility_resolution_edit'] = $params['possibility_resolution_edit'][$keyPlan][$keyTypePlan];
                    }

                    if (isset($params['is_decision'])
                        && isset($params['is_decision'][$keyPlan])
                        && isset($params['is_decision'][$keyPlan][$keyTypePlan])
                    ) {
                        $dataUpdate['is_decision'] = $params['is_decision'][$keyPlan][$keyTypePlan];
                    }

                    if (isset($params['is_confirm'])
                        && isset($params['is_confirm'][$keyPlan])
                        && isset($params['is_confirm'][$keyPlan][$keyTypePlan])
                        && $params['is_confirm'][$keyPlan][$keyTypePlan] == 1
                    ) {
                        $dataUpdate['is_confirm'] = PlanDetail::IS_CONFIRM_TRUE;
                    }

                    if (isset($params['page']) && $params['page'] == 'a203shu') {
                        $planDetail = $this->planDetailService->updateOrCreate(
                            [
                                'id' => (int) $params['plan_detail_id'][$keyPlan][$keyTypePlan],
                            ],
                            [
                                'admin_id' => Auth::user()->id,
                                'plan_id' => $plan->id,
                                'type_plan_id_edit' => $dataUpdate['type_plan_id_edit'] ?? null,
                                'plan_description_edit' => $dataUpdate['plan_description_edit'] ?? null,
                                'plan_content_edit' => $dataUpdate['plan_content_edit'] ?? null,
                                'possibility_resolution_edit' => $dataUpdate['possibility_resolution_edit'] ?? null,
                                'is_decision' => $dataUpdate['is_decision'] ?? 0,
                                'is_confirm' => $dataUpdate['is_confirm'] ?? 0,
                            ]
                        );
                    } else {
                        $planDetail = $this->planDetailService->updateOrCreate(
                            [
                                'id' => (int) $params['plan_detail_id'][$keyPlan][$keyTypePlan],
                            ],
                            [
                                'admin_id' => Auth::user()->id,
                                'plan_id' => $plan->id,
                                'type_plan_id' => isset($params['type_plan_id'])
                                && isset($params['type_plan_id'][$keyPlan])
                                && isset($params['type_plan_id'][$keyPlan][$keyTypePlan]) ? $params['type_plan_id'][$keyPlan][$keyTypePlan] : null,
                                'plan_description' => isset($params['plan_description'])
                                && isset($params['plan_description'][$keyPlan])
                                && isset($params['plan_description'][$keyPlan][$keyTypePlan]) ? $params['plan_description'][$keyPlan][$keyTypePlan] : null,
                                'plan_content' => isset($params['plan_content'])
                                && isset($params['plan_content'][$keyPlan])
                                && isset($params['plan_content'][$keyPlan][$keyTypePlan]) ? $params['plan_content'][$keyPlan][$keyTypePlan] : null,
                                'possibility_resolution' => (isset($params['possibility_resolution'])
                                    && isset($params['possibility_resolution'][$keyPlan])
                                    && isset($params['possibility_resolution'][$keyPlan][$keyTypePlan])) ? $params['possibility_resolution'][$keyPlan][$keyTypePlan] : null,
                                'is_confirm' => $dataUpdate['is_confirm'] ?? 0,
                                'is_choice' => PlanDetail::IS_NOT_CHOICE,
                                'is_choice_past' => PlanDetail::IS_CHOICE_PAST_FALSE
                            ]
                        );
                    }
                    if (isset($params['plan_detail_doc_id'][$keyPlan][$keyTypePlan])) {
                        foreach ($params['plan_detail_doc_id'][$keyPlan][$keyTypePlan] as $keyTypeDoc => $planDetailDocId) {
                            $planDetailDoc = $this->planDetailDocService->updateOrCreate(
                                [
                                    'id' => $planDetailDocId,
                                ],
                                [
                                    'plan_detail_id' => $planDetail->id,
                                    'm_type_plan_doc_id' => (isset($params['type_plan_doc_id'])
                                        && isset($params['type_plan_doc_id'][$keyPlan])
                                        && isset($params['type_plan_doc_id'][$keyPlan][$keyTypePlan])
                                        && isset($params['type_plan_doc_id'][$keyPlan][$keyTypePlan][$keyTypeDoc]))
                                        ? $params['type_plan_doc_id'][$keyPlan][$keyTypePlan][$keyTypeDoc] : null,
                                    'doc_requirement_des' => (isset($params['doc_requirement_des'])
                                        && isset($params['doc_requirement_des'][$keyPlan])
                                        && isset($params['doc_requirement_des'][$keyPlan][$keyTypePlan])
                                        && isset($params['doc_requirement_des'][$keyPlan][$keyTypePlan][$keyTypeDoc]))
                                        ? $params['doc_requirement_des'][$keyPlan][$keyTypePlan][$keyTypeDoc] : null,
                                    'm_type_plan_doc_id_edit' => (isset($params['type_plan_doc_id_edit'])
                                        && isset($params['type_plan_doc_id_edit'][$keyPlan])
                                        && isset($params['type_plan_doc_id_edit'][$keyPlan][$keyTypePlan])
                                        && isset($params['type_plan_doc_id_edit'][$keyPlan][$keyTypePlan][$keyTypeDoc]))
                                        ? $params['type_plan_doc_id_edit'][$keyPlan][$keyTypePlan][$keyTypeDoc] : null,
                                    'doc_requirement_des_edit' => (isset($params['doc_requirement_des_edit']) &&
                                        isset($params['doc_requirement_des_edit'][$keyPlan]) &&
                                        isset($params['doc_requirement_des_edit'][$keyPlan][$keyTypePlan]) &&
                                        isset($params['doc_requirement_des_edit'][$keyPlan][$keyTypePlan][$keyTypeDoc]))
                                        ? $params['doc_requirement_des_edit'][$keyPlan][$keyTypePlan][$keyTypeDoc] : null,
                                    'is_completed' => PlanDetailDoc::IS_COMPLETED_FALSE,
                                ]
                            );
                        }
                    }
                    if (!isset($params['type_create'][$keyPlan][$keyTypePlan])) {
                        unset($params['products'], $params['codes']);
                        $trademarkPlan = $trademarkPlan->load([
                            'plans.planDetails.planDetailProducts.mProduct',
                            'plans.planDetails.planDetailProducts.planDetailDistinct.mDistinction',
                            'plans.planDetails.planDetailProducts.planDetailProductCodes.mCode',
                        ]);
                        $products = $trademarkPlan->getProducts();
                        foreach ($products as $key => $value) {
                            $params['products'][] = $value['plan_detail_product'];
                            $params['codes'][] = $value['codes'];
                        }
                        $products->distincts = $trademarkPlan->getProducts()->groupBy('distinction.id');
                        foreach ($products->distincts as $keyDistinct => $distinct) {
                            $params['distincts'][] = $keyDistinct;
                        }
                        if (isset($params['products']) && count($params['products']) > 0) {
                            foreach ($params['products'] as $keyProduct => $product) {
                                $planDetailDistinct = $this->planDetailDistinctService->updateOrCreate(
                                    [
                                        'plan_detail_id' => $planDetail->id,
                                        'm_distinction_id' => $product->planDetailDistinct->m_distinction_id,
                                    ],
                                    [
                                        'is_distinct_settlement' => (isset($params['distinct_settement'])
                                            && isset($params['distinct_settement'][$keyPlan])
                                            && isset($params['distinct_settement'][$keyPlan][$keyTypePlan])
                                            && in_array($product->planDetailDistinct->m_distinction_id, $params['distinct_settement'][$keyPlan][$keyTypePlan]))
                                            ? PlanDetailDistinct::IS_DISTINCT_SETTLEMENT_TRUE : PlanDetailDistinct::IS_DISTINCT_SETTLEMENT_FALSE,
                                        'is_leave_all' => (isset($params['is_leave_all'])
                                            && isset($params['is_leave_all'][$keyPlan])
                                            && isset($params['is_leave_all'][$keyPlan][$keyTypePlan])
                                            && (int) $params['is_leave_all'][$keyPlan][$keyTypePlan] == 1) ? 1 : 0,
                                        'is_add' => (isset($params['distinct_is_add'])
                                            && isset($params['distinct_is_add'][$keyPlan])
                                            && isset($params['distinct_is_add'][$keyPlan][$keyTypePlan])
                                            && in_array($product->planDetailDistinct->m_distinction_id, $params['distinct_is_add'][$keyPlan][$keyTypePlan]))
                                            ? PlanDetailDistinct::IS_ADD_TRUE : PlanDetailDistinct::IS_ADD_FALSE,
                                        'is_decision' => (isset($params['is_decision'])
                                            && isset($params['is_decision'][$keyPlan])
                                            && isset($params['is_decision'][$keyPlan][$keyTypePlan])
                                            && $params['is_decision'][$keyPlan][$keyTypePlan] == PlanDetail::IS_DECISION_EDIT)
                                            ? PlanDetail::IS_DECISION_EDIT : PlanDetail::IS_DECISION_DRAFT,
                                    ]
                                );
                                if (isset($params['page']) && $params['page'] == 'a203shu') {
                                    $planDetailDistinct->update([
                                        'is_distinct_settlement_edit' => (isset($params['is_distinct_settlement_edit'])
                                            && isset($params['is_distinct_settlement_edit'][$keyPlan])
                                            && isset($params['is_distinct_settlement_edit'][$keyPlan][$keyTypePlan])
                                            && in_array($product->planDetailDistinct->m_distinction_id, $params['is_distinct_settlement_edit'][$keyPlan][$keyTypePlan]))
                                            ? PlanDetailDistinct::IS_DISTINCT_SETTLEMENT_EDIT : PlanDetailDistinct::IS_DISTINCT_SETTLEMENT_EDIT_FALSE,
                                        'is_leave_all_edit' => (isset($params['is_leave_all_edit'])
                                            && isset($params['is_leave_all_edit'][$keyPlan])
                                            && isset($params['is_leave_all_edit'][$keyPlan][$keyTypePlan])
                                            && $params['is_leave_all_edit'][$keyPlan][$keyTypePlan] == 1) ? 1 : 0
                                    ]);
                                }
                                $planDetailProduct = $this->planDetailProductService->updateOrCreate([
                                    'id' => $product->id,
                                ], [
                                    'is_choice' => $product->is_choice ?? 0,
                                ]);
                                foreach ($params['codes'][$keyProduct] as $code) {
                                    $planDetailProductCode = $this->planDetailProductCodeService->updateOrCreate(
                                        [
                                            'plan_detail_product_id' => $planDetailProduct->id,
                                            'm_code_id' => $code->id,
                                        ],
                                        [
                                            'plan_detail_product_id' => $planDetailProduct->id,
                                            'm_code_id' => $code->id,
                                        ]
                                    );
                                }
                            }
                        }
                    } else {
                        if ($params['page'] == A203) {
                            $roleAdd = PlanDetailProduct::ROLE_ADD_OTHER;
                        } elseif ($params['page'] == A203SHU) {
                            $roleAdd = PlanDetailProduct::ROLE_ADD_PERSON_CHARGE;
                        } elseif ($params['page'] == A203n) {
                            $roleAdd = PlanDetailProduct::ROLE_ADD_RESPONSIBLE_PERSON;
                        }
                        unset($params['products'], $params['codes']);
                        $trademarkPlan = $trademarkPlan->load([
                            'plans.planDetails.planDetailProducts.mProduct',
                            'plans.planDetails.planDetailProducts.planDetailDistinct.mDistinction',
                            'plans.planDetails.planDetailProducts.planDetailProductCodes.mCode',
                        ]);
                        $products = $trademarkPlan->getProducts();
                        foreach ($products as $key => $value) {
                            $params['products'][] = $value['plan_detail_product'];
                            $params['codes'][] = $value['codes'];
                        }
                        $products->distincts = $trademarkPlan->getProducts()->groupBy('distinction.id');

                        foreach ($params['products'] as $keyProduct => $product) {
                            $planDetailDistinct = $this->planDetailDistinctService->create(
                                [
                                    'plan_detail_id' => $planDetail->id,
                                    'm_distinction_id' => $product->planDetailDistinct->m_distinction_id,
                                    'is_distinct_settlement' => (isset($params['distinct_settement'])
                                        && isset($params['distinct_settement'][$keyPlan])
                                        && isset($params['distinct_settement'][$keyPlan][$keyTypePlan])
                                        && in_array($product->planDetailDistinct->m_distinction_id, $params['distinct_settement'][$keyPlan][$keyTypePlan]))
                                        ? PlanDetailDistinct::IS_DISTINCT_SETTLEMENT_TRUE : PlanDetailDistinct::IS_DISTINCT_SETTLEMENT_FALSE,
                                    'is_leave_all' => (isset($params['is_leave_all'])
                                        && isset($params['is_leave_all'][$keyPlan])
                                        && isset($params['is_leave_all'][$keyPlan][$keyTypePlan])
                                        && $params['is_leave_all'][$keyPlan][$keyTypePlan] == 1) ? 1 : 0,
                                    'is_distinct_settlement_edit' => (isset($params['is_distinct_settlement_edit'])
                                        && isset($params['is_distinct_settlement_edit'][$keyPlan])
                                        && isset($params['is_distinct_settlement_edit'][$keyPlan][$keyTypePlan])
                                        && in_array($product->planDetailDistinct->m_distinction_id, $params['is_distinct_settlement_edit'][$keyPlan][$keyTypePlan]))
                                        ? PlanDetailDistinct::IS_DISTINCT_SETTLEMENT_EDIT : PlanDetailDistinct::IS_DISTINCT_SETTLEMENT_EDIT_FALSE,
                                    'is_leave_all_edit' => (isset($params['is_leave_all_edit'])
                                        && isset($params['is_leave_all_edit'][$keyPlan])
                                        && isset($params['is_leave_all_edit'][$keyPlan][$keyTypePlan])
                                        && $params['is_leave_all_edit'][$keyPlan][$keyTypePlan] == 1) ? 1 : 0,
                                    'is_add' => (isset($params['distinct_is_add'])
                                        && isset($params['distinct_is_add'][$keyPlan])
                                        && isset($params['distinct_is_add'][$keyPlan][$keyTypePlan])
                                        && in_array($product->planDetailDistinct->m_distinction_id, $params['distinct_is_add'][$keyPlan][$keyTypePlan]))
                                        ? PlanDetailDistinct::IS_ADD_TRUE : PlanDetailDistinct::IS_ADD_FALSE,
                                    'is_decision' => (isset($params['is_decision'])
                                        && isset($params['is_decision'][$keyPlan])
                                        && isset($params['is_decision'][$keyPlan][$keyTypePlan])
                                        && $params['is_decision'][$keyPlan][$keyTypePlan] == PlanDetail::IS_DECISION_EDIT)
                                        ? PlanDetail::IS_DECISION_EDIT : PlanDetail::IS_DECISION_DRAFT,
                                ]
                            );
                            $planDetailProduct = $this->planDetailProductService->create([
                                'plan_detail_id' => $planDetail->id,
                                'plan_detail_distinct_id' => $planDetailDistinct->id,
                                'leave_status' => null,
                                'role_add' => $product->role_add,
                                'is_choice' => $product->is_choice ?? 0,
                                'm_product_id' => $product->mProduct->id
                            ]);
                            foreach ($params['codes'][$keyProduct] as $code) {
                                $planDetailProductCode = $this->planDetailProductCodeService->create(
                                    [
                                        'plan_detail_product_id' => $planDetailProduct->id,
                                        'm_code_id' => $code->id,
                                    ]
                                );
                            }
                        }
                    }
                }
            }
        }
    }


    /**
     * Create or update distinction and product of plan.
     *
     * @param array $param
     */
    public function saveDistinctProduct(array $params)
    {
        try {
            DB::beginTransaction();

            $mCodeService = app(MCodeService::class);
            $mProductService = app(MProductService::class);

            // Delete Plan Detail Product
            $deletePlanDetailProductIds = $params['delete_plan_detail_product_ids'] ?? '';
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
                    $mProduct = $mProductService->find($planDetailProduct->m_product_id);
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
                        $codeData = $mCodeService->createOrUpdateName($code);
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

                if ($mProduct) {
                    $mProduct->mCode()->sync($codeIds);
                }
            }

            // Create addition product/distinct
            $currentAdmin = Auth::guard('admin')->user();
            $products = $params['products'] ?? [];
            foreach ($products as $product) {
                // Create/Update Code
                $productCode = [];
                $productCode[] = $mCodeService->find($product['m_code_id']);

                // Create/Update Product
                $dataCreateProduct = [
                    'm_distinction_id' => $product['m_distinction_id'],
                    'admin_id' => $currentAdmin->id,
                    'name' => $product['product_name'],
                    'type' => MProduct::TYPE_CREATIVE_CLEAN,
                ];
                $dataCreateProduct['products_number'] = $mProductService->generateProductCode(
                    $dataCreateProduct['type'],
                    $dataCreateProduct['m_distinction_id'],
                );
                $newProduct = $mProductService->create($dataCreateProduct);

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
                        'role_add' => $currentAdmin->role == ROLE_MANAGER ? PlanDetailProduct::ROLL_ADD_MANAGER : PlanDetailProduct::ROLL_ADD_SUPERVISOR,
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

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e);
        }
    }

    /**
     * Format Material Supervisor [a204han | a204han_n]
     *
     * @param Collection $plans
     * @param int $round
     * @return Collection
     */
    public function formatMaterialSupervisor(Collection $plans, int $round = 0): Collection
    {
        $plans->map(function ($item, $index) use ($round) {
            $item->index = $index + 1;

            // Get Plan Reason
            $reasons = $item->reason;
            $reasonNameArray = $reasons->pluck('reason_name')->toArray();
            $reasonStr = count($reasonNameArray) > 0 ? implode('、', $reasonNameArray) : '';
            $item->reason_name = $reasonStr;
            $docs = [];
            $totalAttachmentUser = 0;
            $requiredDocumentPlan = $item->requiredDocumentPlans->first();
            if ($requiredDocumentPlan) {
                $item->required_document_plan_is_completed = $requiredDocumentPlan->is_completed;
            }
            $planDetails = $item->planDetails;
            $requiredDocument = $item->requiredDocuments->last();
            if ($requiredDocument) {
                $item->required_document_id = $requiredDocument->id;
                $requiredDocumentMiss = $requiredDocument->RequiredDocumentMiss->where('plan_id', $item->id)->first();
                if ($requiredDocumentMiss) {
                    $item->required_document_miss_description_docs_miss = $requiredDocumentMiss->description_docs_miss;
                }
            }
            $planDocCmts = $item->planDocCmts;

            $planDocCmts->map(function ($item) {
                $arrExplode = explode('_', $item->from_send_doc);
                if (count($arrExplode) > 1) {
                    $item->round = $arrExplode[1];
                }
            });

            $planDocCmts = $planDocCmts->filter(function ($item) use ($round) {
                return $item->round <= $round;
            });
            $item->planDocCmts = $planDocCmts;
            foreach ($planDetails as $planDetail) {
                foreach ($planDetails as $key => $planDetailData) {
                    if ($planDetail->id == $planDetailData->id) {
                        $planDetail->index = $key + 1;
                    }
                }

                // Get Plan Detail Distinct data
                $planDetailDistincts = $planDetail->planDetailDistincts ?? collect([]);
                $firstPlanDetailDistincts = $planDetailDistincts->first();

                $planDetailDistinctSettlements = $planDetailDistincts->where('is_distinct_settlement', true);
                $planDetail->planDetailDistinctSettlements = $planDetailDistinctSettlements;
                $planDetail->is_leave_all = $firstPlanDetailDistincts->is_leave_all ?? false;

                // Get Plan Detail Doc
                $planDetailDocs = $planDetail->planDetailDocs ?? collect([]);
                foreach ($planDetailDocs as $planDetailDoc) {
                    $docs[$planDetailDoc->id] = $planDetailDoc;

                    $details = [];
                    if ($requiredDocument) {
                        $requiredDocumentDetails = $requiredDocument->requiredDocumentDetails;

                        foreach ($requiredDocumentDetails as $requiredDocumentDetail) {
                            $planDetailDocRequired = $requiredDocumentDetail->planDetailDoc;
                            if ($planDetailDocRequired && $planDetailDocRequired->id == $planDetailDoc->id) {
                                $attachmentUser = json_decode($requiredDocumentDetail->attachment_user ?? '[]', true);
                                $attachmentUser = collect($attachmentUser)->map(function ($attach) {
                                    $value = $attach['value'] ?? null;

                                    if (strpos($value, 'uploads') !== false) {
                                        $attach['value'] = asset($value);
                                    }

                                    if (!empty($value)) {
                                        $attach['name'] = Str::replace(FOLDER_MATERIAL . '/', '', $value);
                                    }
                                    return $attach;
                                });
                                $attachmentUser->sortBy('sending_date');
                                $requiredDocumentDetail->attachment_user_format = $attachmentUser->toArray();
                                $requiredDocumentDetail->attachment_user_group = $attachmentUser->groupBy('sending_date')->toArray();

                                if (!empty($fromSendDoc)) {
                                    $fromSendDoc = explode('_', $requiredDocumentDetail->from_send_doc);
                                    $requiredDocumentDetail->round = $fromSendDoc[1];
                                }

                                $docTotalAttachmentUser = count($attachmentUser->toArray());
                                $totalAttachmentUser += $docTotalAttachmentUser;

                                $details[] = $requiredDocumentDetail;
                            }
                        }
                    }

                    $docs[$planDetailDoc->id]['required_document_details'] = $details;
                }
            }

            $planDetailsIsChoices = $planDetails->where('is_choice', true);
            $item->planDetailsIsChoices = $planDetailsIsChoices;

            $planDetailsNotChoices = $planDetails->where('is_choice', false);
            $item->planDetailsNotChoices = $planDetailsNotChoices;

            $item->planDetailDocs = collect($docs);
            $item->total_attachment_user = $totalAttachmentUser;
        });
        return $plans;
    }
}
