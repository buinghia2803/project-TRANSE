<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\CommonHelper;
use App\Http\Requests\Admin\RefulsalPreQuestionReSuperVisorRequest;
use App\Models\AppTrademark;
use App\Models\MailTemplate;
use App\Models\MLawsRegulation;
use App\Models\Notice;
use App\Models\NoticeDetail;
use App\Models\PlanCorrespondence;
use App\Models\ReasonComment;
use App\Models\ReasonNo;
use App\Models\Reason;
use App\Models\ReasonQuestion;
use App\Models\ReasonQuestionDetail;
use App\Models\ReasonQuestionNo;
use App\Models\Trademark;
use App\Models\TrademarkDocument;
use App\Models\User;
use App\Services\Common\NoticeService;
use App\Services\Common\TrademarkTableService;
use App\Services\ComparisonTrademarkResultService;
use App\Services\NoticeDetailService;
use App\Services\PlanCorrespondenceService;
use App\Services\ReasonCommentService;
use App\Services\ReasonNoService;
use App\Services\ReasonRefNumProdService;
use App\Services\ReasonService;
use App\Services\TrademarkService;
use App\Services\TrademarkDocumentService;
use App\Services\PlanCorrespondenceProdService;
use App\Services\Common\NoticeService as CommonNoticeService;
use App\Services\AdminService;
use App\Services\MailTemplateService;
use App\Services\ReasonQuestionService;
use App\Services\ReasonQuestionNoService;
use App\Services\ReasonQuestionDetailService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class ComparisonTrademarkResultController extends BaseController
{
    protected $comparisonTrademarkResultService;
    protected $reasonCommentService;
    protected $reasonRefNumProdService;
    protected $noticeService;
    protected $trademarkTableService;
    protected $trademarkService;
    protected $planCorrespondenceService;
    protected $reasonNoService;
    protected $reasonService;
    protected $trademarkDocumentService;
    protected $planCorrespondenceProdService;
    protected $commonNoticeService;
    protected $adminService;
    protected $reasonQuestionService;
    protected $reasonQuestionNoService;
    protected $reasonQuestionDetailService;
    protected $noticeDetailService;
    protected MailTemplateService $mailTemplateService;

    /**
     * Constructor
     *
     * @param ComparisonTrademarkResultService $comparisonTrademarkResultService
     * @param ReasonCommentService $reasonCommentService
     * @param ReasonRefNumProdService $reasonRefNumProdService
     * @param CommonNoticeService $noticeService
     * @param PlanCorrespondenceService $planCorrespondenceService
     * @param ReasonNoService $reasonNoService
     * @param ReasonService $reasonService
     * @param TrademarkTableService $trademarkTableService
     * @param TrademarkService $trademarkService
     * @param TrademarkDocumentService $trademarkDocumentService
     * @param PlanCorrespondenceProdService $planCorrespondenceProdService
     * @param CommonNoticeService $commonNoticeService
     * @param AdminService $adminService
     * @param ReasonQuestionService $reasonQuestionService
     * @param ReasonQuestionNoService $reasonQuestionNoService
     * @param ReasonQuestionDetailService $reasonQuestionDetailService
     * @param NoticeDetailService $noticeDetailService
     * @param MailTemplateService $mailTemplateService
     */
    public function __construct(
        ComparisonTrademarkResultService    $comparisonTrademarkResultService,
        ReasonCommentService                $reasonCommentService,
        ReasonRefNumProdService             $reasonRefNumProdService,
        NoticeService                       $noticeService,
        PlanCorrespondenceService           $planCorrespondenceService,
        ReasonNoService                     $reasonNoService,
        ReasonService                       $reasonService,
        TrademarkTableService               $trademarkTableService,
        TrademarkService                    $trademarkService,
        TrademarkDocumentService            $trademarkDocumentService,
        PlanCorrespondenceProdService       $planCorrespondenceProdService,
        CommonNoticeService                 $commonNoticeService,
        AdminService                        $adminService,
        ReasonQuestionService               $reasonQuestionService,
        ReasonQuestionNoService             $reasonQuestionNoService,
        ReasonQuestionDetailService         $reasonQuestionDetailService,
        NoticeDetailService                 $noticeDetailService,
        MailTemplateService                 $mailTemplateService
    )
    {
        parent::__construct();
        $this->comparisonTrademarkResultService = $comparisonTrademarkResultService;
        $this->reasonCommentService = $reasonCommentService;
        $this->reasonRefNumProdService = $reasonRefNumProdService;
        $this->noticeService = $noticeService;
        $this->trademarkTableService = $trademarkTableService;
        $this->trademarkService = $trademarkService;
        $this->planCorrespondenceService = $planCorrespondenceService;
        $this->reasonNoService = $reasonNoService;
        $this->reasonService = $reasonService;
        $this->trademarkDocumentService = $trademarkDocumentService;
        $this->planCorrespondenceProdService = $planCorrespondenceProdService;
        $this->commonNoticeService = $commonNoticeService;
        $this->adminService = $adminService;
        $this->reasonQuestionService = $reasonQuestionService;
        $this->reasonQuestionNoService = $reasonQuestionNoService;
        $this->reasonQuestionDetailService = $reasonQuestionDetailService;
        $this->noticeDetailService = $noticeDetailService;
        $this->mailTemplateService = $mailTemplateService;

        // Check permission
        $this->middleware('permission:comparison_trademark_result.postCreateReason')->only(['postCreateReason']);
        $this->middleware('permission:comparison_trademark_result.postCreateReasonSupervisor')->only(['postCreateReasonSupervisor']);
        $this->middleware('permission:comparison_trademark_result.postCreateExamine')->only(['postCreateExamine']);
        $this->middleware('permission:comparison_trademark_result.createPreQuestion')->only(['createPreQuestion']);
        $this->middleware('permission:comparison_trademark_result.postCreateExamineSupervisor')->only(['postCreateExamineSupervisor']);
        $this->middleware('permission:comparison_trademark_result.postEditReason')->only(['postEditReason']);
        $this->middleware('permission:comparison_trademark_result.postEditExamineSupervisor')->only(['postEditExamineSupervisor']);
        $this->middleware('permission:comparison_trademark_result.postSupervisor')->only(['postSupervisor']);
        $this->middleware('permission:comparison_trademark_result.postEditReasonSupervisor')->only(['postEditReasonSupervisor']);
        $this->middleware('permission:comparison_trademark_result.savePreQuestionReShow')->only(['savePreQuestionReShow']);
    }

    /**
     * Pre-question
     *
     * @param int $id
     * @return mixed
     */
    public function preQuestionIndex(int $id)
    {
        $comparisonTrademarkResult = $this->comparisonTrademarkResultService->find($id);
        if (!$comparisonTrademarkResult) {
            abort(404);
        }

        $relation = $comparisonTrademarkResult->load([
            'trademark',
            'planCorrespondence.reasonQuestion.reasonQuestionNos.reasonQuestionDetails',
            'planCorrespondence.reasonComments',
        ]);

        $trademark = $relation->trademark;
        if (!$trademark) {
            abort(404);
        }

        $planCorrespondence = $relation->planCorrespondence;
        if (!$planCorrespondence) {
            abort(404);
        }

        $reasonQuestion = $planCorrespondence->reasonQuestion ?? null;
        $reasonQuestionNo = null;
        $reasonQuestionDetails = [];
        if ($reasonQuestion) {
            $reasonQuestionNo = $reasonQuestion->reasonQuestionNos->first();
            $reasonQuestionDetails = $reasonQuestionNo ? $reasonQuestionNo->reasonQuestionDetails : [];
        }

        // Get Comment
        $reasonComments = $planCorrespondence->reasonComments ?? collect([]);
        $reasonComment = $reasonComments->where('type', ReasonComment::TYPE_1)
            ->where('type_comment_step', ReasonComment::STEP_2)
            ->first();

        $checkFlagRole = false;
        if ($reasonQuestionNo && $reasonQuestionNo->flag_role == ReasonQuestion::FLAG_ROLE_2) {
            $checkFlagRole = true;
        }

        $trademarkDocuments = $this->trademarkDocumentService->getByCondition([
            'trademark_id' => $trademark->id,
            'type' => TrademarkDocument::TYPE_1,
            'flow' => Notice::FLOW_RESPONSE_REASON,
            'step' => Notice::STEP_1,
        ])->pluck('url');

        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_ADMIN_1, $trademark->id, [
            SHOW_LINK_ANKEN_TOP => true,
        ]);

        return view('admin.modules.comparison_trademark_results.pre_question_index', compact(
            'trademark',
            'reasonComment',
            'checkFlagRole',
            'trademarkTable',
            'reasonQuestion',
            'reasonQuestionNo',
            'trademarkDocuments',
            'planCorrespondence',
            'reasonQuestionDetails',
            'comparisonTrademarkResult'
        ));
    }

    /**
     * Create Examine.
     *
     * @param Request $request
     * @return mixed
     */
    public function createPreQuestion(Request $request)
    {
        try {
            DB::beginTransaction();

            $params = $request->all();
            $params['user_response_deadline'] = Carbon::createFromFormat('Y年m月d日', $request->user_response_deadline)->toDateTimeString();
            $comparisonTrademarkResult = $this->comparisonTrademarkResultService->find($params['id']);
            if (!$comparisonTrademarkResult) {
                abort(404);
            }

            $relation = $comparisonTrademarkResult->load([
                'trademark',
                'planCorrespondence',
                'machingResult',
            ]);

            $trademark = $relation->trademark;
            if (!$trademark) {
                abort(404);
            }

            $planCorrespondence = $relation->planCorrespondence;
            if (!$planCorrespondence) {
                abort(404);
            }

            $reasonQuestion = $this->reasonQuestionService->updateOrCreate([
                'plan_correspondence_id' => $planCorrespondence->id,
            ], [
                'updated_at' => now(),
            ]);

            $reasonQuestionNo = $this->reasonQuestionNoService->updateOrCreate([
                'reason_question_id' => $reasonQuestion->id,
            ], [
                'admin_id' => auth()->guard(ADMIN_ROLE)->user()->id,
                'user_response_deadline' => $params['user_response_deadline'],
                'flag_role' => isset($params['submit']) ? ReasonQuestion::FLAG_ROLE_1 : ReasonQuestion::FLAG_ROLE_2,
                'question_status' => $params['question_status'] ?? ReasonQuestion::QUESTION_STATUS_NOT_REQUIRED,
            ]);

            foreach ($params['data'] ?? [] as $value) {
                $this->reasonQuestionDetailService->updateOrCreate([
                    'id' => $value['id'],
                    'reason_question_no_id' => $reasonQuestionNo->id,
                ], [
                    'reason_question_id' => $reasonQuestion->id,
                    'question' => $value['question'],
                ]);
            }

            $this->reasonCommentService->updateOrCreate([
                'plan_correspondence_id' => $planCorrespondence->id,
            ], [
                'admin_id' => auth()->guard(ADMIN_ROLE)->user()->id,
                'content' => $params['content'],
                'type' => ReasonComment::TYPE_1,
                'type_comment_step' => ReasonComment::STEP_2,
            ]);

            if (isset($params['submitRedirect'])) {
                $this->sendNoticeSubmitRedirect($params, $trademark, $comparisonTrademarkResult, $planCorrespondence, $reasonQuestionNo);
            }

            DB::commit();

            if (isset($params['submit'])) {
                return redirect()->back()->with('message', __('messages.general.update_success'))->withInput();
            } elseif (isset($params['submitRedirect'])) {
                return redirect()->route('admin.home')->with('message', __('messages.general.tantou_send_to_seki'))->withInput();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            CommonHelper::setMessage($request, MESSAGE_ERROR, __('messages.error'));
            return redirect()->back();
        }
    }

    /**
     * Send Notice Submit Redirect
     *
     * @param array $params
     * @param Model $trademark
     * @param Model $comparisonTrademarkResult
     * @param Model $planCorrespondence
     * @return void
     */
    public function sendNoticeSubmitRedirect(array $params, Model $trademark, Model $comparisonTrademarkResult, Model $planCorrespondence, Model $reasonQuestionNo)
    {
        //update comment notices
        $this->noticeService->updateComment(
            Notice::FLOW_RESPONSE_REASON,
            $params['content'] ?? '',
            $trademark->id
        );

        // Update Notice at a202 (No 40, 42: F G)
        $stepBeforeNotice = $this->noticeDetailService->findByCondition([
            'completion_date' => null,
        ])->with('notice')->get()
            ->where('type_acc', '<>', NoticeDetail::TYPE_USER)
            ->where('notice.trademark_id', $trademark->id)
            ->where('notice.user_id', $trademark->user_id)
            ->whereIn('notice.flow', [Notice::FLOW_RESPONSE_REASON, Notice::FLOW_RENEWAL_BEFORE_DEADLINE])
            ->filter(function ($item) {
                if ($item->notice->flow == Notice::FLOW_RESPONSE_REASON && in_array($item->notice->step, [Notice::STEP_1, Notice::STEP_2])) {
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

        $notice = [
            'trademark_id' => $trademark->id,
            'trademark_info_id' => null,
            'user_id' => $trademark->user_id,
            'flow' => Notice::FLOW_RESPONSE_REASON,
            'step' => Notice::STEP_2,
        ];

        $idSeki = $this->adminService->findByCondition([
            'role' => ROLE_SUPERVISOR,
        ])->first()->id;
        $targetPage = route('admin.refusal.pre-question.index', ['id' => $comparisonTrademarkResult->id]);
        $redirectPage = route('admin.refusal.pre-question.supervisor', ['id' => $comparisonTrademarkResult->id, 'reason_question_no' => $reasonQuestionNo->id]);

        // Set response deadline
        $responseDeadline = null;
        $machingResult = $comparisonTrademarkResult->machingResult;
        $responseDeadline = $machingResult->calculateResponseDeadline(-9);
        if (!empty($machingResult) && $planCorrespondence->type == PlanCorrespondence::TYPE_2) {
            $responseDeadline = $machingResult->calculateResponseDeadline(-14);
        }

        $noticeDetails = [
            [
                'target_id' => null,
                'type_acc' => NoticeDetail::TYPE_SUPERVISOR,
                'target_page' => $targetPage,
                'redirect_page' => $redirectPage,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                'is_action' => true,
                'content' => '責任者　拒絶理由通知対応：事前質問修正',
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
                'content' => '責任者　拒絶理由通知対応：事前質問修正',
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
     * Ajax Delete Question
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxDeleteQuestion(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $this->reasonQuestionDetailService->find($request->id)->delete();
            DB::commit();

            return response()->json([
                'message' => __('messages.update_success'),
            ], CODE_SUCCESS_200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            return response()->json([
                'message' => __('messages.update_fail'),
            ], CODE_ERROR_500);
        }
    }

    /**
     * Pre-Question Supervisor.
     *
     * @param int $id
     * @return mixed
     */
    public function preQuestionSupervisor(Request $request, int $id)
    {
        $reasonQuestionNoId = $request->reason_question_no;
        if (!isset($reasonQuestionNoId)) {
            abort(404);
        }

        $comparisonTrademarkResult = $this->comparisonTrademarkResultService->find($id);
        if (!$comparisonTrademarkResult) {
            abort(404);
        }

        $relation = $comparisonTrademarkResult->load([
            'trademark',
            'planCorrespondence.reasonQuestion.reasonQuestionNos.reasonQuestionDetails',
            'planCorrespondence.reasonComment',
        ]);

        $trademark = $relation->trademark;
        if (!$trademark) {
            abort(404);
        }

        $planCorrespondence = $relation->planCorrespondence;
        if (!$planCorrespondence) {
            abort(404);
        }

        $reasonQuestion = $planCorrespondence->reasonQuestion ?? null;
        $reasonQuestionNo = null;
        $reasonQuestionDetails = [];
        if ($reasonQuestion) {
            $reasonQuestionNo = $reasonQuestion->reasonQuestionNos->where('id', $reasonQuestionNoId)->first();
            $reasonQuestionDetails = $reasonQuestionNo ? $reasonQuestionNo->reasonQuestionDetails : [];
        }
        $reasonComment = $planCorrespondence->reasonComment ?? null;

        if (!$reasonQuestionNo) {
            abort(404);
        }

        $checkIsConfirm = false;
        if (!empty($reasonQuestionNo->is_confirm) && $reasonQuestionNo->is_confirm == true) {
            $checkIsConfirm = true;
        }

        $trademarkDocuments = $this->trademarkDocumentService->getByCondition([
            'trademark_id' => $trademark->id,
            'type' => TrademarkDocument::TYPE_1,
            'flow' => Notice::FLOW_RESPONSE_REASON,
            'step' => Notice::STEP_1,
        ])->pluck('url');

        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_ADMIN_1, $trademark->id, [
            SHOW_LINK_ANKEN_TOP => true,
            A202S => true
        ]);

        return view('admin.modules.comparison_trademark_results.supervisor', compact(
            'trademark',
            'reasonComment',
            'trademarkTable',
            'reasonQuestion',
            'checkIsConfirm',
            'reasonQuestionNo',
            'reasonQuestionNoId',
            'trademarkDocuments',
            'planCorrespondence',
            'reasonQuestionDetails',
            'comparisonTrademarkResult'
        ));
    }

    /**
     * Post Supervisor.
     *
     * @param Request $request
     * @return mixed
     */
    public function postSupervisor(Request $request)
    {
        try {
            DB::beginTransaction();

            $params = $request->all();
            $params['user_response_deadline'] = Carbon::createFromFormat('Y年m月d日', $request->user_response_deadline)->toDateTimeString();

            $comparisonTrademarkResult = $this->comparisonTrademarkResultService->find($params['id']);
            if (!$comparisonTrademarkResult) {
                abort(404);
            }

            $relation = $comparisonTrademarkResult->load([
                'trademark',
                'planCorrespondence.reasonQuestion.reasonQuestionNos',
                'machingResult',
                'trademark.user',
            ]);

            $trademark = $relation->trademark;
            if (!$trademark) {
                abort(404);
            }

            $planCorrespondence = $relation->planCorrespondence;
            if (!$planCorrespondence) {
                abort(404);
            }

            $isConfirmReasonQuestion = false;
            if (isset($params['submit']) || isset($params['submit_no_question'])) {
                $isConfirmReasonQuestion = true;
            }
            $questionStatus = $params['question_status'] ?? ReasonQuestion::QUESTION_STATUS_NOT_REQUIRED;
            if (isset($params['submit_no_question'])) {
                $questionStatus = ReasonQuestion::QUESTION_STATUS_NOT_REQUIRED;
            }

            $reasonQuestion = $planCorrespondence->reasonQuestion ?? null;
            $reasonQuestionNo = $reasonQuestion ? $reasonQuestion->reasonQuestionNos->where('id', $params['reason_question_no'])->first() : null;

            $reasonQuestionNo->update([
                'admin_id' => auth()->guard(ADMIN_ROLE)->user()->id,
                'user_response_deadline' => $params['user_response_deadline'],
                'question_status' => $questionStatus,
                'is_confirm' => $isConfirmReasonQuestion
            ]);

            if (isset($params['draft']) || isset($params['submit_no_question'])) {
                foreach ($params['data'] ?? [] as $value) {
                    $questionEdit = $value['question_edit_hidden'];
                    if (isset($value['question_edit'])) {
                        $questionEdit = $value['question_edit'];
                    }
                    $this->reasonQuestionDetailService->updateOrCreate([
                        'id' => $value['id'],
                        'reason_question_no_id' => $reasonQuestionNo->id,
                    ], [
                        'reason_question_id' => $reasonQuestion->id,
                        'question_edit' => $questionEdit,
                        'question_decision' => $value['question_decision'],
                        'is_confirm' => isset($value['is_confirm']) ? $value['is_confirm'] : 0,
                    ]);
                }
            } elseif (isset($params['submit'])) {
                foreach ($params['data'] ?? [] as $value) {
                    $this->reasonQuestionDetailService->updateOrCreate([
                        'id' => $value['id'],
                        'reason_question_no_id' => $reasonQuestionNo->id,
                    ], [
                        'reason_question_id' => $reasonQuestion->id,
                        'question' => $value['question_decision'],
                        'question_edit' => null,
                        'question_decision' => null,
                        'is_confirm' => isset($value['is_confirm']) ? $value['is_confirm'] : 0,
                    ]);
                }
            }

            $this->reasonCommentService->updateOrCreate([
                'plan_correspondence_id' => $planCorrespondence->id,
            ], [
                'content' => $params['content'],
                'type' => ReasonComment::TYPE_1,
                'type_comment_step' => ReasonComment::STEP_2,
            ]);

            if (isset($params['submit'])) {
                $this->sendNoticeSupervisorSubmit($params, $trademark, $comparisonTrademarkResult, $planCorrespondence, $reasonQuestionNo);

                // send mail a202s
                $dataMail = [
                    'from_page' => A202S,
                    'user' => $trademark->user
                ];
                $this->mailTemplateService->sendMailRequest($dataMail, MailTemplate::TYPE_OTHER, MailTemplate::GUARD_TYPE_ADMIN);
            } elseif (isset($params['submit_no_question'])) {
                $this->sendNoticeSupervisorSubmitNoQuestion($params, $trademark, $comparisonTrademarkResult, $planCorrespondence, $reasonQuestionNo);
            }

            DB::commit();

            if (isset($params['draft'])) {
                return redirect()->back()->with('message', __('messages.general.update_success'))->withInput();
            } elseif (isset($params['submit'])) {
                return redirect()->route('admin.home')->with('message', __('messages.general.seki_submit_to_user'))->withInput();
            } elseif (isset($params['submit_no_question'])) {
                return redirect()->route('admin.home')->with('message', __('messages.general.Common_E050'))->withInput();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            return redirect()->back();
        }
    }

    /**
     * Send Notice Supervisor.
     *
     * @param array $params
     * @param Model $trademark
     * @param Model $comparisonTrademarkResult
     * @param Model $planCorrespondence
     * @return void
     */
    public function sendNoticeSupervisorSubmit(array $params, Model $trademark, Model $comparisonTrademarkResult, Model $planCorrespondence, Model $reasonQuestionNo)
    {
        //update comment notices
        $this->noticeService->updateComment(
            Notice::FLOW_RESPONSE_REASON,
            $params['content'] ?? '',
            $trademark->id ?? 0
        );

        // Update Notice at a202s (No 51: F G)
        $stepBeforeNotice = $this->noticeDetailService->findByCondition([
            'completion_date' => null,
        ])->with('notice')->get()
            ->where('type_acc', '<>', NoticeDetail::TYPE_USER)
            ->where('notice.trademark_id', $trademark->id)
            ->where('notice.user_id', $trademark->user_id)
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

        $notice = [
            'trademark_id' => $trademark->id,
            'trademark_info_id' => null,
            'user_id' => $trademark->user_id,
            'flow' => Notice::FLOW_RESPONSE_REASON,
            'step' => Notice::STEP_2,
        ];

        $targetPage = route('admin.refusal.pre-question.supervisor', ['id' => $comparisonTrademarkResult->id, 'reason_question_no' => $reasonQuestionNo->id]);
        $redirectPage = route('user.refusal.pre-question.reply', ['id' => $comparisonTrademarkResult->id, 'reason_question_no' => $reasonQuestionNo->id]);
        // Set response deadline
        $responseDeadline = null;
        $responseDeadlineUser = null;
        $machingResult = $comparisonTrademarkResult->machingResult;
        $responseDeadline = $machingResult->calculateResponseDeadline(-9);
        $responseDeadlineUser = $machingResult->calculateResponseDeadline(-11);
        if (!empty($machingResult) && $planCorrespondence->type == PlanCorrespondence::TYPE_2) {
            $responseDeadline = $machingResult->calculateResponseDeadline(-14);
            $responseDeadlineUser = $machingResult->calculateResponseDeadline(-16);
        }

        $noticeDetails = [
            //A-000anken_top
            [
                'target_id' => null,
                'type_acc' => NoticeDetail::TYPE_SUPERVISOR,
                'target_page' => $targetPage,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                'content' => '責任者　拒絶理由通知対応：事前質問連絡済',
                'attribute' => 'お客様へ',
                'response_deadline' => $responseDeadline,
                'completion_date' => now(),
            ],
            //U-000top
            [
                'target_id' => $trademark->user_id,
                'type_acc' => NoticeDetail::TYPE_USER,
                'target_page' => $targetPage,
                'redirect_page' => $redirectPage,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                'is_action' => true,
                'content' => '拒絶理由通知対応：事前質問',
                'response_deadline' => $responseDeadlineUser,
                'response_deadline_ams' => $reasonQuestionNo->user_response_deadline ?? null,
            ],
            //U-000anken_top
            [
                'target_id' => $trademark->user_id,
                'type_acc' => NoticeDetail::TYPE_USER,
                'target_page' => $targetPage,
                'redirect_page' => $redirectPage,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                'content' => '拒絶理由通知対応：事前質問',
                'response_deadline' => $responseDeadlineUser,
                'response_deadline_ams' => $reasonQuestionNo->user_response_deadline ?? null,
            ],
        ];

        $this->noticeService->sendNotice([
            'notices' => $notice,
            'notice_details' => $noticeDetails,
        ]);
    }

    /**
     * Send Notice Supervisor.
     *
     * @param array $params
     * @param Model $trademark
     * @param Model $comparisonTrademarkResult
     * @param Model $planCorrespondence
     * @return void
     */
    public function sendNoticeSupervisorSubmitNoQuestion(array $params, Model $trademark, Model $comparisonTrademarkResult, Model $planCorrespondence, Model $reasonQuestionNo)
    {
        //update comment notices
        $this->noticeService->updateComment(
            Notice::FLOW_RESPONSE_REASON,
            $params['content'] ?? '',
            $trademark->id ?? 0
        );

        // Update Notice at a202s (No 51: F G)
        $stepBeforeNotice = $this->noticeDetailService->findByCondition([
            'completion_date' => null,
        ])->with('notice')->get()
            ->where('type_acc', '<>', NoticeDetail::TYPE_USER)
            ->where('notice.trademark_id', $trademark->id)
            ->where('notice.user_id', $trademark->user_id)
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

        $notice = [
            'trademark_id' => $trademark->id,
            'trademark_info_id' => null,
            'user_id' => $trademark->user_id,
            'flow' => Notice::FLOW_RESPONSE_REASON,
            'step' => Notice::STEP_3,
        ];

        $idTantou = $this->adminService->findByCondition([
            'role' => ROLE_MANAGER,
        ])->first()->id;
        $targetPage = route('admin.refusal.pre-question.supervisor', ['id' => $comparisonTrademarkResult->id, 'reason_question_no' => $reasonQuestionNo->id]);
        $redirectPage = route('admin.refusal.response-plan.index', ['id' => $comparisonTrademarkResult->id]);
        // Set response deadline
        $responseDeadlineTantou = null;
        $machingResult = $comparisonTrademarkResult->machingResult;
        $responseDeadlineTantou = $machingResult->calculateResponseDeadline(-15);
        if (!empty($machingResult) && $planCorrespondence->type == PlanCorrespondence::TYPE_2) {
            $responseDeadlineTantou = $machingResult->calculateResponseDeadline(-21);
        }

        $noticeDetails = [
            //A-000top
            [
                'target_id' => null,
                'type_acc' => NoticeDetail::TYPE_MANAGER,
                'target_page' => $targetPage,
                'redirect_page' => $redirectPage,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                'is_action' => true,
                'content' => '担当者　拒絶理由通知対応：方針案作成',
                'response_deadline' => $responseDeadlineTantou,
            ],
            //A-000anken_top
            [
                'target_id' => null,
                'type_acc' => NoticeDetail::TYPE_MANAGER,
                'target_page' => $targetPage,
                'redirect_page' => $redirectPage,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                'content' => '担当者　拒絶理由通知対応：方針案作成',
                'response_deadline' => $responseDeadlineTantou,
            ],
        ];

        $this->noticeService->sendNotice([
            'notices' => $notice,
            'notice_details' => $noticeDetails,
        ]);
    }

    /**
     * Create Examine.
     *
     * @param Request $request
     * @param int $id
     * @return View
     */
    public function createExamine(Request $request, int $id): View
    {
        $comparisonTrademarkResult = $this->comparisonTrademarkResultService->find($id);
        if (empty($comparisonTrademarkResult)) {
            abort(CODE_ERROR_404);
        }

        $comparisonTrademarkResult = $comparisonTrademarkResult->load([
            'planCorrespondences.reasonComments',
            'planCorrespondences.reasonNos',
        ]);

        $planCorrespondences = $comparisonTrademarkResult->planCorrespondences;
        $planCorrespondence = $planCorrespondences->last();
        if (empty($planCorrespondence)) {
            abort(CODE_ERROR_404);
        }

        // Get reason no
        $reasonNoId = $request->reason_no_id;
        if (!empty($reasonNoId)) {
            $reasonNo = $planCorrespondence->reasonNos->where('id', $reasonNoId)->first();
        } else {
            $reasonNo = $planCorrespondence->reasonNos->last();
        }

        if (!$reasonNo) {
            abort(CODE_ERROR_404);
        }
        // Get products and reasons
        $products = $comparisonTrademarkResult->getProducts()->groupBy('mDistinction.name');

        $reasons = $this->reasonService->findByCondition(['reason_no_id' => $reasonNo->id])->get();

        // Get reason comment
        $reasonComment = $this->reasonCommentService->findByCondition([
            'plan_correspondence_id' => $planCorrespondence->id,
            'type' => ReasonComment::TYPE_1,
            'type_comment_step' => ReasonComment::STEP_1,
        ])->first();

        $hasSendSeki = false;
        if (!empty($reasonNo)) {
            if ($reasonNo->flag_role == ReasonNo::FLAG_2) {
                $hasSendSeki = true;
            }
        }

        // Url Back
        $urlBackDefault = route('admin.home');
        $checkUrl = route('admin.refusal.eval-report.create-examine', $comparisonTrademarkResult->id);
        $backUrl = CommonHelper::setBackUrl($urlBackDefault, $checkUrl);

        return view('admin.modules.comparison_trademark_results.create-examine', compact(
            'comparisonTrademarkResult',
            'products',
            'reasons',
            'reasonComment',
            'hasSendSeki',
            'backUrl',
            'reasonNo'
        ));
    }

    /**
     * Post Create Examine.
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function postCreateExamine(Request $request, int $id): RedirectResponse
    {
        $comparisonTrademarkResult = $this->comparisonTrademarkResultService->find($id);
        if (empty($comparisonTrademarkResult)) {
            abort(CODE_ERROR_404);
        }

        $comparisonTrademarkResult = $comparisonTrademarkResult->load([
            'planCorrespondence', 'trademark.user',
        ]);

        $planCorrespondence = $comparisonTrademarkResult->planCorrespondence;
        if (empty($planCorrespondence)) {
            abort(CODE_ERROR_404);
        }

        try {
            DB::beginTransaction();

            $currentAdmin = Auth::guard('admin')->user();

            $reasonNo = $this->reasonNoService->find($request->reason_no_id);
            if (!empty($reasonNo)) {
                if ($reasonNo->flag_role == ReasonNo::FLAG_2) {
                    return redirect()->back();
                }
            }

            // Create/Update Reason Comment
            $this->reasonCommentService->updateOrCreate([
                'plan_correspondence_id' => $planCorrespondence->id,
                'type' => ReasonComment::TYPE_1,
                'type_comment_step' => ReasonComment::STEP_1,
            ], [
                'admin_id' => $currentAdmin->id,
                'content' => $request->content ?? '',
            ]);

            $products = $request->products ?? collect();
            $reasonRefNumProdArray = [];
            foreach ($products as $product) {
                $product['admin_id'] = $currentAdmin->id;
                $product['comment_patent_agent'] = $product['comment_patent_agent'] ?? null;
                $product['rank'] = $product['rank'] ?? null;
                $product['vote_reason_id'] = json_encode($product['vote_reason_id'] ?? []);
                $product['is_choice'] = true;

                $reasonRefNumProdArray[] = $this->reasonRefNumProdService->updateOrCreate([
                    'plan_correspondence_prod_id' => $product['plan_correspondence_prod_id'] ?? null,
                    'reason_no_id' => $request->reason_no_id ?? 0
                ], $product);
            }

            $redirect = redirect()->back()->getTargetUrl();
            if (isset($request->submit) || isset($request->submit_supervisor)) {
                // Update flag_role of reasonNo
                if (!empty($reasonNo)) {
                    $reasonNo->update([
                        'flag_role' => ReasonNo::FLAG_2,
                        'round' => 1
                    ]);
                }

                $this->noticeService->updateComment(
                    Notice::FLOW_RESPONSE_REASON,
                    $request->content ?? '',
                    $comparisonTrademarkResult->trademark_id
                );

                // Send Notice
                if (isset($request->submit)) {
                    $this->noticeCreateExamineManager($comparisonTrademarkResult, $reasonNo);
                } elseif (isset($request->submit_supervisor)) {
                    if (!empty($reasonNo)) {
                        $reasonNo->update([
                            'is_confirm' => true,
                        ]);
                    }

                    foreach ($reasonRefNumProdArray as $reasonRefNumProd) {
                        $prod = $reasonRefNumProd->load(['planCorrespondenceProd']);
                        $planCorrespondenceProd = $prod->planCorrespondenceProd ?? null;

                        if (!empty($planCorrespondenceProd) && $planCorrespondenceProd->is_register == true) {
                            $planCorrespondenceProd->update(['completed_evaluation' => true]);
                        }
                    }

                    $this->noticeCreateExamineSupervisor($comparisonTrademarkResult, $reasonNo);
                }

                $redirect = route('admin.home');
            }

            DB::commit();

            if (isset($request->submit_supervisor)) {
                CommonHelper::setMessage($request, MESSAGE_SUCCESS, __('messages.general.Common_E035'));
            } elseif (isset($request->submit)) {
                CommonHelper::setMessage($request, MESSAGE_SUCCESS, __('messages.general.Common_E048'));
            } else {
                CommonHelper::setMessage($request, MESSAGE_SUCCESS, __('messages.update_success'));
            }

            return redirect($redirect);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            CommonHelper::setMessage($request, MESSAGE_ERROR, __('messages.error'));
            return redirect()->back();
        }
    }

    /**
     * Send Notice Create Examine at Manager
     *
     * @param Model $comparisonTrademarkResult
     * @return void
     */
    public function noticeCreateExamineManager(Model $comparisonTrademarkResult, $reasonNo)
    {
        $comparisonTrademarkResult = $comparisonTrademarkResult->load([
            'trademark',
            'machingResult',
        ]);

        $trademark = $comparisonTrademarkResult->trademark;

        $targetPage = route('admin.refusal.eval-report.create-examine', [
            'id' => $comparisonTrademarkResult->id,
            'reason_no_id' => $reasonNo->id,
        ]);
        $redirectPage = route('admin.refusal.eval-report.create-reason.supervisor', [
            'id' => $comparisonTrademarkResult->id,
            'reason_no_id' => $reasonNo->id,
        ]);

        // Update Notice at no 27|29|31|33|34
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
                'completion_date' => Carbon::now(),
            ]);
        });

        // Set response deadline
        $responseDeadline = null;
        $machingResult = $comparisonTrademarkResult->machingResult;
        if (!empty($machingResult)) {
            $responseDeadline = $machingResult->calculateResponseDeadline(-4);
        }

        $notice = [
            'flow' => Notice::FLOW_RESPONSE_REASON,
            'step' => Notice::STEP_1,
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
                'content' => '責任者　拒絶理由通知対応：登録可能性評価レポート　理由数設定・作成',
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
                'content' => '責任者　拒絶理由通知対応：登録可能性評価レポート　理由数設定・作成',
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
     * Send Notice Create Examine at Supervisor
     *
     * @param Model $comparisonTrademarkResult
     * @return void
     */
    public function noticeCreateExamineSupervisor(Model $comparisonTrademarkResult, Model $reasonNo)
    {
        $comparisonTrademarkResult = $comparisonTrademarkResult->load([
            'trademark',
            'machingResult',
        ]);

        $trademark = $comparisonTrademarkResult->trademark;

        $targetPage = route('admin.refusal.eval-report.create-examine', $comparisonTrademarkResult->id);
        $redirectPage = route('user.refusal.select-eval-report-show', ['id' => $comparisonTrademarkResult->id, 'reason_no_id' => $reasonNo->id ?? '']);

        // Set response deadline
        $responseDeadlineBefore3Day = null;
        $responseDeadlineBefore7Day = null;
        $machingResult = $comparisonTrademarkResult->machingResult;
        if (!empty($machingResult)) {
            $responseDeadlineBefore3Day = $machingResult->calculateResponseDeadline(-3);
            $responseDeadlineBefore7Day = $machingResult->calculateResponseDeadline(-7);
        }

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
                'completion_date' => Carbon::now(),
            ]);
        });
        $notice = [
            'flow' => Notice::FLOW_RESPONSE_REASON,
            'step' => Notice::STEP_1,
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
                'content' => '拒絶理由通知対応：登録可能性評価レポート連絡済',
                'attribute' => 'お客様へ',
                'response_deadline' => $responseDeadlineBefore3Day ?? null,
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
                'content' => '拒絶理由通知対応：登録可能性評価レポート＆拒絶理由通知対応お申込み',
                'attribute' => null,
                'response_deadline' => $responseDeadlineBefore7Day ?? null,
            ],
            [
                'target_id' => $trademark->user_id,
                'type_acc' => NoticeDetail::TYPE_USER,
                'target_page' => $targetPage,
                'redirect_page' => $redirectPage,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                'is_action' => false,
                'content' => '拒絶理由通知対応：登録可能性評価レポート＆拒絶理由通知対応お申込み',
                'attribute' => null,
                'response_deadline' => $responseDeadlineBefore7Day ?? null,
            ],
        ];

        $this->noticeService->sendNotice([
            'notices' => $notice,
            'notice_details' => $noticeDetails,
        ]);
    }

    /**
     * Create Reason Supervisor
     *
     * @param Request $request
     * @param int $id
     * @return View
     */
    public function createReasonSupervisor(Request $request, int $id): View
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
        $appTrademark = $trademark->appTrademark;
        $trademarkDocuments = $this->trademarkDocumentService->getByCondition([
            'trademark_id' => $trademark->id,
            'type' => TrademarkDocument::TYPE_1,
            'flow' => Notice::FLOW_RESPONSE_REASON,
            'step' => Notice::STEP_1,
        ])->pluck('url');
        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_ADMIN_1, $trademark->id);
        $planCorrespondence = $comparisonTrademarkResult->planCorrespondence;
        if (!$planCorrespondence) {
            abort(404);
        }
        $reasonNoId = $request->reason_no_id;
        if (!isset($request->reason_no_id) || empty($reasonNoId) || !is_numeric($reasonNoId)) {
            abort(404);
        }
        $reasonNo = $this->reasonNoService->find($request->reason_no_id);

        if ($reasonNo) {
            $reasonNo->load([
                'reasons' => function ($query) {
                    $query->where('reason_name', '!=', Reason::NO_REASON);
                },
            ]);
            $reasons = $reasonNo->reasons;
        } else {
            abort(404);
        }

        $mLawsRegulations = MLawsRegulation::where('name', '!=', Reason::NO_REASON)->get();
        $productGroupByDistinctions = $comparisonTrademarkResult->getProducts()->groupBy('mDistinction.name');

        return view('admin.modules.comparison_trademark_results.create-reason-supervisor', compact(
            'comparisonTrademarkResult',
            'trademarkTable',
            'reasonNo',
            'mLawsRegulations',
            'productGroupByDistinctions',
            'planCorrespondence',
            'reasons',
            'trademarkDocuments',
            'appTrademark'
        ));
    }

    /**
     * Create Reason Supervisor
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function postCreateReasonSupervisor(Request $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $params = $request->all();
            $resultReasonNo = $this->reasonNoService->updateOrCreate(
                [
                    'id' => $params['reason_no_id'],
                ],
                [
                    'plan_correspondence_id' => $params['plan_correspondence_id'],
                    'reason_number' => $params['reason_number'],
                    'reason_branch_number' => $params['reason_branch_number'],
                    'response_deadline' => isset($params['response_deadline']) ? $params['response_deadline'] : null,
                ]
            );
            if (!isset($params['reason_id'])) {
                $resultReason = $this->reasonService->findByCondition(['reason_no_id' => $resultReasonNo->id])->get();
                if ($resultReason) {
                    foreach ($resultReason as $value) {
                        $value->delete();
                    }
                }

                if (isset($params['m_laws_regulation_id'])) {
                    foreach ($params['m_laws_regulation_id'] as $key => $mlawRegulation) {
                        if (isset($params['reference_number'][$key])) {
                            $covnertReferenceNumbers = json_encode($params['reference_number'][$key]);
                        } else {
                            $covnertReferenceNumbers = '';
                        }
                        Reason::create([
                            'admin_id' => Auth::user()->id,
                            'reason_no_id' => $resultReasonNo->id,
                            'reason_name' => $params['reason_name'][$key],
                            'm_laws_regulation_id' => $mlawRegulation,
                            'reference_number' => $covnertReferenceNumbers
                        ]);
                    }
                }
            } else {
                foreach ($params['m_laws_regulation_id'] as $key => $mlawRegulation) {
                    if (isset($params['reference_number'][$key])) {
                        $covnertReferenceNumbers = json_encode($params['reference_number'][$key]);
                    } else {
                        $covnertReferenceNumbers = '';
                    }
                    $this->reasonService->updateOrCreate(
                        [
                            'id' => $params['reason_id'][$key],
                        ],
                        [
                            'admin_id' => Auth::user()->id,
                            'reason_no_id' => $resultReasonNo->id,
                            'reason_name' => $params['reason_name'][$key],
                            'm_laws_regulation_id' => $mlawRegulation,
                            'reference_number' => $covnertReferenceNumbers
                        ]
                    );
                }
            }
            if (isset($params['code_name'])) {
                $mCodes = $params['code_name'];
                foreach ($mCodes as $key => $mCode) {
                    foreach ($mCode as $value) {
                        if (empty($value)) {
                            return redirect()->back()->with('error', __('messages.general.correspondence_A201b_E001'))->withInput();
                        }
                    }
                }
                $this->comparisonTrademarkResultService->insertProductAndCode($params);
            }
            DB::commit();

            return redirect()->route('admin.refusal.eval-report.create-examine.supervisor', [
                'id' => $params['id'],
                'reason_no_id' => $params['reason_no_id'],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            CommonHelper::setMessage($request, MESSAGE_ERROR, __('messages.error'));
            return redirect()->back();
        }
    }

    /**
     * Create Reason.
     *
     * @return View
     */
    public function createReason(Request $request, $id): View
    {
        $comparisonTrademarkResult = $this->comparisonTrademarkResultService->find($id);
        if (!$comparisonTrademarkResult) {
            abort(404);
        }
        $comparisonTrademarkResult->load('planCorrespondences', 'trademark', 'trademark.appTrademark', 'planCorrespondence.reasonNos');
        $trademark = $comparisonTrademarkResult->trademark;
        if (!$trademark) {
            abort(404);
        }

        $trademarkDocuments = $this->trademarkDocumentService->getByCondition([
            'trademark_id' => $trademark->id,
            'type' => TrademarkDocument::TYPE_1,
            'flow' => Notice::FLOW_RESPONSE_REASON,
            'step' => Notice::STEP_1,
        ])->pluck('url');

        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_ADMIN_1, $trademark->id);
        $planCorrespondence = $comparisonTrademarkResult->planCorrespondences->last();
        if (!isset($request->reason_no_id) || empty($request->reason_no_id)) {
            $reasonNo = $planCorrespondence->reasonNos->first();
        } else {
            $reasonNo = $this->reasonNoService->findByCondition(['id' => $request->reason_no_id])->first();
            if ($reasonNo->plan_correspondence_id != $planCorrespondence->id) {
                abort(CODE_ERROR_404);
            }
        }
        if ($reasonNo) {
            $reasonNo->load([
                'reasons' => function ($query) {
                    $query->where('reason_name', '!=', Reason::NO_REASON);
                },
            ]);
            $reasons = $reasonNo->reasons;
        } else {
            $reasons = [];
        }

        $mLawsRegulations = MLawsRegulation::where('name', '!=', Reason::NO_REASON)->get();
        $productGroupByDistinctions = $comparisonTrademarkResult->getProducts()->groupBy('mDistinction.name');

        return view('admin.modules.comparison_trademark_results.create-reason', compact(
            'comparisonTrademarkResult',
            'trademarkTable',
            'reasonNo',
            'mLawsRegulations',
            'productGroupByDistinctions',
            'planCorrespondence',
            'reasons',
            'trademarkDocuments'
        ));
    }

    /**
     * Function Ppost create Reason.
     *
     * @return RedirectResponse
     */
    public function postCreateReason(Request $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $params = $request->all();
            $resultReasonNo = $this->reasonNoService->updateOrCreate(
                [
                    'id' => $params['reason_no_id'],
                ],
                [
                    'plan_correspondence_id' => $params['plan_correspondence_id'],
                    'reason_number' => $params['reason_number'],
                    'reason_branch_number' => $params['reason_branch_number'],
                    'response_deadline' => isset($params['response_deadline']) ? $params['response_deadline'] : null,
                ]
            );
            // Create a record reason 理由無(no reason)
            $noReasonRecord = $this->reasonService->findByCondition([
                'reason_no_id' => $resultReasonNo->id ?? 0,
                'reason_name' => Reason::NO_REASON,
            ])->whereNull('reference_number')->count();

            if (!$noReasonRecord) {
                $mLawsRegulation = MLawsRegulation::where('name', Reason::NO_REASON)
                    ->where('rank', MLawsRegulation::RANK_A)
                    ->first();

                $defaultReason = $this->reasonService->create([
                    'admin_id' => Auth::user()->id,
                    'reason_no_id' => $resultReasonNo->id ?? 0,
                    'reason_name' => Reason::NO_REASON,
                    'm_laws_regulation_id' => $mLawsRegulation->id,
                ]);
            }
            if (!isset($params['reason_id'])) {
                $resultReason = $this->reasonService->findByCondition(['reason_no_id' => $resultReasonNo->id])->get();
                if ($resultReason) {
                    foreach ($resultReason as $value) {
                        if ($defaultReason && $defaultReason->id == $value->id) {
                            continue;
                        }
                        $value->delete();
                    }
                }

                if (isset($params['m_laws_regulation_id'])) {
                    foreach ($params['m_laws_regulation_id'] as $key => $mlawRegulation) {
                        if (isset($params['reference_number'][$key])) {
                            $covnertReferenceNumbers = json_encode($params['reference_number'][$key]);
                        } else {
                            $covnertReferenceNumbers = '';
                        }
                        Reason::create([
                            'admin_id' => Auth::user()->id,
                            'reason_no_id' => $resultReasonNo->id,
                            'reason_name' => $params['reason_name'][$key],
                            'm_laws_regulation_id' => $mlawRegulation,
                            'reference_number' => $covnertReferenceNumbers
                        ]);
                    }
                }
            } else {
                foreach ($params['m_laws_regulation_id'] as $key => $mlawRegulation) {
                    if (isset($params['reference_number'][$key])) {
                        $covnertReferenceNumbers = json_encode($params['reference_number'][$key]);
                    } else {
                        $covnertReferenceNumbers = '';
                    }
                    $this->reasonService->updateOrCreate(
                        [
                            'id' => $params['reason_id'][$key],
                        ],
                        [
                            'admin_id' => Auth::user()->id,
                            'reason_no_id' => $resultReasonNo->id,
                            'reason_name' => $params['reason_name'][$key],
                            'm_laws_regulation_id' => $mlawRegulation,
                            'reference_number' => $covnertReferenceNumbers
                        ]
                    );
                }
            }


            if (isset($params['code_name'])) {
                $mCodes = $params['code_name'];
                foreach ($mCodes as $key => $mCode) {
                    foreach ($mCode as $value) {
                        if (empty($value)) {
                            return redirect()->back()->with('error', __('messages.general.correspondence_A201b_E001'))->withInput();
                        }
                    }
                }
                $this->comparisonTrademarkResultService->insertProductAndCode($params);
            }
            DB::commit();

            return redirect()->route('admin.refusal.eval-report.create-examine', [
                'id' => $params['id'],
                'reason_no_id' => $resultReasonNo->id,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            CommonHelper::setMessage($request, MESSAGE_ERROR, __('messages.error'));

            return redirect()->back();
        }
    }

    /**
     * Create Examine Supervisor - a201b02s
     *
     * @param Request $request
     * @param int $id
     * @return View
     */
    public function createExamineSupervisor(Request $request, int $id): View
    {
        $comparisonTrademarkResult = $this->comparisonTrademarkResultService->find($id);

        if (empty($comparisonTrademarkResult)) {
            abort(CODE_ERROR_404);
        }

        $comparisonTrademarkResult = $comparisonTrademarkResult->load([
            'planCorrespondence.reasonComments'
        ]);

        $planCorrespondence = $comparisonTrademarkResult->planCorrespondence;

        if (empty($planCorrespondence)) {
            abort(CODE_ERROR_404);
        }
        $reasonNoId = $request->reason_no_id;
        if (!isset($reasonNoId) || empty($reasonNoId) || !is_numeric($reasonNoId)) {
            abort(CODE_ERROR_404);
        }
        $reasonNo = $this->reasonNoService->find($reasonNoId);
        if (!$reasonNo) {
            abort(CODE_ERROR_404);
        }

        if ($reasonNo->plan_correspondence_id != $planCorrespondence->id) {
            abort(CODE_ERROR_404);
        }

        // Get products and reasons
        $products = $comparisonTrademarkResult->getProducts()->groupBy('mDistinction.name');
        $reasons = $this->reasonService->findByCondition(['reason_no_id' => $reasonNo->id])->get();

        // Get reason comment
        $reasonComment = $this->reasonCommentService->findByCondition([
            'plan_correspondence_id' => $planCorrespondence->id,
            'type' => ReasonComment::TYPE_1,
            'type_comment_step' => ReasonComment::STEP_1,
        ])->first();

        // Has Sent User
        $hasSendUser = false;
        if (!empty($reasonNo)) {
            if ($reasonNo->is_confirm == true && !isset($request->type)) {
                $hasSendUser = true;
            }
        }
        $showBtnDefault = false;
        $comparisonTrademarkResult->load(['trademark.appTrademark', 'planCorrespondence']);
        if (isset($comparisonTrademarkResult->trademark) && isset($comparisonTrademarkResult->trademark->appTrademark)) {
            $appTrademark = $comparisonTrademarkResult->trademark->appTrademark;
            $planCorrespondence = $comparisonTrademarkResult->planCorrespondence;
            if (($appTrademark->pack = AppTrademark::PACK_A || $appTrademark->pack = AppTrademark::PACK_B)
                && $planCorrespondence->type == PlanCorrespondence::TYPE_SELECT
            ) {
                $showBtnDefault = true;
            }
        }

        // Url Back
        $urlBackDefault = route('admin.home');
        $checkUrl = route('admin.refusal.eval-report.create-examine.supervisor', $comparisonTrademarkResult->id);
        $backUrl = CommonHelper::setBackUrl($urlBackDefault, $checkUrl);

        return view('admin.modules.comparison_trademark_results.create-examine-supervisor', compact(
            'comparisonTrademarkResult',
            'showBtnDefault',
            'products',
            'reasons',
            'reasonComment',
            'hasSendUser',
            'backUrl',
            'reasonNo'
        ));
    }

    /**
     * Create Examine Supervisor - a201b02s - submit
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function postCreateExamineSupervisor(Request $request, int $id): RedirectResponse
    {
        $comparisonTrademarkResult = $this->comparisonTrademarkResultService->find($id);
        if (empty($comparisonTrademarkResult)) {
            abort(CODE_ERROR_404);
        }

        $currentAdmin = Auth::guard('admin')->user();
        $comparisonTrademarkResult = $comparisonTrademarkResult->load([
            'planCorrespondence', 'trademark.user',
        ]);

        $planCorrespondence = $comparisonTrademarkResult->planCorrespondence;
        if (empty($planCorrespondence)) {
            abort(CODE_ERROR_404);
        }

        $trademark = $comparisonTrademarkResult->trademark;
        if (!$trademark) {
            abort(CODE_ERROR_404);
        }

        $appTrademark = $trademark->appTrademark;
        if (!$appTrademark) {
            abort(CODE_ERROR_404);
        }
        try {
            DB::beginTransaction();

            $reasonNo = $this->reasonNoService->find($request->reason_no_id);
            if (!empty($reasonNo)) {
                if ($reasonNo->flag_role == ReasonNo::FLAG_1) {
                    return redirect()->back();
                }
            }

            // Create/Update Reason Comment
            $this->reasonCommentService->updateOrCreate([
                'plan_correspondence_id' => $planCorrespondence->id,
                'type' => ReasonComment::TYPE_1,
                'type_comment_step' => ReasonComment::STEP_1,
            ], [
                'admin_id' => $currentAdmin->id,
                'content' => $request->content ?? '',
            ]);

            $products = $request->products ?? collect();
            $reasonRefNumProdArray = [];
            foreach ($products as $product) {
                $product['admin_id'] = $currentAdmin->id;
                $product['comment_patent_agent'] = $product['comment_patent_agent'] ?? null;
                $product['rank'] = $product['rank'] ?? null;
                $product['vote_reason_id'] = json_encode($product['vote_reason_id'] ?? []);
                $product['is_choice'] = true;

                $reasonRefNumProdArray[] = $this->reasonRefNumProdService->updateOrCreate([
                    'plan_correspondence_prod_id' => $product['plan_correspondence_prod_id'] ?? null,
                ], $product);
            }

            $redirect = redirect()->back()->getTargetUrl();
            if (isset($request[SUBMIT])) {
                // Update flag_role of reasonNo
                if (!empty($reasonNo)) {
                    $reasonNo->update([
                        'flag_role' => ReasonNo::FLAG_2,
                        'is_confirm' => true,
                    ]);
                }

                foreach ($reasonRefNumProdArray as $reasonRefNumProd) {
                    $prod = $reasonRefNumProd->load(['planCorrespondenceProd']);
                    $planCorrespondenceProd = $prod->planCorrespondenceProd ?? null;

                    if (!empty($planCorrespondenceProd) && $planCorrespondenceProd->is_register == true) {
                        $planCorrespondenceProd->update(['completed_evaluation' => true]);
                    }
                }

                $this->noticeService->updateComment(
                    Notice::FLOW_RESPONSE_REASON,
                    $request->content ?? '',
                    $trademark->id
                );

                // Send Notice
                if ($request->has('send_notice') && $request->send_notice == SEND_NOTICE_USER) {
                    $this->noticeCreateExamineForUser($comparisonTrademarkResult, $reasonNo);
                    $user = User::find($trademark->user_id);
                    $params = [
                        'from_page' => A201B02S,
                        'user' => $user,
                    ];
                    $this->mailTemplateService->sendMailRequest($params, MailTemplate::TYPE_OTHER, MailTemplate::GUARD_TYPE_ADMIN);
                } else {
                    $this->noticeCreateExamineForLawyer($comparisonTrademarkResult, $reasonNo);
                }

                $redirect = route('admin.home');

                if ($reasonNo->is_confirm == ReasonNo::IS_CONFIRM_TRUE && $planCorrespondence->type == PlanCorrespondence::TYPE_3) {
                    CommonHelper::setMessage($request, MESSAGE_SUCCESS, __('messages.general.send_to_tantou'));
                }
                if ($reasonNo->is_confirm == ReasonNo::IS_CONFIRM_TRUE && $planCorrespondence->type == PlanCorrespondence::TYPE_2) {
                    CommonHelper::setMessage($request, MESSAGE_SUCCESS, __('messages.general.Common_E035'));
                } elseif ($reasonNo->is_confirm == ReasonNo::IS_CONFIRM_TRUE && $planCorrespondence->type == PlanCorrespondence::TYPE_1) {
                    CommonHelper::setMessage($request, MESSAGE_SUCCESS, __('messages.general.send_to_tantou'));
                }
            } else {
                CommonHelper::setMessage($request, MESSAGE_SUCCESS, __('messages.update_success'));
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
     * Send Notice Create Examine at Supervisor
     *
     * @param Model $comparisonTrademarkResult
     * @return void
     */
    public function noticeCreateExamineForLawyer(Model $comparisonTrademarkResult, $reasonNo)
    {
        $this->comparisonTrademarkResultService->noticeCreateExamineForLawyer($comparisonTrademarkResult, $reasonNo);
    }

    /**
     * Send Notice Create Examine at Supervisor
     *
     * @param Model $comparisonTrademarkResult
     * @return void
     */
    public function noticeCreateExamineForUser(Model $comparisonTrademarkResult, Model $reasonNo)
    {
        $comparisonTrademarkResult = $comparisonTrademarkResult->load([
            'trademark',
            'machingResult',
        ]);

        $trademark = $comparisonTrademarkResult->trademark;

        $targetPage = route('admin.refusal.eval-report.create-examine.supervisor', [
            'id'  => $comparisonTrademarkResult->id,
            'reason_no_id' => $reasonNo->id,
        ]);
        $redirectPage = route('user.refusal.select-eval-report-show', [
            'id'  => $comparisonTrademarkResult->id,
            'reason_no_id' => $reasonNo->id,
        ]);

        // Set response deadline
        $responseDeadlineBefore4Day = null;
        $responseDeadlineBefore7Day = null;
        $machingResult = $comparisonTrademarkResult->machingResult;
        if (!empty($machingResult)) {
            $responseDeadlineBefore4Day = $machingResult->calculateResponseDeadline(-4);
            $responseDeadlineBefore7Day = $machingResult->calculateResponseDeadline(-7);
        }

        // Update Notice at a201b02 (No 36)
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
                'completion_date' => Carbon::now(),
            ]);
        });

        // Notice at a201b02s
        $notice = [
            'flow' => Notice::FLOW_RESPONSE_REASON,
            'step' => Notice::STEP_1,
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
                'content' => '拒絶理由通知対応：登録可能性評価レポート連絡済',
                'attribute' => 'お客様へ',
                'response_deadline' => $responseDeadlineBefore4Day ?? null,
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
                'content' => '拒絶理由通知対応：登録可能性評価レポート＆拒絶理由通知対応お申込み',
                'attribute' => null,
                'response_deadline' => $responseDeadlineBefore7Day ?? null,
                'response_deadline_ams' => $reasonNo->response_deadline ?? null,
            ],
            [
                'target_id' => $trademark->user_id,
                'type_acc' => NoticeDetail::TYPE_USER,
                'target_page' => $targetPage,
                'redirect_page' => $redirectPage,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                'is_action' => false,
                'content' => '拒絶理由通知対応：登録可能性評価レポート＆拒絶理由通知対応お申込み',
                'attribute' => null,
                'response_deadline' => $responseDeadlineBefore7Day ?? null,
                'response_deadline_ams' => $reasonNo->response_deadline ?? null,
            ],
        ];

        $this->noticeService->sendNotice([
            'notices' => $notice,
            'notice_details' => $noticeDetails,
        ]);
    }

    /**
     * Delete question detail by id
     *
     * @param int $id
     */
    public function deleteQuestionDetail($id)
    {
        try {
            $reasonQuestionDetail = $this->reasonQuestionDetailService->find($id);
            $reasonQuestionDetail->delete();

            return response()->json(['success' => true], 200);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['failure' => true], 400);
        }
    }

    /**
     * Edit Examine
     *
     * @param Request $request
     * @param int $id
     * @return View
     */
    public function editExamine(Request $request, int $id): View
    {
        $comparisonTrademarkResult = $this->comparisonTrademarkResultService->find($id);

        if (empty($comparisonTrademarkResult)) {
            abort(CODE_ERROR_404);
        }

        $comparisonTrademarkResult = $comparisonTrademarkResult->load([
            'planCorrespondence.reasonComments'
        ]);

        $planCorrespondence = $comparisonTrademarkResult->planCorrespondence;
        // Get products and reasons
        $products = $comparisonTrademarkResult->getProducts()->groupBy('mDistinction.name');
        $reasonNoId = $request->reason_no_id;
        if (!isset($request->reason_no_id) || empty($reasonNoId) || !is_numeric($reasonNoId)) {
            abort(CODE_ERROR_404);
        }
        $reasonNo = $this->reasonNoService->find($reasonNoId);
        if (!$reasonNo) {
            abort(CODE_ERROR_404);
        }
        if ($reasonNo->plan_correspondence_id != $planCorrespondence->id) {
            abort(CODE_ERROR_404);
        }
        $reasons = $reasonNo->reasons;

        // Get reason comment
        $planCorrespondence = $comparisonTrademarkResult->planCorrespondence;

        if (empty($planCorrespondence)) {
            abort(CODE_ERROR_404);
        }

        $reasonComment = $this->reasonCommentService->findByCondition([
            'plan_correspondence_id' => $planCorrespondence->id,
            'type' => ReasonComment::TYPE_1,
            'type_comment_step' => ReasonComment::STEP_1,
        ])->first();

        $hasSendSeki = false;

        if (!empty($reasonNo)) {
            if ($reasonNo->flag_role == ReasonNo::FLAG_2) {
                $hasSendSeki = true;
            }
        }

        // Url Back
        $urlBackDefault = route('admin.home');
        $checkUrl = route('admin.refusal.eval-report.edit-examine', $comparisonTrademarkResult->id);
        $backUrl = CommonHelper::setBackUrl($urlBackDefault, $checkUrl);

        return view('admin.modules.comparison_trademark_results.edit-examine', compact(
            'comparisonTrademarkResult',
            'products',
            'reasons',
            'reasonComment',
            'backUrl',
            'reasonNo',
            'hasSendSeki',
        ));
    }

    /**
     * Showing pre question screen. a202n_s
     *
     * @param integer $id - comparison_trademark_result_id?reason_question_no
     * @param Request $request
     *
     * @return View
     */
    public function preQuestionReShow(Request $request, int $id): View
    {
        $reasonQuestionNoId = null;
        if (!isset($request->type)) {
            $reasonQuestionNoId = $request->reason_question_no;
            if (!isset($reasonQuestionNoId)) {
                abort(404);
            }
        }

        $comparisonTrademarkResult = $this->comparisonTrademarkResultService->find($id);
        $planCorrespondence = $comparisonTrademarkResult->planCorrespondence;
        $reasonQuestion = $planCorrespondence->reasonQuestion ?? null;
        if (!$comparisonTrademarkResult || !$reasonQuestion) {
            abort(404);
        }

        if (!isset($request->type)) {
            $reasonQuestionNo = $reasonQuestion->reasonQuestionNos->find($reasonQuestionNoId) ?? null;
        } else {
            $reasonQuestionNo = $reasonQuestion->reasonQuestionNos->last() ?? null;
            if (!$reasonQuestionNo) {
                abort(404);
            }
            $reasonQuestionNoId = $reasonQuestionNo->id;
        }

        if (!$reasonQuestionNo) {
            abort(404);
        }

        $reasonQuestionDetailsOld = $this->reasonQuestionDetailService->getReasonQuestionDetailDataV2(
            $comparisonTrademarkResult->id,
            ReasonQuestionDetail::IS_ANSWER,
            SORT_TYPE_DESC,
            $reasonQuestionNoId,
            ReasonQuestionNo::IS_CONFIRM,
            '<='
        );
        $reasonQuestionNoDraft = $this->reasonQuestionNoService->findByCondition([
            'reason_question_id' => $reasonQuestion->id,
        ])->where('id', '>', $reasonQuestionNoId)->first();

        $reasonQuestionDetails = [];
        if ($reasonQuestionNoDraft) {
            $reasonQuestionDetails = $this->reasonQuestionDetailService->getReasonQuestionDetailDataV2(
                $comparisonTrademarkResult->id,
                ReasonQuestionDetail::IS_NOT_ANSWER,
                SORT_TYPE_ASC,
                $reasonQuestionNoDraft->id,
                ReasonQuestionNo::IS_NOT_CONFIRM,
                '='
            );
        }

        $checkIsConfirm = false;
        if ($reasonQuestionNoDraft && $reasonQuestionNoDraft->is_confirm == ReasonQuestionNo::IS_CONFIRM) {
            $checkIsConfirm = true;
        }

        $reasonCommentOld = $this->reasonCommentService->findByCondition([
            'plan_correspondence_id' => $planCorrespondence ? $planCorrespondence->id : null,
            'type' => ReasonComment::TYPE_1,
            'type_comment_step' => ReasonComment::STEP_2,
        ])->first();

        $questionStatusRequired = ReasonQuestionNo::QUESTION_STATUS_NECESSARY;
        $isConfirmReasonQuestion = ReasonQuestionNo::IS_CONFIRM;

        $trademark = $comparisonTrademarkResult->trademark;
        if (!$trademark) {
            abort(404);
        }

        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_ADMIN_1, $trademark->id, [
            SHOW_LINK_ANKEN_TOP => true,
        ]);

        $trademarkDocuments = $this->trademarkDocumentService->getByCondition([
            'trademark_id' => $comparisonTrademarkResult->trademark_id,
            'type' => TrademarkDocument::TYPE_1,
            'flow' => Notice::FLOW_RESPONSE_REASON,
            'step' => Notice::STEP_1,
        ])->pluck('url');

        $adminRole = auth()->guard(ADMIN_ROLE)->user()->role;

        $isModal = (int) $request->is_modal ?? 0;

        //result data
        $data = [
            'adminRole' => $adminRole,
            'comparisonTrademarkResult' => $comparisonTrademarkResult,
            'reasonQuestion' => $reasonQuestion,
            'trademarkTable' => $trademarkTable,
            'trademarkDocuments' => $trademarkDocuments,
            'reasonQuestionDetails' => $reasonQuestionDetails,
            'reasonQuestionDetailsOld' => $reasonQuestionDetailsOld,
            'isConfirmReasonQuestion' => $isConfirmReasonQuestion,
            'questionStatusRequired' => $questionStatusRequired,
            'reasonCommentOld' => $reasonCommentOld,
            'isModal' => $isModal,
            'reasonQuestionNo' => $reasonQuestionNo,
            'reasonQuestionNoDraft' => $reasonQuestionNoDraft,
            'checkIsConfirm' => $checkIsConfirm,
        ];

        return view('admin.modules.comparison_trademark_results.pre-question-re.index', $data);
    }

    /**
     * Save data in database - post a202n_s
     *
     * @param integer $id - comparison_trademark_result_id
     * @param Request $request
     */
    public function savePreQuestionReShow(RefulsalPreQuestionReSuperVisorRequest $request, int $id)
    {
        $comparisonTrademarkResult = $this->comparisonTrademarkResultService->find($id);
        if (!$comparisonTrademarkResult) {
            abort(404);
        }
        $inputs = $request->all();
        $inputs['comparison_trademark_result_id'] = $id;
        $inputs['plan_correspondence_id'] = $comparisonTrademarkResult->planCorrespondence->id;
        $inputs['trademark_id'] = $comparisonTrademarkResult->trademark->id;
        $inputs['user_id'] = $comparisonTrademarkResult->trademark->user->id;
        $inputs['reason_question_id'] = $comparisonTrademarkResult->planCorrespondence->reasonQuestion ? $comparisonTrademarkResult->planCorrespondence->reasonQuestion->id : null;

        $result = $this->comparisonTrademarkResultService->savePreQuestionReShowAdmin($inputs);
        if ($result) {
            if ($inputs['code'] == SAVE_TO_END_USER) {
                $dataMail = [
                    'from_page' => A202N_S,
                    'user' => $comparisonTrademarkResult->trademark->user
                ];
                $this->mailTemplateService->sendMailRequest($dataMail, MailTemplate::TYPE_OTHER, MailTemplate::GUARD_TYPE_ADMIN);
                return redirect()->route('admin.home')->with('message', __('messages.general.seki_submit_to_user'));
            } elseif ($inputs['code'] == SAVE_COMPLATE_QUESTION) {
                return redirect()->route('admin.home')->with('message', __('messages.general.requested_a_draft_policy'));
            }
            return redirect()->back()->with('message', __('messages.general.update_success'));
        }

        return redirect()->back()->with('error', __('messages.error'));
    }

    /**
     * Post Edit Examine.
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function postEditExamine(Request $request, int $id): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $comparisonTrademarkResult = $this->comparisonTrademarkResultService->find($id);

            if (empty($comparisonTrademarkResult)) {
                abort(CODE_ERROR_404);
            }

            $currentAdmin = Auth::guard('admin')->user();
            $comparisonTrademarkResult = $comparisonTrademarkResult->load([
                'planCorrespondence.reasonNos', 'trademark.user',
            ]);

            $planCorrespondence = $comparisonTrademarkResult->planCorrespondence;
            if (empty($planCorrespondence)) {
                abort(CODE_ERROR_404);
            }

            $reasonNo = $this->reasonNoService->find($request->reason_no_id);
            if (!empty($reasonNo)) {
                if ($reasonNo->flag_role == ReasonNo::FLAG_2) {
                    return redirect()->back();
                }
            }

            // Create/Update Reason Comment
            $this->reasonCommentService->updateOrCreate([
                'plan_correspondence_id' => $planCorrespondence->id,
                'type' => ReasonComment::TYPE_1,
                'type_comment_step' => ReasonComment::STEP_1,
            ], [
                'admin_id' => $currentAdmin->id,
                'content' => $request->content ?? '',
            ]);

            $products = $request->products ?? collect();
            $reasonRefNumProdArray = [];
            foreach ($products as $product) {
                if (isset($product['completed_evaluation']) && $product['completed_evaluation'] == false) {
                    $product['admin_id'] = $currentAdmin->id;
                    $product['comment_patent_agent'] = $product['comment_patent_agent'] ?? null;
                    $product['rank'] = $product['rank'] ?? null;
                    $product['vote_reason_id'] = json_encode($product['vote_reason_id'] ?? []);
                    $product['is_choice'] = true;
                    $product['reason_no_id'] = $reasonNo->id;

                    $reasonRefNumProdArray[] = $this->reasonRefNumProdService->updateOrCreate([
                        'plan_correspondence_prod_id' => $product['plan_correspondence_prod_id'] ?? null,
                        'reason_no_id' => $product['reason_no_id'] ?? null,
                    ], $product);
                }
            }

            $redirect = redirect()->back()->getTargetUrl();
            if (isset($request->submit) || isset($request->submit_supervisor)) {
                // Update flag_role of reasonNo
                if (!empty($reasonNo)) {
                    $reasonNo->update([
                        'flag_role' => ReasonNo::FLAG_2,
                    ]);
                }

                $this->noticeService->updateComment(
                    Notice::FLOW_RESPONSE_REASON,
                    $request->content ?? '',
                    $comparisonTrademarkResult->trademark_id
                );

                // Send Notice
                if (isset($request->submit)) {
                    $this->noticeEditExamineManager($comparisonTrademarkResult, $reasonNo);
                } elseif (isset($request->submit_supervisor)) {
                    if (!empty($reasonNo)) {
                        $reasonNo->update([
                            'is_confirm' => true,
                        ]);
                    }

                    foreach ($reasonRefNumProdArray as $reasonRefNumProd) {
                        $prod = $reasonRefNumProd->load(['planCorrespondenceProd']);
                        $planCorrespondenceProd = $prod->planCorrespondenceProd ?? null;

                        if (!empty($planCorrespondenceProd) && $planCorrespondenceProd->is_register == true) {
                            $planCorrespondenceProd->update(['completed_evaluation' => true]);
                        }
                    }

                    $this->noticeEditExamineSupervisor($comparisonTrademarkResult, $reasonNo);
                }

                $redirect = route('admin.home');
            }

            DB::commit();

            if (isset($request->submit_supervisor)) {
                CommonHelper::setMessage($request, MESSAGE_SUCCESS, __('messages.general.seki_submit_to_user'));
            } elseif (isset($request->submit)) {
                CommonHelper::setMessage($request, MESSAGE_SUCCESS, __('messages.flug_role_seki'));
            } else {
                CommonHelper::setMessage($request, MESSAGE_SUCCESS, __('messages.update_success'));
            }

            return redirect($redirect);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            CommonHelper::setMessage($request, MESSAGE_ERROR, __('messages.error'));
            return redirect()->back();
        }
    }

    /**
     * Send Notice Create Examine at Manager
     *
     * @param Model $comparisonTrademarkResult
     * @return void
     */
    public function noticeEditExamineManager(Model $comparisonTrademarkResult, $reasonNo)
    {
        $comparisonTrademarkResult = $comparisonTrademarkResult->load([
            'trademark',
            'machingResult',
        ]);

        $trademark = $comparisonTrademarkResult->trademark;

        $targetPage = route('admin.refusal.eval-report.edit-examine', [
            'id' => $comparisonTrademarkResult->id,
            'reason_no_id' => $reasonNo->id,
        ]);
        $redirectPage = route('admin.refusal.eval-report.edit-reason.supervisor', [
            'id' => $comparisonTrademarkResult->id,
            'reason_no_id' => $reasonNo->id,
        ]);

        // Set response deadline
        $responseDeadline = null;
        $machingResult = $comparisonTrademarkResult->machingResult;
        if (!empty($machingResult)) {
            $responseDeadline = $machingResult->calculateResponseDeadline(-4);
        }

        // Update Notice at Before step (No 43 | 45)
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
                'completion_date' => Carbon::now(),
            ]);
        });

        // Notice
        $notice = [
            'flow' => Notice::FLOW_RESPONSE_REASON,
            'step' => Notice::STEP_1,
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
                'content' => '責任者　拒絶理由通知対応：登録可能性評価レポート　理由数設定・作成',
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
                'content' => '責任者　拒絶理由通知対応：登録可能性評価レポート　理由数設定・作成',
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
     * Send Notice Create Examine at Supervisor
     *
     * @param Model $comparisonTrademarkResult
     * @return void
     */
    public function noticeEditExamineSupervisor(Model $comparisonTrademarkResult, Model $reasonNo)
    {
        $comparisonTrademarkResult = $comparisonTrademarkResult->load([
            'trademark',
            'machingResult',
        ]);

        $trademark = $comparisonTrademarkResult->trademark;

        $targetPage = route('admin.refusal.eval-report.edit-examine', [
            'id' => $comparisonTrademarkResult->id,
            'reason_no_id' => $reasonNo->id,
        ]);
        $redirectPage = route('user.refusal.select-eval-report-show', [
            'id' => $comparisonTrademarkResult->id,
            'reason_no_id' => $reasonNo->id,
        ]);

        // Set response deadline
        $responseDeadlineBefore3Day = null;
        $responseDeadlineBefore7Day = null;
        $machingResult = $comparisonTrademarkResult->machingResult;
        if (!empty($machingResult)) {
            $responseDeadlineBefore3Day = $machingResult->calculateResponseDeadline(-3);
            $responseDeadlineBefore7Day = $machingResult->calculateResponseDeadline(-7);
        }

        // Update Notice at Before step (No 43 | 45)
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
                'completion_date' => Carbon::now(),
            ]);
        });

        // Notice
        $notice = [
            'flow' => Notice::FLOW_RESPONSE_REASON,
            'step' => Notice::STEP_1,
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
                'content' => '拒絶理由通知対応：登録可能性評価レポート連絡済',
                'attribute' => 'お客様へ',
                'response_deadline' => $responseDeadlineBefore3Day ?? null,
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
                'content' => '拒絶理由通知対応：登録可能性評価レポート＆拒絶理由通知対応お申込み',
                'attribute' => null,
                'response_deadline' => $responseDeadlineBefore7Day ?? null,
                'response_deadline_ams' => $reasonNo->response_deadline ?? null,
            ],
            [
                'target_id' => $trademark->user_id,
                'type_acc' => NoticeDetail::TYPE_USER,
                'target_page' => $targetPage,
                'redirect_page' => $redirectPage,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                'is_action' => false,
                'content' => '拒絶理由通知対応：登録可能性評価レポート＆拒絶理由通知対応お申込み',
                'attribute' => null,
                'response_deadline' => $responseDeadlineBefore7Day ?? null,
                'response_deadline_ams' => $reasonNo->response_deadline ?? null,
            ],
        ];

        $this->noticeService->sendNotice([
            'notices' => $notice,
            'notice_details' => $noticeDetails,
        ]);
    }

    /**
     * Create Reason Supervisor
     *
     * @param Request $request
     * @param int $id
     * @return View
     */
    public function editReason(Request $request, int $id): View
    {
        $comparisonTrademarkResult = $this->comparisonTrademarkResultService->find($id);
        if (!$comparisonTrademarkResult) {
            abort(CODE_ERROR_404);
        }
        $comparisonTrademarkResult->load('planCorrespondences', 'trademark', 'trademark.appTrademark');
        $trademark = $comparisonTrademarkResult->trademark;
        if (!$trademark) {
            abort(CODE_ERROR_404);
        }

        $trademarkDocuments = $this->trademarkDocumentService->getByCondition([
            'trademark_id' => $trademark->id,
            'type' => TrademarkDocument::TYPE_1,
            'flow' => Notice::FLOW_RESPONSE_REASON,
            'step' => Notice::STEP_1,
        ])->pluck('url');
        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_ADMIN_1, $trademark->id);
        $planCorrespondence = $comparisonTrademarkResult->planCorrespondence;
        if (!$planCorrespondence) {
            abort(CODE_ERROR_404);
        }
        $reasonNoId = $request->reason_no_id;
        if (!isset($request->reason_no_id) || empty($reasonNoId) || !is_numeric($reasonNoId)) {
            abort(CODE_ERROR_404);
        }
        $reasonNo = $this->reasonNoService->find($request->reason_no_id);
        if ($reasonNo) {
            $reasonNo->load([
                'reasons' => function ($query) {
                    $query->where('reason_name', '!=', Reason::NO_REASON);
                },
            ]);
            $reasons = $reasonNo->reasons;
        } else {
            abort(CODE_ERROR_404);
        }

        if ($reasonNo->plan_correspondence_id != $planCorrespondence->id) {
            abort(CODE_ERROR_404);
        }

        $mLawsRegulations = MLawsRegulation::where('name', '!=', Reason::NO_REASON)->get();
        $productGroupByDistinctions = $comparisonTrademarkResult->getProducts()->groupBy('mDistinction.name');
        return view('admin.modules.comparison_trademark_results.edit-reason', compact(
            'comparisonTrademarkResult',
            'trademarkTable',
            'reasonNo',
            'mLawsRegulations',
            'productGroupByDistinctions',
            'planCorrespondence',
            'reasons',
            'trademarkDocuments'
        ));
    }

    /**
     * Create Reason Supervisor
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function postEditReason(Request $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $params = $request->all();
            $resultReasonNo = $this->reasonNoService->updateOrCreate(
                [
                    'id' => $params['reason_no_id'],
                ],
                [
                    'plan_correspondence_id' => $params['plan_correspondence_id'],
                    'reason_number' => $params['reason_number'],
                    'reason_branch_number' => $params['reason_branch_number'],
                    'response_deadline' => isset($params['response_deadline']) ? $params['response_deadline'] : null,
                ]
            );
            // Create a record reason 理由無(no reason)
            $noReasonRecord = $this->reasonService->findByCondition([
                'reason_no_id' => $resultReasonNo->id ?? 0,
                'reason_name' => Reason::NO_REASON,
            ])->whereNull('reference_number')->count();

            if (!$noReasonRecord) {
                $mLawsRegulation = MLawsRegulation::where('name', Reason::NO_REASON)
                    ->where('rank', MLawsRegulation::RANK_A)
                    ->first();

                $defaultReason = $this->reasonService->create([
                    'admin_id' => Auth::user()->id,
                    'reason_no_id' => $resultReasonNo->id ?? 0,
                    'reason_name' => Reason::NO_REASON,
                    'm_laws_regulation_id' => $mLawsRegulation->id,
                ]);
            }
            if (!isset($params['reason_id'])) {
                $resultReason = $this->reasonService->findByCondition(['reason_no_id' => $resultReasonNo->id])->get();
                if ($resultReason) {
                    foreach ($resultReason as $value) {
                        if ($defaultReason && $defaultReason->id == $value->id) {
                            continue;
                        }
                        $value->delete();
                    }
                }

                if (isset($params['m_laws_regulation_id'])) {
                    foreach ($params['m_laws_regulation_id'] as $key => $mlawRegulation) {
                        if (isset($params['reference_number'][$key])) {
                            $covnertReferenceNumbers = json_encode($params['reference_number'][$key]);
                        } else {
                            $covnertReferenceNumbers = '';
                        }
                        Reason::create([
                            'admin_id' => Auth::user()->id,
                            'reason_no_id' => $resultReasonNo->id,
                            'reason_name' => $params['reason_name'][$key],
                            'm_laws_regulation_id' => $mlawRegulation,
                            'reference_number' => $covnertReferenceNumbers
                        ]);
                    }
                }
            } else {
                foreach ($params['m_laws_regulation_id'] as $key => $mlawRegulation) {
                    if (isset($params['reference_number'][$key])) {
                        $covnertReferenceNumbers = json_encode($params['reference_number'][$key]);
                    } else {
                        $covnertReferenceNumbers = '';
                    }
                    $this->reasonService->updateOrCreate(
                        [
                            'id' => $params['reason_id'][$key],
                        ],
                        [
                            'admin_id' => Auth::user()->id,
                            'reason_no_id' => $resultReasonNo->id,
                            'reason_name' => $params['reason_name'][$key],
                            'm_laws_regulation_id' => $mlawRegulation,
                            'reference_number' => $covnertReferenceNumbers
                        ]
                    );
                }
            }
            if (isset($params['code_name'])) {
                $mCodes = $params['code_name'];
                foreach ($mCodes as $key => $mCode) {
                    foreach ($mCode as $value) {
                        if (empty($value)) {
                            return redirect()->back()->with('error', __('messages.general.correspondence_A201b_E001'))->withInput();
                        }
                    }
                }
                $this->comparisonTrademarkResultService->insertProductAndCode($params);
            }
            DB::commit();

            return redirect()->route('admin.refusal.eval-report.edit-examine', [
                'id' => $params['id'],
                'reason_no_id' => $params['reason_no_id'],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            CommonHelper::setMessage($request, MESSAGE_ERROR, __('messages.error'));
            return redirect()->back();
        }
    }


    /**
     * Create Reason Supervisor
     *
     * @param Request $request
     * @param int $id
     * @return View
     */
    public function editReasonSupervisor(Request $request, int $id): View
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

        $trademarkDocuments = $this->trademarkDocumentService->getByCondition([
            'trademark_id' => $trademark->id,
            'type' => TrademarkDocument::TYPE_1,
            'flow' => Notice::FLOW_RESPONSE_REASON,
        ])->pluck('url');
        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_ADMIN_1, $trademark->id);
        $planCorrespondence = $comparisonTrademarkResult->planCorrespondence;
        if (!$planCorrespondence) {
            abort(404);
        }
        $reasonNoId = $request->reason_no_id;
        if (!isset($reasonNoId) || empty($reasonNoId) || !is_numeric($reasonNoId)) {
            abort(CODE_ERROR_404);
        }
        $reasonNo = $this->reasonNoService->find($request->reason_no_id);
        if ($reasonNo) {
            $reasonNo->load([
                'reasons' => function ($query) {
                    $query->where('reason_name', '!=', Reason::NO_REASON);
                },
            ]);
            $reasons = $reasonNo->reasons;
        } else {
            abort(CODE_ERROR_404);
        }
        if ($planCorrespondence->id != $reasonNo->plan_correspondence_id) {
            abort(CODE_ERROR_404);
        }

        $mLawsRegulations = MLawsRegulation::where('name', '!=', Reason::NO_REASON)->get();
        $productGroupByDistinctions = $comparisonTrademarkResult->getProducts()->groupBy('mDistinction.name');
        return view('admin.modules.comparison_trademark_results.edit-reason-supervisor', compact(
            'comparisonTrademarkResult',
            'trademarkTable',
            'reasonNo',
            'mLawsRegulations',
            'productGroupByDistinctions',
            'planCorrespondence',
            'reasons',
            'trademarkDocuments'
        ));
    }

    /**
     * Create Reason Supervisor
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function postEditReasonSupervisor(Request $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $params = $request->all();
            $resultReasonNo = $this->reasonNoService->updateOrCreate(
                [
                    'id' => $params['reason_no_id'],
                ],
                [
                    'plan_correspondence_id' => $params['plan_correspondence_id'],
                    'reason_number' => $params['reason_number'],
                    'reason_branch_number' => $params['reason_branch_number'],
                    'response_deadline' => isset($params['response_deadline']) ? $params['response_deadline'] : null,
                ]
            );
            if (!isset($params['reason_id'])) {
                $resultReason = $this->reasonService->findByCondition(['reason_no_id' => $resultReasonNo->id])->get();
                if ($resultReason) {
                    foreach ($resultReason as $value) {
                        $value->delete();
                    }
                }

                if (isset($params['m_laws_regulation_id'])) {
                    foreach ($params['m_laws_regulation_id'] as $key => $mlawRegulation) {
                        if (isset($params['reference_number'][$key])) {
                            $covnertReferenceNumbers = json_encode($params['reference_number'][$key]);
                        } else {
                            $covnertReferenceNumbers = '';
                        }
                        $this->reasonService->create([
                            'admin_id' => Auth::user()->id,
                            'reason_no_id' => $resultReasonNo->id,
                            'reason_name' => $params['reason_name'][$key],
                            'm_laws_regulation_id' => $mlawRegulation,
                            'reference_number' => $covnertReferenceNumbers
                        ]);
                    }
                }
            } else {
                foreach ($params['m_laws_regulation_id'] as $key => $mlawRegulation) {
                    if (isset($params['reference_number'][$key])) {
                        $covnertReferenceNumbers = json_encode($params['reference_number'][$key]);
                    } else {
                        $covnertReferenceNumbers = '';
                    }
                    $this->reasonService->updateOrCreate(
                        [
                            'id' => $params['reason_id'][$key],
                        ],
                        [
                            'admin_id' => Auth::user()->id,
                            'reason_no_id' => $resultReasonNo->id,
                            'reason_name' => $params['reason_name'][$key],
                            'm_laws_regulation_id' => $mlawRegulation,
                            'reference_number' => $covnertReferenceNumbers
                        ]
                    );
                }
            }

            if (isset($params['code_name'])) {
                $mCodes = $params['code_name'];
                foreach ($mCodes as $key => $mCode) {
                    foreach ($mCode as $value) {
                        if (empty($value)) {
                            return redirect()->back()->with('error', __('messages.general.correspondence_A201b_E001'))->withInput();
                        }
                    }
                }
                $this->comparisonTrademarkResultService->insertProductAndCode($params);
            }
            DB::commit();

            return redirect()->route('admin.refusal.eval-report.edit-examine.supervisor', [
                'id' => $params['id'],
                'reason_no_id' => $params['reason_no_id'],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            CommonHelper::setMessage($request, MESSAGE_ERROR, __('messages.error'));
            return redirect()->back();
        }
    }

    /**
     * Edit Examine Supervisor
     *
     * @param Request $request
     * @param int $id
     * @return View
     */
    public function editExamineSupervisor(Request $request, int $id): View
    {
        $comparisonTrademarkResult = $this->comparisonTrademarkResultService->find($id);

        if (empty($comparisonTrademarkResult)) {
            abort(CODE_ERROR_404);
        }

        $comparisonTrademarkResult = $comparisonTrademarkResult->load([
            'planCorrespondence.reasonComments'
        ]);
        $reasonNoId = $request->reason_no_id;
        if (!isset($reasonNoId) || empty($reasonNoId) || !is_numeric($reasonNoId)) {
            abort(CODE_ERROR_404);
        }
        $reasonNo = $this->reasonNoService->find($reasonNoId);
        if (!$reasonNo) {
            abort(CODE_ERROR_404);
        }
        // Get products and reasons
        $products = $comparisonTrademarkResult->getProducts()->groupBy('mDistinction.name');
        $reasons = $reasonNo->reasons;

        // Get reason comment
        $planCorrespondence = $comparisonTrademarkResult->planCorrespondence;

        if (empty($planCorrespondence)) {
            abort(CODE_ERROR_404);
        }

        if ($reasonNo->plan_correspondence_id != $planCorrespondence->id) {
            abort(CODE_ERROR_404);
        }

        $reasonComment = $this->reasonCommentService->findByCondition([
            'plan_correspondence_id' => $planCorrespondence->id,
            'type' => ReasonComment::TYPE_1,
            'type_comment_step' => ReasonComment::STEP_1,
        ])->first();

        // Has Sent User
        $hasSendUser = false;
        if (!empty($reasonNo)) {
            if ($reasonNo->is_confirm == true) {
                $hasSendUser = true;
            }
        }

        // Url Back
        $urlBackDefault = route('admin.home');
        $checkUrl = route('admin.refusal.eval-report.edit-examine.supervisor', $comparisonTrademarkResult->id);
        $backUrl = CommonHelper::setBackUrl($urlBackDefault, $checkUrl);

        return view('admin.modules.comparison_trademark_results.edit-examine-supervisor', compact(
            'comparisonTrademarkResult',
            'products',
            'reasons',
            'reasonComment',
            'hasSendUser',
            'backUrl',
            'reasonNo'
        ));
    }

    /**
     * Edit Examine Supervisor
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function postEditExamineSupervisor(Request $request, int $id): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $comparisonTrademarkResult = $this->comparisonTrademarkResultService->find($id);

            if (empty($comparisonTrademarkResult)) {
                abort(CODE_ERROR_404);
            }

            $currentAdmin = Auth::guard('admin')->user();
            $comparisonTrademarkResult = $comparisonTrademarkResult->load([
                'planCorrespondence.reasonNos', 'trademark.user',
            ]);

            $planCorrespondence = $comparisonTrademarkResult->planCorrespondence;
            if (empty($planCorrespondence)) {
                abort(CODE_ERROR_404);
            }

            $reasonNo = $this->reasonNoService->find($request->reason_no_id);
            if (!empty($reasonNo)) {
                if ($reasonNo->is_confirm == true) {
                    CommonHelper::setMessage($request, MESSAGE_SUCCESS, __('messages.general.seki_submit_to_user'));
                    return redirect()->back();
                }
            }

            // Create/Update Reason Comment
            $this->reasonCommentService->updateOrCreate([
                'plan_correspondence_id' => $planCorrespondence->id,
                'type' => ReasonComment::TYPE_1,
                'type_comment_step' => ReasonComment::STEP_1,
            ], [
                'admin_id' => $currentAdmin->id,
                'content' => $request->content ?? '',
            ]);

            $products = $request->products ?? collect();
            $reasonRefNumProdArray = [];
            foreach ($products as $product) {
                if (isset($product['completed_evaluation']) && $product['completed_evaluation'] == false) {
                    $product['admin_id'] = $currentAdmin->id;
                    $product['comment_patent_agent'] = $product['comment_patent_agent'] ?? null;
                    $product['rank'] = $product['rank'] ?? null;
                    $product['vote_reason_id'] = json_encode($product['vote_reason_id'] ?? []);
                    $product['is_choice'] = true;
                    $product['reason_no_id'] = $reasonNo->id;

                    $reasonRefNumProdArray[] = $this->reasonRefNumProdService->updateOrCreate([
                        'plan_correspondence_prod_id' => $product['plan_correspondence_prod_id'] ?? null,
                        'reason_no_id' => $product['reason_no_id'] ?? null,
                    ], $product);
                }
            }

            $redirect = redirect()->back()->getTargetUrl();
            if (isset($request->submit)) {
                // Update flag_role of reasonNo
                if (!empty($reasonNo)) {
                    $reasonNo->update([
                        'flag_role' => ReasonNo::FLAG_2,
                        'is_confirm' => true,
                    ]);
                }

                foreach ($reasonRefNumProdArray as $reasonRefNumProd) {
                    $prod = $reasonRefNumProd->load(['planCorrespondenceProd']);
                    $planCorrespondenceProd = $prod->planCorrespondenceProd ?? null;

                    if (!empty($planCorrespondenceProd) && $planCorrespondenceProd->is_register == true) {
                        $planCorrespondenceProd->update(['completed_evaluation' => true]);
                    }
                }

                $this->noticeService->updateComment(
                    Notice::FLOW_RESPONSE_REASON,
                    $request->content ?? '',
                    $comparisonTrademarkResult->trademark_id
                );

                // Send Notice
                $this->noticeEditExamineForUser($comparisonTrademarkResult, $reasonNo);

                // Send mail to user
                $trademark = Trademark::find($comparisonTrademarkResult->trademark_id);
                $redirect = route('admin.home');
                $user = User::find($trademark->user_id);
                $params = [
                    'from_page' => A201B02_S_N,
                    'user' => $user,
                ];
                $this->mailTemplateService->sendMailRequest($params, MailTemplate::TYPE_OTHER, MailTemplate::GUARD_TYPE_ADMIN);

                CommonHelper::setMessage($request, MESSAGE_SUCCESS, __('messages.general.seki_submit_to_user'));
            } else {
                CommonHelper::setMessage($request, MESSAGE_SUCCESS, __('messages.update_success'));
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
     * Send Notice Create Examine at Supervisor
     *
     * @param Model $comparisonTrademarkResult
     * @return void
     */
    public function noticeEditExamineForUser(Model $comparisonTrademarkResult, Model $reasonNo)
    {
        $comparisonTrademarkResult = $comparisonTrademarkResult->load([
            'trademark',
            'machingResult',
        ]);

        $trademark = $comparisonTrademarkResult->trademark;

        $targetPage = route('admin.refusal.eval-report.edit-examine.supervisor', ['id' => $comparisonTrademarkResult->id, 'reason_no_id' => $reasonNo->id]);
        $redirectPage = route('user.refusal.select-eval-report-show', ['id' => $comparisonTrademarkResult->id, 'reason_no_id' => $reasonNo->id]);

        // Set response deadline
        $responseDeadlineBefore4Day = null;
        $responseDeadlineBefore7Day = null;
        $machingResult = $comparisonTrademarkResult->machingResult;
        if (!empty($machingResult)) {
            $responseDeadlineBefore4Day = $machingResult->calculateResponseDeadline(-4);
            $responseDeadlineBefore7Day = $machingResult->calculateResponseDeadline(-7);
        }

        // Update Notice at a201b02 (No 47)
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
                'completion_date' => Carbon::now(),
            ]);
        });

        // Notice at a201b02s
        $notice = [
            'flow' => Notice::FLOW_RESPONSE_REASON,
            'step' => Notice::STEP_1,
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
                'content' => '拒絶理由通知対応：登録可能性評価レポート連絡済',
                'attribute' => 'お客様へ',
                'response_deadline' => $responseDeadlineBefore4Day ?? null,
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
                'content' => '拒絶理由通知対応：登録可能性評価レポート＆拒絶理由通知対応お申込み',
                'attribute' => null,
                'response_deadline' => $responseDeadlineBefore7Day ?? null,
                'response_deadline_ams' => $reasonNo->response_deadline ?? null,
            ],
            [
                'target_id' => $trademark->user_id,
                'type_acc' => NoticeDetail::TYPE_USER,
                'target_page' => $targetPage,
                'redirect_page' => $redirectPage,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                'is_action' => false,
                'content' => '拒絶理由通知対応：登録可能性評価レポート＆拒絶理由通知対応お申込み',
                'attribute' => null,
                'response_deadline' => $responseDeadlineBefore7Day ?? null,
                'response_deadline_ams' => $reasonNo->response_deadline ?? null,
            ],
        ];

        $this->noticeService->sendNotice([
            'notices' => $notice,
            'notice_details' => $noticeDetails,
        ]);
    }
}
