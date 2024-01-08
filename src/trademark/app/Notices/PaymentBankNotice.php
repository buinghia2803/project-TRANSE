<?php

namespace App\Notices;

use App\Models\Admin;
use App\Models\ChangeInfoRegister;
use App\Models\Notice;
use App\Models\NoticeDetail;
use App\Models\Payment;
use App\Models\PlanDetail;
use App\Models\Precheck;
use App\Models\ComparisonTrademarkResult;
use App\Models\MatchingResult;
use App\Models\PlanCorrespondence;
use App\Models\RegisterTrademark;
use App\Services\Common\NoticeService;
use App\Services\PaymentService;
use App\Services\NoticeDetailService;
use App\Services\NoticeService as ServicesNoticeService;
use App\Services\PlanService;
use App\Services\XMLProcedures\ProcedureInfomation;
use Carbon\Carbon;

class PaymentBankNotice extends BaseNotice
{
    private NoticeService $noticeService;
    private int $typeUser;
    private int $typeOfficeManager;
    private int $typeManager;
    private int $typeSupervisor;
    private int $typeNotifyTodo;
    private int $typeNotifyDefault;
    private int $typePageTop;
    private int $typePageAnkenTop;
    private string $targetPage;
    private string $redirectPage;
    private Payment $payment;
    private $trademark;
    private int $flow;
    private $trademarkInfoId;
    private $servicesNoticeService;
    private $noticeDetailService;
    private $paymentService;
    private PlanService $planService;
    protected array $data;

    /**
     * Constructor
     *
     * @return  void
     */
    public function __construct(
        NoticeService $noticeService,
        PaymentService $paymentService,
        NoticeDetailService $noticeDetailService,
        ServicesNoticeService $servicesNoticeService,
        PlanService $planService
    )
    {
        $this->data = [];
        parent::__construct();

        $this->noticeService = $noticeService;

        $this->typeUser = NoticeDetail::TYPE_USER;
        $this->typeOfficeManager = NoticeDetail::TYPE_OFFICE_MANAGER;
        $this->typeManager = NoticeDetail::TYPE_MANAGER;
        $this->typeSupervisor = NoticeDetail::TYPE_SUPERVISOR;

        $this->typeNotifyTodo = NoticeDetail::TYPE_NOTIFY_TODO;
        $this->typeNotifyDefault = NoticeDetail::TYPE_NOTIFY_DEFAULT;

        $this->typePageTop = NoticeDetail::TYPE_PAGE_TOP;
        $this->typePageAnkenTop = NoticeDetail::TYPE_PAGE_ANKEN_TOP;

        $this->paymentService = $paymentService;
        $this->servicesNoticeService = $servicesNoticeService;
        $this->noticeDetailService = $noticeDetailService;
        $this->planService = $planService;
    }

    /**
     * Set data.
     *
     * @param array $data
     * @return void
     */
    public function setData(array $data = []): void
    {
        $this->data = $data;
    }

