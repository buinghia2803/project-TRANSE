<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\CommonHelper;
use App\Jobs\SendGeneralMailJob;
use App\Models\MailTemplate;
use App\Models\Notice;
use App\Models\NoticeDetail;
use App\Models\PlanComment;
use App\Models\PlanCorrespondence;
use App\Models\PlanDocCmt;
use App\Models\RequiredDocument;
use App\Models\RequiredDocumentComment;
use App\Models\RequiredDocumentDetail;
use App\Models\RequiredDocumentMiss;
use App\Models\RequiredDocumentPlan;
use App\Models\TrademarkPlan;
use App\Notices\CommonNotice;
use App\Services\Common\NoticeService;
use App\Services\Common\TrademarkTableService;
use App\Services\ComparisonTrademarkResultService;
use App\Services\MCodeService;
use App\Services\MDistinctionService;
use App\Services\MProductService;
use App\Services\MTypePlanDocService;
use App\Services\MTypePlanService;
use App\Services\NoticeDetailService;
use App\Services\PlanCommentService;
use App\Services\PlanCorrespondenceService;
use App\Services\PlanDetailDistinctService;
use App\Services\PlanDetailDocService;
use App\Services\PlanDetailProductCodeService;
use App\Services\PlanDetailProductService;
use App\Services\PlanDetailService;
use App\Services\PlanReasonService;
use App\Services\PlanService;
use App\Services\RequiredDocumentCommentService;
use App\Services\RequiredDocumentDetailService;
use App\Services\RequiredDocumentMissService;
use App\Services\MailTemplateService;
use App\Services\RequiredDocumentPlanService;
use App\Services\RequiredDocumentService;
use App\Services\TrademarkDocumentService;
use App\Services\TrademarkPlanService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MaterialController extends BaseController
{
    protected TrademarkTableService $trademarkTableService;
    protected ComparisonTrademarkResultService $comparisonTrademarkResultService;
    protected TrademarkPlanService $trademarkPlanService;
    protected PlanService $planService;
    protected TrademarkDocumentService $trademarkDocumentService;
    protected PlanCommentService $planCommentService;
    protected PlanDetailService $planDetailService;
    protected PlanDetailDocService $planDetailDocService;
    protected PlanReasonService $planReasonService;
    protected MTypePlanService $mTypePlanService;
    protected MTypePlanDocService $mTypePlanDocService;
    protected MDistinctionService $mDistinctionService;
    protected PlanDetailProductService $planDetailProductService;
    protected MCodeService $mCodeService;
    protected MProductService $mProductService;
    protected PlanDetailProductCodeService $planDetailProductCodeService;
    protected PlanDetailDistinctService $planDetailDistinctService;
    protected NoticeService $noticeService;
    protected NoticeDetailService $noticeDetailService;
    protected PlanCorrespondenceService $planCorrespondenceService;
    protected RequiredDocumentService  $requiredDocumentService;
    protected RequiredDocumentCommentService  $requiredDocumentCommentService;
    protected RequiredDocumentDetailService  $requiredDocumentDetailService;
    protected RequiredDocumentPlanService  $requiredDocumentPlanService;
    protected RequiredDocumentMissService  $requiredDocumentMissService;
    protected MailTemplateService $mailTemplateService;

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
        RequiredDocumentService          $requiredDocumentService,
        RequiredDocumentDetailService    $requiredDocumentDetailService,
        RequiredDocumentCommentService   $requiredDocumentCommentService,
        RequiredDocumentPlanService      $requiredDocumentPlanService,
        RequiredDocumentMissService      $requiredDocumentMissService,
        MailTemplateService              $mailTemplateService
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
        $this->requiredDocumentService = $requiredDocumentService;
        $this->requiredDocumentCommentService = $requiredDocumentCommentService;
        $this->requiredDocumentDetailService = $requiredDocumentDetailService;
        $this->requiredDocumentPlanService = $requiredDocumentPlanService;
        $this->requiredDocumentMissService = $requiredDocumentMissService;
        $this->mailTemplateService = $mailTemplateService;

        // Check permission
        $this->middleware('permission:materials.postSupervisor')->only(['postSupervisor']);
        $this->middleware('permission:materials.noMaterial')->only(['noMaterial']);
        $this->middleware('permission:materials.postNoMaterial')->only(['postNoMaterial']);
        $this->middleware('permission:materials.postCheckSupervisor')->only(['postCheckSupervisor']);
        $this->middleware('permission:materials.reSupervisor')->only(['reSupervisor']);
        $this->middleware('permission:materials.postReSupervisor')->only(['postReSupervisor']);
        $this->middleware('permission:materials.confirm')->only(['confirm']);
    }

    /**
     * Get material supervisor - a204han
     *
     * @param Request $request
     * @param $id
     * @return View
     */
    public function supervisor(Request $request, $id): View
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

        $requiredDocumentId = $request->required_document_id;
        if (!isset($requiredDocumentId) && empty($requiredDocumentId) || !is_numeric($requiredDocumentId)) {
            abort(CODE_ERROR_404);
        }
        $requiredDocument = $this->requiredDocumentService->find($requiredDocumentId);
        if (!$requiredDocument) {
            abort(CODE_ERROR_404);
        }
        $plans = $trademarkPlan->plans;
        $plans->load([
            'requiredDocumentPlans' => function ($query) use ($requiredDocumentId) {
                $query->where('required_document_plans.required_document_id', $requiredDocumentId);
            },
            'requiredDocuments' => function ($query) use ($requiredDocumentId) {
                $query->where('required_documents.id', $requiredDocumentId);
            },
            'requiredDocuments.requiredDocumentDetails' => function ($query) use ($requiredDocumentId) {
                $query->where('required_document_id', $requiredDocumentId)
                    ->where('from_send_doc', U204);
            },
            'requiredDocuments.requiredDocumentDetails.planDetailDoc',
            'planDetails.planDetailDocs',
            'reason',
            'planDocCmts' => function ($query) {
                $query->where('type', PlanDocCmt::TYPE_U204);
            },
            'planDetails.planDetailDistincts',
            'planDetails.planDetailDocs.MTypePlanDoc',
        ]);
        $plans = $this->planService->formatMaterialSupervisor($plans);
        $planComment = $this->requiredDocumentCommentService->findByCondition([
            'type_comment_step' => RequiredDocumentComment::TYPE_COMMENT_STEP_1,
            'from_send_doc' => RequiredDocumentComment::FROM_SEND_DOC_U204,
            'required_document_id' => $requiredDocument->id,
        ])->first();
        // Trademark Table
        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_ADMIN_1, $trademark->id, [
            SHOW_LINK_ANKEN_TOP => true,
        ]);
        $flagDisabled = false;
        if (!isset($request->type)) {
            if ($requiredDocument->is_confirm == IS_CONFIRM_TRUE) {
                $flagDisabled = true;
            }
        }

        return view('admin.modules.materials.supervisor', compact(
            'comparisonTrademarkResult',
            'trademarkPlan',
            'planComment',
            'trademarkTable',
            'plans',
            'requiredDocumentId',
            'flagDisabled'
        ));
    }

    /**
     * Post material supervisor - a204han
     *
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function postSupervisor(Request $request, $id): RedirectResponse
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

        $requiredDocument = $this->requiredDocumentService->find($request->required_document_id ?? 0);
        if (empty($requiredDocument)) {
            abort(CODE_ERROR_404);
        }

        try {
            DB::beginTransaction();

            $params = $request->all();

            // Update Comment
            $this->requiredDocumentCommentService->updateOrCreate(
                [
                    'type_comment_step' => RequiredDocumentComment::TYPE_COMMENT_STEP_1,
                    'required_document_id' => $params['required_document_id'],
                    'from_send_doc' => RequiredDocumentComment::FROM_SEND_DOC_U204,
                ],
                [
                    'content' => $params['content'] ?? '',
                ]
            );

            // Update Plan Detail Docs
            $planDetailDocs = $params['plan_detail_docs'] ?? [];
            foreach ($planDetailDocs as $planDetailDoc) {
                $id = $planDetailDoc['id'] ?? 0;
                $isCompleted = !empty($planDetailDoc['is_completed']);
                $this->requiredDocumentDetailService->updateById($id, [
                    'is_completed' => $isCompleted,
                ]);
            }
            // Update Plans
            $plans = $params['plans'] ?? [];
            foreach ($plans as $plan) {
                $id = $plan['id'] ?? 0;
                $isCompleted = !empty($plan['is_completed']);
                $requiredDocumentPlan = RequiredDocumentPlan::where('plan_id', $id)->where('required_document_id', $plan['required_document_id'])->first();
                if ($requiredDocumentPlan) {
                    $requiredDocumentPlan->update(['is_completed' => $isCompleted]);
                }
            }

            if (!empty($params[A203SHU])) {
                $requiredDocument->update([
                    'is_confirm' => true,
                ]);

                $trademarkPlan->duplicateA203Group();
                $newTrademarkPlan = $this->trademarkPlanService->findByCondition([
                    'plan_correspondence_id' => $trademarkPlan->plan_correspondence_id,
                ])->get()->last();
                $newTrademarkPlan->update([
                    'flag_role' => TrademarkPlan::FLAG_ROLE_2,
                    'is_redirect' => true,
                ]);

                $commonNotice = app(CommonNotice::class);
                $commonNotice->noticeA204hanToA203Shu($comparisonTrademarkResult, $newTrademarkPlan, [
                    'target_page' => route('admin.refusal.materials.supervisor', [
                        'id' => $comparisonTrademarkResult->id,
                        'trademark_plan_id' => $trademarkPlan->id,
                        'required_document_id' => $requiredDocument->id,
                    ]), // route a204han
                ]);

                $redirect = route('admin.refusal.response-plan.edit.supervisor', [
                    'id' => $comparisonTrademarkResult->id,
                    'trademark_plan_id' => $newTrademarkPlan->id,
                ]);
            } elseif (!empty($params[SAVE])) {
                $redirect = route('admin.refusal.materials-re.supervisor', [
                    'id' => $comparisonTrademarkResult->id,
                    'trademark_plan_id' => $trademarkPlan->id,
                    'required_document_id' => $params['required_document_id'],
                    'round' => $trademarkPlan->getRound(),
                ]);
            } elseif (!empty($params[SUBMIT])) {
                // Update Trademark Plan
                $trademarkPlan->update([
                    'is_confirm_docs' => true,
                ]);

                // Send Email
                $user = $trademark->user;
                if (!empty($user)) {
                    SendGeneralMailJob::dispatch('emails.material-supervisor', [
                        'to' => $user->getListMail(),
                        'subject' => __('labels.emails.subject.material-supervisor'),
                    ]);
                }

                // Send Notice
                $this->noticeSupervisor($comparisonTrademarkResult, $trademarkPlan, $params['required_document_id']);

                CommonHelper::setMessage($request, MESSAGE_SUCCESS, __('messages.general.send_to_tantou'));
                $redirect = route('admin.home');
            } else {
                CommonHelper::setMessage($request, MESSAGE_SUCCESS, __('messages.update_success'));
                $redirect = route('admin.refusal.materials.supervisor', [
                    'id' => $comparisonTrademarkResult->id,
                    'trademark_plan_id' => $trademarkPlan->id,
                    'required_document_id' => $params['required_document_id'],
                ]);
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
     * Notice material supervisor - a204han
     *
     * @param Model $comparisonTrademarkResult
     * @param Model $trademarkPlan
     * @return void
     */
    public function noticeSupervisor(Model $comparisonTrademarkResult, Model $trademarkPlan, $requiredDocumentId)
    {
        $trademark = $comparisonTrademarkResult->trademark;

        $targetPage = route('admin.refusal.materials.supervisor', [
            'id' => $comparisonTrademarkResult->id,
            'trademark_plan_id' => $trademarkPlan->id,
            'required_document_id' => $requiredDocumentId,
        ]);
        $redirectPage = route('admin.refusal.documents.create', [
            'id' => $comparisonTrademarkResult->id,
            'trademark_plan_id' => $trademarkPlan->id,
        ]);

        // Update Notice at no 81
        $stepBeforeNotice = $this->noticeDetailService->findByCondition([
            'completion_date' => null,
        ])->with('notice')->get()
            ->where('type_acc', '<>', NoticeDetail::TYPE_USER)
            ->where('notice.trademark_id', $trademark->id)
            ->where('notice.user_id', $trademark->user_id)
            ->whereIn('notice.flow', [Notice::FLOW_RESPONSE_REASON, Notice::FLOW_RENEWAL_BEFORE_DEADLINE])
            ->filter(function ($item) {
                if ($item->notice->flow == Notice::FLOW_RESPONSE_REASON && $item->notice->step == Notice::STEP_4) {
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

        $responseDeadlineSupervisor = null;
        $responseDeadlineUser = null;
        $machingResult = $comparisonTrademarkResult->machingResult;
        if (!empty($machingResult) && !empty($planCorrespondence)) {
            if ($planCorrespondence->type == PlanCorrespondence::TYPE_2) {
                $responseDeadlineSupervisor = $machingResult->calculateResponseDeadline(-36);
            } else {
                $responseDeadlineSupervisor = $machingResult->calculateResponseDeadline(-31);
            }
        }

        $notice = [
            'flow' => Notice::FLOW_RESPONSE_REASON,
            'step' => Notice::STEP_5,
            'trademark_id' => $trademark->id,
            'user_id' => $trademark->user_id,
            'trademark_info_id' => null,
        ];

        $noticeDetails = [
            // Send Notice Tantou
            [
                'target_id' => null,
                'type_acc' => NoticeDetail::TYPE_MANAGER,
                'target_page' => $targetPage,
                'redirect_page' => $redirectPage,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                'is_action' => true,
                'content' => '拒絶理由通知対応：提出書類作成',
                'attribute' => null,
                'response_deadline' => $responseDeadlineSupervisor,
            ],
            [
                'target_id' => null,
                'type_acc' => NoticeDetail::TYPE_MANAGER,
                'target_page' => $targetPage,
                'redirect_page' => $redirectPage,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                'is_action' => false,
                'content' => '担当者　拒絶理由通知対応：提出書類作成',
                'attribute' => 'お客様へ',
                'response_deadline' => $responseDeadlineSupervisor,
            ],
            // Send Notice User
            [
                'target_id' => $trademark->user_id,
                'type_acc' => NoticeDetail::TYPE_USER,
                'target_page' => $targetPage,
                'redirect_page' => null,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_DEFAULT,
                'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                'is_action' => false,
                'content' => '拒絶理由通知対応：方針案選択/必要資料提出済・返信待ち',
                'attribute' => null,
                'response_deadline' => $responseDeadlineUser,
            ],
            [
                'target_id' => $trademark->user_id,
                'type_acc' => NoticeDetail::TYPE_USER,
                'target_page' => $targetPage,
                'redirect_page' => null,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                'is_action' => false,
                'content' => '拒絶理由通知対応：方針案選択/必要資料提出済・返信待ち',
                'attribute' => null,
                'response_deadline' => $responseDeadlineUser,
            ],
        ];

        $this->noticeService->sendNotice([
            'notices' => $notice,
            'notice_details' => $noticeDetails,
        ]);
    }

    /**
     * Get material-re check supervisor - a204han_n
     *
     * @param Request $request
     * @param $id
     * @return View
     */
    public function checkSupervisor(Request $request, $id)
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

        $requiredDocumentId = $request->required_document_id;
        if (!isset($request->required_document_id) || empty($requiredDocumentId) || !is_numeric($requiredDocumentId)) {
            abort(CODE_ERROR_404);
        }

        $requiredDocument = $this->requiredDocumentService->find($requiredDocumentId);
        if (!$requiredDocument) {
            abort(CODE_ERROR_404);
        }

        $round = $request->round;
        if (!$round) {
            abort(CODE_ERROR_404);
        }
        $plans = $trademarkPlan->plans;
        $plans->load([
            'requiredDocumentPlans' => function ($query) use ($requiredDocumentId) {
                $query->where('required_document_plans.required_document_id', $requiredDocumentId);
            },
            'requiredDocuments' => function ($query) use ($requiredDocumentId) {
                $query->where('required_documents.id', $requiredDocumentId);
            },
            'requiredDocuments.requiredDocumentDetails' => function ($query) use ($requiredDocumentId, $round) {
                $query->where('required_document_id', $requiredDocumentId)
                    ->where('from_send_doc', U204 . '_' . $round);
            },
            'requiredDocuments.requiredDocumentDetails.planDetailDoc',
            'requiredDocuments.requiredDocumentMiss',
            'planDetails.planDetailDocs',
            'reason',
            'planDocCmts',
            'planDetails.planDetailDistincts',
            'planDetails.planDetailDocs.MTypePlanDoc',
        ]);
        $plans = $this->planService->formatMaterialSupervisor($plans, $round);
        $plans = $plans->sortBy('is_completed');
        // Get Plan Comment
        $planComment = $this->requiredDocumentCommentService->findByCondition([
            'type_comment_step' => RequiredDocumentComment::TYPE_COMMENT_STEP_1,
            'from_send_doc' => RequiredDocumentComment::FROM_SEND_DOC_U204N . '_' . $round,
            'required_document_id' => $requiredDocument->id,
        ])->first();
        // Trademark Table
        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_ADMIN_1, $trademark->id, [
            SHOW_LINK_ANKEN_TOP => true,
        ]);

        $disabledFlag = false;
        if ($requiredDocument->is_confirm == RequiredDocument::IS_CONFIRM) {
            $disabledFlag = true;
        }

        return view('admin.modules.materials.check-supervisor', compact(
            'comparisonTrademarkResult',
            'trademarkPlan',
            'planComment',
            'trademarkTable',
            'plans',
            'round',
            'disabledFlag',
            'requiredDocument'
        ));
    }

    /**
     * Post material-re check supervisor - a204han_n
     *
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function postCheckSupervisor(Request $request, $id)
    {
        $trademarkPlan = $this->trademarkPlanService->find($request->trademark_plan_id ?? 0);
        if (empty($trademarkPlan)) {
            abort(CODE_ERROR_404);
        }

        $comparisonTrademarkResult = $this->comparisonTrademarkResultService->find($id);
        if (empty($comparisonTrademarkResult)) {
            abort(CODE_ERROR_404);
        }

        $comparisonTrademarkResult->load(['trademark.user', 'planCorrespondence']);
        $planCorrespondence = $comparisonTrademarkResult->planCorrespondence;
        if ($trademarkPlan->plan_correspondence_id != $planCorrespondence->id) {
            abort(CODE_ERROR_404);
        }

        $trademark = $comparisonTrademarkResult->trademark;
        if (empty($trademark)) {
            abort(CODE_ERROR_404);
        }

        $requiredDocumentId = $request->required_document_id;
        if (!isset($request->required_document_id) || empty($requiredDocumentId) || !is_numeric($requiredDocumentId)) {
            abort(CODE_ERROR_404);
        }
        $requiredDocument = $this->requiredDocumentService->find($requiredDocumentId);
        if (!$requiredDocument) {
            abort(CODE_ERROR_404);
        }

        if ($requiredDocument->is_confirm == RequiredDocument::IS_CONFIRM) {
            return redirect()->back();
        }

        try {
            DB::beginTransaction();

            $params = $request->all();

            // Update Trademark Plan
            $responseDeadline = Carbon::createFromFormat('Y-m-d', $params['response_deadline'] ?? null)->endOfDay();
            $requiredDocument->update([
                'response_deadline' => $responseDeadline ?? null,
            ]);

            // Update Comment
            $this->requiredDocumentCommentService->updateOrCreate(
                [
                    'type_comment_step' => RequiredDocumentComment::TYPE_COMMENT_STEP_1,
                    'required_document_id' => $params['required_document_id'],
                    'from_send_doc' => RequiredDocumentComment::FROM_SEND_DOC_U204N . '_' . $params['round'],
                ],
                [
                    'content' => $params['content'] ?? '',
                ]
            );

            // Update Plan Detail Docs
            $planDetailDocs = $params['plan_detail_docs'] ?? [];
            foreach ($planDetailDocs as $planDetailDoc) {
                $id = $planDetailDoc['id'] ?? 0;
                $isCompleted = !empty($planDetailDoc['is_completed']);

                $this->requiredDocumentDetailService->updateById($id, [
                    'is_completed' => $isCompleted,
                ]);
            }
            // Update Plans
            $plans = $params['plans'] ?? [];
            foreach ($plans as $plan) {
                $id = $plan['id'] ?? 0;
                $isCompleted = !empty($plan['is_completed']);
                $requiredDocumentPlan = RequiredDocumentPlan::where('plan_id', $id)->where('required_document_id', $plan['required_document_id'])->first();
                if ($requiredDocumentPlan) {
                    $requiredDocumentPlan->update(['is_completed' => $isCompleted]);
                }
            }

            if (!empty($params[A203SHU])) {
                $requiredDocument->update([
                    'is_confirm' => true,
                ]);

                $trademarkPlan->duplicateA203Group();
                $newTrademarkPlan = $this->trademarkPlanService->findByCondition([
                    'plan_correspondence_id' => $trademarkPlan->plan_correspondence_id,
                ])->get()->last();
                $newTrademarkPlan->update([
                    'flag_role' => TrademarkPlan::FLAG_ROLE_2,
                    'is_redirect' => true,
                ]);

                $commonNotice = app(CommonNotice::class);
                $commonNotice->noticeA204hanToA203Shu($comparisonTrademarkResult, $newTrademarkPlan, [
                    'target_page' => route('admin.refusal.materials-re.check.supervisor', [
                        'id' => $comparisonTrademarkResult->id,
                        'trademark_plan_id' => $trademarkPlan->id,
                        'required_document_id' => $requiredDocument->id,
                        'round' => $params['round'] ?? null,
                    ]), // route a204han_n
                ]);

                $redirect = route('admin.refusal.response-plan.edit.supervisor', [
                    'id' => $comparisonTrademarkResult->id,
                    'trademark_plan_id' => $newTrademarkPlan->id,
                ]);
            } elseif (!empty($params[SAVE])) {
                $redirect = route('admin.refusal.materials-re.supervisor', [
                    'id' => $comparisonTrademarkResult->id,
                    'trademark_plan_id' => $trademarkPlan->id,
                    'required_document_id' => $params['required_document_id'],
                    'round' => $trademarkPlan->getRound() + 1,
                ]);
            } elseif (!empty($params[SUBMIT])) {
                // Update Trademark Plan
                $trademarkPlan->update([
                    'is_confirm_docs' => true,
                ]);

                $requiredDocument->update([
                    'is_confirm' => true,
                ]);

                // Send Notice
                $this->noticeCheckSupervisor($comparisonTrademarkResult, $trademarkPlan, $params['required_document_id']);

                CommonHelper::setMessage($request, MESSAGE_SUCCESS, __('messages.general.send_to_tantou'));
                $redirect = route('admin.home');
            } else {
                CommonHelper::setMessage($request, MESSAGE_SUCCESS, __('messages.update_success'));
                $redirect = route('admin.refusal.materials-re.check.supervisor', [
                    'id' => $comparisonTrademarkResult->id,
                    'trademark_plan_id' => $trademarkPlan->id,
                    'required_document_id' => $params['required_document_id'],
                    'round' => $params['round']
                ]);
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
     * Notice material-re check supervisor - a204han_n
     *
     * @param Model $comparisonTrademarkResult
     * @param Model $trademarkPlan
     * @return void
     */
    public function noticeCheckSupervisor(Model $comparisonTrademarkResult, Model $trademarkPlan, $requiredDocumentId)
    {
        $trademark = $comparisonTrademarkResult->trademark;

        $explodeFromSendDoc = explode('_', $trademarkPlan->from_send_doc);
        $step = $explodeFromSendDoc[1] ?? 0;

        $targetPage = route('admin.refusal.materials-re.check.supervisor', [
            'id' => $comparisonTrademarkResult->id,
            'trademark_plan_id' => $trademarkPlan->id,
            'required_document_id' => $requiredDocumentId,
        ]);
        $redirectPage = route('admin.refusal.documents.create', [
            'id' => $comparisonTrademarkResult->id,
            'trademark_plan_id' => $trademarkPlan->id,
        ]);

        // Update Notice at no 84
        $stepBeforeNotice = $this->noticeDetailService->findByCondition([
            'completion_date' => null,
        ])->with('notice')->get()
            ->where('type_acc', '<>', NoticeDetail::TYPE_USER)
            ->where('notice.trademark_id', $trademark->id)
            ->where('notice.user_id', $trademark->user_id)
            ->whereIn('notice.flow', [Notice::FLOW_RESPONSE_REASON, Notice::FLOW_RENEWAL_BEFORE_DEADLINE])
            ->filter(function ($item) {
                if ($item->notice->flow == Notice::FLOW_RESPONSE_REASON && $item->notice->step == Notice::STEP_4) {
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

        $responseDeadlineSupervisor = null;
        $responseDeadlineUser = null;
        $machingResult = $comparisonTrademarkResult->machingResult;
        if (!empty($machingResult) && !empty($planCorrespondence)) {
            if ($planCorrespondence->type == PlanCorrespondence::TYPE_2) {
                $responseDeadlineSupervisor = $machingResult->calculateResponseDeadline(-36);
            } else {
                $responseDeadlineSupervisor = $machingResult->calculateResponseDeadline(-31);
            }
        }

        $notice = [
            'flow' => Notice::FLOW_RESPONSE_REASON,
            'step' => Notice::STEP_4,
            'trademark_id' => $trademark->id,
            'user_id' => $trademark->user_id,
            'trademark_info_id' => null,
        ];

        $noticeDetails = [
            // Send Notice Tantou
            [
                'target_id' => null,
                'type_acc' => NoticeDetail::TYPE_MANAGER,
                'target_page' => $targetPage,
                'redirect_page' => $redirectPage,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                'is_action' => true,
                'content' => '拒絶理由通知対応：提出書類作成',
                'attribute' => null,
                'response_deadline' => $responseDeadlineSupervisor,
            ],
            [
                'target_id' => null,
                'type_acc' => NoticeDetail::TYPE_MANAGER,
                'target_page' => $targetPage,
                'redirect_page' => $redirectPage,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                'is_action' => false,
                'content' => '担当者　拒絶理由通知対応：提出書類作成',
                'attribute' => 'お客様へ',
                'response_deadline' => $responseDeadlineSupervisor,
            ],
            // Send Notice User
            [
                'target_id' => $trademark->user_id,
                'type_acc' => NoticeDetail::TYPE_USER,
                'target_page' => $targetPage,
                'redirect_page' => null,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_DEFAULT,
                'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                'is_action' => false,
                'content' => '拒絶理由通知対応：方針案選択/必要資料提出済・返信待ち',
                'attribute' => null,
                'response_deadline' => $responseDeadlineUser,
            ],
            [
                'target_id' => $trademark->user_id,
                'type_acc' => NoticeDetail::TYPE_USER,
                'target_page' => $targetPage,
                'redirect_page' => null,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                'is_action' => false,
                'content' => '拒絶理由通知対応：方針案選択/必要資料提出済・返信待ち',
                'attribute' => null,
                'response_deadline' => $responseDeadlineUser,
            ],
        ];

        $this->noticeService->sendNotice([
            'notices' => $notice,
            'notice_details' => $noticeDetails,
        ]);
    }

    /**
     * Get page material-re supervisor - a204n
     *
     * @param Request $request
     * @param $id
     * @return View
     */
    public function reSupervisor(Request $request, $id): View
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

        $requiredDocumentId = $request->required_document_id;
        if (!isset($requiredDocumentId) && empty($requiredDocumentId) || !is_numeric($requiredDocumentId)) {
            abort(CODE_ERROR_404);
        }
        $requiredDocument = $this->requiredDocumentService->find($requiredDocumentId);
        if (!$requiredDocument) {
            abort(CODE_ERROR_404);
        }
        $plans = $trademarkPlan->plans;
        $round = $request->round;
        if (!isset($round) && empty($round) || !is_numeric($round)) {
            abort(CODE_ERROR_404);
        }

        $roundBeforeFromSendDoc = U204;
        if ($round - 1 >= 1) {
            $roundBeforeFromSendDoc = $roundBeforeFromSendDoc . '_' . ($round - 1);
        }

        $plans->load([
            'requiredDocumentPlans' => function ($query) use ($requiredDocumentId) {
                $query->where('required_document_plans.required_document_id', $requiredDocumentId);
            },
            'requiredDocuments' => function ($query) use ($requiredDocumentId) {
                $query->where('required_documents.id', $requiredDocumentId);
            },
            'requiredDocuments.requiredDocumentDetails' => function ($query) use ($requiredDocumentId, $roundBeforeFromSendDoc) {
                $query->where('required_document_id', $requiredDocumentId)
                    ->where('from_send_doc', $roundBeforeFromSendDoc);
            },
            'requiredDocuments.requiredDocumentDetails.planDetailDoc',
            'requiredDocuments.requiredDocumentMiss',
            'planDetails.planDetailDocs',
            'reason',
            'planDocCmts',
            'planDetails.planDetailDistincts',
            'planDetails.planDetailDocs.MTypePlanDoc',
        ]);

        $plans = $this->planService->formatMaterialSupervisor($plans, $round);
        // Get Plan Comment
        $planComment = $this->requiredDocumentCommentService->findByCondition([
            'type_comment_step' => RequiredDocumentComment::TYPE_COMMENT_STEP_2,
            'from_send_doc' => RequiredDocumentComment::FROM_SEND_DOC_U204N . '_' . $round,
            'required_document_id' => $requiredDocumentId,
        ])->first();
        $planComments = $this->planCommentService->findByCondition([
            'trademark_plan_id' => $trademarkPlan->id,
            'type' => PlanComment::TYPE_1,
            'type_comment_steps' => [
                PlanComment::STEP_6,
                PlanComment::STEP_7,
            ],
        ])->orderBy('created_at', SORT_BY_DESC)->get();
        $planComments = $planComments->filter(function ($item) {
            return !empty($item->content);
        });

        // Trademark Table
        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_ADMIN_1, $trademark->id, [
            SHOW_LINK_ANKEN_TOP => true,
        ]);

        $disabledFlag = false;
        if ($requiredDocument->is_confirm == RequiredDocument::IS_CONFIRM) {
            $disabledFlag = true;
        }

        // Url Back
        $urlBackDefault = route('admin.home');
        $checkUrl = route('admin.refusal.materials-re.supervisor', $comparisonTrademarkResult->id);
        $backUrl = CommonHelper::setBackUrl($urlBackDefault, $checkUrl);

        return view('admin.modules.materials.re-supervisor', compact(
            'comparisonTrademarkResult',
            'requiredDocument',
            'trademarkPlan',
            'planComment',
            'planComments',
            'trademarkTable',
            'plans',
            'backUrl',
            'disabledFlag',
            'requiredDocumentId'
        ));
    }

    /**
     * Post material-re supervisor - a204n
     *
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function postReSupervisor(Request $request, $id): RedirectResponse
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

        if ($trademarkPlan->is_confirm_docs == true) {
            return redirect()->back();
        }

        try {
            DB::beginTransaction();

            $params = $request->all();

            $requiredDocument = $this->requiredDocumentService->find($params['required_document_id']);

            // Update response_deadline
            $responseDeadline = Carbon::createFromFormat('Y-m-d', $params['response_deadline'] ?? null)->endOfDay();
            $requiredDocument->update([
                'response_deadline' => $responseDeadline ?? null,
            ]);

            // Update Comment
            $this->requiredDocumentCommentService->updateOrCreate(
                [
                    'type_comment_step' => RequiredDocumentComment::TYPE_COMMENT_STEP_2,
                    'required_document_id' => $params['required_document_id'],
                    'from_send_doc' => RequiredDocumentComment::FROM_SEND_DOC_U204N . '_' . $params['round'],
                ],
                [
                    'content' => $params['content'] ?? '',
                ]
            );

            // Update Plans
            $plans = $params['plans'] ?? [];
            foreach ($plans as $planId => $plan) {
                $fromSendoc = U204N;
                if ($params['round'] - 1 > 0) {
                    $fromSendoc = $fromSendoc . '_' . $params['round'];
                }
                $this->requiredDocumentMissService->updateOrCreate(
                    [
                        'plan_id' => $planId,
                        'required_document_id' => $params['required_document_id'],
                    ],
                    [
                        'type_comment_step' => RequiredDocumentComment::TYPE_COMMENT_STEP_1,
                        'from_send_doc' => $fromSendoc,
                        'description_docs_miss' => $plan['description_documents_miss'],
                    ]
                );
            }

            if (!empty($params[SUBMIT])) {
                $requiredDocument->update([
                    'is_confirm' => RequiredDocument::IS_CONFIRM,
                ]);
                $requiredDocument->load([
                    'RequiredDocumentDetails',
                    'RequiredDocumentMiss',
                    'RequiredDocumentPlans',
                    'RequiredDocumentComments',
                ]);

                $newRequiredDocument = $requiredDocument->replicate();
                $newRequiredDocument->is_confirm = RequiredDocument::IS_NOT_CONFIRM;
                $newRequiredDocument->is_send = RequiredDocument::IS_NOT_SEND;
                $newRequiredDocument->save();

                $requiredDocumentComments = $requiredDocument->RequiredDocumentComments ?? collect([]);
                foreach ($requiredDocumentComments as $requiredDocumentComment) {
                    $fromSendDoc = $requiredDocumentComment->from_send_doc;
                    $newRequiredDocumentComment = $requiredDocumentComment->replicate();
                    $newRequiredDocumentComment->required_document_id = $newRequiredDocument->id;
                    $newRequiredDocumentComment->from_send_doc = $fromSendDoc;
                    $newRequiredDocumentComment->content = '';
                    $newRequiredDocumentComment->save();
                }

                $requiredDocumentPlans = $requiredDocument->RequiredDocumentPlans ?? collect([]);
                foreach ($requiredDocumentPlans as $requiredDocumentPlan) {
                    $newRequiredDocumentPlan = $requiredDocumentPlan->replicate();
                    $newRequiredDocumentPlan->required_document_id = $newRequiredDocument->id;
                    $newRequiredDocumentPlan->save();
                }

                $requiredDocumentMiss = $requiredDocument->RequiredDocumentMiss ?? collect([]);
                foreach ($requiredDocumentMiss as $requiredDocumentMissData) {
                    $fromSendDoc = $requiredDocumentMissData->from_send_doc;
                    $fromSendDocExplore = explode('_', $fromSendDoc);
                    $from = $fromSendDocExplore[0] ?? U204N;

                    $newRequiredDocumentMiss = $requiredDocumentMissData->replicate();
                    $newRequiredDocumentMiss->required_document_id = $newRequiredDocument->id;
                    $newRequiredDocumentMiss->from_send_doc = $from . '_' . $params['round'];
                    $newRequiredDocumentMiss->description_docs_miss = $requiredDocumentMissData->description_docs_miss;
                    $newRequiredDocumentMiss->save();
                }

                $requiredDocumentDetails = $requiredDocument->RequiredDocumentDetails ?? collect([]);
                foreach ($requiredDocumentDetails as $requiredDocumentDetail) {
                    $fromSendDoc = $requiredDocumentDetail->from_send_doc;
                    $fromSendDocExplore = explode('_', $fromSendDoc);
                    $from = $fromSendDocExplore[0] ?? U204;

                    $newRequiredDocumentDetail = $requiredDocumentDetail->replicate();
                    $newRequiredDocumentDetail->required_document_id = $newRequiredDocument->id;
                    $newRequiredDocumentDetail->from_send_doc = $from . '_' . $params['round'];
                    $newRequiredDocumentDetail->save();
                }

                $this->noticeService->updateComment(
                    Notice::FLOW_RESPONSE_REASON,
                    $params['content'] ?? '',
                    $comparisonTrademarkResult->trademark_id
                );

                // Send Notice
                $this->noticeReSupervisor($comparisonTrademarkResult, $trademarkPlan, $newRequiredDocument->id, $params['round']);

                CommonHelper::setMessage($request, MESSAGE_SUCCESS, __('messages.general.Common_E035'));
                $redirect = route('admin.home');
            } else {
                CommonHelper::setMessage($request, MESSAGE_SUCCESS, __('messages.update_success'));
                $redirect = route('admin.refusal.materials-re.supervisor', [
                    'id' => $comparisonTrademarkResult->id,
                    'trademark_plan_id' => $trademarkPlan->id,
                    'required_document_id' => $params['required_document_id'],
                    'round' => $params['round'] ?? $trademarkPlan->getRound(),
                ]);
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
     * Notice material-re supervisor - a204n
     *
     * @param Model $comparisonTrademarkResult
     * @param Model $trademarkPlan
     * @return void
     */
    public function noticeReSupervisor(Model $comparisonTrademarkResult, Model $trademarkPlan, $requiredDocumentId, $round)
    {
        $trademark = $comparisonTrademarkResult->trademark;
        $requiredDocument = $this->requiredDocumentService->find($requiredDocumentId);

        $targetPage = route('admin.refusal.materials-re.supervisor', [
            'id' => $comparisonTrademarkResult->id,
            'trademark_plan_id' => $trademarkPlan->id,
            'required_document_id' => $requiredDocumentId,
            'round' => $trademarkPlan->getRound(),
        ]);
        $redirectPage = route('user.refusal.materials-re.index', [
            'id' => $comparisonTrademarkResult->id,
            'trademark_plan_id' => $trademarkPlan->id,
            'required_document_id' => $requiredDocumentId,
            'round' => $round,
        ]);

        // Update Notice at no 81
        $stepBeforeNotice = $this->noticeDetailService->findByCondition([
            'completion_date' => null,
        ])->with('notice')->get()
            ->where('type_acc', '<>', NoticeDetail::TYPE_USER)
            ->where('notice.trademark_id', $trademark->id)
            ->where('notice.user_id', $trademark->user_id)
            ->whereIn('notice.flow', [Notice::FLOW_RESPONSE_REASON, Notice::FLOW_RENEWAL_BEFORE_DEADLINE])
            ->filter(function ($item) {
                if ($item->notice->flow == Notice::FLOW_RESPONSE_REASON && $item->notice->step == Notice::STEP_4) {
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

        $responseDeadlineSupervisor = null;
        $responseDeadlineUser = null;
        $machingResult = $comparisonTrademarkResult->machingResult;
        if (!empty($machingResult) && !empty($planCorrespondence)) {
            if ($planCorrespondence->type == PlanCorrespondence::TYPE_2) {
                $responseDeadlineUser = $machingResult->calculateResponseDeadline(-32);
            } else {
                $responseDeadlineUser = $machingResult->calculateResponseDeadline(-29);
            }
        }

        $notice = [
            'flow' => Notice::FLOW_RESPONSE_REASON,
            'step' => Notice::STEP_4,
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
                'content' => '拒絶理由通知対応：必要資料依頼連絡済',
                'attribute' => 'お客様へ',
                'response_deadline' => null,
                'completion_date' => Carbon::now(),
            ],
            [
                'target_id' => null,
                'type_acc' => NoticeDetail::TYPE_SUPERVISOR,
                'target_page' => $targetPage,
                'redirect_page' => null,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                'is_action' => false,
                'content' => 'お客様からの回答',
                'attribute' => 'お客様へ',
                'response_deadline' => null,
                'completion_date' => Carbon::now(),
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
                'content' => '拒絶理由通知対応：必要資料提出',
                'attribute' => null,
                'response_deadline' => $responseDeadlineUser,
                'response_deadline_ams' => $requiredDocument->response_deadline ?? null,
            ],
            [
                'target_id' => $trademark->user_id,
                'type_acc' => NoticeDetail::TYPE_USER,
                'target_page' => $targetPage,
                'redirect_page' => $redirectPage,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                'is_action' => false,
                'content' => '拒絶理由通知対応：必要資料提出',
                'attribute' => null,
                'response_deadline' => $responseDeadlineUser,
                'response_deadline_ams' => $requiredDocument->response_deadline ?? null,
            ],
        ];

        $this->noticeService->sendNotice([
            'notices' => $notice,
            'notice_details' => $noticeDetails,
        ]);
    }

    /**
     * Get page no material - a204no_mat
     *
     * @param Request $request
     * @param $id
     * @return View
     */
    public function noMaterial(Request $request, $id): View
    {
        $comparisonTrademarkResult = $this->comparisonTrademarkResultService->find($id);
        if (empty($comparisonTrademarkResult)) {
            abort(CODE_ERROR_404);
        }
        $comparisonTrademarkResult->load(['trademark.trademarkDocuments', 'planCorrespondence']);
        $planCorrespondence = $comparisonTrademarkResult->planCorrespondence;

        $trademarkPlan = $this->trademarkPlanService->find($request->trademark_plan_id ?? 0);

        if (empty($trademarkPlan)) {
            abort(CODE_ERROR_404);
        }

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

        $plans->map(function ($plan) {
            $planDetails = $plan->planDetails ?? collect([]);
            $isChoiceDetail = $planDetails->where('is_choice', true)->first();

            $plan->isChoiceDetail = null;
            $plan->getTypePlanName = null;
            if (!empty($isChoiceDetail)) {
                foreach ($planDetails as $key => $planDetail) {
                    if ($isChoiceDetail->id == $planDetail->id) {
                        $isChoiceDetail->index = $key + 1;
                    }
                }

                $plan->isChoiceDetail = $isChoiceDetail;
                $plan->getTypePlanName = $isChoiceDetail->getTypePlanName();
                $plan->isRequiredTypePlan = $isChoiceDetail->isRequiredTypePlan();
            }

            return $plan;
        });

        // Get Plan Comment
        $planComments = $this->planCommentService->findByCondition([
            'trademark_plan_id' => $trademarkPlan->id,
            'type' => PlanComment::TYPE_1,
            'type_comment_steps' => [
                PlanComment::STEP_6,
                PlanComment::STEP_7,
                PlanComment::STEP_8,
            ],
        ])->orderBy('created_at', SORT_BY_DESC)->get();
        $planComments = $planComments->filter(function ($item) {
            return !empty($item->content);
        });

        // Trademark Table
        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_ADMIN_1, $trademark->id, [
            SHOW_LINK_ANKEN_TOP => true,
        ]);

        return view('admin.modules.materials.no-materials', compact(
            'trademarkPlan',
            'comparisonTrademarkResult',
            'trademarkTable',
            'planComments',
            'plans',
            'products',
        ));
    }

    /**
     * Post material supervisor - a204han
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function postNoMaterial(Request $request, int $id): RedirectResponse
    {
        $comparisonTrademarkResult = $this->comparisonTrademarkResultService->find($id);
        if (empty($comparisonTrademarkResult)) {
            abort(CODE_ERROR_404);
        }
        $comparisonTrademarkResult->load(['planCorrespondence']);
        $planCorrespondence = $comparisonTrademarkResult->planCorrespondence;

        $trademarkPlan = $this->trademarkPlanService->find($request->trademark_plan_id ?? 0);

        if (empty($trademarkPlan)) {
            abort(CODE_ERROR_404);
        }

        if ($trademarkPlan->plan_correspondence_id != $planCorrespondence->id) {
            abort(CODE_ERROR_404);
        }

        try {
            DB::beginTransaction();

            $trademarkPlan->update([
                'is_confirm_docs' => true,
            ]);

            DB::commit();

            return redirect()->route('admin.refusal.documents.create', [
                'id' => $comparisonTrademarkResult->id,
                'trademark_plan_id' => $trademarkPlan->id,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            return redirect()->back();
        }
    }

    /**
     * Get material confirm - a203_204kakunin
     *
     * @param Request $request
     * @param int $id
     * @return View
     */
    public function confirm(Request $request, int $id): View
    {
        $comparisonTrademarkResult = $this->comparisonTrademarkResultService->find($id);
        if (empty($comparisonTrademarkResult)) {
            abort(CODE_ERROR_404);
        }
        $comparisonTrademarkResult->load(['trademark.trademarkDocuments', 'planCorrespondence']);
        $planCorrespondence = $comparisonTrademarkResult->planCorrespondence;

        $trademarkPlan = $this->trademarkPlanService->find($request->trademark_plan_id ?? 0);

        if (empty($trademarkPlan)) {
            abort(CODE_ERROR_404);
        }

        if (empty($planCorrespondence) || $trademarkPlan->plan_correspondence_id != $planCorrespondence->id) {
            abort(CODE_ERROR_404);
        }

        $trademark = $comparisonTrademarkResult->trademark;
        if (empty($trademark)) {
            abort(CODE_ERROR_404);
        }

        // Get Plan
        $trademarkPlan = $trademarkPlan->load([
            'plans.planDetails.mTypePlan.mTypePlanDocs',
            'plans.planDetails.planDetailProducts.mProduct',
            'plans.planDetails.planDetailProducts.planDetailDistinct.mDistinction',
            'plans.planDetails.planDetailProducts.planDetailProductCodes.mCode',

            'plans.planReasons.reason',
            'plans.planDetails.planDetailDocs',
            'plans.reason',
            'plans.planDocCmts' => function ($query) {
                $query->where('type', PlanDocCmt::TYPE_U204);
            },
            'plans.planDetails.planDetailDistincts',
            'plans.planDetails.planDetailDocs.MTypePlanDoc',
        ]);

        $plans = $trademarkPlan->getPlans();
        $plans = $this->planService->formatMaterialSupervisor($plans);
        $products = $trademarkPlan->getProducts()->sortBy('distinction.id');

        $plans->map(function ($plan) {
            $planDetails = $plan->planDetails ?? collect([]);
            $isChoiceDetail = $planDetails->where('is_choice', true)->first();

            $plan->isChoiceDetail = null;
            $plan->getTypePlanName = null;
            if (!empty($isChoiceDetail)) {
                foreach ($planDetails as $key => $planDetail) {
                    if ($isChoiceDetail->id == $planDetail->id) {
                        $isChoiceDetail->index = $key + 1;
                    }
                }

                $plan->isChoiceDetail = $isChoiceDetail;
                $plan->getTypePlanName = $isChoiceDetail->getTypePlanName();
                $plan->isRequiredTypePlan = $isChoiceDetail->isRequiredTypePlan();
            }

            return $plan;
        });

        // Has Required Type Plan
        $totalRequiredTypePlan = 0;
        foreach ($plans as $plan) {
            if ($plan->isRequiredTypePlan == true) {
                $totalRequiredTypePlan++;
            }
        }

        // Get Plan Comment
        $planComments = $this->planCommentService->findByCondition([
            'trademark_plan_id' => $trademarkPlan->id,
            'type' => PlanComment::TYPE_1,
            'type_comment_steps' => [
                PlanComment::STEP_1,
                PlanComment::STEP_2,
                PlanComment::STEP_3,
                PlanComment::STEP_4,
                PlanComment::STEP_5,
            ],
        ])->orderBy('created_at', SORT_BY_DESC)->get();
        $planComments = $planComments->filter(function ($item) {
            return !empty($item->content);
        });

        // Trademark Table
        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_ADMIN_1, $trademark->id, [
            SHOW_LINK_ANKEN_TOP => true,
        ]);

        return view('admin.modules.materials.confirm', compact(
            'trademarkPlan',
            'comparisonTrademarkResult',
            'trademarkTable',
            'planComments',
            'plans',
            'products',
            'totalRequiredTypePlan',
        ));
    }
}
