<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\CommonHelper;
use App\Models\Agent;
use App\Models\AgentGroup;
use App\Models\AgentGroupMap;
use App\Models\AppTrademark;
use App\Models\MailTemplate;
use App\Models\Notice;
use App\Models\NoticeDetail;
use App\Models\NoticeDetailBtn;
use App\Models\Payment;
use App\Models\Precheck;
use App\Models\RegisterTrademark;
use App\Models\RegisterTrademarkRenewal;
use App\Models\Trademark;
use App\Models\TrademarkDocument;
use App\Services\AgentGroupService;
use App\Services\AgentService;
use App\Services\AppTrademarkService;
use App\Services\Common\TrademarkTableService;
use App\Services\ComparisonTrademarkResultService;
use App\Services\FreeHistoryService;
use App\Services\MailTemplateService;
use App\Services\NoticeDetailService;
use App\Services\NoticeService;
use App\Services\PaymentService;
use App\Services\PrecheckService;
use App\Services\RegisterTrademarkService;
use App\Services\SupportFirstTimeService;
use App\Services\TrademarkService;
use App\Services\NoticeDetailBtnService;
use App\Services\MatchingResultService;
use App\Services\DocSubmissionService;
use App\Services\TrademarkDocumentService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\Common\NoticeService as CommonNoticeService;
use App\Services\RegisterTrademarkRenewalService;
use Illuminate\Http\RedirectResponse;
use IntlDateFormatter;

class TrademarkController extends BaseController
{
    private TrademarkService $trademarkService;
    private TrademarkTableService $trademarkTableService;
    private PaymentService $paymentService;
    private NoticeService $noticeService;
    private NoticeDetailService $noticeDetailService;
    private SupportFirstTimeService $supportFirstTimeService;
    private PrecheckService $precheckService;
    private FreeHistoryService $freeHistoryService;
    private ComparisonTrademarkResultService $comparisonTrademarkResultService;
    private AppTrademarkService $appTrademarkService;
    private CommonNoticeService $commonNoticeService;
    private NoticeDetailBtnService $noticeDetailBtnService;
    private AgentService $agentService;
    private MatchingResultService $matchingResultService;
    private DocSubmissionService $docSubmissionService;
    protected $trademarkDocumentService;
    protected $registerTrademarkRenewalService;
    protected AgentGroupService $agentGroupService;
    private MailTemplateService $mailTemplateService;
    private RegisterTrademarkService $registerTrademarkService;

    /**
     * Constructor
     *
     * @param TrademarkService $trademarkService
     * @param TrademarkTableService $trademarkTableService
     * @param PaymentService $paymentService
     * @param NoticeService $noticeService
     * @param MatchingResultService $matchingResultService
     * @param DocSubmissionService $docSubmissionService
     */
    public function __construct(
        TrademarkService                 $trademarkService,
        TrademarkTableService            $trademarkTableService,
        PaymentService                   $paymentService,
        NoticeService                    $noticeService,
        NoticeDetailService              $noticeDetailService,
        SupportFirstTimeService          $supportFirstTimeService,
        PrecheckService                  $precheckService,
        FreeHistoryService               $freeHistoryService,
        ComparisonTrademarkResultService $comparisonTrademarkResultService,
        AppTrademarkService              $appTrademarkService,
        AgentService                     $agentService,
        CommonNoticeService              $commonNoticeService,
        NoticeDetailBtnService           $noticeDetailBtnService,
        MatchingResultService            $matchingResultService,
        DocSubmissionService             $docSubmissionService,
        TrademarkDocumentService         $trademarkDocumentService,
        RegisterTrademarkRenewalService  $registerTrademarkRenewalService,
        RegisterTrademarkService         $registerTrademarkService,
        AgentGroupService                $agentGroupService,
        MailTemplateService              $mailTemplateService
    )
    {
        parent::__construct();
        $this->middleware('permission:document_to_check.updateTrademark')->only(['updateTrademark']);
        $this->middleware('permission:decided_to_refuse.postFinalRefusal')->only(['postFinalRefusal']);
        $this->middleware('permission:extension_period.updateDataAlert')->only(['updateDataAlert']);
        $this->middleware('permission:extension_period.registrationNotify')->only(['registrationNotify']);
        $this->middleware('permission:extension_period.postRegistrationNotify')->only(['postRegistrationNotify']);

        $this->trademarkService = $trademarkService;
        $this->trademarkTableService = $trademarkTableService;
        $this->paymentService = $paymentService;
        $this->noticeService = $noticeService;
        $this->noticeDetailService = $noticeDetailService;
        $this->supportFirstTimeService = $supportFirstTimeService;
        $this->precheckService = $precheckService;
        $this->freeHistoryService = $freeHistoryService;
        $this->comparisonTrademarkResultService = $comparisonTrademarkResultService;
        $this->appTrademarkService = $appTrademarkService;
        $this->agentService = $agentService;
        $this->commonNoticeService = $commonNoticeService;
        $this->noticeDetailBtnService = $noticeDetailBtnService;
        $this->matchingResultService = $matchingResultService;
        $this->docSubmissionService = $docSubmissionService;
        $this->trademarkDocumentService = $trademarkDocumentService;
        $this->registerTrademarkRenewalService = $registerTrademarkRenewalService;
        $this->registerTrademarkService = $registerTrademarkService;
        $this->agentGroupService = $agentGroupService;
        $this->mailTemplateService = $mailTemplateService;
    }