    /**
     * Send notice for payment bank transfer
     *
     * @param Payment $payment
     * @return void
     */
    public function notice(Payment $payment)
    {
        try {
            $fromPage = $payment->from_page ?? null;
            // Handle for U201Select02_n
            if (str_contains($payment->from_page, U201SELECT02)) {
                $fromPage = U201SELECT02;
            }
            if (str_contains($payment->from_page, U201_SELECT_01_N)) {
                $fromPage = U201_SELECT_01_N;
            }
            // Handle for U302_402_5YR_KOUKI
            if (str_contains($payment->from_page, U302_402_5YR_KOUKI)) {
                $fromPage = U302_402_5YR_KOUKI;
            }
            // Handle for U302_402TSUINO_5YR_KOUKI
            if (str_contains($payment->from_page, U302_402TSUINO_5YR_KOUKI)) {
                $fromPage = U302_402TSUINO_5YR_KOUKI;
            }
            // Handle for U402 U402TSUINO
            if (str_contains($payment->from_page, U402TSUINO)) {
                $fromPage = U402TSUINO;
            } elseif (str_contains($payment->from_page, U402)) {
                $fromPage = U402;
            }

            $this->payment = $payment->load('trademark');
            $this->trademark = $payment->trademark;

            // Get Trademark Info
            $trademark = $this->trademark->load('appTrademark.trademarkInfo');
            $appTrademark = $trademark->appTrademark;

            $lastTrademarkInfos = null;
            if (!empty($appTrademark)) {
                $trademarkInfos = $appTrademark->trademarkInfo;
                $lastTrademarkInfos = $trademarkInfos->last();
            }

            switch ($fromPage) {
                case U011:
                    $this->targetPage = route('user.sft.index');
                    $this->redirectPage = route('admin.support-first-time.create', $this->trademark->id);

                    $this->noticeU011();
                    break;
                case U011B:
                case U021B:
                case U021B_31:
                case U031_EDIT_WITH_NUMBER:
                case U031:
                case U031B:
                case U031EDIT:
                case U031D:
                    $trademark = $this->trademark->load([
                        'supportFirstTime',
                    ]);

                    $this->redirectPage = route('admin.apply-trademark-document-to-check', $this->trademark->id);

                    $this->trademarkInfoId = $lastTrademarkInfos->id ?? null;

                    if ($fromPage == U011B) {
                        $sft = $trademark->supportFirstTime;
                        $this->targetPage = route('user.support.first.time.u011b', $sft->id ?? null);
                    }

                    if ($fromPage == U021B) {
                        $this->targetPage = route('user.precheck.application-trademark', $this->trademark->id);
                    }

                    if ($fromPage == U021B_31) {
                        $this->targetPage = route('user.precheck.application-trademark-v2', $this->trademark->id);
                    }

                    if ($fromPage == U031_EDIT_WITH_NUMBER) {
                        $this->targetPage = route('user.precheck.apply-trademark-with-number', $this->trademark->id);
                    }

                    if ($fromPage == U031) {
                        $this->targetPage = route('user.apply-trademark-register', $this->trademark->id);
                    }

                    if ($fromPage == U031B) {
                        $this->targetPage = route('user.register-apply-trademark-after-search', $trademark->id);
                    }

                    if ($fromPage == U031EDIT) {
                        $this->targetPage = route('user.apply-trademark-free-input', $this->trademark->id);
                    }

                    if ($fromPage == U031D) {
                        $this->targetPage = route('user.apply-trademark-without-number');
                    }

                    $this->noticeU011b();
                    break;
                case U011B_31:
                    $this->targetPage = route('user.sft.proposal-ams', $this->trademark->id);
                    $this->redirectPage = route('admin.apply-trademark-document-to-check', $this->trademark->id);

                    $this->noticeU011b();
                    break;
                case U021:
                    $payment = $payment->loadDataTargetId();
                    $precheck = $payment->precheck;

                    if (empty($precheck)) {
                        break;
                    }
                    $this->targetPage = route('user.precheck.register-precheck', $this->trademark->id);

                    if ($precheck->type_precheck == Precheck::TYPE_CHECK_SIMPLE) {
                        $this->redirectPage = route('admin.precheck.view-precheck-simple', [
                            'id' => $this->trademark->id,
                            'precheck_id' => $precheck->id,
                        ]);

                        $this->noticeU021();
                    } elseif ($precheck->type_precheck == Precheck::TYPE_CHECK_SELECT) {
                        $this->redirectPage = route('admin.precheck.check-precheck-result', [
                            'id' => $this->trademark->id,
                            'precheck_id' => $precheck->id,
                        ]);

                        $this->noticeU021Distinct();
                    }
                    break;
                case U021N:
                    $payment = $payment->loadDataTargetId();
                    $precheck = $payment->precheck;

                    if (empty($precheck)) {
                        break;
                    }

                    $this->targetPage = route('user.precheck.register-time-n', [
                        'id' => $this->trademark->id,
                        'precheck_id' => $precheck->id,
                    ]);

                    if ($precheck->type_precheck == Precheck::TYPE_CHECK_SIMPLE) {
                        $this->redirectPage = route('admin.precheck.view-precheck-simple', [
                            'id' => $this->trademark->id,
                            'precheck_id' => $precheck->id,
                        ]);

                        $this->noticeU021();
                    } elseif ($precheck->type_precheck == Precheck::TYPE_CHECK_SELECT) {
                        $this->redirectPage = route('admin.precheck.check-precheck-result', [
                            'id' => $this->trademark->id,
                            'precheck_id' => $precheck->id,
                        ]);

                        $this->noticeU021Distinct();
                    }
                    break;
                case U000LIST_CHANGE_ADDRESS_02:
                    $payment = $payment->load('changeInfoRegister');
                    $changeInfoRegister = $payment->changeInfoRegister ?? null;

                    $this->targetPage = route('user.application-list.change-address.applicant', [
                        'id' => $this->trademark->id,
                        'from' => 'change_address',
                    ]);
                    $this->redirectPage = !empty($changeInfoRegister) ? route('admin.registration.change-address.index', [
                        'id' => $this->trademark->id,
                        'change_info_register_id' => $changeInfoRegister->id ?? 0,
                    ]) : '';

                    $this->noticeChangeAddress();
                    break;
                case U000LIST_CHANGE_ADDRESS_02_KENRISHA:
                    $payment = $payment->load('changeInfoRegister');
                    $changeInfoRegister = $payment->changeInfoRegister ?? null;

                    $this->targetPage = route('user.application-list.change-address.registered', [
                        'id' => $this->trademark->id,
                        'from' => 'change_address',
                    ]);
                    $this->redirectPage = !empty($changeInfoRegister) ? route('admin.update.change_address.index', [
                        'id' => $this->trademark->id,
                        'change_info_register_id' => $changeInfoRegister->id ?? 0,
                    ]) : '';

                    $this->noticeChangeAddressKenrisha();
                    break;
                case U201SELECT02:
                    $planCorrespondence = PlanCorrespondence::find($payment->target_id);
                    $planCorrespondence->load('comparisonTrademarkResult');
                    $comparisonTrademarkResult = $planCorrespondence->comparisonTrademarkResult;
                    $arrFromPage = explode('_', $payment->from_page);
                    $reasonNoId = $arrFromPage[1] ?? '';
                    $this->targetPage = route('user.refusal.select-eval-report-show', [
                        'id' => $comparisonTrademarkResult->id,
                        'reason_no_id' => $reasonNoId,
                    ]);

                    $this->redirectPage = route('admin.refusal.pre-question.index', ['id' => $comparisonTrademarkResult->id]);

                    $this->noticeU201Select02($reasonNoId);
                    break;
                case U201_SIMPLE:
                    $payment = $payment->load(['trademark.comparisonTrademarkResult']);
                    $comparisonTrademarkResult = $payment->trademark->comparisonTrademarkResult;
                    $this->targetPage = route('user.refusal.plans.simple', [
                        'comparison_trademark_result_id' => $comparisonTrademarkResult->id,
                    ]);

                    $this->redirectPage = route('admin.refusal.eval-report.create-reason', [
                        'id' => $comparisonTrademarkResult->id,
                    ]);

                    $this->noticeU201Simple();
                    break;
                case U201_SELECT_01:
                    $payment = $payment->load(['trademark.comparisonTrademarkResult']);
                    $comparisonTrademarkResult = $payment->trademark->comparisonTrademarkResult;
                    $this->targetPage = route('user.refusal.plans.select', [
                        'comparison_trademark_result_id' => $comparisonTrademarkResult->id,
                    ]);

                    $this->redirectPage = route('admin.refusal.eval-report.create-reason', [
                        'id' => $comparisonTrademarkResult->id,
                    ]);

                    $this->noticeU201Select01();
                    break;
                case U201_SELECT_01_N:
                    $arrFromPage = explode('_', $payment->from_page);
                    $reasonNoId = end($arrFromPage);
                    $payment = $payment->load(['trademark.comparisonTrademarkResult']);
                    $comparisonTrademarkResult = $payment->trademark->comparisonTrademarkResult;

                    $this->targetPage = route('user.refusal.plans.select-eval-report-re', [
                        'comparison_trademark_result_id' => $comparisonTrademarkResult->id,
                        'reason_no_id' => $reasonNoId,
                    ]);

                    // TODO: waiting create pre-question page
                    $this->redirectPage = route('admin.refusal.eval-report.edit-reason', [
                        'id' => $comparisonTrademarkResult->id,
                        'reason_no_id' => $reasonNoId,
                    ]);

                    $this->noticeU201Select01n($reasonNoId);
                    break;
                case U203:
                case U203N:
                    $payment->load('trademark.comparisonTrademarkResult');
                    $comparisonTrademarkResult = $payment->trademark->comparisonTrademarkResult;

                    if ($payment->type == Payment::TYPE_SELECT_POLICY) {
                        $trademarkPlanId = $payment->target_id;

                        $plans = $this->planService->findByCondition(['trademark_plan_id' => $trademarkPlanId], ['planDetails.mTypePlan'])->get();
                        $mTypePlans = $plans->pluck('planDetails')->flatten()->where('is_choice', PlanDetail::IS_CHOICE)->whereIn('mTypePlan.id', [2, 4, 5, 7, 8]);
                    }

                    $this->targetPage = route('user.refusal.response-plan.refusal_response_plan.confirm', [
                        'comparison_trademark_result_id' => $comparisonTrademarkResult->id ?? 0,
                        'trademark_plan_id' => $payment->target_id ?? 0,
                    ]);

                    $this->redirectPage = route('user.refusal.materials.index', [
                        'id' => $comparisonTrademarkResult->id,
                        'trademark_plan_id' => $payment->target_id,
                    ]);

                    if (isset($mTypePlans) && $mTypePlans->count() == 0) {
                        $this->redirectPage = route('admin.refusal.material.no-material', [
                            'id' => $comparisonTrademarkResult->id,
                            'trademark_plan_id' => $payment->target_id,
                        ]); // a204no_mat

                        $this->noticeU203B02HasFee();
                    } else {
                        $this->noticeU203B02();
                    }
                    break;
                case U210_ALERT_02:
                    $payment = $payment->load(['trademark']);
                    $trademark = $payment->trademark;
                    $this->targetPage = route('user.refusal.extension-period.alert', ['id' => $trademark->id]);
                    $this->redirectPage = route('admin.refusal.create-request.alert', [
                        'id' => $trademark->id,
                    ]);

                    $this->noticeU210Alert02();
                    break;
                case U210_OVER_02:
                    $payment = $payment->load(['trademark']);
                    $trademark = $payment->trademark;
                    $this->targetPage = route('user.refusal.extension-period.over', ['id' => $trademark->id]);
                    $this->redirectPage = route('admin.refusal.create-request.over', [
                        'id' => $trademark->id,
                    ]);

                    $this->noticeU210Over02();
                    break;
                case U302:
                    $payment = $payment->load(['trademark']);
                    $trademark = $payment->trademark;
                    $this->noticeU302();
                    break;
                case U302_402_5YR_KOUKI:
                case U302_402TSUINO_5YR_KOUKI:
                    $registerTrademarkID = $payment->target_id;
                    $oldRegisterTrademark = RegisterTrademark::where('id', '<', $registerTrademarkID)
                        ->where('is_register', RegisterTrademark::IS_REGISTER)
                        ->orderBy('id', SORT_TYPE_DESC)->first();

                    $this->targetPage = route('user.registration.notice-latter-period', $oldRegisterTrademark->id);

                    if ($fromPage == U302_402TSUINO_5YR_KOUKI) {
                        $this->targetPage = route('user.registration.notice-later-period.overdue', $oldRegisterTrademark->id);
                    }

                    $this->noticeU3024025YRKOUKI();
                    break;
                case U402:
                case U402TSUINO:
                    $registerTrademarkID = $payment->target_id;
                    $oldRegisterTrademark = RegisterTrademark::where('id', '<', $registerTrademarkID)
                        ->where('is_register', RegisterTrademark::IS_REGISTER)
                        ->orderBy('id', SORT_TYPE_DESC)->first();

                    $this->targetPage = route('user.update.notify-procedure', $oldRegisterTrademark->id);

                    if ($fromPage == U402TSUINO) {
                        $this->targetPage = route('user.update.notify-procedure.overdue', $oldRegisterTrademark->id);
                    }

                    $this->noticeU402();
                    break;
                case U000FREE:
                    $data = $this->data;
                    $payment = $payment->load(['trademark']);
                    $this->targetPage = route('user.free-history.show-create', $data['free_history_id']);
                    $this->redirectPage = route('admin.free-history.re-confirm', $data['free_history_id']);
                    $this->noticeU000Free();
                    break;
                default:
                    if (str_contains($fromPage, U203 . '_')) {
                        $payment->load('trademark.comparisonTrademarkResult');
                        $comparisonTrademarkResult = $payment->trademark->comparisonTrademarkResult;

                        $this->targetPage = route('user.refusal.response-plan.refusal_response_plan.confirm', [
                            'comparison_trademark_result_id' => $comparisonTrademarkResult->id ?? 0,
                            'trademark_plan_id' => $payment->target_id ?? 0,
                        ]);
                        $this->redirectPage = route('user.refusal.materials.index', [
                            'id' => $comparisonTrademarkResult->id,
                            'trademark_plan_id' => $payment->target_id,
                        ]);

                        $this->noticeU203B02();
                    }
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Send notice for payment with from_page U000free
     *
     * @return void
     */
    public function noticeU000Free()
    {
        $trademark = $this->trademark;
        $stepBeforeNotice = $this->noticeDetailService->findByCondition([
            'type_acc' => NoticeDetail::TYPE_USER,
            'is_answer' => NoticeDetail::IS_NOT_ANSWER,
        ])->with('notice')->get()
            ->where('notice.trademark_id', $trademark->id)
            ->where('notice.user_id', $trademark->user_id)
            ->where('notice.flow', Notice::FLOW_FREE_HISTORY);
        $stepBeforeNotice->map(function ($item) {
            $item->update([
                'is_answer' => true,
            ]);
        });
        $notice = [
            'flow' => Notice::FLOW_FREE_HISTORY,
            'user_id' => $trademark->user_id,
            'trademark_id' => $trademark->id,
            'trademark_info_id' => null,
        ];
        $admin = Admin::where('role', ROLE_OFFICE_MANAGER)->first();

        $noticeDetails = [
            // send notice a-000top
            [
                'type_acc' => $this->typeOfficeManager,
                'target_page' => $this->targetPage,
                'redirect_page' => $this->redirectPage,
                'type_notify' => $this->typeNotifyTodo,
                'type_page' => $this->typePageTop,
                'is_action' => true,
                'content' => '事務担当　フリー履歴　お客様からの回答',
            ],

            //send notice a-000anken_top
            [
                'type_acc' => $this->typeOfficeManager,
                'target_page' => $this->targetPage,
                'redirect_page' => $this->redirectPage,
                'type_notify' => $this->typeNotifyTodo,
                'type_page' => $this->typePageAnkenTop,
                'content' => '事務担当　フリー履歴　お客様からの回答',
            ],

            //send notice u-000top
            [
                'target_id' => $trademark->user_id,
                'type_acc' => $this->typeUser,
                'target_page' => $this->targetPage,
                'redirect_page' => null,
                'type_notify' => $this->typeNotifyDefault,
                'type_page' => $this->typePageTop,
                'content' => ' (書類名)決済完了・返信待ち',
                'payment_id' => $this->payment->id,
                'payment_status' => PAYMENT_STATUS_2,
            ],

            //send notice u-000anken_top
            [
                'target_id' => $trademark->user_id,
                'type_acc' => $this->typeUser,
                'target_page' => $this->targetPage,
                'redirect_page' => null,
                'type_notify' => $this->typeNotifyTodo,
                'type_page' => $this->typePageAnkenTop,
                'content' => '(書類名)決済完了・返信待ち',
                'payment_id' => $this->payment->id,
                'payment_status' => PAYMENT_STATUS_2,
            ],
        ];

        $this->noticeService->sendNotice([
            'notices' => $notice,
            'notice_details' => $noticeDetails,
        ]);
    }

    /**
     * Send notice for payment with from_page U302
     *
     * @return void
     */
    public function noticeU302()
    {
        $registerTrademark = RegisterTrademark::find($this->payment->target_id);
        $trademark = $this->trademark->load('machingResults');
        // $matchingResult = $trademark->getMatchingResultFrmDocName(N_FLOW_TYPE_REGISTRATION);
        $matchingResult = MatchingResult::where('pi_document_name', ProcedureInfomation::REGISTRATION_ASSESSMENT)->orderBy('id', SORT_TYPE_DESC)->first();
        if (!$matchingResult) {
            throw new \Exception('Not found MatchingResult model!');
        }
        $stepBeforeNotice = $this->noticeDetailService->findByCondition([
            'type_acc' => NoticeDetail::TYPE_USER,
            'is_answer' => NoticeDetail::IS_NOT_ANSWER,
        ])->with('notice')->get()
            ->where('notice.trademark_id', $trademark->id)
            ->where('notice.user_id', $trademark->user_id)
            ->where('notice.flow', Notice::FLOW_REGISTER_TRADEMARK);
        $stepBeforeNotice->map(function ($item) {
            $item->update([
                'is_answer' => true,
            ]);
        });

        $notice = [
            'step' => Notice::STEP_1,
            'flow' => Notice::FLOW_REGISTER_TRADEMARK,
            'trademark_id' => $trademark->id,
            'user_id' => $trademark->user_id,
            'trademark_info_id' => null,
        ];
        $admin = Admin::where('role', ROLE_OFFICE_MANAGER)->first();

        if (!$registerTrademark->trademark_info_change_status) {
            $noticeDetails = [
                // A000top
                [
                    'target_id' => $admin->id,
                    'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                    'target_page' => route('user.registration.procedure', [
                        'id' => $matchingResult->id,
                        'register_trademark_id' => $registerTrademark->id,
                    ]),
                    'redirect_page' => route('admin.registration.skip', [
                        'id' => $matchingResult->id,
                        'register_trademark_id' => $registerTrademark->id,
                    ]),
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                    'content' => '事務担当 登録査定:出願人DB変更（依頼無し）',
                    'is_action' => true,
                ],
                // A000anken_top
                [
                    'target_id' => $admin->id,
                    'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                    'target_page' => route('user.registration.procedure', [
                        'id' => $matchingResult->id,
                        'register_trademark_id' => $registerTrademark->id,
                    ]),
                    'redirect_page' => null,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'content' => '登録査定：登録料納付決済確認済',
                    'attribute' => 'お客様へ',
                    'completion_date' => now(),
                ],
                [
                    'target_id' => $admin->id,
                    'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                    'target_page' => route('user.registration.procedure', [
                        'id' => $matchingResult->id,
                        'register_trademark_id' => $registerTrademark->id,
                    ]),
                    'redirect_page' => route('admin.registration.skip', [
                        'id' => $matchingResult->id,
                        'register_trademark_id' => $registerTrademark->id,
                    ]),
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'content' => '事務担当 登録査定:出願人DB変更（依頼無し）',
                    'attribute' => 'お客様から',
                ],
                // U000top
                [
                    'target_id' => $trademark->user_id,
                    'type_acc' => NoticeDetail::TYPE_USER,
                    'target_page' => route('user.registration.procedure', [
                        'id' => $matchingResult->id,
                        'register_trademark_id' => $registerTrademark->id,
                    ]),
                    'redirect_page' => null,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_DEFAULT,
                    'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                    'content' => '登録査定：登録手続き決済完了・報告待ち',
                    'payment_id' => $this->payment->id,
                    'payment_status' => Payment::STATUS_PAID,
                ],
                // U000anken_top
                [
                    'target_id' => $trademark->user_id,
                    'type_acc' => NoticeDetail::TYPE_USER,
                    'target_page' => route('user.registration.procedure', [
                        'id' => $matchingResult->id,
                        'register_trademark_id' => $registerTrademark->id,
                    ]),
                    'redirect_page' => null,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'content' => '登録査定：登録手続き決済完了・報告待ち',
                    'payment_id' => $this->payment->id,
                    'payment_status' => Payment::STATUS_PAID,
                ],
            ];
        } else {
            $noticeDetails = [
                // A000top
                [
                    'target_id' => $admin->id,
                    'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                    'target_page' => route('user.registration.procedure', [
                        'id' => $matchingResult->id,
                        'register_trademark_id' => $registerTrademark->id,
                    ]),
                    'redirect_page' => route('admin.registration.change-address.index', [
                        'id' => $trademark->id,
                        'register_trademark_id' => $registerTrademark->id,
                    ]),
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                    'is_action' => true,
                    'content' => '事務担当　登録査定：出願人DB変更（依頼無し）',
                ],
                // A000anken_top
                [
                    'target_id' => $admin->id,
                    'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                    'target_page' => route('user.registration.procedure', [
                        'id' => $matchingResult->id,
                        'register_trademark_id' => $registerTrademark->id,
                    ]),
                    'redirect_page' => null,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'is_action' => true,
                    'content' => '登録査定：登録料納付決済確認済',
                    'attribute' => 'お客様へ',
                    'completion_date' => now(),
                ],
                [
                    'target_id' => $admin->id,
                    'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                    'target_page' => route('user.registration.procedure', [
                        'id' => $matchingResult->id,
                        'register_trademark_id' => $registerTrademark->id,
                    ]),
                    'redirect_page' => route('admin.registration.change-address.index', [
                        'id' => $trademark->id,
                        'register_trademark_id' => $registerTrademark->id,
                    ]),
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'is_action' => true,
                    'content' => '事務担当　登録査定：出願人DB変更（依頼無し）',
                    'attribute' => '所内処理',
                ],
                // U000top
                [
                    'target_id' => $trademark->user_id,
                    'type_acc' => NoticeDetail::TYPE_USER,
                    'target_page' => route('user.registration.procedure', [
                        'id' => $matchingResult->id,
                        'register_trademark_id' => $registerTrademark->id,
                    ]),
                    'redirect_page' => null,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_DEFAULT,
                    'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                    'content' => '登録査定：出願人住所/名称お申し込み完了・報告待ち',
                    'payment_id' => $this->payment->id,
                    'payment_status' => $this->payment->payment_status,
                ],
                // U000anken_top
                [
                    'target_id' => $trademark->user_id,
                    'type_acc' => NoticeDetail::TYPE_USER,
                    'target_page' => route('user.registration.procedure', [
                        'id' => $matchingResult->id,
                        'register_trademark_id' => $registerTrademark->id,
                    ]),
                    'redirect_page' => null,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'content' => '登録査定：出願人住所/名称お申し込み完了・報告待ち',
                    'payment_id' => $this->payment->id,
                    'payment_status' => $this->payment->payment_status,
                ],
            ];
        }

        $this->noticeService->sendNotice([
            'notices' => $notice,
            'notice_details' => $noticeDetails,
        ]);
    }

    /**
     * Send notice for payment with from_page U203B02
     *
     * @return void
     */
    public function noticeU203B02()
    {
        $trademark = $this->trademark;
        $trademark->load(['machingResults.comparisonTrademarkResult.planCorrespondence']);

        $matchingResult = $trademark->machingResults->last();
        $planCorrespondence = $matchingResult->comparisonTrademarkResult->planCorrespondence;
        $notice = [
            'step' => Notice::STEP_4,
            'flow' => Notice::FLOW_RESPONSE_REASON,
            'trademark_id' => $trademark->id,
            'user_id' => $trademark->user_id,
            'trademark_info_id' => $this->trademarkInfoId ?? null,
        ];

        $subDays = 0;
        if ($planCorrespondence->type == PlanCorrespondence::TYPE_SELECTION) {
            $subDays = -30;
        } else {
            $subDays = -25;
        }

        $stepBeforeNotice = $this->noticeDetailService->findByCondition([
            'type_acc' => NoticeDetail::TYPE_USER,
            'is_answer' => NoticeDetail::IS_NOT_ANSWER,
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
                'is_answer' => true,
            ]);
        });
        $responseDeadline = $matchingResult->calculateResponseDeadline($subDays);
        $noticeDetails = [
            [
                'target_id' => null,
                'type_acc' => $this->typeManager,
                'target_page' => $this->targetPage,
                'redirect_page' => null,
                'type_notify' => $this->typeNotifyTodo,
                'type_page' => $this->typePageAnkenTop,
                'is_action' => false,
                'content' => '拒絶理由通知対応：方針案選択決済確認',
                'attribute' => 'お客様から',
                'response_deadline' => $responseDeadline,
            ],
            [
                'target_id' => $trademark->user_id,
                'type_acc' => $this->typeUser,
                'target_page' => $this->targetPage,
                'redirect_page' => null,
                'type_notify' => $this->typeNotifyDefault,
                'type_page' => $this->typePageTop,
                'content' => '拒絶理由通知対応：方針案選択決済完了',
                'payment_id' => $this->payment->id,
                'payment_status' => PAYMENT_STATUS_2,
            ],
            [
                'target_id' => $trademark->user_id,
                'type_acc' => $this->typeUser,
                'target_page' => $this->targetPage,
                'redirect_page' => $this->redirectPage,
                'type_notify' => $this->typeNotifyTodo,
                'type_page' => $this->typePageTop,
                'is_action' => true,
                'content' => '拒絶理由通知対応：必要資料提出',
                'attribute' => null,
                'response_deadline ' => $responseDeadline,
            ],
            [
                'target_id' => $trademark->user_id,
                'type_acc' => $this->typeUser,
                'target_page' => $this->targetPage,
                'redirect_page' => null,
                'type_notify' => $this->typeNotifyTodo,
                'type_page' => $this->typePageAnkenTop,
                'content' => '拒絶理由通知対応：方針案選択決済完了',
                'attribute' => null,
                'payment_id' => $this->payment->id,
                'payment_status' => PAYMENT_STATUS_2,
            ],
            [
                'target_id' => $trademark->user_id,
                'type_acc' => $this->typeUser,
                'target_page' => $this->targetPage,
                'redirect_page' => $this->redirectPage,
                'type_notify' => $this->typeNotifyTodo,
                'type_page' => $this->typePageAnkenTop,
                'content' => '拒絶理由通知対応：必要資料提出',
                'attribute' => null,
                'response_deadline ' => $responseDeadline,
            ],
        ];

        $this->noticeService->sendNotice([
            'notices' => $notice,
            'notice_details' => $noticeDetails,
        ]);
    }

    /**
     * Send notice for payment with from_page u203b02 with has fee.
     *
     * @return void
     */
    public function noticeU203B02HasFee()
    {
        $trademark = $this->trademark;
        $trademark->load('machingResults');
        $matchingResult = $trademark->machingResults->last();
        $matchingResult->load('comparisonTrademarkResult.planCorrespondence');
        $planCorrespondence = $matchingResult->comparisonTrademarkResult->planCorrespondence;

        $stepBeforeNotice = $this->noticeDetailService->findByCondition([
            'raw' => 'type_acc <> ' . NoticeDetail::TYPE_USER,
            'completion_date' => null,
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
                'completion_date' => now(),
            ]);
        });

        if ($planCorrespondence->type == PlanCorrespondence::TYPE_SELECTION) {
            $subDays = -30;
        } else {
            $subDays = -25;
        }
        $responseDeadline = $matchingResult->calculateResponseDeadline($subDays);

        $notice = [
            'flow' => Notice::FLOW_RESPONSE_REASON,
            'trademark_id' => $trademark->id,
            'step' => Notice::STEP_4,
            'trademark_info_id' => null,
            'user_id' => $trademark->user_id,
        ];

        $noticeDetails = [
            // A-000top
            [
                'type_acc' => $this->typeManager,
                'target_page' => $this->targetPage,
                'redirect_page' => $this->redirectPage,
                'type_notify' => $this->typeNotifyTodo,
                'type_page' => $this->typePageTop,
                'is_action' => true,
                'content' => '拒絶理由通知対応：対応方針案作成',
                'response_deadline' => $responseDeadline,
            ],
            // A-000anken_top
            [
                'type_acc' => $this->typeManager,
                'target_page' => $this->targetPage,
                'redirect_page' => $this->redirectPage,
                'type_notify' => $this->typeNotifyTodo,
                'type_page' => $this->typePageAnkenTop,
                'is_action' => false,
                'content' => '担当者　拒絶理由通知対応：対応方針案作成',
                'attribute' => null,
                'response_deadline' => $responseDeadline,
            ],
            // U-000top
            [
                'target_id' => $trademark->user_id,
                'type_acc' => $this->typeUser,
                'target_page' => $this->targetPage,
                'type_notify' => $this->typeNotifyDefault,
                'type_page' => $this->typePageTop,
                'is_action' => false,
                'content' => '拒絶理由通知対応：方針案選択決済完了・返信待ち',
                'payment_id' => $this->payment->id,
                'payment_status' => PAYMENT_STATUS_2,
            ],
            // U-000anken_top
            [
                'target_id' => $trademark->user_id,
                'type_acc' => $this->typeUser,
                'target_page' => $this->targetPage,
                'type_notify' => $this->typeNotifyTodo,
                'type_page' => $this->typePageAnkenTop,
                'content' => '拒絶理由通知対応：方針案選択決済完了・返信待ち',
                'payment_id' => $this->payment->id,
                'payment_status' => PAYMENT_STATUS_2,
            ],
        ];

        $this->noticeService->sendNotice([
            'notices' => $notice,
            'notice_details' => $noticeDetails,
        ]);
    }

    /**
     * Send notice for payment with from_page U201Select02
     *
     * @return void
     */
    public function noticeU201Select02(int $reasonNoId = 0)
    {
        $trademark = $this->trademark;
        $trademark->load('machingResults', 'comparisonTrademarkResult');
        $comparisonTrademarkResult = $trademark->comparisonTrademarkResult;
//        $matchingResult = $trademark->machingResults->sortByDesc('id')->first();
        $matchingResult = $comparisonTrademarkResult->machingResult;

        $matchingResult->load('comparisonTrademarkResult.planCorrespondence');
        $comparisonTrademarkResult = $matchingResult->comparisonTrademarkResult;
        $planCorrespondence = $comparisonTrademarkResult->planCorrespondence;
        if ($planCorrespondence->type == PlanCorrespondence::TYPE_SELECTION) {
            $responseDeadline = $matchingResult->calculateResponseDeadline(-11);
        } else {
            $responseDeadline = $matchingResult->calculateResponseDeadline(-7);
        }

        // Update Old Notice
        $stepBeforeNotice = $this->noticeDetailService->findByCondition([
            'type_acc' => NoticeDetail::TYPE_USER,
            'is_answer' => NoticeDetail::IS_NOT_ANSWER,
        ])->with('notice')->get()
            ->where('notice.trademark_id', $trademark->id)
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
                'is_answer' => NoticeDetail::IS_ANSWER,
            ]);
        });

        $notice = [
            'step' => Notice::STEP_2,
            'flow' => Notice::FLOW_RESPONSE_REASON,
            'trademark_id' => $trademark->id,
            'user_id' => $trademark->user_id,
            'trademark_info_id' => $this->trademarkInfoId ?? null,
        ];

        $noticeDetails = [
            [
                'target_id' => null,
                'type_acc' => $this->typeManager,
                'target_page' => $this->targetPage,
                'redirect_page' => $this->redirectPage,
                'type_notify' => $this->typeNotifyTodo,
                'type_page' => $this->typePageTop,
                'is_action' => true,
                'content' => '担当者　拒絶理由通知対応：事前質問作成',
                'attribute' => null,
                'response_deadline' => $responseDeadline,
            ],
            [
                'target_id' => null,
                'type_acc' => $this->typeManager,
                'target_page' => $this->targetPage,
                'redirect_page' => null,
                'type_notify' => $this->typeNotifyTodo,
                'type_page' => $this->typePageAnkenTop,
                'is_action' => false,
                'content' => '拒絶理由通知対応：対応決済確認',
                'attribute' => 'お客様へ',
                'response_deadline' => $responseDeadline,
                'completion_date' => now(),
            ],
            [
                'target_id' => null,
                'type_acc' => $this->typeManager,
                'target_page' => $this->targetPage,
                'redirect_page' => $this->redirectPage,
                'type_notify' => $this->typeNotifyTodo,
                'type_page' => $this->typePageAnkenTop,
                'is_action' => false,
                'content' => '担当者　拒絶理由通知対応：事前質問作成',
                'attribute' => '所内処理',
                'response_deadline' => $responseDeadline,
            ],
            // Send Notice for User
            [
                'target_id' => $trademark->user_id,
                'type_acc' => $this->typeUser,
                'target_page' => $this->targetPage,
                'redirect_page' => null,
                'type_notify' => $this->typeNotifyDefault,
                'type_page' => $this->typePageTop,
                'is_action' => false,
                'content' => '拒絶理由通知対応：対応決済完了・返信待ち',
                'attribute' => null,
                'response_deadline' => $responseDeadline,
                'payment_id' => $this->payment->id,
                'payment_status' => PAYMENT_STATUS_2
            ],
            [
                'target_id' => $trademark->user_id,
                'type_acc' => $this->typeUser,
                'target_page' => $this->targetPage,
                'redirect_page' => null,
                'type_notify' => $this->typeNotifyTodo,
                'type_page' => $this->typePageAnkenTop,
                'is_action' => false,
                'content' => '拒絶理由通知対応：対応決済完了・返信待ち',
                'attribute' => null,
                'response_deadline' => $responseDeadline,
                'payment_id' => $this->payment->id,
                'payment_status' => PAYMENT_STATUS_2
            ],
        ];

        $this->noticeService->sendNotice([
            'notices' => $notice,
            'notice_details' => $noticeDetails,
        ]);
    }

    /**
     * Send notice for payment with from_page U011
     *
     * @return void
     */
    public function noticeU011()
    {
        $trademark = $this->trademark;

        $targetPagePeriou = route('user.sft.index');
        $targetPagePeriou = str_replace(request()->root(), '', $targetPagePeriou);

        $noticeDetailPeriou1 = $this->noticeDetailService->findByCondition([
            'type_acc' => $this->typeUser,
            'target_page' => $targetPagePeriou,
            'is_answer' => NoticeDetail::IS_NOT_ANSWER,
            'type_notify' => $this->typeNotifyDefault,
            'type_page' => $this->typePageTop,
            'content' => 'はじめからサポート ：お申し込み完了'
        ], ['notice'])->orderBy('id', 'DESC')->get()
            ->where('notice.flow', Notice::FLOW_SFT)
            ->where('notice.user_id', $trademark->user_id)
            ->where('notice.trademark_id', $trademark->id)
            ->first();
        if ($noticeDetailPeriou1) {
            $noticeDetailPeriou1->update([
                'is_answer' => NoticeDetail::IS_ANSWER,
            ]);
        }

        $noticeDetailPeriou2 = $this->noticeDetailService->findByCondition([
            'type_acc' => $this->typeUser,
            'target_page' => $targetPagePeriou,
            'is_answer' => NoticeDetail::IS_NOT_ANSWER,
            'type_notify' => $this->typeNotifyDefault,
            'type_page' => $this->typePageTop,
            'content' => 'はじめからサポート ：お申し込み完了・決済待ち'
        ], ['notice'])->orderBy('id', 'DESC')->get()
            ->where('notice.flow', Notice::FLOW_SFT)
            ->where('notice.user_id', $trademark->user_id)
            ->where('notice.trademark_id', $trademark->id)
            ->first();
        if ($noticeDetailPeriou2) {
            $noticeDetailPeriou2->update([
                'is_answer' => NoticeDetail::IS_ANSWER,
            ]);
        }

        $noticeDetailPeriou3 = $this->noticeDetailService->findByCondition([
            'type_acc' => $this->typeUser,
            'target_page' => $targetPagePeriou,
            'is_answer' => NoticeDetail::IS_NOT_ANSWER,
            'type_notify' => $this->typeNotifyTodo,
            'type_page' => $this->typePageTop,
            'content' => 'はじめからサポート：未入金です'
        ], ['notice'])->orderBy('id', 'DESC')->get()
            ->where('notice.flow', Notice::FLOW_SFT)
            ->where('notice.user_id', $trademark->user_id)
            ->where('notice.trademark_id', $trademark->id)
            ->first();
        if ($noticeDetailPeriou3) {
            $noticeDetailPeriou3->update([
                'is_answer' => NoticeDetail::IS_ANSWER,
            ]);
        }
        $notice = [
            'flow' => $this->flow ?? Notice::FLOW_SFT,
            'trademark_id' => $trademark->id,
            'user_id' => $trademark->user_id,
            'trademark_info_id' => $this->trademarkInfoId ?? null,
        ];

        $noticeDetails = [
            // Send Notice for tantou
            [
                'target_id' => null,
                'type_acc' => $this->typeManager,
                'target_page' => $this->targetPage,
                'redirect_page' => $this->redirectPage,
                'type_notify' => $this->typeNotifyTodo,
                'type_page' => $this->typePageTop,
                'is_action' => true,
                'content' => 'はじめからサポート：提案内容作成',
                'attribute' => null,
            ],
            [
                'target_id' => null,
                'type_acc' => $this->typeManager,
                'target_page' => $this->targetPage,
                'redirect_page' => null,
                'type_notify' => $this->typeNotifyTodo,
                'type_page' => $this->typePageAnkenTop,
                'is_action' => false,
                'content' => 'はじめからサポート：決済確認済',
                'attribute' => 'お客様へ',
                'completion_date' => now(),
            ],
            [
                'target_id' => null,
                'type_acc' => $this->typeManager,
                'target_page' => $this->targetPage,
                'redirect_page' => $this->redirectPage,
                'type_notify' => $this->typeNotifyTodo,
                'type_page' => $this->typePageAnkenTop,
                'is_action' => false,
                'content' => '担当者　はじめからサポート：提案内容作成',
                'attribute' => '所内処理',
            ],
            // Send Notice for User
            [
                'target_id' => $trademark->user_id,
                'type_acc' => $this->typeUser,
                'target_page' => $this->targetPage,
                'redirect_page' => null,
                'type_notify' => $this->typeNotifyDefault,
                'type_page' => $this->typePageTop,
                'is_action' => false,
                'content' => 'はじめからサポート：決済完了・提案待ち',
                'attribute' => null,
                'payment_id' => $this->payment->id,
                'payment_status' => PAYMENT_STATUS_2,
            ],
            [
                'target_id' => $trademark->user_id,
                'type_acc' => $this->typeUser,
                'target_page' => $this->targetPage,
                'redirect_page' => null,
                'type_notify' => $this->typeNotifyTodo,
                'type_page' => $this->typePageAnkenTop,
                'is_action' => false,
                'content' => 'はじめからサポート：決済完了・提案待ち',
                'attribute' => null,
                'payment_id' => $this->payment->id,
                'payment_status' => PAYMENT_STATUS_2,
            ],
        ];

        $this->noticeService->sendNotice([
            'notices' => $notice,
            'notice_details' => $noticeDetails,
        ]);
    }

    /**
     * Send notice for payment with from_page U011B, U011B_31
     *
     * @return void
     */
    public function noticeU011b()
    {
        $trademark = $this->trademark;

        $notice = [
            'flow' => $this->flow ?? Notice::FLOW_APP_TRADEMARK,
            'trademark_id' => $trademark->id,
            'user_id' => $trademark->user_id,
            'trademark_info_id' => $this->trademarkInfoId ?? null,
        ];
        $this->noticeDetailService->findByCondition([
            'type_acc' => $this->typeUser,
            'is_answer' => NoticeDetail::IS_NOT_ANSWER,
        ], ['notice'])->get()
            ->where('notice.flow', Notice::FLOW_APP_TRADEMARK)
            ->where('notice.trademark_id', $trademark->id)
            ->where('notice.user_id', $trademark->user_id)
            ->map(function ($item) {
                $item->update([
                    'is_answer' => NoticeDetail::IS_ANSWER,
                ]);
            });
        $noticeDetails = [
            // Send Notice for tantou
            [
                'target_id' => null,
                'type_acc' => $this->typeOfficeManager,
                'target_page' => $this->targetPage,
                'redirect_page' => $this->redirectPage,
                'type_notify' => $this->typeNotifyTodo,
                'type_page' => $this->typePageTop,
                'is_action' => true,
                'content' => '出願：提出書類確認依頼',
                'attribute' => null,
            ],
            [
                'target_id' => null,
                'type_acc' => $this->typeOfficeManager,
                'target_page' => $this->targetPage,
                'redirect_page' => null,
                'type_notify' => $this->typeNotifyTodo,
                'type_page' => $this->typePageAnkenTop,
                'is_action' => false,
                'content' => '出願：決済確認済',
                'attribute' => 'お客様へ',
                'completion_date' => now(),
            ],
            [
                'target_id' => null,
                'type_acc' => $this->typeOfficeManager,
                'target_page' => $this->targetPage,
                'redirect_page' => route('admin.apply-trademark-document-to-check', $trademark->id),
                'type_notify' => $this->typeNotifyTodo,
                'type_page' => $this->typePageAnkenTop,
                'is_action' => false,
                'content' => '事務担当　出願：提出書類確認依頼',
                'attribute' => '所内処理',
            ],
            // Send Notice for User
            [
                'target_id' => $trademark->user_id,
                'type_acc' => $this->typeUser,
                'target_page' => $this->targetPage,
                'redirect_page' => null,
                'type_notify' => $this->typeNotifyDefault,
                'type_page' => $this->typePageTop,
                'is_action' => false,
                'content' => '出願：決済完了・返信待ち',
                'attribute' => null,
                'payment_id' => $this->payment->id,
                'payment_status' => PAYMENT_STATUS_2,
            ],
            [
                'target_id' => $trademark->user_id,
                'type_acc' => $this->typeUser,
                'target_page' => $this->targetPage,
                'redirect_page' => null,
                'type_notify' => $this->typeNotifyTodo,
                'type_page' => $this->typePageAnkenTop,
                'is_action' => false,
                'content' => '出願：決済完了・返信待ち',
                'attribute' => null,
                'payment_id' => $this->payment->id,
                'payment_status' => PAYMENT_STATUS_2,
            ],
        ];

        $this->noticeService->sendNotice([
            'notices' => $notice,
            'notice_details' => $noticeDetails,
        ]);
    }

    /**
     * Send notice for payment with from_page U021, U021N
     *
     * @return void
     */
    public function noticeU021()
    {
        $trademark = $this->trademark;

        $this->noticeDetailService->findByCondition([
            'type_acc' => $this->typeUser,
            'is_answer' => NoticeDetail::IS_NOT_ANSWER,
        ], ['notice'])->whereHas('notice', function ($query) {
            $query->where('flow', Notice::FLOW_SFT)
                ->orWhere('flow', Notice::FLOW_PRECHECK);
        })->get()
            ->where('notice.trademark_id', $trademark->id)
            ->where('notice.user_id', $trademark->user_id)
            ->map(function ($item) {
                $item->update([
                    'is_answer' => NoticeDetail::IS_ANSWER,
                ]);
            });

        $notice = [
            'flow' => $this->flow ?? Notice::FLOW_PRECHECK,
            'trademark_id' => $trademark->id,
            'user_id' => $trademark->user_id,
            'trademark_info_id' => $this->trademarkInfoId ?? null,
        ];

        $noticeDetails = [
            // Send Notice for tantou
            [
                'target_id' => null,
                'type_acc' => $this->typeManager,
                'target_page' => $this->targetPage,
                'redirect_page' => $this->redirectPage,
                'type_notify' => $this->typeNotifyTodo,
                'type_page' => $this->typePageTop,
                'is_action' => true,
                'content' => 'プレチェックサービス：レポート　簡易調査',
                'attribute' => null,
            ],
            [
                'target_id' => null,
                'type_acc' => $this->typeManager,
                'target_page' => $this->targetPage,
                'redirect_page' => null,
                'type_notify' => $this->typeNotifyTodo,
                'type_page' => $this->typePageAnkenTop,
                'is_action' => false,
                'content' => 'プレチェックサービス：決済確認済',
                'attribute' => 'お客様へ',
                'completion_date' => now(),
            ],
            [
                'target_id' => null,
                'type_acc' => $this->typeManager,
                'target_page' => $this->targetPage,
                'redirect_page' => $this->redirectPage,
                'type_notify' => $this->typeNotifyTodo,
                'type_page' => $this->typePageAnkenTop,
                'is_action' => false,
                'content' => '担当者　プレチェックサービス：レポート　簡易調査',
                'attribute' => '所内処理',
            ],
            // Send Notice for User
            [
                'target_id' => $trademark->user_id,
                'type_acc' => $this->typeUser,
                'target_page' => $this->targetPage,
                'redirect_page' => null,
                'type_notify' => $this->typeNotifyDefault,
                'type_page' => $this->typePageTop,
                'is_action' => false,
                'content' => 'プレチェックレポート：決済完了・調査レポート待ち',
                'attribute' => null,
                'payment_id' => $this->payment->id,
                'payment_status' => PAYMENT_STATUS_2,
            ],
            [
                'target_id' => $trademark->user_id,
                'type_acc' => $this->typeUser,
                'target_page' => $this->targetPage,
                'redirect_page' => null,
                'type_notify' => $this->typeNotifyTodo,
                'type_page' => $this->typePageAnkenTop,
                'is_action' => false,
                'content' => 'プレチェックレポート：決済完了・調査レポート待ち',
                'attribute' => null,
                'payment_id' => $this->payment->id,
                'payment_status' => PAYMENT_STATUS_2,
            ],
        ];

        $this->noticeService->sendNotice([
            'notices' => $notice,
            'notice_details' => $noticeDetails,
        ]);
    }

    /**
     * Send notice for payment with from_page U021, U021N
     *
     * @return void
     */
    public function noticeU021Distinct()
    {
        $trademark = $this->trademark;

        $notice = [
            'flow' => $this->flow ?? Notice::FLOW_PRECHECK,
            'trademark_id' => $trademark->id,
            'user_id' => $trademark->user_id,
            'trademark_info_id' => $this->trademarkInfoId ?? null,
        ];
        $this->noticeDetailService->findByCondition([
            'type_acc' => $this->typeUser,
            'is_answer' => NoticeDetail::IS_NOT_ANSWER,
        ], ['notice'])->get()
            ->where('notice.flow', Notice::FLOW_PRECHECK)
            ->where('notice.trademark_id', $trademark->id)
            ->where('notice.user_id', $trademark->user_id)
            ->map(function ($item) {
                $item->update([
                    'is_answer' => NoticeDetail::IS_ANSWER,
                ]);
            });
        $noticeDetails = [
            // Send Notice for tantou
            [
                'target_id' => null,
                'type_acc' => $this->typeManager,
                'target_page' => $this->targetPage,
                'redirect_page' => $this->redirectPage,
                'type_notify' => $this->typeNotifyTodo,
                'type_page' => $this->typePageTop,
                'is_action' => true,
                'content' => 'プレチェックサービス：レポート　識別力調査',
                'attribute' => null,
            ],
            [
                'target_id' => null,
                'type_acc' => $this->typeManager,
                'target_page' => $this->targetPage,
                'redirect_page' => null,
                'type_notify' => $this->typeNotifyTodo,
                'type_page' => $this->typePageAnkenTop,
                'is_action' => false,
                'content' => 'プレチェックサービス：決済確認済',
                'attribute' => 'お客様へ',
                'completion_date' => now(),
            ],
            [
                'target_id' => null,
                'type_acc' => $this->typeManager,
                'target_page' => $this->targetPage,
                'redirect_page' => $this->redirectPage,
                'type_notify' => $this->typeNotifyTodo,
                'type_page' => $this->typePageAnkenTop,
                'is_action' => false,
                'content' => '担当者　プレチェックサービス：レポート　識別力調査',
                'attribute' => '所内処理',
            ],
            // Send Notice for User
            [
                'target_id' => $trademark->user_id,
                'type_acc' => $this->typeUser,
                'target_page' => $this->targetPage,
                'redirect_page' => null,
                'type_notify' => $this->typeNotifyDefault,
                'type_page' => $this->typePageTop,
                'is_action' => false,
                'content' => 'プレチェックレポート：決済完了・調査レポート待ち',
                'attribute' => null,
                'payment_id' => $this->payment->id,
                'payment_status' => PAYMENT_STATUS_2,
            ],
            [
                'target_id' => $trademark->user_id,
                'type_acc' => $this->typeUser,
                'target_page' => $this->targetPage,
                'redirect_page' => null,
                'type_notify' => $this->typeNotifyTodo,
                'type_page' => $this->typePageAnkenTop,
                'is_action' => false,
                'content' => 'プレチェックレポート：決済完了・調査レポート待ち',
                'attribute' => null,
                'payment_id' => $this->payment->id,
                'payment_status' => PAYMENT_STATUS_2,
            ],
        ];

        $this->noticeService->sendNotice([
            'notices' => $notice,
            'notice_details' => $noticeDetails,
        ]);
    }

    /**
     * Send notice for payment with from_page u000list_change_address02
     *
     * @return void
     */
    public function noticeChangeAddress()
    {
        $trademark = $this->trademark;

        // Update Notice
        $stepBeforeNotice = $this->noticeDetailService->findByCondition([
            'type_acc' => NoticeDetail::TYPE_USER,
            'is_answer' => NoticeDetail::IS_NOT_ANSWER,
        ])->with('notice')->get()
            ->where('notice.trademark_id', $trademark->id)
            ->where('notice.user_id', $trademark->user_id)
            ->where('notice.flow', Notice::FLOW_CHANGE_INFO);
        $stepBeforeNotice->map(function ($item) {
            $item->update([
                'is_answer' => NoticeDetail::IS_ANSWER,
            ]);
        });

        $notice = [
            'flow' => $this->flow ?? Notice::FLOW_CHANGE_INFO,
            'trademark_id' => $trademark->id,
            'user_id' => $trademark->user_id,
            'trademark_info_id' => $this->trademarkInfoId ?? null,
        ];

        $noticeDetails = [
            // Send Notice for Jimu
            [
                'target_id' => null,
                'type_acc' => $this->typeOfficeManager,
                'target_page' => $this->targetPage,
                'redirect_page' => $this->redirectPage,
                'type_notify' => $this->typeNotifyTodo,
                'type_page' => $this->typePageTop,
                'is_action' => true,
                'content' => '更新手続き：出願人名・住所DB変更',
                'attribute' => null,
            ],
            [
                'target_id' => null,
                'type_acc' => $this->typeOfficeManager,
                'target_page' => $this->targetPage,
                'redirect_page' => null,
                'type_notify' => $this->typeNotifyTodo,
                'type_page' => $this->typePageAnkenTop,
                'is_action' => false,
                'content' => '更新手続き：決済確認済',
                'attribute' => 'お客様へ',
                'completion_date' => now(),
            ],
            [
                'target_id' => null,
                'type_acc' => $this->typeOfficeManager,
                'target_page' => $this->targetPage,
                'redirect_page' => $this->redirectPage,
                'type_notify' => $this->typeNotifyTodo,
                'type_page' => $this->typePageAnkenTop,
                'is_action' => false,
                'content' => '事務担当者　更新手続き：出願人名・住所DB変更',
                'attribute' => '所内処理',
            ],
            // Send Notice for User
            [
                'target_id' => $trademark->user_id,
                'type_acc' => $this->typeUser,
                'target_page' => $this->targetPage,
                'redirect_page' => null,
                'type_notify' => $this->typeNotifyDefault,
                'type_page' => $this->typePageTop,
                'is_action' => false,
                'content' => '更新手続き：決済完了・変更待ち',
                'attribute' => null,
                'payment_id' => $this->payment->id,
                'payment_status' => PAYMENT_STATUS_2,
            ],
            [
                'target_id' => $trademark->user_id,
                'type_acc' => $this->typeUser,
                'target_page' => $this->targetPage,
                'redirect_page' => null,
                'type_notify' => $this->typeNotifyTodo,
                'type_page' => $this->typePageAnkenTop,
                'is_action' => false,
                'content' => '更新手続き：決済完了・変更待ち',
                'attribute' => null,
                'payment_id' => $this->payment->id,
                'payment_status' => PAYMENT_STATUS_2,
            ],
        ];

        $this->noticeService->sendNotice([
            'notices' => $notice,
            'notice_details' => $noticeDetails,
        ]);
    }

    /**
     * Send notice for payment with from_page u000list_change_address02kenrisha
     *
     * @return void
     */
    public function noticeChangeAddressKenrisha()
    {
        $trademark = $this->trademark;

        // Update Notice
        $stepBeforeNotice = $this->noticeDetailService->findByCondition([
            'type_acc' => NoticeDetail::TYPE_USER,
            'is_answer' => NoticeDetail::IS_NOT_ANSWER,
        ])->with('notice')->get()
            ->where('notice.trademark_id', $trademark->id)
            ->where('notice.user_id', $trademark->user_id)
            ->where('notice.flow', Notice::FLOW_CHANGE_INFO);

        $stepBeforeNotice->map(function ($item) {
            $item->update([
                'is_answer' => NoticeDetail::IS_ANSWER,
            ]);
        });

        $notice = [
            'flow' => $this->flow ?? Notice::FLOW_CHANGE_INFO,
            'trademark_id' => $trademark->id,
            'user_id' => $trademark->user_id,
            'trademark_info_id' => $this->trademarkInfoId ?? null,
        ];

        $noticeDetails = [
            // Send Notice for Jimu
            [
                'target_id' => null,
                'type_acc' => $this->typeOfficeManager,
                'target_page' => $this->targetPage,
                'redirect_page' => $this->redirectPage,
                'type_notify' => $this->typeNotifyTodo,
                'type_page' => $this->typePageTop,
                'is_action' => true,
                'content' => '更新手続き：登録名義人名・住所DB変更',
                'attribute' => null,
            ],
            [
                'target_id' => null,
                'type_acc' => $this->typeOfficeManager,
                'target_page' => $this->targetPage,
                'redirect_page' => null,
                'type_notify' => $this->typeNotifyTodo,
                'type_page' => $this->typePageAnkenTop,
                'is_action' => false,
                'content' => '更新手続き：決済確認済',
                'attribute' => 'お客様へ',
                'completion_date' => now(),
            ],
            [
                'target_id' => null,
                'type_acc' => $this->typeOfficeManager,
                'target_page' => $this->targetPage,
                'redirect_page' => $this->redirectPage,
                'type_notify' => $this->typeNotifyTodo,
                'type_page' => $this->typePageAnkenTop,
                'is_action' => false,
                'content' => '事務担当者　更新手続き：登録名義人名・住所DB変更',
                'attribute' => '所内処理',
            ],
            // Send Notice for User
            [
                'target_id' => $trademark->user_id,
                'type_acc' => $this->typeUser,
                'target_page' => $this->targetPage,
                'redirect_page' => null,
                'type_notify' => $this->typeNotifyDefault,
                'type_page' => $this->typePageTop,
                'is_action' => false,
                'content' => '更新手続き：決済完了・変更待ち',
                'attribute' => null,
                'payment_id' => $this->payment->id,
                'payment_status' => PAYMENT_STATUS_2,
            ],
            [
                'target_id' => $trademark->user_id,
                'type_acc' => $this->typeUser,
                'target_page' => $this->targetPage,
                'redirect_page' => null,
                'type_notify' => $this->typeNotifyTodo,
                'type_page' => $this->typePageAnkenTop,
                'is_action' => false,
                'content' => '更新手続き：決済完了・変更待ち',
                'attribute' => null,
                'payment_id' => $this->payment->id,
                'payment_status' => PAYMENT_STATUS_2,
            ],
        ];

        $this->noticeService->sendNotice([
            'notices' => $notice,
            'notice_details' => $noticeDetails,
        ]);
    }

    /**
     * Notice U201 Simple
     *
     * @return void
     */
    public function noticeU201Simple()
    {
        $trademark = $this->trademark;
        $trademark->load('machingResults', 'comparisonTrademarkResult');
        $comparisonTrademarkResult = $trademark->comparisonTrademarkResult;
        $matchingResult = $trademark->machingResults->sortByDesc('id')->first();
        $notice = [
            'flow' => Notice::FLOW_RESPONSE_REASON,
            'trademark_id' => $trademark->id,
            'user_id' => $trademark->user_id,
            'step' => Notice::STEP_1
        ];
        // Update Notice at a201simple01 (No 32: H I)
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

        $responseDeadline = $matchingResult->calculateResponseDeadline(-3);

        $noticeDetails = [
            // Send Notice Tantou
            [
                'type_acc' => $this->typeManager,
                'target_page' => $this->targetPage,
                'redirect_page' => $this->redirectPage,
                'type_notify' => $this->typeNotifyTodo,
                'type_page' => $this->typePageTop,
                'is_action' => true,
                'content' => '担当者　拒絶理由通知対応：登録可能性評価レポート　理由数設定・作成',
                'attribute' => null,
                'response_deadline' => $responseDeadline,
            ],
            [
                'target_id' => null,
                'type_acc' => $this->typeManager,
                'target_page' => $this->targetPage,
                'redirect_page' => null,
                'type_notify' => $this->typeNotifyTodo,
                'type_page' => $this->typePageAnkenTop,
                'is_action' => false,
                'content' => '拒絶理由通知対応：シンプルプラン決済確認済',
                'attribute' => 'お客様へ',
                'response_deadline' => $responseDeadline,
                'completion_date' => now(),
            ],
            [
                'type_acc' => $this->typeManager,
                'target_page' => $this->targetPage,
                'redirect_page' => $this->redirectPage,
                'type_notify' => $this->typeNotifyTodo,
                'type_page' => $this->typePageAnkenTop,
                'is_action' => false,
                'content' => '担当者　拒絶理由通知対応：登録可能性評価レポート　理由数設定・作成',
                'attribute' => '所内処理',
                'response_deadline' => $responseDeadline,
            ],
            // Send Notice User
            [
                'target_id' => $trademark->user_id,
                'type_acc' => $this->typeUser,
                'target_page' => $this->targetPage,
                'redirect_page' => null,
                'type_notify' => $this->typeNotifyDefault,
                'type_page' => $this->typePageTop,
                'is_action' => false,
                'content' => '拒絶理由通知対応：シンプルプラン決済完了・返信待ち',
                'attribute' => null,
                'response_deadline' => $responseDeadline,
                'payment_id' => $this->payment->id,
                'payment_status' => PAYMENT_STATUS_2
            ],
            [
                'target_id' => $trademark->user_id,
                'type_acc' => $this->typeUser,
                'target_page' => $this->targetPage,
                'redirect_page' => null,
                'type_notify' => $this->typeNotifyTodo,
                'type_page' => $this->typePageAnkenTop,
                'is_action' => false,
                'content' => '拒絶理由通知対応：シンプルプラン決済完了・返信待ち',
                'attribute' => null,
                'response_deadline' => $responseDeadline,
                'payment_id' => $this->payment->id,
                'payment_status' => PAYMENT_STATUS_2
            ],
        ];

        $this->noticeService->sendNotice([
            'notices' => $notice,
            'notice_details' => $noticeDetails,
        ]);
    }

    /**
     * Notice U201 Select
     *
     * @return void
     */
    public function noticeU201Select01()
    {
        $trademark = $this->trademark;
        $trademark->load('machingResults', 'comparisonTrademarkResult');
        $comparisonTrademarkResult = $trademark->comparisonTrademarkResult;
        $matchingResult = $trademark->machingResults->sortByDesc('id')->first();
        $notice = [
            'flow' => Notice::FLOW_RESPONSE_REASON,
            'trademark_id' => $trademark->id,
            'user_id' => $trademark->user_id,
            'step' => Notice::STEP_1
        ];
        // Update Notice at a201select01 (No 28: H I)
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
        $noticeDetails = [];

        $responseDeadline = $matchingResult->calculateResponseDeadline(-3);

        $noticeDetails = [
            // Send Notice Tantou
            [
                'type_acc' => $this->typeManager,
                'target_page' => $this->targetPage,
                'redirect_page' => $this->redirectPage,
                'type_notify' => $this->typeNotifyTodo,
                'type_page' => $this->typePageTop,
                'is_action' => true,
                'content' => '担当者　拒絶理由通知対応：登録可能性評価レポート　理由数設定・作成',
                'attribute' => null,
                'response_deadline' => $responseDeadline,
            ],
            [
                'target_id' => null,
                'type_acc' => $this->typeManager,
                'target_page' => $this->targetPage,
                'redirect_page' => null,
                'type_notify' => $this->typeNotifyTodo,
                'type_page' => $this->typePageAnkenTop,
                'is_action' => false,
                'content' => '拒絶理由通知対応：セレクトプラン決済確認済',
                'attribute' => 'お客様へ',
                'response_deadline' => $responseDeadline,
                'completion_date' => now(),
            ],
            [
                'type_acc' => $this->typeManager,
                'target_page' => $this->targetPage,
                'redirect_page' => $this->redirectPage,
                'type_notify' => $this->typeNotifyTodo,
                'type_page' => $this->typePageAnkenTop,
                'is_action' => false,
                'content' => '担当者　拒絶理由通知対応：登録可能性評価レポート　理由数設定・作成',
                'attribute' => '所内処理',
                'response_deadline' => $responseDeadline,
            ],
            // Send Notice User
            [
                'target_id' => $trademark->user_id,
                'type_acc' => $this->typeUser,
                'target_page' => $this->targetPage,
                'redirect_page' => null,
                'type_notify' => $this->typeNotifyDefault,
                'type_page' => $this->typePageTop,
                'is_action' => false,
                'content' => '拒絶理由通知対応：セレクトプラン決済完了・返信待ち',
                'attribute' => null,
                'response_deadline' => $responseDeadline,
                'payment_id' => $this->payment->id,
                'payment_status' => PAYMENT_STATUS_2
            ],
            [
                'target_id' => $trademark->user_id,
                'type_acc' => $this->typeUser,
                'target_page' => $this->targetPage,
                'redirect_page' => null,
                'type_notify' => $this->typeNotifyTodo,
                'type_page' => $this->typePageAnkenTop,
                'is_action' => false,
                'content' => '拒絶理由通知対応：セレクトプラン決済完了・返信待ち',
                'attribute' => null,
                'response_deadline' => $responseDeadline,
                'payment_id' => $this->payment->id,
                'payment_status' => PAYMENT_STATUS_2
            ],
        ];

        $this->noticeService->sendNotice([
            'notices' => $notice,
            'notice_details' => $noticeDetails,
        ]);
    }
    /**
     * Notice U201 Select01n
     *
     * @return void
     */
    public function noticeU201Select01n($reasonNoId = null)
    {
        $trademark = $this->trademark;
        $trademark->load('machingResults', 'comparisonTrademarkResult');
        $comparisonTrademarkResult = $trademark->comparisonTrademarkResult;
        $matchingResult = $trademark->machingResults->sortByDesc('id')->first();

        // Update Notice at a201select01 (No 44: H I)
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

        $notice = [
            'flow' => Notice::FLOW_RESPONSE_REASON,
            'trademark_id' => $trademark->id,
            'user_id' => $trademark->user_id,
            'step' => Notice::STEP_1
        ];

        $responseDeadline = $matchingResult->calculateResponseDeadline(-9);

        $noticeDetails = [
            // Send Notice Tantou
            [
                'type_acc' => $this->typeManager,
                'target_page' => $this->targetPage,
                'redirect_page' => $this->redirectPage,
                'type_notify' => $this->typeNotifyTodo,
                'type_page' => $this->typePageTop,
                'is_action' => true,
                'content' => '担当者　拒絶理由通知対応：登録可能性評価レポート　理由数設定・作成',
                'attribute' => null,
                'response_deadline' => $responseDeadline,
            ],
            [
                'target_id' => null,
                'type_acc' => $this->typeManager,
                'target_page' => $this->targetPage,
                'redirect_page' => null,
                'type_notify' => $this->typeNotifyTodo,
                'type_page' => $this->typePageAnkenTop,
                'is_action' => false,
                'content' => '拒絶理由通知対応：セレクトプラン決済確認済',
                'attribute' => 'お客様へ',
                'response_deadline' => $responseDeadline,
                'completion_date' => now(),
            ],
            [
                'type_acc' => $this->typeManager,
                'target_page' => $this->targetPage,
                'redirect_page' => $this->redirectPage,
                'type_notify' => $this->typeNotifyTodo,
                'type_page' => $this->typePageAnkenTop,
                'is_action' => false,
                'content' => '担当者　拒絶理由通知対応：登録可能性評価レポート　理由数設定・作成',
                'attribute' => '所内処理',
                'response_deadline' => $responseDeadline,
            ],
            // Send Notice User
            [
                'target_id' => $trademark->user_id,
                'type_acc' => $this->typeUser,
                'target_page' => $this->targetPage,
                'redirect_page' => null,
                'type_notify' => $this->typeNotifyDefault,
                'type_page' => $this->typePageTop,
                'is_action' => false,
                'content' => '拒絶理由通知対応：セレクトプラン決済完了・返信待ち',
                'attribute' => null,
                'response_deadline' => $responseDeadline,
                'payment_id' => $this->payment->id,
                'payment_status' => PAYMENT_STATUS_1
            ],
            [
                'target_id' => $trademark->user_id,
                'type_acc' => $this->typeUser,
                'target_page' => $this->targetPage,
                'redirect_page' => null,
                'type_notify' => $this->typeNotifyTodo,
                'type_page' => $this->typePageAnkenTop,
                'is_action' => false,
                'content' => '拒絶理由通知対応：セレクトプラン決済完了・返信待ち',
                'attribute' => null,
                'response_deadline' => $responseDeadline,
                'payment_id' => $this->payment->id,
                'payment_status' => PAYMENT_STATUS_1
            ],
        ];

        $this->noticeService->sendNotice([
            'notices' => $notice,
            'notice_details' => $noticeDetails,
        ]);
    }

    /**
     * Notice U210 Alert02
     *
     * @return void
     */
    public function noticeU210Alert02()
    {
        $trademark = $this->trademark;
        $trademark->load('machingResult', 'comparisonTrademarkResult');

        $stepBeforeNotice = $this->noticeDetailService->findByCondition([
            'type_acc' => NoticeDetail::TYPE_USER,
            'is_answer' => NoticeDetail::IS_NOT_ANSWER,
        ])->with('notice')->get()
            ->where('notice.trademark_id', $trademark->id)
            ->where('notice.user_id', $trademark->user_id)
            ->where('notice.flow', Notice::FLOW_RENEWAL_BEFORE_DEADLINE);

        $stepBeforeNotice->map(function ($item) {
            $item->update([
                'is_answer' => NoticeDetail::IS_ANSWER,
            ]);
        });

        $notice = [
            'trademark_id' => $trademark->id,
            'user_id' => $trademark->user_id,
            'flow' => Notice::FLOW_RENEWAL_BEFORE_DEADLINE,
        ];

        $noticeDetails = [];
        $comparisonTrademarkResult = $trademark->comparisonTrademarkResult;
        $responseDeadline = Carbon::parse($comparisonTrademarkResult->response_deadline)->subDays(4);

        $noticeDetails = [
            // A-000top
            [
                'type_acc' => $this->typeOfficeManager,
                'target_page' => $this->targetPage,
                'redirect_page' => $this->redirectPage,
                'type_notify' => $this->typeNotifyTodo,
                'type_page' => $this->typePageTop,
                'is_action' => true,
                'content' => '期限日前期間延長請求書作成',
                'response_deadline' => $responseDeadline,
            ],
            // A-000anken_top
            [
                'type_acc' => $this->typeOfficeManager,
                'target_page' => $this->targetPage,
                'type_notify' => $this->typeNotifyTodo,
                'type_page' => $this->typePageAnkenTop,
                'content' => '事務担当　期限日前期間延長決済確認済',
                'attribute' => 'お客様へ',
                'completion_date' => now(),
            ],
            [
                'type_acc' => $this->typeOfficeManager,
                'target_page' => $this->targetPage,
                'redirect_page' => $this->redirectPage,
                'type_notify' => $this->typeNotifyTodo,
                'type_page' => $this->typePageAnkenTop,
                'is_action' => true,
                'content' => '事務担当　期限日前期間延長請求書作成',
                'attribute' => '所内処理',
                'response_deadline' => $responseDeadline,
            ],
            // U-000top
            [
                'target_id' => $trademark->user_id,
                'type_acc' => $this->typeUser,
                'target_page' => $this->targetPage,
                'type_notify' => $this->typeNotifyDefault,
                'type_page' => $this->typePageTop,
                'content' => '決済完了・返信待ち',
                'payment_id' => $this->payment->id,
                'payment_status' => PAYMENT_STATUS_2,
            ],
            // U-000anken_top
            [
                'target_id' => $trademark->user_id,
                'type_acc' => $this->typeUser,
                'target_page' => $this->targetPage,
                'type_notify' => $this->typeNotifyTodo,
                'type_page' => $this->typePageAnkenTop,
                'content' => '決済完了・返信待ち',
                'payment_id' => $this->payment->id,
                'payment_status' => PAYMENT_STATUS_2,
            ],
        ];

        $this->noticeService->sendNotice([
            'notices' => $notice,
            'notice_details' => $noticeDetails,
        ]);
    }

    /**
     * Notice U210 Over02
     *
     * @return void
     */
    public function noticeU210Over02()
    {
        $trademark = $this->trademark;
        $trademark->load('machingResult', 'comparisonTrademarkResult');

        $stepBeforeNotice = $this->noticeDetailService->findByCondition([
            'type_acc' => NoticeDetail::TYPE_USER,
            'is_answer' => NoticeDetail::IS_NOT_ANSWER,
        ])->with('notice')->get()
            ->where('notice.trademark_id', $trademark->id)
            ->where('notice.user_id', $trademark->user_id)
            ->where('notice.flow', Notice::FLOW_RENEWAL_BEFORE_DEADLINE);

        $stepBeforeNotice->map(function ($item) {
            $item->update([
                'is_answer' => NoticeDetail::IS_ANSWER,
            ]);
        });

        $notice = [
            'trademark_id' => $trademark->id,
            'user_id' => $trademark->user_id,
            'flow' => Notice::FLOW_RENEWAL_BEFORE_DEADLINE,
        ];

        $comparisonTrademarkResult = $trademark->comparisonTrademarkResult;
        $responseDeadline = $comparisonTrademarkResult->response_deadline;

        $noticeDetails = [
            // A-000top
            [
                'type_acc' => $this->typeOfficeManager,
                'target_page' => $this->targetPage,
                'redirect_page' => $this->redirectPage,
                'type_notify' => $this->typeNotifyTodo,
                'type_page' => $this->typePageTop,
                'is_action' => true,
                'content' => '期間外延長請求書作成',
                'response_deadline' => $responseDeadline,
            ],
            // A-000anken_top
            [
                'type_acc' => $this->typeOfficeManager,
                'target_page' => $this->targetPage,
                'type_notify' => $this->typeNotifyTodo,
                'type_page' => $this->typePageAnkenTop,
                'content' => '事務担当　期間外延長決済確認済',
                'attribute' => 'お客様へ',
                'completion_date' => now(),
            ],
            [
                'type_acc' => $this->typeOfficeManager,
                'target_page' => $this->targetPage,
                'type_notify' => $this->typeNotifyTodo,
                'type_page' => $this->typePageAnkenTop,
                'is_action' => true,
                'content' => '事務担当　期間外延長請求書作成',
                'attribute' => '所内処理',
                'response_deadline' => $responseDeadline,
            ],
            // U-000top
            [
                'target_id' => $trademark->user_id,
                'type_acc' => $this->typeUser,
                'target_page' => $this->targetPage,
                'type_notify' => $this->typeNotifyDefault,
                'type_page' => $this->typePageTop,
                'content' => '期間外延長：決済完了・返信待ち',
                'payment_id' => $this->payment->id,
                'payment_status' => PAYMENT_STATUS_2,
            ],
            // U-000anken_top
            [
                'target_id' => $trademark->user_id,
                'type_acc' => $this->typeUser,
                'target_page' => $this->targetPage,
                'type_notify' => $this->typeNotifyTodo,
                'type_page' => $this->typePageAnkenTop,
                'content' => '期間外延長：決済完了・返信待ち',
                'payment_id' => $this->payment->id,
                'payment_status' => PAYMENT_STATUS_2,
            ],
        ];

        $this->noticeService->sendNotice([
            'notices' => $notice,
            'notice_details' => $noticeDetails,
        ]);
    }

    /**
     * Notice U3024025YRKOUKI
     *
     * @return void
     */
    public function noticeU3024025YRKOUKI()
    {
        $payment = $this->payment;
        $trademark = $this->trademark;

        $registerTrademark = RegisterTrademark::find($payment->target_id);

        // Update Notice
        $stepBeforeNotice = $this->noticeDetailService->findByCondition([
            'type_acc' => NoticeDetail::TYPE_USER,
            'is_answer' => NoticeDetail::IS_NOT_ANSWER,
        ])->with('notice')->get()
            ->where('notice.trademark_id', $trademark->id)
            ->where('notice.user_id', $trademark->user_id)
            ->whereIn('notice.flow', [Notice::FLOW_RENEWAL, Notice::FLOW_REGISTRATION_5_YEARS]);
        $stepBeforeNotice->map(function ($item) {
            $item->update([
                'is_answer' => NoticeDetail::IS_ANSWER,
            ]);
        });

        $notice = [
            'trademark_id' => $trademark->id,
            'user_id' => $trademark->user_id,
            'flow' => Notice::FLOW_RENEWAL,
        ];

        $noticeDetails = [];

        if (empty($registerTrademark->id_register_trademark_choice)) {
            $this->redirectPage = route('admin.update.change_address.skip', [
                'id' => $registerTrademark->id,
            ]);

            $noticeDetails = [
                // A-000top
                [
                    'target_id' => null,
                    'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                    'target_page' => $this->targetPage,
                    'redirect_page' => $this->redirectPage,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                    'is_action' => true,
                    'content' => '事務担当　後期納付手続き：登録名義人DB変更（依頼無し）',
                    'attribute' => null,
                    'payment_id' => null,
                    'payment_status' => null,
                ],
                // A-000anken_top
                [
                    'target_id' => null,
                    'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'is_action' => false,
                    'content' => '後期納付手続き：決済確認済',
                    'attribute' => 'お客様へ',
                    'payment_id' => null,
                    'payment_status' => null,
                    'completion_date' => now(),
                ],
                [
                    'target_id' => null,
                    'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                    'target_page' => $this->targetPage,
                    'redirect_page' => $this->redirectPage,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'is_action' => false,
                    'content' => '事務担当　後期納付手続き：登録名義人DB変更（依頼無し）',
                    'attribute' => '所内処理',
                    'payment_id' => null,
                    'payment_status' => null,
                ],
                // U-000top
                [
                    'target_id' => $trademark->user_id,
                    'type_acc' => NoticeDetail::TYPE_USER,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_DEFAULT,
                    'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                    'content' => '後期納付手続き：決済完了・報告待ち',
                    'attribute' => null,
                    'payment_id' => $payment->id,
                    'payment_status' => PAYMENT_STATUS_2,
                ],
                // U-000anken_top
                [
                    'target_id' => $trademark->user_id,
                    'type_acc' => NoticeDetail::TYPE_USER,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'content' => '後期納付手続き：決済完了・報告待ち',
                    'attribute' => null,
                    'payment_id' => $payment->id,
                    'payment_status' => PAYMENT_STATUS_2,
                ],
            ];
        } else {
            $this->redirectPage = route('admin.update.change_address.index', [
                'id' => $trademark->id,
                'register_trademark_id' => $registerTrademark->id,
                'type' => TYPE_1,
            ]);

            $noticeDetails = [
                // A-000top
                [
                    'target_id' => null,
                    'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                    'target_page' => $this->targetPage,
                    'redirect_page' => $this->redirectPage,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                    'is_action' => true,
                    'content' => '事務担当　後期納付手続き：登録名義人DB変更',
                    'attribute' => null,
                    'payment_id' => null,
                    'payment_status' => null,
                ],
                // A-000anken_top
                [
                    'target_id' => null,
                    'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'is_action' => false,
                    'content' => '後期納付手続き：決済確認済',
                    'attribute' => 'お客様へ',
                    'payment_id' => null,
                    'payment_status' => null,
                    'completion_date' => now(),
                ],
                [
                    'target_id' => null,
                    'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                    'target_page' => $this->targetPage,
                    'redirect_page' => $this->redirectPage,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'is_action' => false,
                    'content' => '事務担当　後期納付手続き：登録名義人DB変更',
                    'attribute' => '所内処理',
                    'payment_id' => null,
                    'payment_status' => null,
                ],
                // U-000top
                [
                    'target_id' => $trademark->user_id,
                    'type_acc' => NoticeDetail::TYPE_USER,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_DEFAULT,
                    'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                    'content' => '後期納付手続き：登録名義人住所／名変更お申込み完了・報告待ち',
                    'attribute' => null,
                    'payment_id' => $payment->id,
                    'payment_status' => PAYMENT_STATUS_2,
                ],
                // U-000anken_top
                [
                    'target_id' => $trademark->user_id,
                    'type_acc' => NoticeDetail::TYPE_USER,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'content' => '期納付手続き：登録名義人住所／名変更お申込み完了・報告待ち',
                    'attribute' => null,
                    'payment_id' => $payment->id,
                    'payment_status' => PAYMENT_STATUS_2,
                ],
            ];
        }

        $this->noticeService->sendNotice([
            'notices' => $notice,
            'notice_details' => $noticeDetails,
        ]);
    }

    /**
     * Notice U402
     *
     * @return void
     */
    public function noticeU402()
    {
        $payment = $this->payment;
        $trademark = $this->trademark;

        $registerTrademark = RegisterTrademark::find($payment->target_id);

        // Update Notice
        $stepBeforeNotice = $this->noticeDetailService->findByCondition([
            'type_acc' => NoticeDetail::TYPE_USER,
            'is_answer' => NoticeDetail::IS_NOT_ANSWER,
        ])->with('notice')->get()
            ->where('notice.trademark_id', $trademark->id)
            ->where('notice.user_id', $trademark->user_id)
            ->whereIn('flow', [Notice::FLOW_RENEWAL, Notice::FLOW_REGISTRATION_5_YEARS]);
        $stepBeforeNotice->map(function ($item) {
            $item->update([
                'is_answer' => NoticeDetail::IS_ANSWER,
            ]);
        });

        $notice = [
            'trademark_id' => $trademark->id,
            'user_id' => $trademark->user_id,
            'flow' => Notice::FLOW_RENEWAL,
        ];

        $noticeDetails = [];

        if (empty($registerTrademark->id_register_trademark_choice)) {
            $this->redirectPage = route('admin.update.change_address.skip', [
                'id' => $registerTrademark->id,
            ]);

            $noticeDetails = [
                // A-000top
                [
                    'target_id' => null,
                    'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                    'target_page' => $this->targetPage,
                    'redirect_page' => $this->redirectPage,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                    'is_action' => true,
                    'content' => '事務担当　更新手続き：登録名義人DB変更（依頼無し）',
                    'attribute' => null,
                    'payment_id' => null,
                    'payment_status' => null,
                ],
                // A-000anken_top
                [
                    'target_id' => null,
                    'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'is_action' => false,
                    'content' => '更新手続き：決済確認済',
                    'attribute' => 'お客様へ',
                    'payment_id' => null,
                    'payment_status' => null,
                    'completion_date' => now(),
                ],
                [
                    'target_id' => null,
                    'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                    'target_page' => $this->targetPage,
                    'redirect_page' => $this->redirectPage,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'is_action' => false,
                    'content' => '事務担当　更新手続き：登録名義人DB変更（依頼無し）',
                    'attribute' => '所内処理',
                    'payment_id' => null,
                    'payment_status' => null,
                ],
                // U-000top
                [
                    'target_id' => $trademark->user_id,
                    'type_acc' => NoticeDetail::TYPE_USER,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_DEFAULT,
                    'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                    'content' => '更新手続き：決済完了・報告待ち',
                    'attribute' => null,
                    'payment_id' => $payment->id,
                    'payment_status' => PAYMENT_STATUS_2,
                ],
                // U-000anken_top
                [
                    'target_id' => $trademark->user_id,
                    'type_acc' => NoticeDetail::TYPE_USER,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'content' => '更新手続き：決済完了・報告待ち',
                    'attribute' => null,
                    'payment_id' => $payment->id,
                    'payment_status' => PAYMENT_STATUS_2,
                ],
            ];
        } else {
            $this->redirectPage = route('admin.update.change_address.index', [
                'id' => $trademark->id,
                'register_trademark_id' => $registerTrademark->id,
                'type' => TYPE_2,
            ]);

            $noticeDetails = [
                // A-000top
                [
                    'target_id' => null,
                    'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                    'target_page' => $this->targetPage,
                    'redirect_page' => $this->redirectPage,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                    'is_action' => true,
                    'content' => '事務担当　更新手続き：登録名義人DB変更',
                    'attribute' => null,
                    'payment_id' => null,
                    'payment_status' => null,
                    'completion_date' => now(),
                ],
                // A-000anken_top
                [
                    'target_id' => null,
                    'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'is_action' => false,
                    'content' => '更新手続き：決済確認済',
                    'attribute' => 'お客様へ',
                    'payment_id' => null,
                    'payment_status' => null,
                ],
                [
                    'target_id' => null,
                    'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                    'target_page' => $this->targetPage,
                    'redirect_page' => $this->redirectPage,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'is_action' => false,
                    'content' => '事務担当　更新手続き：登録名義人DB変更',
                    'attribute' => '所内処理',
                    'payment_id' => null,
                    'payment_status' => null,
                ],
                // U-000top
                [
                    'target_id' => $trademark->user_id,
                    'type_acc' => NoticeDetail::TYPE_USER,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_DEFAULT,
                    'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                    'content' => '更新手続き：登録名義人住所／名変更お申込み完了・報告待',
                    'attribute' => null,
                    'payment_id' => $payment->id,
                    'payment_status' => PAYMENT_STATUS_2,
                ],
                // U-000anken_top
                [
                    'target_id' => $trademark->user_id,
                    'type_acc' => NoticeDetail::TYPE_USER,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'content' => '更新手続き：登録名義人住所／名変更お申込み完了・報告待',
                    'attribute' => null,
                    'payment_id' => $payment->id,
                    'payment_status' => PAYMENT_STATUS_2,
                ],
            ];
        }

        $this->noticeService->sendNotice([
            'notices' => $notice,
            'notice_details' => $noticeDetails,
        ]);
    }
}
