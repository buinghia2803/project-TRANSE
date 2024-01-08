<?php

namespace App\Http\Controllers\User;

use App\Helpers\FileHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\ComparisonTrademarkResult\PostRefusalPreQuestionReplyRequest;
use App\Http\Requests\User\ComparisonTrademarkResult\PostReReplyRefusalPreQuestionRequest;
use App\Models\Admin;
use App\Models\AppTrademark;
use App\Models\AppTrademarkProd;
use App\Models\MPriceList;
use App\Models\Notice;
use App\Models\NoticeDetail;
use App\Models\Payment;
use App\Models\PlanCorrespondence;
use App\Models\PlanCorrespondenceProd;
use App\Models\ReasonQuestionDetail;
use App\Models\SupportFirstTime;
use App\Models\TrademarkDocument;
use App\Services\AdminService;
use App\Services\Common\NoticeService as CommonNoticeService;
use App\Services\Common\TrademarkTableService;
use App\Services\ComparisonTrademarkResultService;
use App\Services\MNationService;
use App\Services\MPrefectureService;
use App\Http\Requests\Refusal\Select\UpdateOrCreateSelect02Request;
use App\Models\ReasonNo;
use App\Models\RegisterTrademarkRenewal;
use App\Services\MPriceListService;
use App\Services\NoticeDetailService;
use App\Services\PaymentService;
use App\Services\PlanCorrespondenceProdService;
use App\Services\PlanCorrespondenceService;
use App\Services\ReasonQuestionDetailService;
use App\Services\ReasonQuestionNoService;
use App\Services\ReasonRefNumProdService;
use App\Services\TrademarkDocumentService;
use App\Services\TrademarkService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ComparisonTrademarkResultController extends Controller
{
    protected MNationService $mNationService;
    protected MPrefectureService $mPrefectureService;
    protected TrademarkService $trademarkService;
    protected ReasonRefNumProdService $reasonRefNumProdService;
    protected TrademarkDocumentService $trademarkDocumentService;
    protected $comparisonTrademarkResultService;
    protected $trademarkTableService;
    protected $planCorrespondenceService;
    protected $planCorrespondenceProdService;
    protected $commonNoticeService;
    protected $paymentService;
    protected $adminService;
    protected $reasonQuestionDetailService;
    protected $noticeDetailService;
    protected ReasonQuestionNoService $reasonQuestionNoService;

    /**
     * Constructor
     *
     * @param ComparisonTrademarkResultService $comparisonTrademarkResultService
     * @param TrademarkTableService $trademarkTableService
     * @param ReasonRefNumProdService $reasonRefNumProdService
     * @param TrademarkDocumentService $trademarkDocumentService
     * @param PlanCorrespondenceService $planCorrespondenceService
     * @param PlanCorrespondenceProdService $planCorrespondenceProdService
     * @param ReasonQuestionDetailService $reasonQuestionDetailService
     * @param NoticeDetailService $noticeDetailService
     * @param ReasonQuestionNoService $reasonQuestionNoService
     *
     * @return  void
     */
    public function __construct(
        ComparisonTrademarkResultService $comparisonTrademarkResultService,
        TrademarkTableService $trademarkTableService,
        TrademarkDocumentService $trademarkDocumentService,
        PlanCorrespondenceService $planCorrespondenceService,
        PlanCorrespondenceProdService $planCorrespondenceProdService,
        CommonNoticeService $commonNoticeService,
        ReasonRefNumProdService $reasonRefNumProdService,
        MPrefectureService $mPrefectureService,
        TrademarkService $trademarkService,
        MNationService $mNationService,
        PaymentService $paymentService,
        AdminService $adminService,
        ReasonQuestionDetailService $reasonQuestionDetailService,
        NoticeDetailService $noticeDetailService,
        MPriceListService $mPriceListService,
        ReasonQuestionNoService $reasonQuestionNoService
    )
    {
        $this->comparisonTrademarkResultService = $comparisonTrademarkResultService;
        $this->reasonRefNumProdService = $reasonRefNumProdService;
        $this->trademarkTableService = $trademarkTableService;
        $this->trademarkDocumentService = $trademarkDocumentService;
        $this->planCorrespondenceService = $planCorrespondenceService;
        $this->planCorrespondenceProdService = $planCorrespondenceProdService;
        $this->commonNoticeService = $commonNoticeService;
        $this->adminService = $adminService;
        $this->mPrefectureService = $mPrefectureService;
        $this->mNationService = $mNationService;
        $this->paymentService = $paymentService;
        $this->trademarkService = $trademarkService;
        $this->reasonQuestionDetailService = $reasonQuestionDetailService;
        $this->noticeDetailService = $noticeDetailService;
        $this->mPriceListService = $mPriceListService;
        $this->reasonQuestionNoService = $reasonQuestionNoService;
    }

    /**
     * Notification index
     *
     * @param int $id
     * @return mixed
     */
    public function index(int $id)
    {
        $comparisonTrademarkResult = $this->comparisonTrademarkResultService->find($id);
        if (!$comparisonTrademarkResult) {
            abort(404);
        }

        $relation = $comparisonTrademarkResult->load(['trademark', 'planCorrespondence']);

        $checkDataPlanCorrespondence = false;
        $planCorrespondence = $relation->planCorrespondence;
        if (($planCorrespondence && $planCorrespondence->register_date) || $comparisonTrademarkResult->is_cancel) {
            $checkDataPlanCorrespondence = true;
        }
        $trademark = $relation->trademark;
        if (!$trademark) {
            abort(404);
        }
        if ($trademark->user_id != auth()->user()->id) {
            abort(403);
        }

        if (now() > $comparisonTrademarkResult->response_deadline) {
            return redirect()->route('user.refusal.notification.over', ['id' => $comparisonTrademarkResult->id]);
        }
        $isCancel = false;
        if (now() > $comparisonTrademarkResult->response_deadline) {
            $isCancel = true;
        }

        $trademarkDocuments = $this->trademarkDocumentService->getByCondition([
            'trademark_id' => $trademark->id,
            'type' => TrademarkDocument::TYPE_1,
            'flow' => Notice::FLOW_RESPONSE_REASON,
            'step' => Notice::STEP_1,
        ])->pluck('url');

        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_4, $comparisonTrademarkResult->trademark_id, [
            U201 => true,
        ]);

        return view('user.modules.comparison-trademark-result.index', compact(
            'isCancel',
            'trademark',
            'trademarkTable',
            'trademarkDocuments',
            'comparisonTrademarkResult',
            'checkDataPlanCorrespondence'
        ));
    }

    /**
     * Over
     *
     * @param int $id
     * @return mixed
     */
    public function over(int $id)
    {
        $comparisonTrademarkResult = $this->comparisonTrademarkResultService->find($id);
        if (!$comparisonTrademarkResult) {
            abort(404);
        }

        $relation = $comparisonTrademarkResult->load(['trademark', 'planCorrespondence']);

        $checkDataPlanCorrespondence = false;
        $planCorrespondence = $relation->planCorrespondence;
        if ($planCorrespondence) {
            $checkDataPlanCorrespondence = true;
        }
        $trademark = $relation->trademark;
        if (!$trademark) {
            abort(404);
        }
        if ($trademark->user_id != auth()->user()->id) {
            abort(403);
        }

        if (now() < $comparisonTrademarkResult->response_deadline) {
            abort(404);
        }
        $isCancel = false;
        if (now() > $comparisonTrademarkResult->response_deadline) {
            $isCancel = true;
        }

        $trademarkDocuments = $this->trademarkDocumentService->getByCondition([
            'trademark_id' => $trademark->id,
            'type' => TrademarkDocument::TYPE_1,
            'flow' => Notice::FLOW_RESPONSE_REASON,
            'step' => Notice::STEP_1,
        ])->pluck('url');

        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_4, $comparisonTrademarkResult->trademark_id);

        return view('user.modules.comparison-trademark-result.over', compact(
            'isCancel',
            'trademarkTable',
            'trademarkDocuments',
            'comparisonTrademarkResult',
            'checkDataPlanCorrespondence'
        ));
    }

    /**
     * Plans index
     *
     * @param int $id
     * @return mixed
     */
    public function plansIndex(int $id)
    {
        $comparisonTrademarkResult = $this->comparisonTrademarkResultService->find($id);
        if (!$comparisonTrademarkResult) {
            abort(404);
        }

        $relation = $comparisonTrademarkResult->load('trademark');
        $trademark = $relation->trademark;
        if (!$trademark) {
            abort(404);
        }
        if ($trademark->user_id != auth()->user()->id) {
            abort(403);
        }

        $trademarkDocuments = $this->trademarkDocumentService->getByCondition([
            'trademark_id' => $trademark->id,
            'type' => TrademarkDocument::TYPE_1,
            'flow' => Notice::FLOW_RESPONSE_REASON,
            'step' => Notice::STEP_1,
        ])->pluck('url');

        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_4, $comparisonTrademarkResult->trademark_id, [
            U201 => true,
        ]);

        return view('user.modules.comparison-trademark-result.plans_index', compact(
            'trademark',
            'trademarkTable',
            'trademarkDocuments',
            'comparisonTrademarkResult'
        ));
    }

    /**
     * Pack
     *
     * @param int $id
     * @return mixed
     */
    public function pack(int $id)
    {
        $comparisonTrademarkResult = $this->comparisonTrademarkResultService->find($id);
        if (!$comparisonTrademarkResult) {
            abort(404);
        }

        $relation = $comparisonTrademarkResult->load(['trademark.appTrademark', 'planCorrespondence']);

        $planCorrespondence = $relation->planCorrespondence ?? null;

        $trademark = $relation->trademark;
        if (!$trademark) {
            abort(404);
        }
        if ($trademark->user_id != auth()->user()->id) {
            abort(403);
        }

        $appTrademark = $trademark->appTrademark;
        if (!$appTrademark || $appTrademark->pack == AppTrademark::PACK_A || $appTrademark->pack == AppTrademark::PACK_B) {
            abort(404);
        }

        $trademarkDocuments = $this->trademarkDocumentService->getByCondition([
            'trademark_id' => $trademark->id,
            'type' => TrademarkDocument::TYPE_1,
            'flow' => Notice::FLOW_RESPONSE_REASON,
            'step' => Notice::STEP_1,
        ])->pluck('url');

        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_4, $comparisonTrademarkResult->trademark_id, [
            U201 => true,
        ]);

        $checkSubmit = false;
        if ($planCorrespondence) {
            $checkSubmit = true;
        }

        return view('user.modules.comparison-trademark-result.pack', compact(
            'trademark',
            'checkSubmit',
            'trademarkTable',
            'trademarkDocuments',
            'comparisonTrademarkResult'
        ));
    }

    /**
     * Create pack
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function createPack(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $relation = $this->comparisonTrademarkResultService->find($request->comparison_trademark_result_id)->load(['machingResult', 'trademark.appTrademark.appTrademarkProd']);
            $machingResult = $relation->machingResult;
            $trademark = $relation->trademark;
            $appTrademark = $relation->trademark->appTrademark;
            $appTrademarkProds = $relation->trademark->appTrademark->appTrademarkProd->where('is_apply', AppTrademarkProd::IS_APPLY)->pluck('id');

            $planCorrespondence = $this->planCorrespondenceService->updateOrCreate([
                'comparison_trademark_result_id' => $request->comparison_trademark_result_id,
            ], [
                'type' => PlanCorrespondence::TYPE_3,
                'updated_at' => now(),
            ]);

            foreach ($appTrademarkProds as $value) {
                $this->planCorrespondenceProdService->updateOrCreate([
                    'plan_correspondence_id' => $planCorrespondence->id,
                    'is_register' => PlanCorrespondenceProd::IS_REGISTER,
                    'app_trademark_prod_id' => $value
                ], [
                    'updated_at' => now(),
                ]);
            }

            // Update Notice at a201a (No 21: H I)
            $stepBeforeNotice = $this->noticeDetailService->findByCondition([
                'type_acc' => NoticeDetail::TYPE_USER,
                'is_answer' => NoticeDetail::IS_NOT_ANSWER,
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

            $tanTou = $this->adminService->findByCondition(['role' => Admin::ROLE_ADMIN_TANTO])->first();
            $targetPage = route('user.refusal.plans.pack', ['id' => $request->comparison_trademark_result_id]);
            $responseDeadline = $machingResult->calculateResponseDeadline(-3);
            $packAppTrademark = $appTrademark->pack;
            $redirectPage = route('admin.refusal.eval-report.create-reason', ['id' => $request->comparison_trademark_result_id]);
            $noticeDetail = [
                // A-000top
                [
                    'target_id' => $tanTou->id,
                    'type_acc' => NoticeDetail::TYPE_MANAGER,
                    'target_page' => $targetPage,
                    'redirect_page' => $redirectPage,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                    'is_action' => true,
                    'content' => '担当者　拒絶理由通知対応：登録可能性評価レポート　理由数設定・作成',
                    'response_deadline' => $responseDeadline,
                ],
                // A-000anken_top
                [
                    'target_id' => $tanTou->id,
                    'type_acc' => NoticeDetail::TYPE_MANAGER,
                    'target_page' => $targetPage,
                    'redirect_page' => $redirectPage,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'attribute' => '所内処理',
                    'content' => '担当者　拒絶理由通知対応：登録可能性評価レポート　理由数設定・作成',
                    'response_deadline' => $responseDeadline,
                ],
            ];

            if ($packAppTrademark == AppTrademark::PACK_C) {
                $noticeDetail[] = [
                    'target_id' => auth()->user()->id,
                    'type_acc' => NoticeDetail::TYPE_USER,
                    'target_page' => $targetPage,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_DEFAULT,
                    'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                    'content' => '拒絶理由通知対応：対応依頼済・返信待ち',
                    'response_deadline' => $responseDeadline,
                ];

                $noticeDetail[] = [
                    'target_id' => auth()->user()->id,
                    'type_acc' => NoticeDetail::TYPE_USER,
                    'target_page' => $targetPage,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'content' => '拒絶理由通知対応：対応依頼済・返信待ち',
                    'response_deadline' => $responseDeadline,
                ];
            }

            $this->commonNoticeService->sendNotice([
                'notices' => [
                    'trademark_id' => $request->trademark_id,
                    'trademark_info_id' => null,
                    'user_id' => auth()->user()->id,
                    'flow' => Notice::FLOW_RESPONSE_REASON,
                    'step' => Notice::STEP_1
                ],
                'notice_details' => $noticeDetail,
            ]);
            DB::commit();

            return response()->json([], CODE_SUCCESS_200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            return response()->json([], CODE_ERROR_500);
        }
    }

    /**
     * Show select plan 02.
     *
     * @param Request $request
     * @param int $id
     * @return View
     */
    public function showSelectPlan02(Request $request, int $id): View
    {
        if (!$request->has('reason_no_id') || !$request->reason_no_id) {
            abort(404);
        }

        $comparisonTrademarkResult = $this->comparisonTrademarkResultService->find($id);

        if (!$comparisonTrademarkResult) {
            abort(404);
        }
        $comparisonTrademarkResult->load(['trademark', 'planCorrespondence']);
        $user = \Auth::user();

        if ($comparisonTrademarkResult->trademark->user_id != $user->id) {
            abort(404);
        }
        $payment = $this->paymentService->findByCondition([
            'target_id' => $comparisonTrademarkResult->planCorrespondence->id,
            'from_page' => U201SELECT02 . '_' . $request->reason_no_id,
        ])->first();

        $statusRegister = 0;
        $payerInfo = null;
        if ($payment) {
            $payment->load('payerInfo');
            $payerInfo = $payment->payerInfo;
            $statusRegister = $payment->payment_status;
        }

        $comparisonTrademarkResult->load(['trademark', 'planCorrespondence']);
        $planCorrespondence = $comparisonTrademarkResult->planCorrespondence;
        $trademark = $comparisonTrademarkResult->trademark;
        $trademark->load([
            'appTrademark.appTrademarkProd' => function ($query) {
                return $query->where('is_apply', AppTrademarkProd::IS_APPLY);
            },
        ]);

        $appTrademarkProd = $trademark->appTrademark->appTrademarkProd;
        $appProdIds = $appTrademarkProd->pluck('id')->toArray();
        $reasonNo = ReasonNo::find($request->reason_no_id);
        $maxReasonNo = (int) $reasonNo->round;

        $planCorrespondenceProds = $this->planCorrespondenceProdService->findByCondition([
            'app_trademark_prod_ids' => $appProdIds,
        ], [
            'appTrademarkProd.mProduct.mDistinction', 'reasonRefNumProds',
        ])->get();

        $planCorrespondenceProdIds = $planCorrespondenceProds->pluck('id')->toArray();
        $totalProd = $planCorrespondenceProds->count();
        $totalProdRound = $planCorrespondenceProds->where('round', '<=', $maxReasonNo)->count();
        $isBlockScreen = false;
        if ($totalProdRound && $totalProd > $totalProdRound) {
            $isBlockScreen = true;
        }

        $reasonRefNumProds = $this->reasonRefNumProdService->findByCondition([
            'plan_correspondence_prod_ids' => $planCorrespondenceProdIds,
            'reason_no_id' => $request->reason_no_id
        ], ['planCorrespondenceProd.appTrademarkProd.mProduct.mDistinction'])->get();

        $planCorrespondenceProds = $planCorrespondenceProds->map(function ($item) use ($request) {
            $reasonRefNumProds = $item->reasonRefNumProds;
            $item->reasonRefNumProd = $reasonRefNumProds->where('reason_no_id', $request->reason_no_id)->first();

            return $item;
        });

        $distinctions = $planCorrespondenceProds->groupBy('appTrademarkProd.mProduct.mDistinction.name')->sortKeys();
        $productIsNotRegister = $planCorrespondenceProds->where('is_register', PlanCorrespondenceProd::IS_REGISTER_FALSE)->count();

        $paymentFee = $this->comparisonTrademarkResultService->getSystemFee(MPriceList::EACH_PAYMENT, MPriceList::BANK_TRANSFER_HANDLING);
        $selectPlanAPrice = $this->comparisonTrademarkResultService->getSelectPlanPrice(MPriceList::REASONS_REFUSAL, MPriceList::SELECT_PLAN_A_RATING);
        $selectPlanBCDEPrice = $this->comparisonTrademarkResultService->getSelectPlanPrice(MPriceList::REASONS_REFUSAL, MPriceList::SELECT_PLAN_B_C_D_E);

        $setting = $this->comparisonTrademarkResultService->getSetting();

        $trademarkDoc = $this->trademarkDocumentService->getByCondition([
            'trademark_id' => $comparisonTrademarkResult->trademark_id,
            'type' => TrademarkDocument::TYPE_1,
            'flow' => Notice::FLOW_RESPONSE_REASON,
            'step' => Notice::STEP_1,
        ]);

        $nations = $this->mNationService->listNationOptions();
        $prefectures = $this->mPrefectureService->listPrefectureOptions();
        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_4, $comparisonTrademarkResult->trademark_id);
        $data = $comparisonTrademarkResult;

        return view(
            'user.modules.plan.select02',
            compact(
                'data',
                'isBlockScreen',
                'reasonNo',
                'maxReasonNo',
                'nations',
                'setting',
                'trademark',
                'selectPlanAPrice',
                'selectPlanBCDEPrice',
                'planCorrespondence',
                'reasonRefNumProds',
                'trademarkTable',
                'paymentFee',
                'trademarkDoc',
                'payerInfo',
                'prefectures',
                'statusRegister',
                'distinctions',
                'productIsNotRegister'
            )
        );
    }

    /**
     * Save data in form.
     *
     * @param Request $request
     * @param Request $request
     */
    public function saveSelectPlan02(UpdateOrCreateSelect02Request $request, int $id)
    {
        $params = $request->all();
        $params['id'] = $id;
        $result = $this->comparisonTrademarkResultService->saveDataSelectPlan($params);
        switch ($result['redirect_to']) {
            case AppTrademark::REDIRECT_TO_COMMON_PAYMENT:
                return redirect()->route('user.payment.index', ['s' => $result['key_session']]);
            case AppTrademark::REDIRECT_TO_GMO_THANK_YOU:
                return redirect()->route('user.payment.GMO.thank-you', ['payment_id' => $result['payment_id']]);
            case AppTrademark::REDIRECT_TO_QUOTE:
                return redirect()->route('user.quote', ['id' => $result['payment_id']]);
            case 'false':
                return redirect()->back()->with('error', __('messages.common.errors.Common_E025'));
        }
    }

    /**
     * Get Permission Edit Page
     *
     * @param int $reasonQuestionNoId
     * @return bool
     */
    public function getPermissionEditPage(int $reasonQuestionNoId): bool
    {
        $data = $this->reasonQuestionDetailService->findByCondition([
            'reason_question_no_id' => $reasonQuestionNoId,
        ])->get();
        foreach ($data as $item) {
            if (($item->is_answer != ReasonQuestionDetail::IS_ANSWER) || ($item->is_confirm != ReasonQuestionDetail::IS_CONFIRM_TRUE)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get Permission Edit Page
     *
     * @param int $reasonQuestionNoId
     * @return bool
     */
    public function getPermissionEditPageU202N(int $reasonQuestionNoId): bool
    {
        $data = $this->reasonQuestionDetailService->findByCondition([
            'reason_question_no_id' => $reasonQuestionNoId,
        ])->get();
        foreach ($data as $item) {
            if ($item->is_answer != ReasonQuestionDetail::IS_ANSWER) {
                return true;
            }
        }

        return false;
    }

    /**
     *  Get refusal pre question reply - u202
     *
     * @param Request $request
     * @param integer $id - comparison_trademark_result_id?reason_question_no={reason_question_no.id}
     * @return View
     */
    public function getRefusalPreQuestionReply(Request $request, int $id): View
    {
        $inputs = $request->all();
        $comparisonTrademarkResult = $this->comparisonTrademarkResultService->getComparisonTradeMarkResultAuthenticate($id);
        if (!$comparisonTrademarkResult) {
            abort(404);
        }

        if (!isset($inputs['reason_question_no']) || empty($inputs['reason_question_no'])) {
            abort(404);
        }
        $comparisonTrademarkResult->load('planCorrespondence.reasonQuestion', 'trademark');
        $reasonQuestion = null;
        $reasonQuestionNo = null;
        if ($comparisonTrademarkResult->planCorrespondence && $comparisonTrademarkResult->planCorrespondence->reasonQuestion) {
            $reasonQuestion = $comparisonTrademarkResult->planCorrespondence->reasonQuestion;
            if ($reasonQuestion) {
                $reasonQuestionNo = $this->reasonQuestionNoService->findByCondition([
                    'id' => $inputs['reason_question_no'],
                    'reason_question_id' => $reasonQuestion->id,
                ])->first();
            }
        }

        if (!$reasonQuestionNo) {
            abort(404);
        }
        //check reason_question_details: is_confirm = 1, is_answer = 1
        $flagEditPage = $this->getPermissionEditPage($reasonQuestionNo->id);

        $reasonQuestionDetails = $this->reasonQuestionDetailService->getReasonQuestionDetailData([
            'comparison_trademark_result_id' => $comparisonTrademarkResult->id,
            'is_answer' => ReasonQuestionDetail::IS_NOT_ANSWER,
            'sort_by' => SORT_TYPE_ASC,
            'reason_question_no_id' => $inputs['reason_question_no']
        ]);

        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_4, $comparisonTrademarkResult->trademark_id, [
            U201 => true,
        ]);
        $trademarkDocuments = $this->trademarkDocumentService->getByCondition([
            'trademark_id' => $comparisonTrademarkResult->trademark_id,
            'type' => TrademarkDocument::TYPE_1,
            'flow' => Notice::FLOW_RESPONSE_REASON,
            'step' => Notice::STEP_1,
        ])->pluck('url');

        $data = [
            'comparisonTrademarkResult' => $comparisonTrademarkResult,
            'reasonQuestionDetails' => $reasonQuestionDetails,
            'trademarkTable' => $trademarkTable,
            'trademarkDocuments' => $trademarkDocuments,
            'reasonQuestion' => $reasonQuestion,
            'flagEditPage' => $flagEditPage,
            'reasonQuestionNo' => $reasonQuestionNo
        ];

        return view('user.modules.comparison-trademark-result.u202.index', $data);
    }

    /**
     * Get Refusal PreQuestion Reply Kakunin - u202 Kakunin
     *
     * @param Request $request
     * @param integer $id comparison_trademark_result_id?reason_question_no={reason_question_no.id}
     * @return View
     */
    public function getRefusalPreQuestionReplyKakunin(Request $request, int $id): View
    {
        $inputs = $request->all();
        $comparisonTrademarkResult = $this->comparisonTrademarkResultService->getComparisonTradeMarkResultAuthenticate($id);
        if (!$comparisonTrademarkResult || !isset($inputs['reason_question_no']) || empty($inputs['reason_question_no'])) {
            abort(404);
        }
        $comparisonTrademarkResult->load('planCorrespondence.reasonQuestion');
        $reasonQuestion = null;
        $reasonQuestionNo = null;
        if ($comparisonTrademarkResult->planCorrespondence && $comparisonTrademarkResult->planCorrespondence->reasonQuestion) {
            $reasonQuestion = $comparisonTrademarkResult->planCorrespondence->reasonQuestion;
            if ($reasonQuestion) {
                $reasonQuestionNo = $this->reasonQuestionNoService->findByCondition([
                    'id' => $inputs['reason_question_no'],
                    'reason_question_id' => $reasonQuestion->id,
                ])->first();
            }
        }
        if (!$reasonQuestionNo) {
            abort(404);
        }
        //check reason_question_details: is_confirm = 1, is_answer = 1
        $flagEditPage = $this->getPermissionEditPage($reasonQuestionNo->id);

        $reasonQuestionDetails = $this->reasonQuestionDetailService->getReasonQuestionDetailData([
            'comparison_trademark_result_id' => $comparisonTrademarkResult->id,
            'is_answer' => ReasonQuestionDetail::IS_NOT_ANSWER,
            'sort_by' => SORT_TYPE_ASC,
            'reason_question_no_id' => $inputs['reason_question_no']
        ]);

        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_4, $comparisonTrademarkResult->trademark_id, [
            U201 => true,
        ]);
        $trademarkDocuments = $this->trademarkDocumentService->findByCondition([
            'trademark_id' => $comparisonTrademarkResult->trademark_id,
            'type' => TrademarkDocument::TYPE_1
        ])->pluck('url');

        $data = [
            'comparisonTrademarkResult' => $comparisonTrademarkResult,
            'reasonQuestionDetails' => $reasonQuestionDetails,
            'trademarkTable' => $trademarkTable,
            'trademarkDocuments' => $trademarkDocuments,
            'reasonQuestion' => $reasonQuestion,
            'reasonQuestionNo' => $reasonQuestionNo,
            'flagEditPage' => $flagEditPage
        ];

        return view('user.modules.comparison-trademark-result.u202.u202kakunin', $data);
    }

    /**
     * Post Refusal PreQuestion Reply - u202 post
     *
     * @param Request $request
     * @param integer $id - comparison_trademark_result_id
     * @return RedirectResponse
     * @throws \Exception
     */
    public function postRefusalPreQuestionReply(Request $request, int $id): RedirectResponse
    {
        $inputs = $request->all();
        $comparisonTrademarkResult = $this->comparisonTrademarkResultService->getComparisonTradeMarkResultAuthenticate($id);
        if (!$comparisonTrademarkResult || !$comparisonTrademarkResult->planCorrespondence || !$comparisonTrademarkResult->planCorrespondence->reasonQuestion) {
            abort(404);
        }

        $reasonQuestion = $comparisonTrademarkResult->planCorrespondence->reasonQuestion;
        if ($reasonQuestion) {
            $reasonQuestionNo = $this->reasonQuestionNoService->findByCondition([
                'id' => $inputs['reason_question_no'],
                'reason_question_id' => $reasonQuestion->id,
            ])->first();
            if (!$reasonQuestionNo) {
                abort(404);
            }

            //check reason_question_details: is_confirm = 1, is_answer = 1
            $flagEditPage = $this->getPermissionEditPage($reasonQuestionNo->id);
            if (!$flagEditPage) {
                abort(404);
            }
        }

        $reasonQuestionDetailsIds = $this->reasonQuestionDetailService
            ->getReasonQuestionDetailData([
                'comparison_trademark_result_id' => $comparisonTrademarkResult->id,
                'is_answer' => ReasonQuestionDetail::IS_NOT_ANSWER,
                'sort_by' => SORT_TYPE_DESC,
                'reason_question_no_id' => $inputs['reason_question_no']
            ])->pluck('id');

        $inputs['reason_question_detail_ids'] = $reasonQuestionDetailsIds;
        $inputs['trademark_id'] = $comparisonTrademarkResult->trademark_id;
        $inputs['comparison_trademark_result_id'] = $id;

        $result = $this->reasonQuestionDetailService->postRefusalPreQuestionReply($inputs);
        if ($result['status']) {
            if ($result['redirect_page']) {
                if ($result['redirect_page'] == U000ANKEN_TOP) {
                    //redirect to u000anken_top
                    return redirect()->route('user.application-detail.index', $comparisonTrademarkResult->trademark_id)->with('message', __('messages.general.Common_E047'));
                } elseif ($result['redirect_page'] == U202) {
                    //redirect to u202
                    return redirect()->route('user.refusal.pre-question.reply', [
                        'id' => $comparisonTrademarkResult->id,
                        'reason_question_no' => $reasonQuestionNo->id,
                    ]);
                } elseif ($result['redirect_page'] == U202KAKUNIN) {
                    //redirect to U202KAKUNIN
                    if ($inputs['from_page'] == U202KAKUNIN) {
                        //redirect to u000top
                        return redirect()->route('user.top')->with('message', __('messages.general.Question_U202_S001'));
                    }
                    return redirect()->route('user.u202refusal.pre-question.reply.kakunin', [
                        'id' => $comparisonTrademarkResult->id,
                        'reason_question_no' => $reasonQuestionNo->id,
                    ]);
                }
            }
        }

        return redirect()->back()->withInput();
    }

    /**
     * Post Refusal PreQuestion Reply Delete File Ajax
     *
     * @param Request $request
     * @return void
     */
    public function postRefusalPreQuestionReplyDeleteFileAjax(Request $request)
    {
        if ($request->ajax()) {
            $inputs = $request->all();
            $reasonQuestionDetail = $this->reasonQuestionDetailService->find($inputs['reason_question_detail_id']);
            if ($reasonQuestionDetail) {
                $attachment = $reasonQuestionDetail->attachment;
                if ($attachment) {
                    $attachment = json_decode($attachment, true);
                    foreach ($attachment as $k => $file) {
                        if ($file == $inputs['path']) {
                            unset($attachment[$k]);
                            //delete file
                            FileHelper::unlink($file);
                        }
                    }
                    $this->reasonQuestionDetailService->update($reasonQuestionDetail, ['attachment' => json_encode($attachment)]);

                    return response()->json(['status' => true]);
                }
            }
        }
        return response()->json(['status' => false]);
    }

    /**
     * Get re reply refusal pre question - u202n
     *
     * @param Request $request
     * @param int $id - {comparison_trademark_result_id}?reason_question_no={reason_question_no.id}
     * @return void
     */
    public function getReReplyRefusalPreQuestion(Request $request, int $id)
    {
        $inputs = $request->all();
        $comparisonTrademarkResult = $this->comparisonTrademarkResultService->getComparisonTradeMarkResultAuthenticate($id);
        if (!$comparisonTrademarkResult || !isset($inputs['reason_question_no']) || empty($inputs['reason_question_no'])) {
            abort(404);
        }

        $comparisonTrademarkResult->load('planCorrespondence.reasonQuestion');
        $reasonQuestion = null;
        $reasonQuestionNo = null;
        if ($comparisonTrademarkResult->planCorrespondence && $comparisonTrademarkResult->planCorrespondence->reasonQuestion) {
            $reasonQuestion = $comparisonTrademarkResult->planCorrespondence->reasonQuestion;
            if ($reasonQuestion) {
                $reasonQuestionNo = $this->reasonQuestionNoService->findByCondition([
                    'id' => $inputs['reason_question_no'],
                    'reason_question_id' => $reasonQuestion ? $reasonQuestion->id : 0,
                ])->first();
            }
        }
        if (!$reasonQuestionNo) {
            abort(404);
        }

        //check reason_question_details: is_confirm = 1, is_answer = 1
        $flagEditPage = $this->getPermissionEditPageU202N($reasonQuestionNo->id);

        $reasonQuestionDetails = $this->reasonQuestionDetailService->getReasonQuestionDetailData([
            'comparison_trademark_result_id' => $comparisonTrademarkResult->id,
            'sort_by' => SORT_TYPE_ASC,
            'reason_question_no_id' => $inputs['reason_question_no'],
        ]);

        $reasonQuestionDetailsOld = $this->reasonQuestionDetailService->getReasonQuestionDetailData([
            'comparison_trademark_result_id' => $comparisonTrademarkResult->id,
            'sort_by' => SORT_TYPE_DESC,
            'reason_question_no_id' => $inputs['reason_question_no'],
            'compare' => '<'
        ]);

        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_4, $comparisonTrademarkResult->trademark_id, [
            U201 => true,
        ]);

        $trademarkDocuments = $this->trademarkDocumentService->getByCondition([
            'trademark_id' => $comparisonTrademarkResult->trademark_id,
            'type' => TrademarkDocument::TYPE_1,
            'flow' => Notice::FLOW_RESPONSE_REASON,
            'step' => Notice::STEP_1,
        ])->pluck('url');

        $data = [
            'id' => $id,
            'comparisonTrademarkResult' => $comparisonTrademarkResult,
            'trademarkTable' => $trademarkTable,
            'trademarkDocuments' => $trademarkDocuments,
            'reasonQuestion' => $reasonQuestion,
            'reasonQuestionNo' => $reasonQuestionNo,
            'reasonQuestionDetails' => $reasonQuestionDetails,
            'reasonQuestionDetailsOld' => $reasonQuestionDetailsOld,
            'flagEditPage' => $flagEditPage
        ];

        return view('user.modules.comparison-trademark-result.u202n.index', $data);
    }

    /**
     * Post re reply refusal pre question - u202n post & u202n-kakunin post
     *
     * @param Request $request
     * @param int $id
     * @return void
     */
    public function postReReplyRefusalPreQuestion(PostReReplyRefusalPreQuestionRequest $request, int $id)
    {
        $inputs = $request->all();
        $comparisonTrademarkResult = $this->comparisonTrademarkResultService->getComparisonTradeMarkResultAuthenticate($id);
        if (!$comparisonTrademarkResult || !isset($inputs['reason_question_no']) || empty($inputs['reason_question_no'])) {
            abort(404);
        }
        $comparisonTrademarkResult->load('planCorrespondence.reasonQuestion');
        if ($comparisonTrademarkResult->planCorrespondence && $comparisonTrademarkResult->planCorrespondence->reasonQuestion) {
            $reasonQuestion = $comparisonTrademarkResult->planCorrespondence->reasonQuestion;
            if ($reasonQuestion) {
                $reasonQuestionNo = $this->reasonQuestionNoService->findByCondition([
                    'id' => $inputs['reason_question_no'],
                    'reason_question_id' => $reasonQuestion->id,
                ])->first();
                if (!$reasonQuestionNo) {
                    abort(404);
                }
                //check reason_question_details: is_confirm = 1, is_answer = 1
                $flagEditPage = $this->getPermissionEditPageU202N($reasonQuestionNo->id);
                if (!$flagEditPage) {
                    abort(404);
                }
            }
        }
        $inputs['trademark_id'] = $comparisonTrademarkResult->trademark_id;
        $inputs['comparison_trademark_result_id'] = $id;
        $inputs['reason_question_detail_ids'] = $this->reasonQuestionDetailService->getReasonQuestionDetailData([
            'comparison_trademark_result_id' => $comparisonTrademarkResult->id,
            'sort_by' => SORT_TYPE_DESC,
            'reason_question_no_id' => $inputs['reason_question_no']
        ])->pluck('id');

        $result = $this->reasonQuestionDetailService->postSaveReReplyRefusalPreQuestion($inputs);

        if ($result && $result['status']) {
            if ($result['redirect_page']) {
                if ($result['redirect_page'] == U000ANKEN_TOP) {
                    //redirect to u000anken_top
                    return redirect()->route('user.application-detail.index', $comparisonTrademarkResult->trademark_id)->with('message', __('messages.general.Common_E047'));
                } elseif ($result['redirect_page'] == U202N) {
                    //redirect to u202n
                    return redirect()->route('user.refusal.pre-question.re-reply', [
                        'id' => $id,
                        'reason_question_no' => $inputs['reason_question_no'],
                    ]);
                } elseif ($result['redirect_page'] == U202N_KAKUNIN) {
                    if ($inputs['from_page'] == U202N_DRAFT_REDIRECT_KAKUNIN) {
                        return redirect()->route('user.refusal.pre-question.re-reply-u202n-kakunin', [
                            'id' => $id,
                            'reason_question_no' => $inputs['reason_question_no'],
                        ]);
                    }
                    //redirect to u000top
                    return redirect()->route('user.top')->with('message', __('messages.general.Question_U202_S001'));
                }
            }
        }

        return redirect()->back()->withInput();
    }

    /**
     * Get ReReply Refusal PreQuestionKakunin - u202n kakunin
     *
     * @param int $id
     * @return View
     */
    public function getReReplyRefusalPreQuestionKakunin(Request $request, int $id): View
    {
        $inputs = $request->all();
        $comparisonTrademarkResult = $this->comparisonTrademarkResultService->getComparisonTradeMarkResultAuthenticate($id);
        if (!$comparisonTrademarkResult) {
            abort(404);
        }

        $comparisonTrademarkResult->load('planCorrespondence.reasonQuestion');
        $reasonQuestion = null;
        $reasonQuestionNo = null;
        if ($comparisonTrademarkResult->planCorrespondence && $comparisonTrademarkResult->planCorrespondence->reasonQuestion) {
            $reasonQuestion = $comparisonTrademarkResult->planCorrespondence->reasonQuestion;
            if ($reasonQuestion) {
                $reasonQuestionNo = $this->reasonQuestionNoService->findByCondition([
                    'id' => $inputs['reason_question_no'],
                    'reason_question_id' => $reasonQuestion->id,
                ])->first();
            }
        }
        if (!$reasonQuestionNo) {
            abort(404);
        }
        //check reason_question_details: is_confirm = 1, is_answer = 1
        $flagEditPage = $this->getPermissionEditPageU202N($reasonQuestionNo->id);

        $reasonQuestionDetails = $this->reasonQuestionDetailService->getReasonQuestionDetailData([
            'comparison_trademark_result_id' => $comparisonTrademarkResult->id,
            'sort_by' => SORT_TYPE_ASC,
            'reason_question_no_id' => $inputs['reason_question_no'],
        ]);

        $reasonQuestionDetailsOld = $this->reasonQuestionDetailService->getReasonQuestionDetailData([
            'comparison_trademark_result_id' => $comparisonTrademarkResult->id,
            'sort_by' => SORT_TYPE_DESC,
            'reason_question_no_id' => $inputs['reason_question_no'],
            'compare' => '<'
        ]);
        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_4, $comparisonTrademarkResult->trademark_id, [
            U201 => true,
        ]);
        $trademarkDocuments = $this->trademarkDocumentService->findByCondition([
            'trademark_id' => $comparisonTrademarkResult->trademark_id,
            'type' => TrademarkDocument::TYPE_1
        ])->pluck('url');

        $data = [
            'id' => $id,
            'comparisonTrademarkResult' => $comparisonTrademarkResult,
            'trademarkTable' => $trademarkTable,
            'trademarkDocuments' => $trademarkDocuments,
            'reasonQuestion' => $reasonQuestion,
            'reasonQuestionNo' => $reasonQuestionNo,
            'reasonQuestionDetails' => $reasonQuestionDetails,
            'reasonQuestionDetailsOld' => $reasonQuestionDetailsOld,
            'flagEditPage' => $flagEditPage
        ];

        return view('user.modules.comparison-trademark-result.u202n.u202n_kakunin', $data);
    }
}