    /**
     * Show screen index.
     *
     * @param Request $request
     * @param int $id
     * @return View
     */
    public function index(Request $request, int $id): View
    {
        $admin = Auth::guard('admin')->user();
        $trademark = $this->trademarkService->find($id);

        if (empty($trademark)) {
            abort(404);
        }

        // Trademark info table
        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_ADMIN_2, $trademark->id, [
            SHOW_LINK_ANKEN_TOP => true,
        ]);

        // Payment Info
        $payments = $this->paymentService->findByCondition([
            'trademark_id' => $trademark->id ?? 0,
            'payment_status_not_zero' => Payment::STATUS_SAVE,
        ])->with(['payerInfo', 'paymentProds', 'paymentProds.product', 'paymentProds.product.mDistinction'])
            ->orderBy('created_at', 'desc')
            ->limit(PAGE_LIMIT_100)->get();
        $payments = $this->paymentService->loadDataTargetId($payments);
        $payments = $this->paymentService->loadContent($payments);
        $payments->map(function ($item) {
            $item->withPaymentInfo();

            $payerInfo = $item->payerInfo ?? null;
            $item->payment_type = '';
            if (!empty($payerInfo)) {
                $item->payment_type = $payerInfo->getPaymentType();
            }

            return $item;
        });

        // History
        $notices = $this->noticeDetailService->findByCondition([
            // 'type_acc' => NoticeDetail::getTypeAcc('admin'),
            // 'target_id' => Auth::guard('admin')->id(),
            'raw' => 'type_acc != ' . NoticeDetail::TYPE_USER,
            'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
            'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
        ])->join('notices', 'notices.id', 'notice_details.notice_id')
            ->join('trademarks', 'trademarks.id', 'notices.trademark_id')
            ->where('trademarks.id', $trademark->id)
            ->select('notice_details.*')
            ->get();
        $notices = $this->noticeDetailService->formatData($notices, true);

        $orderField = $request->orderField ?? 'notice_created_at';
        $orderType = $request->orderType ?? SORT_TYPE_DESC;
        $notices = $notices->sortByDesc('id');
        if ($orderType == SORT_TYPE_DESC) {
            $notices = $notices->sortByDesc($orderField);
        } else {
            $notices = $notices->sortBy($orderField);
        }

        // Cancel
        $cancelInfo = [
            'is_cancel' => false,
            'type' => null,
            'id' => 0,
        ];
        $trademarkCancel = false;
        if ($trademark->status_management == false) {
            $trademarkCancel = true;
            $cancelInfo = [
                'is_cancel' => true,
                'type' => TRADEMARK,
                'id' => $trademark->id ?? 0,
            ];
        } else {
            $lastNotice = $notices->first();
            if (!empty($lastNotice)) {
                $cancelInfo = $this->noticeDetailService->isCancel($lastNotice);
                $lastNotice->is_cancel = $cancelInfo['is_cancel'] ?? false;
            }
        }

        return view('admin.modules.trademarks.index', compact(
            'id',
            'admin',
            'trademark',
            'trademarkTable',
            'payments',
            'notices',
            'cancelInfo',
            'trademarkCancel',
        ));
    }

    /**
     * Restore data cancel
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function restore(Request $request)
    {
        try {
            DB::beginTransaction();

            $type = $request->type ?? '';
            $id = $request->id ?? 0;

            switch ($type) {
                case TRADEMARK:
                    $trademark = $this->trademarkService->find($id);
                    $this->trademarkService->update($trademark, [
                        'status_management' => true,
                    ]);
                    break;
                case Notice::FLOW_SFT:
                    $sft = $this->supportFirstTimeService->find($id);
                    $this->supportFirstTimeService->update($sft, [
                        'is_cancel' => false,
                    ]);
                    break;
                case Notice::FLOW_PRECHECK:
                    $precheck = $this->precheckService->find($id);
                    $this->precheckService->update($precheck, [
                        'is_cancel' => false,
                    ]);
                    break;
                case Notice::FLOW_FREE_HISTORY:
                    $freeHistory = $this->freeHistoryService->find($id);
                    $this->freeHistoryService->update($freeHistory, [
                        'is_cancel' => false,
                    ]);
                    break;
                case Notice::FLOW_APP_TRADEMARK:
                    $appTrademark = $this->appTrademarkService->find($id);
                    $this->appTrademarkService->update($appTrademark, [
                        'is_cancel' => false,
                    ]);
                    break;
                case Notice::FLOW_RESPONSE_REASON:
                    $comparisonTrademarkResult = $this->comparisonTrademarkResultService->find($id);
                    $this->comparisonTrademarkResultService->update($comparisonTrademarkResult, [
                        'is_cancel' => false,
                    ]);
                    break;
            }

            DB::commit();

            return response()->json([], CODE_SUCCESS_200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            return response()->json([], CODE_ERROR_500);
        }
    }

    /**
     * Upload PDF
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function uploadPDF(Request $request)
    {
        $params = $request->all();
        $upload = $this->trademarkService->uploadPDF($params);

        if (!$upload) {
            return redirect()->back();
        }

        return redirect()->back();
    }

    /**
     * Contact customer
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function contactCustomer(Request $request)
    {
        try {
            DB::beginTransaction();
            $params = $request->all();
            $this->noticeDetailBtnService->find($params['notice_detail_btn_id'])->update([
                'date_click' => now(),
            ]);
            $trademark = $this->trademarkService->find($params['trademark_id']);
            $appTrademark = $trademark->load('appTrademark')->appTrademark;
            $userId = $trademark->user_id;

            $redirectPageU000Top = '';
            if ($appTrademark->pack == AppTrademark::PACK_A || $appTrademark->pack == AppTrademark::PACK_B) {
                $redirectPageU000Top = route('user.refusal.notification.index', [
                    'id' => $params['comparison_trademark_result_id'],
                ]);
            } elseif ($appTrademark->pack == AppTrademark::PACK_C) {
                $redirectPageU000Top = 'TODO';
            }

            $targetPage = route('admin.refusal-request-review', [
                'id' => $params['trademark_id'],
                'maching_result_id' => $params['maching_result_id'],
            ]);

            $this->commonNoticeService->sendNotice([
                'notices' => [
                    'trademark_id' => $params['trademark_id'],
                    'trademark_info_id' => null,
                    'user_id' => $userId,
                    'flow' => Notice::FLOW_RESPONSE_REASON,
                    'step' => Notice::STEP_1
                ],
                'notice_details' => [
                    // A-000anken_top
                    [
                        'notice_id' => $params['notice_id'],
                        'target_id' => auth()->guard('admin')->user()->id,
                        'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                        'target_page' => $targetPage,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                        'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                        'content' => '事務担当　拒絶理由通知対応：お知らせ・対応検討依頼',
                        'attribute' => 'お客様へ',
                        'response_deadline' => $params['response_deadline'] ?? null,
                        'completion_date' => now(),
                    ],
                    // U-000top
                    [
                        'target_id' => $userId,
                        'type_acc' => NoticeDetail::TYPE_USER,
                        'target_page' => $targetPage,
                        'redirect_page' => $redirectPageU000Top,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                        'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                        'is_action' => true,
                        'content' => '審査結果のお知らせ（拒絶理由通知）、今後の流れのご説明',
                        'response_deadline' => $params['response_deadline'] ?? null,
                    ],
                    // U-000anken_top
                    [
                        'target_id' => $userId,
                        'type_acc' => NoticeDetail::TYPE_USER,
                        'target_page' => $targetPage,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                        'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                        'content' => '審査結果のお知らせ（拒絶理由通知）、今後の流れのご説明',
                        'response_deadline' => $params['response_deadline'] ?? null,
                    ],
                ],
            ]);
            DB::commit();

            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return redirect()->back();
        }
    }

    /**
     * View Apply Trademark
     *
     * @param mixed $id
     * @return void
     */
    public function viewApplyTrademark($id)
    {
        $trademark = $this->trademarkService->getTrademarkApplyDocumentToCheck($id);
        if (!count($trademark)) {
            abort(CODE_ERROR_404);
        }

        $agentIdentifierCodeNominated = $this->agentService->getIdentifierCodeNominated($id);
        $agentIdentifierCodeNotNominated = $this->agentService->getIdentifierCodeNotNominated($id);
        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_ADMIN_1, $id, [
            SHOW_LINK_ANKEN_TOP => true,
            TRADEMARK_TABLE_A031 => true,
        ]);
        $authRole = Auth::user()->role;

        return view('admin.modules.trademarks.apply-trademark', compact(
            'id',
            'trademark',
            'trademarkTable',
            'agentIdentifierCodeNominated',
            'agentIdentifierCodeNotNominated',
            'authRole'
        ));
    }

    /**
     * Update Trademark
     *
     * @param mixed $request
     * @param mixed $id
     * @return void
     */
    public function updateTrademark(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $appTrademark = $this->appTrademarkService->findByCondition(['trademark_id' => $id])->first();
            if (!$appTrademark) {
                abort(404);
            }

            $params = $request->all();
            $params['status'] = AppTrademark::STATUS_WAITING_FOR_USER_CONFIRM;
            $params['comment_office'] = $request['comment_office'];
            $params['cancellation_deadline'] = now()->addHours(24);
            $this->appTrademarkService->update($appTrademark, $params);
            $trademark = $this->trademarkService->find($id);
            $redirectPage = route('user.apply-trademark.confirm', $id);
            if ($trademark) {
                $this->noticeDetailService->findByCondition([
                    'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                    'completion_date' => null,
                ])->with('notice')->get()
                    ->where('notice.trademark_id', $trademark->id)
                    ->where('notice.user_id', $trademark->user_id)
                    ->where('notice.flow', Notice::FLOW_APP_TRADEMARK)
                    ->map(function ($item) {
                        $item->update([
                            'completion_date' => now(),
                        ]);
                    });

                // $this->noticeDetailService->findByCondition([
                //     'type_acc' => NoticeDetail::TYPE_USER,
                //     'is_answer' => false,
                // ])->with('notice')->get()
                //     ->where('notice.trademark_id', $trademark->id)
                //     ->where('notice.user_id', $trademark->user_id)
                //     ->where('notice.flow', Notice::FLOW_APP_TRADEMARK)
                //     ->map(function ($item) {
                //         $item->update([
                //             'is_answer' => NoticeDetail::IS_ANSWER,
                //         ]);
                //     });

                $this->commonNoticeService->updateComment(
                    Notice::FLOW_APP_TRADEMARK,
                    $params['comment_office'] ?? '',
                    $trademark->id,
                );

                $this->commonNoticeService->sendNotice([
                    'notices' => [
                        'flow' => Notice::FLOW_APP_TRADEMARK,
                        'user_id' => $trademark->user_id,
                        'trademark_id' => $id,
                    ],
                    'notice_details' => [
                        // Send Notice jimu
                        [
                            'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                            'content' => '出願：提出書類確認依頼連絡済',
                            'target_page' => route('admin.apply-trademark-document-to-check', $id),
                            'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                            'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                            'attribute' => 'お客様へ',
                            'completion_date' => Carbon::now(),
                        ],
                        // Send Notice user
                        [
                            'target_id' => $trademark->user_id,
                            'type_acc' => NoticeDetail::TYPE_USER,
                            'content' => '出願：提出書類ご確認',
                            'target_page' => route('admin.apply-trademark-document-to-check', $id),
                            'redirect_page' => $redirectPage,
                            'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                            'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                            'is_action' => NoticeDetail::IS_ACTION_TRUE,
                            'response_deadline' => isset($appTrademark->cancellation_deadline) && $appTrademark->cancellation_deadline ?
                                $appTrademark->cancellation_deadline : null,
                        ],
                        [
                            'target_id' => $trademark->user_id,
                            'type_acc' => NoticeDetail::TYPE_USER,
                            'content' => '出願：提出書類ご確認',
                            'target_page' => route('admin.apply-trademark-document-to-check', $id),
                            'redirect_page' => $redirectPage,
                            'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                            'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                            'is_action' => NoticeDetail::IS_ACTION_TRUE,
                            'response_deadline' => isset($appTrademark->cancellation_deadline) && $appTrademark->cancellation_deadline ?
                                $appTrademark->cancellation_deadline : null,
                        ],
                    ],
                ]);

                // Send mail
                $params = [
                    'from_page' => A031,
                    'user' => $trademark->user
                ];
                $this->mailTemplateService->sendMailRequest($params, MailTemplate::TYPE_OTHER, MailTemplate::GUARD_TYPE_ADMIN);
            }
            DB::commit();

            switch ($request['submit_type']) {
                case SUBMIT:
                    return redirect()->route('admin.home')->with('message', __('messages.general.Common_E030'));
                case BACK_URL:
                    return redirect()->route('admin.home');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
        }
    }

    /**
     * Final refusal.
     *
     * @param mixed $request
     * @param mixed $id
     * @return View
     */
    public function finalRefusal(Request $request, $id)
    {
        if (empty($request->maching_result_id)) {
            abort(404);
        }

        $trademark = $this->trademarkService->find($id);
        if (!$trademark) {
            abort(404);
        }

        if ($trademark->is_refusal == Trademark::IS_REFUSAL_NOT_REFUSAL) {
            abort(404);
        }

        $matchingResult = $this->matchingResultService->findByCondition([
            'id' => $request->maching_result_id,
            'trademark_id' => $trademark->id,
        ])->first();
        if (!$matchingResult) {
            abort(404);
        }

        $comparisonTrademarkResult = $this->comparisonTrademarkResultService->findByCondition([
            'trademark_id' => $trademark->id,
        ])->get()->last();
        if (!$comparisonTrademarkResult) {
            abort(404);
        }
        $planCorrespondence = $comparisonTrademarkResult->planCorrespondence;
        if (!$planCorrespondence) {
            abort(404);
        }
        $trademarkPlans = $planCorrespondence->trademarkPlans;
        if (!count($trademarkPlans)) {
            abort(404);
        }

        $trademarkPlanLast = $trademarkPlans->last()->load([
            'plans.planDetails.planDetailProducts'
        ]);

        $checkIsRefusal = false;
        if ($trademark->is_refusal == Trademark::IS_REFUSAL_CONFIRM) {
            $checkIsRefusal = true;
        }

        $getProducts = $trademarkPlanLast->getProductsV2();
        $countProducts = count($getProducts);
        $productsGroupedByDistinction = $getProducts->groupBy('distinction.id');

        $productsRankA = $trademarkPlanLast->getProductsRankA();
        $countProductRankA = count($productsRankA);
        $productsRankAGroupedByDistinction = $productsRankA->groupBy('mDistinction.id');

        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_ADMIN_1, $trademark->id, [
            SHOW_LINK_ANKEN_TOP => true,
        ]);

        $trademarkDocuments = $this->trademarkDocumentService->getByCondition([
            'trademark_id' => $trademark->id,
            'flow' => Notice::FLOW_RESPONSE_REASON,
            'step' => Notice::STEP_1,
        ]);

        $trademarkDocumentsType1 = $trademarkDocuments->where('type', TrademarkDocument::TYPE_1)->pluck('url');
        $trademarkDocumentsType2 = $trademarkDocuments->where('type', TrademarkDocument::TYPE_2)->pluck('url');
        $trademarkDocumentsType3 = $trademarkDocuments->where('type', TrademarkDocument::TYPE_3)->pluck('url');

        return view('admin.modules.trademarks.final-refusal', compact(
            'trademark',
            'countProducts',
            'checkIsRefusal',
            'matchingResult',
            'trademarkTable',
            'countProductRankA',
            'trademarkPlanLast',
            'trademarkDocumentsType1',
            'trademarkDocumentsType2',
            'trademarkDocumentsType3',
            'comparisonTrademarkResult',
            'productsGroupedByDistinction',
            'productsRankAGroupedByDistinction',
        ));
    }

    /**
     * Post final refusal.
     *
     * @param mixed $request
     * @param mixed $id
     * @return RedirectResponse
     */
    public function postFinalRefusal(Request $request, $id)
    {
        if (empty($request->maching_result_id)) {
            abort(404);
        }

        $trademark = $this->trademarkService->find($id);
        if (!$trademark) {
            abort(404);
        }

        if ($trademark->is_refusal == Trademark::IS_REFUSAL_NOT_REFUSAL) {
            abort(404);
        }

        $matchingResult = $this->matchingResultService->findByCondition([
            'id' => $request->maching_result_id,
            'trademark_id' => $trademark->id,
        ])->first();
        if (!$matchingResult) {
            abort(404);
        }

        $comparisonTrademarkResult = $this->comparisonTrademarkResultService->findByCondition([
            'trademark_id' => $trademark->id,
        ])->get()->last();
        if (!$comparisonTrademarkResult) {
            abort(404);
        }
        $planCorrespondence = $comparisonTrademarkResult->planCorrespondence;
        if (!$planCorrespondence) {
            abort(404);
        }
        $trademarkPlans = $planCorrespondence->trademarkPlans;
        if (!count($trademarkPlans)) {
            abort(404);
        }

        $trademarkPlanLast = $trademarkPlans->last()->load([
            'plans.planDetails.planDetailProducts'
        ]);

        try {
            DB::beginTransaction();

            $trademark->update([
                'comment_refusal' => $request->comment_refusal,
                'is_refusal' => Trademark::IS_REFUSAL_CONFIRM,
            ]);

            // Update Notice at A-206kyo_s (No 17: F G)
            $stepBeforeNotice = $this->noticeDetailService->findByCondition([])
                ->with('notice')->get()
                ->where('notice.trademark_id', $trademark->id)
                ->where('notice.user_id', $trademark->user_id)
                ->where('notice.flow', Notice::FLOW_RESPONSE_NOTICE_REASON_REFUSAL)
                ->where('type_acc', '!=', NoticeDetail::TYPE_USER)
                ->whereNull('completion_date');

            $stepBeforeNotice->map(function ($item) {
                $item->update([
                    'completion_date' => Carbon::now(),
                ]);
            });

            $targetPage = route('admin.refusal.final-refusal.index', ['id' => $trademark->id, 'maching_result_id' => $matchingResult->id]);
            $redirectPage = route('user.refusal.final-refusal', [
                'trademark_id' => $trademark->id,
                'comparison_trademark_result_id' => $comparisonTrademarkResult->id,
                'plan_correspondence_id' => $planCorrespondence->id,
            ]);

            $notice = [
                'trademark_id' => $trademark->id,
                'trademark_info_id' => null,
                'user_id' => $trademark->user_id,
                'flow' => Notice::FLOW_RESPONSE_NOTICE_REASON_REFUSAL,
            ];

            $noticeDetails = [
                [
                    'target_id' => $trademark->user_id,
                    'type_acc' => NoticeDetail::TYPE_USER,
                    'target_page' => $targetPage,
                    'redirect_page' => $redirectPage,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                    'is_action' => true,
                    'content' => '拒絶査定のお知らせ',
                ],
                [
                    'target_id' => $trademark->user_id,
                    'type_acc' => NoticeDetail::TYPE_USER,
                    'target_page' => $targetPage,
                    'redirect_page' => $redirectPage,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'content' => '拒絶査定のお知らせ',
                ],
            ];

            $this->commonNoticeService->sendNotice([
                'notices' => $notice,
                'notice_details' => $noticeDetails,
            ]);

            DB::commit();

            CommonHelper::setMessage($request, MESSAGE_SUCCESS, __('messages.general.update_success'));
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            CommonHelper::setMessage($request, MESSAGE_ERROR, __('messages.general.Common_E027'));
            return redirect()->back();
        }
    }

    /**
     * Show A210 Alert
     *
     * @param  int $id
     * @return View
     */
    public function showA210alert($id): View
    {
        $trademark = $this->trademarkService->find($id);
        if (!$trademark) {
            abort(404);
        }
        $data = $this->trademarkService->getTrademark($trademark, RegisterTrademarkRenewal::TYPE_EXTENSION_PERIOD_BEFORE_DEADLINE);
        if (!$data['register_trademark_renewals']->count()) {
            abort(404);
        }
        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_ADMIN_1, $id, [
            SHOW_LINK_ANKEN_TOP => true,
            A210Alert => true,
        ]);

        return view('admin.modules.extension-period.alert', compact(
            'data',
            'trademarkTable',
            'id'
        ));
    }

    /**
     * Show A210 Alert
     *
     * @param  mixed $id
     * @return view
     */
    public function showA210Over($id): View
    {
        $trademark = $this->trademarkService->find($id);
        if (!$trademark) {
            abort(404);
        }
        $data = $this->trademarkService->getTrademark($trademark, RegisterTrademarkRenewal::TYPE_EXTENSION_OUTSIDE_PERIOD);
        if (!$data['register_trademark_renewals']->count()) {
            abort(404);
        }

        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_ADMIN_1, $id, [
            SHOW_LINK_ANKEN_TOP => true,
            A210Alert => true,
        ]);

        return view('admin.modules.extension-period.over', compact(
            'data',
            'trademarkTable',
            'id'
        ));
    }

    /**
     * Update Data Alert
     *
     * @param  Request $request
     * @param  int $id
     * @return void
     */
    public function updateDataAlert(Request $request, int $id)
    {
        $registerTrademarkRenewals = $this->registerTrademarkRenewalService->findByCondition([
            'ids' => $request['register_trademark_renewals'],
        ])->get();

        foreach ($registerTrademarkRenewals as $registerTrademarkRenewal) {
            $registerTrademarkRenewal->update(['status' => RegisterTrademarkRenewal::COMPLEDTED]);
        }

        $this->createOrUpdateNoticeA210($id, $request['from_page']);

        return redirect()->route('admin.application-detail.index', ['id' => $id])->with('message', __('messages.general.update_success'));
    }

    /**
     * Create Or Update Notice A210
     *
     * @param  int $trademarkId
     * @param  string $fromPage
     * @return void
     */
    public function createOrUpdateNoticeA210(int $trademarkId, string $fromPage)
    {
        $trademark = $this->trademarkService->find($trademarkId);
        $trademark->load('notices', 'comparisonTrademarkResults');
        $comparisonTrademarkResult = $trademark->comparisonTrademarkResults->last();

        $stepNoticeBefore = $this->noticeDetailService->findByCondition([
            'completion_date' => null,
        ])->with('notice')->get()
            ->where('type_acc', '!=', NoticeDetail::TYPE_USER)
            ->where('notice.flow', Notice::FLOW_RENEWAL_BEFORE_DEADLINE)
            ->where('notice.trademark_id', $trademark->id)
            ->where('notice.user_id', $trademark->user_id);

        $stepNoticeBefore->map(function ($item) {
            $item->update(['completion_date' => now()]);
        });
        switch ($fromPage) {
            case A210Alert:
                $targetPage = route('admin.refusal.create-request.alert', ['id' => $trademarkId]);
                break;
            case A210Over:
                $targetPage = route('admin.refusal.create-request.over', ['id' => $trademarkId]);
                break;
        }
        $redirectPage = route('admin.application-detail.index', ['id' => $trademarkId]);
        $responseDeadline = $comparisonTrademarkResult->response_deadline;
        $notices = [
            'flow' => Notice::FLOW_RENEWAL_BEFORE_DEADLINE,
            'user_id' => $trademark->user_id,
            'trademark_id' => $trademark->id,
        ];
        $noticeDetails = [
            // A-000top
            [
                'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                'target_page' => $targetPage,
                'redirect_page' => $redirectPage,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                'is_action' => true,
                'content' => '期限日前期間延長請求書HTML作成、提出作業中',
                'response_deadline' => $responseDeadline,
            ],
            // A000anken_top
            [
                'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                'target_page' => $targetPage,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                'is_action' => true,
                'content' => '事務担当　期限日前期間延長請求書HTML作成、提出作業中',
                'attribute' => '特許庁へ',
                'response_deadline' => $responseDeadline,
                'buttons' => [
                    [
                        "btn_type"  => NoticeDetailBtn::BTN_CREATE_HTML,
                        "url"  => $targetPage . '?type=' . VIEW,
                        "from_page" => $fromPage,
                    ],
                ]
            ],
        ];

        $this->commonNoticeService->sendNotice([
            'notices' => $notices,
            'notice_details' => $noticeDetails,
        ]);
    }

    /**
     * Registration notify.
     *
     * @param mixed $request
     * @param mixed $id
     * @return View
     */
    public function registrationNotify(Request $request, $id)
    {
        if (empty($request->maching_result_id)) {
            abort(404);
        }
        $trademark = $this->trademarkService->find($id);
        if (!$trademark) {
            abort(404);
        }
        $matchingResult = $this->matchingResultService->findByCondition([
            'id' => $request->maching_result_id,
            'trademark_id' => $trademark->id,
        ])->first();
        if (!$matchingResult) {
            abort(404);
        }

        $relation = $trademark->load([
            'appTrademark.trademarkInfo',
            'registerTrademark',
        ]);

        $registerTrademark = $relation->registerTrademark;
        $checkIsConfirm = false;
        if ($registerTrademark && $registerTrademark->is_confirm == true) {
            $checkIsConfirm = true;
        }
        $appTrademark = $relation->appTrademark;
        if (!$appTrademark) {
            abort(404);
        }

        $trademarkInfo = $appTrademark->trademarkInfo->last();

        $ts = Carbon::parse($matchingResult->pi_dd_date)->format('Y年m月d日');

        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_ADMIN_1, $trademark->id, [
            SHOW_LINK_ANKEN_TOP => true,
        ]);

        // Url Back
        $urlBackDefault = route('admin.home');
        $checkUrl = route('admin.registration.notify', ['id' => $trademark->id, 'maching_result_id' => $matchingResult->id]);
        $backUrl = CommonHelper::setBackUrl($urlBackDefault, $checkUrl);
        $route = route('admin.registration.change-address.index', [
            'id' => $trademark->id,
            'trademark_info_id' => $trademarkInfo->id,
            'matching_result_id' => $matchingResult->id,
            'from_page' => A301,
        ]);

        return view('admin.modules.trademarks.registration-notify', compact(
            'ts',
            'backUrl',
            'trademark',
            'trademarkInfo',
            'checkIsConfirm',
            'matchingResult',
            'trademarkTable',
            'route'
        ));
    }

    /**
     * Post registration notify.
     *
     * @param mixed $request
     * @param mixed $id
     * @return RedirectResponse
     */
    public function postRegistrationNotify(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $params = $request->all();
            $params['user_response_deadline'] = Carbon::createFromFormat('Y年m月d日', $request->user_response_deadline)->toDateTimeString();

            $trademark = $this->trademarkService->find($id);
            if (!$trademark) {
                abort(404);
            }
            $relation = $trademark->load([
                'appTrademark.trademarkInfo',
                'registerTrademark',
            ]);

            $matchingResult = $this->matchingResultService->findByCondition([
                'id' => $request->maching_result_id,
                'trademark_id' => $trademark->id,
            ])->first();
            if (!$matchingResult) {
                abort(404);
            }

            $appTrademark = $relation->appTrademark;
            if (!$appTrademark) {
                abort(404);
            }

            $trademarkInfo = $appTrademark->trademarkInfo->last();

            $agentGroupStChoice = $this->agentGroupService->findByCondition(['status_choice' => AgentGroup::STATUS_CHOICE_TRUE])->first();
            $registerTrademark = $relation->registerTrademark;

            if (!$registerTrademark) {
                $registerTrademark = $this->registerTrademarkService->create([
                    'trademark_id' => $trademark->id,
                    'admin_id' => Auth::guard('admin')->user()->id,
                    'period_registration_fee' => 0,
                    'agent_group_id' => $agentGroupStChoice->id,
                    'trademark_info_id' => $trademarkInfo->id,
                    'trademark_info_change_fee' => 0,
                    'option' => '',
                    'display_info_status' => ''
                ]);
            }

            $registerTrademark->update([
                'user_response_deadline' => $params['user_response_deadline'],
                'is_confirm' => RegisterTrademark::IS_CONFIRM,
            ]);

            // Update Notice at A-301 (No 16: F G)
            $stepBeforeNotice = $this->noticeDetailService->findByCondition([])
                ->with('notice')->get()
                ->where('notice.trademark_id', $trademark->id)
                ->where('notice.flow', Notice::FLOW_REGISTER_TRADEMARK)
                ->where('type_acc', '!=', NoticeDetail::TYPE_USER)
                ->whereNull('completion_date');

            $stepBeforeNotice->map(function ($item) {
                $item->update([
                    'completion_date' => Carbon::now(),
                ]);
            });

            $responseDeadline = $matchingResult->calculateResponseDeadline();

            $notice = [
                'trademark_id' => $trademark->id,
                'user_id' => $trademark->user_id,
                'flow' => Notice::FLOW_REGISTER_TRADEMARK,
            ];

            $noticeDetails = [
                [
                    'target_id' => null,
                    'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                    'target_page' => route('admin.registration.notify', ['id' => $trademark->id, 'maching_result_id' => $matchingResult->id]),
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'content' => '事務担当　登録査定：登録手続きのお申込みへ​お知らせ',
                    'attribute' => '特許庁から',
                    'response_deadline' => $responseDeadline,
                    'buttons' => [
                        [
                            'btn_type' => NoticeDetailBtn::BTN_PDF_UPLOAD,
                            'from_page' => A301,
                        ],
                    ],
                ],
            ];

            $this->commonNoticeService->sendNotice([
                'notices' => $notice,
                'notice_details' => $noticeDetails,
            ]);

            DB::commit();

            return redirect()->route('admin.application-detail.index', ['id' => $trademark->id])->with('message', __('messages.update_success'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            CommonHelper::setMessage($request, MESSAGE_ERROR, __('messages.error'));
            return redirect()->back();
        }
    }
}
