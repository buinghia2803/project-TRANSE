<?php

namespace App\Notices;

use App\Models\User;
use App\Models\Admin;
use App\Models\Notice;
use App\Models\Payment;
use App\Models\Precheck;
use App\Models\Trademark;
use App\Models\PlanDetail;
use App\Models\NoticeDetail;
use App\Models\PlanCorrespondence;
use App\Models\RegisterTrademark;
use App\Services\Common\NoticeService;
use App\Services\NoticeDetailService;
use App\Services\NoticeService as ServicesNoticeService;
use App\Services\PaymentService;
use App\Services\PrecheckService;
use App\Services\TrademarkPlanService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use App\Services\XMLProcedures\ProcedureInfomation;

class PaymentNotice extends BaseNotice
{
    protected NoticeService $noticeService;
    protected PrecheckService $precheckService;
    protected PaymentService $paymentService;
    protected NoticeDetailService $noticeDetailService;
    protected ServicesNoticeService $servicesNoticeService;
    protected TrademarkPlanService $trademarkPlanService;

    protected int $typeUser;
    protected int $typeOfficeManager;
    protected int $typeManager;
    protected int $typeSupervisor;
    protected int $typeNotifyTodo;
    protected int $typeNotifyDefault;
    protected int $typePageTop;
    protected int $typePageAnkenTop;
    protected string $targetPage;
    protected string $redirectPage;
    protected Payment $payment;
    protected $trademark;
    protected $paymentType;
    protected $currentUser;
    protected array $data;
    protected $fromPage;

    /**
     * Constructor
     *
     * @return  void
     */
    public function __construct(
        NoticeService   $noticeService,
        PrecheckService $precheckService,
        PaymentService $paymentService,
        ServicesNoticeService $servicesNoticeService,
        NoticeDetailService $noticeDetailService,
        TrademarkPlanService $trademarkPlanService
    )
    {
        $this->data = [];
        parent::__construct();

        $this->noticeService = $noticeService;
        $this->precheckService = $precheckService;

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
        $this->trademarkPlanService = $trademarkPlanService;
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
     * Set trademark
     *
     * @param Trademark $trademark
     *
     * @return void
     */
    public function setTrademark(Trademark $trademark)
    {
        $this->trademark = $trademark;
    }

    /**
     * Set trademark
     *
     * @param Trademark $trademark
     *
     * @return void
     */
    public function setCurrentUser(User $user)
    {
        $this->currentUser = $user;
    }

    /**
     * Send notice for payment
     *
     * @param Payment $payment
     * @param mixed $paymentType
     * @return void
     */
    public function notice(Payment $payment, $paymentType)
    {
        $fromPage = $payment->from_page;
        // Handle for U201Select02_n
        if (str_contains($payment->from_page, U201SELECT02)) {
            $fromPage = U201SELECT02;
        }
        // Handle for U201Select01n
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
        } elseif (str_contains($payment->from_page, U021N)) {
            $fromPage = U021N;
        }

        $this->paymentType = $paymentType;
        $this->payment = $payment->load('trademark');
        $this->trademark = $payment->trademark;
        $this->currentUser = Auth::guard('web')->user();
        $this->fromPage = $fromPage;

        switch ($fromPage) {
            case U011:
                $this->targetPage = route('user.sft.index');
                $this->redirectPage = route('admin.support-first-time.create', $this->trademark->id);
                $this->noticeU011();
                break;
            case U011B:
            case U011B_31:
            case U021B:
            case U021B_31:
            case U031_EDIT_WITH_NUMBER:
            case U031:
            case U031EDIT:
            case U031B:
            case U031C:
            case U031D:
                $trademark = $this->trademark
                    ->load([
                        'supportFirstTime',
                        'appTrademark.trademarkInfo',
                    ]);
                $appTrademark = $trademark->appTrademark;
                $trademarkInfos = $appTrademark->trademarkInfo;
                $lastTrademarkInfos = $trademarkInfos->last();

                if ($fromPage == U011B) {
                    $supportFirstTime = $trademark->supportFirstTime;
                    $sftID = $supportFirstTime->id;
                    $this->targetPage = route('user.support.first.time.u011b', $sftID);
                    $this->redirectPage = route('admin.apply-trademark-document-to-check', $trademark->id);
                }

                if ($fromPage == U011B_31) {
                    $this->targetPage = route('user.sft.proposal-ams', $trademark->id);
                    $this->redirectPage = route('admin.apply-trademark-document-to-check', $trademark->id);
                }

                if ($fromPage == U021B) {
                    $this->targetPage = route('user.precheck.application-trademark', $trademark->id);
                    $this->redirectPage = route('admin.apply-trademark-document-to-check', $trademark->id);
                }

                if ($fromPage == U021B_31) {
                    $this->targetPage = route('user.precheck.application-trademark-v2', $trademark->id);
                    $this->redirectPage = route('admin.apply-trademark-document-to-check', $trademark->id);
                }

                if ($fromPage == U031_EDIT_WITH_NUMBER) {
                    $this->targetPage = route('user.precheck.apply-trademark-with-number', $trademark->id);
                    $this->redirectPage = route('admin.apply-trademark-document-to-check', $trademark->id);
                }

                if ($fromPage == U031_EDIT_WITH_NUMBER) {
                    $this->targetPage = route('user.precheck.apply-trademark-with-number', $trademark->id);
                    $this->redirectPage = route('admin.apply-trademark-document-to-check', $trademark->id);
                }

                if ($fromPage == U031) {
                    $this->targetPage = route('user.apply-trademark-register', $trademark->id);
                    $this->redirectPage = route('admin.apply-trademark-document-to-check', $trademark->id);
                }

                if ($fromPage == U031EDIT) {
                    $this->targetPage = route('user.registration.notify-number', $trademark->id);
                    $this->redirectPage = route('admin.apply-trademark-document-to-check', $trademark->id);
                }

                if ($fromPage == U031B) {
                    $this->targetPage = route('user.register-apply-trademark-after-search', $trademark->id);
                    $this->redirectPage = route('admin.apply-trademark-document-to-check', $trademark->id);
                }

                if ($fromPage == U031C) {
                    $this->targetPage = route('user.apply-trademark-with-product-copied');
                    $this->redirectPage = route('admin.apply-trademark-document-to-check', $trademark->id);
                }

                if ($fromPage == U031D) {
                    $this->targetPage = route('user.apply-trademark-without-number', $trademark->id);
                    $this->redirectPage = route('admin.apply-trademark-document-to-check', $trademark->id);
                }

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
                    $this->noticePrecheckSimple();
                } elseif ($precheck->type_precheck == Precheck::TYPE_CHECK_SELECT) {
                    $this->redirectPage = route('admin.precheck.check-precheck-result', [
                        'id' => $this->trademark->id,
                        'precheck_id' => $precheck->id,
                    ]);
                    $this->noticePrecheckSelect();
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
                    $this->noticePrecheckSimple();
                } elseif ($precheck->type_precheck == Precheck::TYPE_CHECK_SELECT) {
                    $this->redirectPage = route('admin.precheck.check-precheck-result', [
                        'id' => $this->trademark->id,
                        'precheck_id' => $precheck->id,
                    ]);
                    $this->noticePrecheckSelect();
                }
                break;
            case U000LIST_CHANGE_ADDRESS_02:
            case U000LIST_CHANGE_ADDRESS_02_KENRISHA:
                $payment = $payment->load('changeInfoRegister');
                $changeInfoRegister = $payment->changeInfoRegister ?? null;
                $this->targetPage = route('user.application-list.change-address.applicant', [
                    'id' => $this->trademark->id,
                    'from' => 'change_address',
                ]);
                $params['id'] = $this->trademark->id;
                if ($payment->from_page == U000LIST_CHANGE_ADDRESS_02) {
                    $params['change_info_register_id'] = $changeInfoRegister->id;
                } elseif ($payment->from_page == A301) {
                    $params['trademark_info_id'] = $changeInfoRegister->trademark_info_id;
                } elseif ($payment->from_page == U302) {
                    $params['register_trademark_id'] = $payment->target_id;
                }
                $this->redirectPage = !empty($changeInfoRegister) ? route('admin.registration.change-address.index', $params) : '';
                if ($fromPage == U000LIST_CHANGE_ADDRESS_02_KENRISHA) {
                    $this->targetPage = route('user.application-list.change-address.registered', [
                        'id' => $this->trademark->id,
                        'from' => 'change_address',
                    ]);
                    $this->redirectPage = !empty($changeInfoRegister) ? route('admin.update.change_address.index', [
                        'id' => $this->trademark->id,
                        'change_info_register_id' => $changeInfoRegister->id ?? 0,
                    ]) : '';
                    $this->noticeChangeAddressKenrisha();
                } else {
                    $this->noticeChangeAddress();
                }
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

                $this->noticeU201Select02($comparisonTrademarkResult, $reasonNoId);
                break;
            case U201_SIMPLE:
                $payment = $payment->load(['trademark.comparisonTrademarkResult']);
                $comparisonTrademarkResult = $payment->trademark->comparisonTrademarkResult;

                $this->targetPage = route('user.refusal.plans.simple', [
                    'comparison_trademark_result_id' => $comparisonTrademarkResult->id,
                ]);

                // TODO: waiting create pre-question page
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

                // TODO: waiting create pre-question page
                $this->redirectPage = route('admin.refusal.eval-report.create-reason', [
                    'id' => $comparisonTrademarkResult->id,
                ]);

                $this->noticeU201Select01();
                break;
            case U201_SELECT_01_N:
                $payment = $payment->load(['trademark.comparisonTrademarkResult']);
                $comparisonTrademarkResult = $payment->trademark->comparisonTrademarkResult;
                $reasonNoId = $this->data['reason_no_id'];
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
                $secret = Str::random(11);
                unset($this->data['_token']);
                Session::put($secret, $this->data);

                $payment = $payment->load(['trademark.comparisonTrademarkResult']);
                $this->targetPage = route('user.refusal.response-plan.refusal_response_plan.confirm', [
                    'comparison_trademark_result_id' => $this->data['comparison_trademark_result_id'] ?? 0,
                    'trademark_plan_id' => $this->data['trademark_plan_id'] ?? 0,
                    's' => $secret
                ]);

                $this->redirectPage = '';

                if (isset($this->data['required_document']) && $this->data['required_document'] == 0) {
                    $this->redirectPage = route('admin.refusal.material.no-material', [
                        'id' => $this->data['comparison_trademark_result_id'] ?? 0,
                        'trademark_plan_id' => $this->data['trademark_plan_id'] ?? 0,
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
                $data = $this->data;

                $this->targetPage = route('user.registration.notice-latter-period', $data['old_register_trademark']);

                if ($fromPage == U302_402TSUINO_5YR_KOUKI) {
                    $this->targetPage = route('user.registration.notice-later-period.overdue', $data['old_register_trademark']);
                }

                $this->noticeU3024025YRKOUKI();
                break;
            case U402:
            case U402TSUINO:
                $data = $this->data;

                $this->targetPage = route('user.update.notify-procedure', $data['old_register_trademark']);

                if ($fromPage == U402TSUINO) {
                    $this->targetPage = route('user.update.notify-procedure.overdue', $data['old_register_trademark']);
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
                    $secret = Str::random(11);
                    unset($this->data['_token']);
                    Session::put($secret, $this->data);
                    $payment = $payment->load(['trademark.comparisonTrademarkResult']);
                    $this->targetPage = route('user.refusal.response-plan.refusal_response_plan.confirm', [
                        'comparison_trademark_result_id' => $this->data['comparison_trademark_result_id'] ?? 0,
                        'trademark_plan_id' => $this->data['trademark_plan_id'] ?? 0,
                        's' => $secret
                    ]);
                    $this->redirectPage = '';
                    $this->noticeU203B02();
                }
        }
    }

    /**
     * Create notice for u000free
     */
    public function noticeU000Free()
    {
        $trademark = $this->trademark;

        // Get Old redirect_page
        $redirectPageOld = $this->targetPage;
        $redirectPageOld = str_replace(request()->root(), '', $redirectPageOld);

        $stepBeforeNotice = $this->noticeDetailService->findByCondition([
            'type_acc' => NoticeDetail::TYPE_USER,
            'is_answer' => NoticeDetail::IS_NOT_ANSWER,
            'redirect_page' => $redirectPageOld,
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

        if ($this->paymentType == Payment::CREDIT_CARD) {
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
                    'content' => ' (書類名)回答済・決済待ち',
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_1,
                ],
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
                    'content' => '(書類名)回答済・決済待ち',
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_1,
                ],
                [
                    'target_id' => $trademark->user_id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageAnkenTop,
                    'content' => ' (書類名)決済完了・返信待ち',
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_2,
                ],
            ];
        } elseif ($this->paymentType == Payment::BANK_TRANSFER) {
            $noticeDetails = [
                //send notice a-000anken_top
                [
                    'type_acc' => $this->typeOfficeManager,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageAnkenTop,
                    'content' => '(書類名)回答受領',
                    'completion_date' => now(),
                ],

                //send notice u-000top
                [
                    'target_id' => $trademark->user_id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyDefault,
                    'type_page' => $this->typePageTop,
                    'content' => '(書類名)回答済・決済待ち',
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_1,
                ],
                [
                    'target_id' => $trademark->user_id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageTop,
                    'content' => 'フリー履歴追加：未入金です',
                ],

                //send notice u-000anken_top
                [
                    'target_id' => $trademark->user_id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageAnkenTop,
                    'content' => '(書類名)回答済・決済待ち',
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_1,
                ],
            ];
        }

        $this->noticeService->sendNotice([
            'notices' => $notice,
            'notice_details' => $noticeDetails,
        ]);
    }
    /**
     * Create notice for U302cancel
     */
    public function noticeU302ACancel($id)
    {
        $admin = Admin::where('role', ROLE_OFFICE_MANAGER)->first();
        $trademark = $this->trademark;

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
            'flow' => Notice::FLOW_REGISTER_TRADEMARK,
            'user_id' => $trademark->user_id,
            'trademark_id' => $trademark->id,
            'trademark_info_id' => null,
        ];
        $noticeDetails = [
            [
                'target_id' => $admin->id,
                'type_acc' => $this->typeOfficeManager,
                'target_page' => route('user.registration.cancel', [
                    'id' => $id ?? 0,
                ]),
                'redirect_page' => null,
                'type_notify' => $this->typeNotifyTodo,
                'type_page' => $this->typePageAnkenTop,
                'is_action' => false,
                'content' => '登録査定：登録料納付不要指示受領',
                'attribute' => 'お客様から',
                'completion_date' => now(),
            ],
            [
                'target_id' => $trademark->user_id,
                'type_acc' => $this->typeUser,
                'target_page' => route('user.registration.cancel', [
                    'id' => $id ?? 0,
                ]),
                'redirect_page' => null,
                'type_notify' => $this->typeNotifyDefault,
                'type_page' => $this->typePageTop,
                'is_action' => false,
                'content' => '登録手続き：依頼しない',
            ],
            [
                'target_id' => $trademark->user_id,
                'type_acc' => $this->typeUser,
                'target_page' => route('user.registration.cancel', [
                    'id' => $id ?? 0,
                ]),
                'redirect_page' => null,
                'type_notify' => $this->typeNotifyTodo,
                'type_page' => $this->typePageAnkenTop,
                'is_action' => false,
                'content' => '登録手続き：依頼しない',
            ],
        ];

        $this->noticeService->sendNotice([
            'notices' => $notice,
            'notice_details' => $noticeDetails,
        ]);
    }

    /**
     * Create notice for U302
     */
    public function noticeU302Auto(array $params)
    {
        $matchingResult = $params['matching_result'] ?? null;
        $notice = [
            'flow' => Notice::FLOW_REGISTER_TRADEMARK,
            'user_id' => $this->trademark->user_id,
            'trademark_id' => $this->trademark->id,
        ];
        $admin = Admin::where('role', ROLE_OFFICE_MANAGER)->first();
        $responseDeadline = isset($matchingResult->response_deadline) && $matchingResult->response_deadline
            ? Carbon::parse($matchingResult->response_deadline)->addMonths($matchingResult->pi_tfr_period ?? 0)
            : null;
        $noticeDetails = [
            [
                'target_id' => $admin->id,
                'type_acc' => ROLE_MANAGER,
                'target_page' => route('user.registration.procedure', [
                    'id' => $matchingResult->id ?? 0,
                    'register_trademark_id' => $params['register_trademark_id'] ?? 0,
                ]),
                'redirect_page' => route('admin.registration.document.modification', [
                    'id' => $matchingResult->id,
                    'register_trademark_id' => $params['register_trademark_id'] ?? 0,
                ]),
                'type_notify' => $this->typeNotifyTodo,
                'type_page' => $this->typePageTop,
                'is_action' => true,
                'content' => '事務担当　登録査定登録料納付書付属手続補正書（内容）作成',
                'response_deadline' => $responseDeadline,
            ],
            [
                'target_id' => $admin->id,
                'type_acc' => ROLE_MANAGER,
                'target_page' => route('user.registration.procedure', [
                    'id' => $matchingResult->id ?? 0,
                    'register_trademark_id' => $params['register_trademark_id'] ?? 0,
                ]),
                'redirect_page' => route('admin.registration.document.modification', [
                    'id' => $matchingResult->id,
                    'register_trademark_id' => $params['register_trademark_id'] ?? 0,
                ]),
                'type_notify' => $this->typeNotifyTodo,
                'type_page' => $this->typePageAnkenTop,
                'is_action' => true,
                'content' => '事務担当　登録査定登録料納付書付属手続補正書（内容）作成',
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
     * Create notice for U302
     */
    public function noticeU302()
    {
        $noticeDetails = [];
        $trademark = $this->trademark;
        $registerTrademark = RegisterTrademark::find($this->data['register_trademark_id']);

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
            'flow' => Notice::FLOW_REGISTER_TRADEMARK,
            'user_id' => $trademark->user_id,
            'trademark_id' => $trademark->id,
            'step' => Notice::STEP_2,
            'trademark_info_id' => null,
        ];
        $admin = Admin::where('role', ROLE_OFFICE_MANAGER)->first();
        // Has/hasn't change name or address or both
        if ($this->paymentType == Payment::CREDIT_CARD) {
            if (!$registerTrademark->trademark_info_change_status) {
                $noticeDetails = [
                    // Send notice for admin
                    // A-000top
                    [
                        'target_id' => $admin->id,
                        'type_acc' => ROLE_MANAGER,
                        'target_page' => route('user.registration.procedure', [
                            'id' => $this->data['maching_result_id'] ?? 0,
                            'register_trademark_id' => $this->data['register_trademark_id'] ?? 0,
                        ]),
                        'redirect_page' => route('admin.registration.skip', [
                            'id' => $this->data['maching_result_id'],
                            'register_trademark_id' => $this->data['register_trademark_id'] ?? 0,
                        ]),
                        'type_notify' => $this->typeNotifyTodo,
                        'type_page' => $this->typePageTop,
                        'is_action' => true,
                        'content' => '事務担当 登録査定:出願人DB変更（依頼無し）',
                        'attribute' => null,
                    ],
                    // A-000anken_top
                    [
                        'target_id' => $admin->id,
                        'type_acc' => ROLE_MANAGER,
                        'target_page' => route('user.registration.procedure', [
                            'id' => $this->data['maching_result_id'] ?? 0,
                            'register_trademark_id' => $this->data['register_trademark_id'] ?? 0,
                        ]),
                        'type_notify' => $this->typeNotifyTodo,
                        'type_page' => $this->typePageAnkenTop,
                        'content' => '登録査定：登録料納付申し込み受領',
                        'attribute' => 'お客様から',
                        'completion_date' => now(),
                    ],
                    [
                        'target_id' => $admin->id,
                        'type_acc' => ROLE_MANAGER,
                        'target_page' => route('user.registration.procedure', [
                            'id' => $this->data['maching_result_id'] ?? 0,
                            'register_trademark_id' => $this->data['register_trademark_id'] ?? 0,
                        ]),
                        'type_notify' => $this->typeNotifyTodo,
                        'type_page' => $this->typePageAnkenTop,
                        'content' => '登録査定：登録料納付決済確認済',
                        'attribute' => 'お客様へ',
                        'completion_date' => now(),
                    ],
                    [
                        'target_id' => $admin->id,
                        'type_acc' => ROLE_MANAGER,
                        'target_page' => route('user.registration.procedure', [
                            'id' => $this->data['maching_result_id'] ?? 0,
                            'register_trademark_id' => $this->data['register_trademark_id'] ?? 0,
                        ]),
                        'redirect_page' => route('admin.registration.skip', [
                            'id' => $this->data['maching_result_id'],
                            'register_trademark_id' => $this->data['register_trademark_id'] ?? 0,
                        ]),
                        'type_notify' => $this->typeNotifyTodo,
                        'type_page' => $this->typePageAnkenTop,
                        'content' => '事務担当 登録査定:出願人DB変更（依頼無し）',
                        'attribute' => 'お客様から',
                    ],
                    // U000top
                    [
                        'target_id' => $this->trademark->user_id,
                        'type_acc' => ROLE_OFFICE_MANAGER,
                        'target_page' => route('user.registration.procedure', [
                            'id' => $this->data['maching_result_id'] ?? 0,
                            'register_trademark_id' => $this->data['register_trademark_id'] ?? 0,
                        ]),
                        'redirect_page' => null,
                        'type_notify' => $this->typeNotifyDefault,
                        'type_page' => $this->typePageTop,
                        'content' => '登録査定：登録手続きお申し込み完了',
                        'attribute' => 'お客様から',
                        'payment_id' => $this->payment->id,
                        'payment_status' => Payment::STATUS_WAITING_PAYMENT,
                    ],
                    [
                        'target_id' => $this->trademark->user_id,
                        'type_acc' => ROLE_OFFICE_MANAGER,
                        'target_page' => route('user.registration.procedure', [
                            'id' => $this->data['maching_result_id'] ?? 0,
                            'register_trademark_id' => $this->data['register_trademark_id'] ?? 0,
                        ]),
                        'redirect_page' => null,
                        'type_notify' => $this->typeNotifyDefault,
                        'type_page' => $this->typePageTop,
                        'content' => '登録査定：登録手続き決済完了・報告待ち',
                        'attribute' => 'お客様から',
                        'payment_id' => $this->payment->id,
                        'payment_status' => Payment::STATUS_PAID,
                    ],
                    // U-000Anken_top
                    [
                        'target_id' => $this->trademark->user_id,
                        'type_acc' => ROLE_OFFICE_MANAGER,
                        'target_page' => route('user.registration.procedure', [
                            'id' => $this->data['maching_result_id'] ?? 0,
                            'register_trademark_id' => $this->data['register_trademark_id'] ?? 0,
                        ]),
                        'redirect_page' => null,
                        'type_notify' => $this->typeNotifyTodo,
                        'type_page' => $this->typePageAnkenTop,
                        'content' => '登録査定：登録手続きお申し込み完了',
                        'attribute' => 'お客様から',
                        'payment_id' => $this->payment->id,
                        'payment_status' => Payment::STATUS_WAITING_PAYMENT,
                    ],
                    [
                        'target_id' => $this->trademark->user_id,
                        'type_acc' => ROLE_OFFICE_MANAGER,
                        'target_page' => route('user.registration.procedure', [
                            'id' => $this->data['maching_result_id'] ?? 0,
                            'register_trademark_id' => $this->data['register_trademark_id'] ?? 0,
                        ]),
                        'redirect_page' => null,
                        'type_notify' => $this->typeNotifyTodo,
                        'type_page' => $this->typePageAnkenTop,
                        'content' => '登録査定：登録手続き決済完了・報告待ち',
                        'attribute' => 'お客様から',
                        'payment_id' => $this->payment->id,
                        'payment_status' => Payment::STATUS_PAID,
                    ],
                ];
            } else {
                $noticeDetails = [
                    // Send notice for admin
                    // A-000top
                     [
                        'target_id' => $admin->id,
                        'type_acc' => ROLE_MANAGER,
                        'target_page' => route('user.registration.procedure', [
                            'id' => $this->data['maching_result_id'] ?? 0,
                            'register_trademark_id' => $this->data['register_trademark_id'] ?? 0,
                        ]),
                        'redirect_page' => route('admin.registration.change-address.index', [
                            'id' => $trademark->id,
                            'register_trademark_id' => $registerTrademark->id ?? 0,
                        ]),
                        'type_notify' => $this->typeNotifyTodo,
                        'type_page' => $this->typePageTop,
                        'is_action' => true,
                        'content' => '事務担当 登録査定:出願人DB変更',
                        'attribute' => null,
                     ],
                    // A-000anken_top
                     [
                        'target_id' => $admin->id,
                        'type_acc' => ROLE_MANAGER,
                        'target_page' => route('user.registration.procedure', [
                            'id' => $this->data['maching_result_id'] ?? 0,
                            'register_trademark_id' => $this->data['register_trademark_id'] ?? 0,
                        ]),
                        'type_notify' => $this->typeNotifyTodo,
                        'type_page' => $this->typePageAnkenTop,
                        'content' => '登録査定：出願人住所/名称変更申し込み受領',
                        'attribute' => 'お客様から',
                        'completion_date' => now(),
                     ],
                     [
                        'target_id' => $admin->id,
                        'type_acc' => ROLE_MANAGER,
                        'target_page' => route('user.registration.procedure', [
                            'id' => $this->data['maching_result_id'] ?? 0,
                            'register_trademark_id' => $this->data['register_trademark_id'] ?? 0,
                        ]),
                        'type_notify' => $this->typeNotifyTodo,
                        'type_page' => $this->typePageAnkenTop,
                        'content' => '登録査定：登録料納付決済確認済',
                        'attribute' => 'お客様へ',
                        'completion_date' => now(),
                     ],
                     [
                        'target_id' => $admin->id,
                        'type_acc' => ROLE_MANAGER,
                        'target_page' => route('user.registration.procedure', [
                            'id' => $this->data['maching_result_id'] ?? 0,
                            'register_trademark_id' => $this->data['register_trademark_id'] ?? 0,
                        ]),
                        'redirect_page' => route('admin.registration.change-address.index', [
                            'id' => $trademark->id,
                            'register_trademark_id' => $registerTrademark->id ?? 0,
                        ]),
                        'type_notify' => $this->typeNotifyTodo,
                        'type_page' => $this->typePageAnkenTop,
                        'content' => '事務担当　登録査定：出願人DB変更',
                        'attribute' => '所内処理',
                     ],
                    // U000top
                     [
                        'target_id' => $this->trademark->user_id,
                        'type_acc' => ROLE_OFFICE_MANAGER,
                        'target_page' => route('user.registration.procedure', [
                            'id' => $this->data['maching_result_id'] ?? 0,
                            'register_trademark_id' => $this->data['register_trademark_id'] ?? 0,
                        ]),
                        'redirect_page' => null,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_DEFAULT,
                        'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                        'content' => '登録査定：出願人住所/名称お申し込み完了',
                        'payment_id' => $this->payment->id,
                        'payment_status' => Payment::STATUS_WAITING_PAYMENT,
                     ],
                     [
                        'target_id' => $this->trademark->user_id,
                        'type_acc' => ROLE_OFFICE_MANAGER,
                        'target_page' => route('user.registration.procedure', [
                            'id' => $this->data['maching_result_id'] ?? 0,
                            'register_trademark_id' => $this->data['register_trademark_id'] ?? 0,
                        ]),
                        'redirect_page' => null,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_DEFAULT,
                        'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                        'content' => '登録査定：登録手続き決済完了・報告待ち',
                        'payment_id' => $this->payment->id,
                        'payment_status' => Payment::STATUS_PAID,
                     ],
                    // U-000Anken_top
                     [
                        'target_id' => $this->trademark->user_id,
                        'type_acc' => ROLE_OFFICE_MANAGER,
                        'target_page' => route('user.registration.procedure', [
                            'id' => $this->data['maching_result_id'] ?? 0,
                            'register_trademark_id' => $this->data['register_trademark_id'] ?? 0,
                        ]),
                        'redirect_page' => null,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                        'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                        'content' => '登録査定：出願人住所/名称お申し込み完了',
                        'payment_id' => $this->payment->id,
                        'payment_status' => Payment::STATUS_WAITING_PAYMENT,
                     ],
                     [
                        'target_id' => $this->trademark->user_id,
                        'type_acc' => ROLE_OFFICE_MANAGER,
                        'target_page' => route('user.registration.procedure', [
                            'id' => $this->data['maching_result_id'] ?? 0,
                            'register_trademark_id' => $this->data['register_trademark_id'] ?? 0,
                        ]),
                        'redirect_page' => null,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                        'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                        'content' => '登録査定：登録手続き決済完了・報告待ち',
                        'payment_id' => $this->payment->id,
                        'payment_status' => Payment::STATUS_PAID,
                     ],
                ];
            }
        } elseif ($this->paymentType == Payment::BANK_TRANSFER) {
            // Has/hasn't change name or address or both
            if (!$registerTrademark->trademark_info_change_status) {
                $noticeDetails = [
                    // A-000anken_top
                    [
                        'target_id' => $admin->id,
                        'type_acc' => ROLE_MANAGER,
                        'target_page' => route('user.registration.procedure', [
                            'id' => $this->data['maching_result_id'] ?? 0,
                            'register_trademark_id' => $this->data['register_trademark_id'] ?? 0,
                        ]),
                        'type_notify' => $this->typeNotifyTodo,
                        'type_page' => $this->typePageAnkenTop,
                        'content' => '登録査定：登録料納付申し込み受領',
                        'attribute' => 'お客様から',
                        'completion_date' => now(),
                    ],
                    // U000top
                    [
                        'target_id' => $this->trademark->user_id,
                        'type_acc' => ROLE_OFFICE_MANAGER,
                        'target_page' => route('user.registration.procedure', [
                            'id' => $this->data['maching_result_id'] ?? 0,
                            'register_trademark_id' => $this->data['register_trademark_id'] ?? 0,
                        ]),
                        'redirect_page' => null,
                        'type_notify' => $this->typeNotifyDefault,
                        'type_page' => $this->typePageTop,
                        'content' => '登録査定：登録手続きお申し込み完了',
                        'payment_id' => $this->payment->id,
                        'payment_status' => Payment::STATUS_SAVE,
                    ],
                    [
                        'target_id' => $this->trademark->user_id,
                        'type_acc' => ROLE_OFFICE_MANAGER,
                        'target_page' => route('user.registration.procedure', [
                            'id' => $this->data['maching_result_id'] ?? 0,
                            'register_trademark_id' => $this->data['register_trademark_id'] ?? 0,
                        ]),
                        'redirect_page' => null,
                        'type_notify' => $this->typeNotifyDefault,
                        'type_page' => $this->typePageTop,
                        'content' => '登録査定：登録手続きお申し込み完了・決済待ち',
                        'payment_id' => $this->payment->id,
                        'payment_status' => Payment::STATUS_WAITING_PAYMENT,
                    ],
                    [
                        'target_id' => $this->trademark->user_id,
                        'type_acc' => ROLE_OFFICE_MANAGER,
                        'target_page' => route('user.registration.procedure', [
                            'id' => $this->data['maching_result_id'] ?? 0,
                            'register_trademark_id' => $this->data['register_trademark_id'] ?? 0,
                        ]),
                        'redirect_page' => null,
                        'type_notify' => $this->typeNotifyTodo,
                        'type_page' => $this->typePageTop,
                        'content' => '登録査定：未入金です。',
                        'payment_id' => $this->payment->id,
                    ],
                    // U-000Anken_top
                    [
                        'target_id' => $this->trademark->user_id,
                        'type_acc' => ROLE_OFFICE_MANAGER,
                        'target_page' => route('user.registration.procedure', [
                            'id' => $this->data['maching_result_id'] ?? 0,
                            'register_trademark_id' => $this->data['register_trademark_id'] ?? 0,
                        ]),
                        'redirect_page' => null,
                        'type_notify' => $this->typeNotifyTodo,
                        'type_page' => $this->typePageAnkenTop,
                        'content' => '登録査定：登録手続きお申し込み完了',
                        'payment_id' => $this->payment->id,
                        'payment_status' => Payment::STATUS_SAVE,
                    ],
                    [
                        'target_id' => $this->trademark->user_id,
                        'type_acc' => ROLE_OFFICE_MANAGER,
                        'target_page' => route('user.registration.procedure', [
                            'id' => $this->data['maching_result_id'] ?? 0,
                            'register_trademark_id' => $this->data['register_trademark_id'] ?? 0,
                        ]),
                        'redirect_page' => null,
                        'type_notify' => $this->typeNotifyTodo,
                        'type_page' => $this->typePageAnkenTop,
                        'content' => '登録査定：登録手続きお申し込み完了・決済待ち',
                        'payment_id' => $this->payment->id,
                        'payment_status' => Payment::STATUS_WAITING_PAYMENT,
                    ],
                ];
            } else {
                // A-000anken_top
                $noticeDetails = [
                    [
                        'target_id' => $admin->id,
                        'type_acc' => ROLE_MANAGER,
                        'target_page' => route('user.registration.procedure', [
                            'id' => $this->data['maching_result_id'] ?? 0,
                            'register_trademark_id' => $this->data['register_trademark_id'] ?? 0,
                        ]),
                        'type_notify' => $this->typeNotifyTodo,
                        'type_page' => $this->typePageAnkenTop,
                        'content' => '登録査定：出願人住所/名称変更申し込み受領',
                        'attribute' => 'お客様から',
                        'completion_date' => now(),
                    ],
                    // U000top
                    [
                        'target_id' => $this->trademark->user_id,
                        'type_acc' => ROLE_OFFICE_MANAGER,
                        'target_page' => route('user.registration.procedure', [
                            'id' => $this->data['maching_result_id'] ?? 0,
                            'register_trademark_id' => $this->data['register_trademark_id'] ?? 0,
                        ]),
                        'redirect_page' => null,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_DEFAULT,
                        'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                        'content' => '登録査定：出願人住所/名称お申し込み完了',
                        'payment_id' => $this->payment->id,
                        'payment_status' => Payment::STATUS_SAVE,
                    ],
                    [
                        'target_id' => $this->trademark->user_id,
                        'type_acc' => ROLE_OFFICE_MANAGER,
                        'target_page' => route('user.registration.procedure', [
                            'id' => $this->data['maching_result_id'] ?? 0,
                            'register_trademark_id' => $this->data['register_trademark_id'] ?? 0,
                        ]),
                        'redirect_page' => null,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_DEFAULT,
                        'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                        'content' => '登録査定：出願人住所/名称お申し込み完了・決済待ち',
                        'payment_id' => $this->payment->id,
                        'payment_status' => Payment::STATUS_WAITING_PAYMENT,
                    ],
                    [
                        'target_id' => $this->trademark->user_id,
                        'type_acc' => ROLE_OFFICE_MANAGER,
                        'target_page' => route('user.registration.procedure', [
                            'id' => $this->data['maching_result_id'] ?? 0,
                            'register_trademark_id' => $this->data['register_trademark_id'] ?? 0,
                        ]),
                        'redirect_page' => null,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                        'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                        'content' => '登録査定：未入金です。',
                    ],
                    // U-000Anken_top
                    [
                        'target_id' => $this->trademark->user_id,
                        'type_acc' => ROLE_OFFICE_MANAGER,
                        'target_page' => route('user.registration.procedure', [
                            'id' => $this->data['maching_result_id'] ?? 0,
                            'register_trademark_id' => $this->data['register_trademark_id'] ?? 0,
                        ]),
                        'redirect_page' => null,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                        'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                        'content' => '登録査定：出願人住所/名称お申し込み完了',
                        'payment_id' => $this->payment->id,
                        'payment_status' => Payment::STATUS_SAVE,
                    ],
                    [
                        'target_id' => $this->trademark->user_id,
                        'type_acc' => ROLE_OFFICE_MANAGER,
                        'target_page' => route('user.registration.procedure', [
                            'id' => $this->data['maching_result_id'] ?? 0,
                            'register_trademark_id' => $this->data['register_trademark_id'] ?? 0,
                        ]),
                        'redirect_page' => null,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                        'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                        'content' => '登録査定：出願人住所/名称お申し込み完了・決済待ち',
                        'payment_id' => $this->payment->id,
                        'payment_status' => Payment::STATUS_WAITING_PAYMENT,
                    ],
                ];
            }
        }
        $this->noticeService->sendNotice([
            'notices' => $notice,
            'notice_details' => $noticeDetails,
        ]);
    }

    /**
     * Send notice for payment with from_page u203b02
     *
     * @return void
     */
    public function noticeU203B02()
    {
        $trademark = $this->trademark;
        $trademark->load([
            'machingResult' => function ($query) {
                return $query->where('pi_document_name', ProcedureInfomation::NOTIFICATION_REASONS_REFUSAL);
            },
        ]);
        $matchingResult = $trademark->machingResults->last();
        $matchingResult->load('comparisonTrademarkResult.planCorrespondence');
        $planCorrespondence = $matchingResult->comparisonTrademarkResult->planCorrespondence;
        if ($planCorrespondence->type == PlanCorrespondence::TYPE_SELECTION) {
            $subDays = -30;
        } else {
            $subDays = -25;
        }
        $responseDeadline = $matchingResult->calculateResponseDeadline($subDays);

        $notice = [
            'flow' => Notice::FLOW_RESPONSE_REASON,
            'trademark_id' => $trademark->id,
            'step' => Notice::STEP_3,
            'trademark_info_id' => null,
            'user_id' => $trademark->user_id,
        ];
        $noticeDetails = [];

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
                'is_answer' => NoticeDetail::IS_ANSWER,
            ]);
        });

        if ($this->paymentType == Payment::CREDIT_CARD) {
            $noticeDetails = [
                // a000anken_top
                [
                    'target_id' => null,
                    'type_acc' => $this->typeManager,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageAnkenTop,
                    'is_action' => false,
                    'content' => '拒絶理由通知対応：方針案選択回答受領',
                    'attribute' => 'お客様から',
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
                    'content' => '拒絶理由通知対応：方針案選択決済確認',
                    'attribute' => 'お客様から',
                    'response_deadline' => $responseDeadline,
                ],
                // u000top
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyDefault,
                    'type_page' => $this->typePageTop,
                    'is_action' => false,
                    'content' => '拒絶理由通知対応：方針案選択完了・決済待ち',
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_1,
                ],
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyDefault,
                    'type_page' => $this->typePageTop,
                    'is_action' => false,
                    'content' => '拒絶理由通知対応：方針案選択決済完了',
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_2,
                ],
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => route('user.refusal.response-plan.notice_next', [
                        'comparison_trademark_result_id' => $this->data['comparison_trademark_result_id'] ?? 0,
                        'trademark_plan_id' => $this->data['trademark_plan_id'] ?? 0,
                    ]),
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageTop,
                    'is_action' => true,
                    'content' => ' 拒絶理由通知対応：必要資料提出',
                    'attribute' => null,
                    'response_deadline' => $responseDeadline,
                ],
                // u000anken_top
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageAnkenTop,
                    'is_action' => false,
                    'content' => '拒絶理由通知対応：方針案選択完了・決済待ち',
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_1,
                ],
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => route('user.refusal.response-plan.notice_next', [
                        'comparison_trademark_result_id' => $this->data['comparison_trademark_result_id'] ?? 0,
                        'trademark_plan_id' => $this->data['trademark_plan_id'] ?? 0,
                    ]),
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageAnkenTop,
                    'is_action' => false,
                    'content' => '拒絶理由通知対応：方針案選択決済完了',
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_2,
                ],
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => route('user.refusal.response-plan.notice_next', [
                        'comparison_trademark_result_id' => $this->data['comparison_trademark_result_id'] ?? 0,
                        'trademark_plan_id' => $this->data['trademark_plan_id'] ?? 0,
                    ]),
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageAnkenTop,
                    'is_action' => false,
                    'content' => '拒絶理由通知対応：必要資料提出',
                    'response_deadline' => $responseDeadline,
                ],
            ];
        } elseif ($this->paymentType == Payment::BANK_TRANSFER) {
            $notice['step'] = Notice::STEP_4;

            $noticeDetails = [
                // a000anken_top
                [
                    'target_id' => null,
                    'type_acc' => $this->typeManager,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageAnkenTop,
                    'is_action' => false,
                    'content' => '拒絶理由通知対応：方針案選択回答受領',
                    'attribute' => 'お客様から',
                    'response_deadline' => $responseDeadline,
                ],
                // Send Notice User
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyDefault,
                    'type_page' => $this->typePageTop,
                    'is_action' => false,
                    'content' => '拒絶理由通知対応：方針案選択完了・決済待ち',
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_1,
                ],
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageTop,
                    'is_action' => false,
                    'content' => '拒絶理由通知対応：未入金です',
                ],
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageAnkenTop,
                    'content' => '拒絶理由通知対応：方針案選択完了・決済待ち',
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_1,
                ],
            ];
        }

        $this->noticeService->sendNotice([
            'notices' => $notice,
            'notice_details' => $noticeDetails,
        ]);
    }

    /**
     * Send notice for payment with from_page u203b02 with no fee.
     *
     * @return void
     */
    public function noticeU203B02NoFee($hasDoc = true)
    {
        $trademark = $this->trademark;

        $trademarkPlan = $this->trademarkPlanService->find($this->data['trademark_plan_id']);
        $planCorrespondence = $trademarkPlan->planCorrespondence;
        $comparisonTrademarkResult = $planCorrespondence->comparisonTrademarkResult;
        $matchingResult = $comparisonTrademarkResult->machingResult;

        $subDays = -25;
        if ($planCorrespondence->type == PlanCorrespondence::TYPE_SELECTION) {
            $subDays = -30;
        }
        $secret = Str::random(11);
        unset($this->data['_token']);
        Session::put($secret, $this->data);

        $this->targetPage = route('user.refusal.response-plan.refusal_response_plan.confirm', [
            'comparison_trademark_result_id' => $this->data['comparison_trademark_result_id'] ?? 0,
            'trademark_plan_id' => $this->data['trademark_plan_id'] ?? 0,
            's' => $secret
        ]);

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
        $notice = [
            'flow' => Notice::FLOW_RESPONSE_REASON,
            'trademark_id' => $trademark->id,
            'step' => Notice::STEP_4,
            'trademark_info_id' => null,
            'user_id' => $trademark->user_id,
        ];
        $noticeDetails = [];
        if ($hasDoc) {
            $noticeDetails = [
                // Send Notice User
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyDefault,
                    'type_page' => $this->typePageTop,
                    'is_action' => false,
                    'content' => '拒絶理由通知対応：方針案選択完了',
                ],
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => route('user.refusal.materials.index', [
                        'id' => $this->data['comparison_trademark_result_id'] ?? '',
                        'trademark_plan_id' => $this->data['trademark_plan_id'],
                    ]),
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageTop,
                    'is_action' => false,
                    'content' => '拒絶理由通知対応：必要資料提出',
                    'response_deadline' => $responseDeadline,
                ],
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageAnkenTop,
                    'is_action' => false,
                    'content' => '拒絶理由通知対応：方針案選択完了',
                ],
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => route('user.refusal.materials.index', [
                        'id' => $this->data['comparison_trademark_result_id'] ?? '',
                        'trademark_plan_id' => $this->data['trademark_plan_id'],
                    ]),
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageAnkenTop,
                    'is_action' => false,
                    'content' => '拒絶理由通知対応：必要資料提出',
                    'response_deadline' => $responseDeadline,
                ],
            ];
        } else {
            $admin = Admin::where('role', ROLE_MANAGER)->first();

            $noticeDetails = [
                // Send Notice Seki
                [
                    'target_id' => $admin->id,
                    'type_acc' => $this->typeManager,
                    'target_page' => route('user.refusal.response-plan.stop', [
                        'id' => $this->data['comparison_trademark_result_id'] ?? '',
                        'trademark_plan_id' => $this->data['trademark_plan_id'],
                    ]),
                    'redirect_page' => route('admin.refusal.material.no-material', [
                        'id' => $this->data['comparison_trademark_result_id'] ?? '',
                        'trademark_plan_id' => $this->data['trademark_plan_id'],
                    ]),
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageTop,
                    'is_action' => true,
                    'content' => '拒絶理由通知対応：対応方針案作成',
                    'response_deadline' => $responseDeadline,
                ],
                // Send Notice tantou
                [
                    'target_id' => $admin->id,
                    'type_acc' => $this->typeManager,
                    'target_page' => route('user.refusal.response-plan.stop', [
                        'id' => $this->data['comparison_trademark_result_id'] ?? '',
                        'trademark_plan_id' => $this->data['trademark_plan_id'],
                    ]),
                    'redirect_page' => route('admin.refusal.material.no-material', [
                        'id' => $this->data['comparison_trademark_result_id'] ?? '',
                        'trademark_plan_id' => $this->data['trademark_plan_id'],
                    ]),
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'is_action' => true,
                    'content' => '担当者　拒絶理由通知対応：対応方針案作成',
                    'response_deadline' => $responseDeadline,
                ],

                //Send notice user
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => route('user.refusal.response-plan.stop', [
                        'id' => $this->data['comparison_trademark_result_id'] ?? '',
                        'trademark_plan_id' => $this->data['trademark_plan_id'],
                    ]),
                    'type_notify' => $this->typeNotifyDefault,
                    'type_page' => $this->typePageTop,
                    'content' => '拒絶理由通知対応：方針案選択完了・決済待ち',
                ],
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => route('user.refusal.response-plan.stop', [
                        'id' => $this->data['comparison_trademark_result_id'] ?? '',
                        'trademark_plan_id' => $this->data['trademark_plan_id'],
                    ]),
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageAnkenTop,
                    'content' => '拒絶理由通知対応：方針案選択決済完了・決済待ち',
                ],
            ];
        }

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
                'is_answer' => NoticeDetail::IS_ANSWER,
            ]);
        });

        if ($planCorrespondence->type == PlanCorrespondence::TYPE_SELECTION) {
            $subDays = -30;
        } else {
            $subDays = -25;
        }
        $responseDeadline = $matchingResult->calculateResponseDeadline($subDays);

        $notice = [];
        $noticeDetails = [];

        if ($this->paymentType == Payment::CREDIT_CARD) {
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
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'type_notify' => $this->typeNotifyDefault,
                    'type_page' => $this->typePageTop,
                    'is_action' => false,
                    'content' => '拒絶理由通知対応：方針案選択完了・決済待ち',
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_1,
                ],
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'type_notify' => $this->typeNotifyDefault,
                    'type_page' => $this->typePageTop,
                    'content' => '拒絶理由通知対応：方針案選択決済完了',
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_2,
                ],
                // U-000anken_top
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageAnkenTop,
                    'content' => '拒絶理由通知対応：方針案選択完了・決済待ち',
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_1,
                ],
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageAnkenTop,
                    'content' => '拒絶理由通知対応：方針案選択決済完了',
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_2,
                ],
            ];
        } elseif ($this->paymentType == Payment::BANK_TRANSFER) {
            $notice = [
                'flow' => Notice::FLOW_RESPONSE_REASON,
                'trademark_id' => $trademark->id,
                'step' => Notice::STEP_3,
                'trademark_info_id' => null,
                'user_id' => $trademark->user_id,
            ];

            $noticeDetails = [
                // U-000top
                [
                    'target_id' => auth()->user()->id,
                    'type_acc' => NoticeDetail::TYPE_USER,
                    'target_page' => $this->targetPage,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_DEFAULT,
                    'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                    'content' => '拒絶理由通知対応：方針案選択完了・決済待ち',
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_1,
                ],
                [
                    'target_id' => auth()->user()->id,
                    'type_acc' => NoticeDetail::TYPE_USER,
                    'target_page' => $this->targetPage,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                    'content' => '拒絶理由通知対応：未入金です',
                ],
                // U-000anken_top
                [
                    'target_id' => auth()->user()->id,
                    'type_acc' => NoticeDetail::TYPE_USER,
                    'target_page' => $this->targetPage,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'content' => '拒絶理由通知対応：方針案選択完了・決済待ち',
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_1,
                ],
            ];
        }

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
    public function noticeU201Select02($comparisonTrademarkResult, $reasonNoId)
    {
        $trademark = $this->trademark;
        $trademark->load('machingResults');
        $matchingResult = $trademark->machingResults->last();

        $planCorrespondence = $comparisonTrademarkResult->planCorrespondence;
        if ($planCorrespondence->type == PlanCorrespondence::TYPE_SELECTION) {
            $responseDeadline = $matchingResult->calculateResponseDeadline(-11);
        } else {
            $responseDeadline = $matchingResult->calculateResponseDeadline(-7);
        }

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
            'step' => Notice::STEP_2
        ];

        $noticeDetails = [];
        if ($this->paymentType == Payment::CREDIT_CARD) {
            $noticeDetails = [
                // Send Notice Tantou
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
                    'content' => '拒絶理由通知対応：対応申し込み受領',
                    'attribute' => __('labels.plan.notice_detail.attribute_2'),
                    'response_deadline' => $responseDeadline,
                    'completion_date' => now(),
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
                    'attribute' => __('labels.plan.notice_detail.attribute_1'),
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
                    'attribute' => __('labels.plan.notice_detail.attribute_2'),
                    'response_deadline' => $responseDeadline,
                ],
                // Send Notice User
                [
                    'target_id' => $this->currentUser->id,
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
                    'target_id' => $this->currentUser->id,
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
        } elseif ($this->paymentType == Payment::BANK_TRANSFER) {
            $noticeDetails = [
                // Send Notice Tantou
                [
                    'target_id' => null,
                    'type_acc' => $this->typeManager,
                    'target_page' => $this->targetPage,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageAnkenTop,
                    'is_action' => false,
                    'content' => '拒絶理由通知対応：対応申し込み受領',
                    'attribute' => __('labels.plan.notice_detail.attribute_2'),
                    'response_deadline' => $responseDeadline,
                    'completion_date' => now(),
                ],
                // Send Notice User
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyDefault,
                    'type_page' => $this->typePageTop,
                    'is_action' => false,
                    'content' => '拒絶理由通知対応：対応申し込み完了',
                    'attribute' => null,
                    'response_deadline' => $responseDeadline,
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_0
                ],
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyDefault,
                    'type_page' => $this->typePageTop,
                    'is_action' => false,
                    'content' => '拒絶理由通知対応：対応申し込み完了・決済待ち',
                    'attribute' => null,
                    'response_deadline' => $responseDeadline,
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_1
                ],
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageTop,
                    'is_action' => false,
                    'content' => '拒絶理由通知対応：未入金です',
                    'attribute' => null,
                    'response_deadline' => $responseDeadline,
                ],
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyDefault,
                    'type_page' => $this->typePageTop,
                    'is_action' => false,
                    'content' => '拒絶理由通知対応：対応申し込み完了',
                    'attribute' => null,
                    'response_deadline' => $responseDeadline,
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_0
                ],
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyDefault,
                    'type_page' => $this->typePageTop,
                    'is_action' => false,
                    'content' => '拒絶理由通知対応：対応申し込み完了・決済待ち',
                    'response_deadline' => $responseDeadline,
                    'attribute' => null,
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_1
                ],
            ];
        }

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

        $notice = [
            'flow' => Notice::FLOW_SFT,
            'trademark_id' => $trademark->id,
            'user_id' => $trademark->user_id,
        ];
        switch ($this->fromPage) {
            case U021B_31:
            case U021B:
            case U031_EDIT_WITH_NUMBER:
                $noticeUpdate = $this->noticeDetailService->findByCondition([
                    'type_acc' => NoticeDetail::TYPE_USER,
                    'is_answer' => NoticeDetail::IS_NOT_ANSWER,
                ], ['notice'])->whereHas('notice', function ($query) {
                    $query->where('flow', Notice::FLOW_SFT)
                        ->orWhere('flow', Notice::FLOW_PRECHECK);
                })->get()
                    ->where('notice.trademark_id', $trademark->id)
                    ->where('notice.user_id', $trademark->user_id);
                $noticeUpdate->map(function ($item) {
                    $item->update([
                        'is_answer' => NoticeDetail::IS_ANSWER,
                    ]);
                });
                break;
        }

        $noticeDetails = [];
        if ($this->paymentType == Payment::CREDIT_CARD) {
            $noticeDetails = [
                // Send Notice Tantou
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
                    'content' => 'はじめからサポート：お申し込み受領',
                    'attribute' => __('labels.plan.notice_detail.attribute'),
                    'completion_date' => now(),
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
                    'attribute' => __('labels.plan.notice_detail.attribute_1'),
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
                    'attribute' => __('labels.plan.notice_detail.attribute_2'),
                ],
                // Send Notice User
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyDefault,
                    'type_page' => $this->typePageTop,
                    'is_action' => false,
                    'content' => 'はじめからサポート：お申し込み完了',
                    'attribute' => null,
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_0,
                ],
                [
                    'target_id' => $this->currentUser->id,
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
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageAnkenTop,
                    'is_action' => false,
                    'content' => 'はじめからサポート：お申し込み完了',
                    'attribute' => null,
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_0,
                ],
                [
                    'target_id' => $this->currentUser->id,
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
        } elseif ($this->paymentType == Payment::BANK_TRANSFER) {
            $noticeDetails = [
                // Send Notice Tantou
                [
                    'target_id' => null,
                    'type_acc' => $this->typeManager,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageAnkenTop,
                    'is_action' => false,
                    'content' => 'はじめからサポート：お申し込み受領',
                    'attribute' => __('labels.plan.notice_detail.attribute'),
                    'completion_date' => now(),
                ],
                // Send Notice User
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyDefault,
                    'type_page' => $this->typePageTop,
                    'is_action' => false,
                    'content' => 'はじめからサポート ：お申し込み完了',
                    'attribute' => null,
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_0,
                ],
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyDefault,
                    'type_page' => $this->typePageTop,
                    'is_action' => false,
                    'content' => 'はじめからサポート ：お申し込み完了・決済待ち',
                    'attribute' => null,
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_1,
                ],
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageTop,
                    'is_action' => false,
                    'content' => 'はじめからサポート：未入金です',
                    'attribute' => null,
                ],
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageAnkenTop,
                    'is_action' => false,
                    'content' => 'はじめからサポート ：お申し込み完了',
                    'attribute' => null,
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_0,
                ],
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageAnkenTop,
                    'is_action' => false,
                    'content' => 'はじめからサポート ：お申し込み完了・決済待ち',
                    'attribute' => null,
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_1,
                ],
            ];
        }

        $this->noticeService->sendNotice([
            'notices' => $notice,
            'notice_details' => $noticeDetails,
        ]);
    }

    /**
     * Send notice for payment with from_page U011b
     *
     * @return void
     */
    public function noticeU011b()
    {
        $trademark = $this->trademark
            ->load([
                'supportFirstTime',
                'appTrademark.trademarkInfo',
            ]);

        $appTrademark = $trademark->appTrademark;
        $trademarkInfos = $appTrademark->trademarkInfo;
        $lastTrademarkInfos = $trademarkInfos->last();

        $notice = [
            'flow' => Notice::FLOW_APP_TRADEMARK,
            'trademark_id' => $trademark->id,
            'user_id' => $this->currentUser->id,
            'trademark_info_id' => $lastTrademarkInfos->id ?? null,
        ];

        $noticeUpdate = $this->noticeDetailService->findByCondition([
            'type_acc' => $this->typeUser,
            'is_answer' => NoticeDetail::IS_NOT_ANSWER,
        ], ['notice'])->whereHas('notice', function ($query) {
            $query->where('flow', Notice::FLOW_SFT)
                ->orWhere('flow', Notice::FLOW_PRECHECK);
        })->get()
            ->where('notice.trademark_id', $trademark->id)
            ->where('notice.user_id', $trademark->user_id);
        $noticeUpdate->map(function ($item) {
            $item->update([
                'is_answer' => NoticeDetail::IS_ANSWER,
            ]);
        });
        if ($this->paymentType == Payment::CREDIT_CARD) {
            $noticeDetails = [
                // Send Notice Jimu
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
                    'content' => '出願：お申し込み受領',
                    'attribute' => __('labels.plan.notice_detail.attribute'),
                    'completion_date' => now(),
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
                    'attribute' => __('labels.plan.notice_detail.attribute_1'),
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
                    'attribute' => __('labels.plan.notice_detail.attribute_2'),
                ],
                // Send Notice User
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyDefault,
                    'type_page' => $this->typePageTop,
                    'is_action' => false,
                    'content' => '出願：お申し込み完了',
                    'attribute' => null,
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_0,
                ],
                [
                    'target_id' => $this->currentUser->id,
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
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageAnkenTop,
                    'is_action' => false,
                    'content' => '出願：お申し込み完了',
                    'attribute' => null,
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_0,
                ],
                [
                    'target_id' => $this->currentUser->id,
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
        } elseif ($this->paymentType == Payment::BANK_TRANSFER) {
            $this->noticeDetailService->findByCondition([
                'type_acc' => $this->typeUser,
                'is_answer' => NoticeDetail::IS_NOT_ANSWER,
            ], ['notice'])->whereHas('notice', function ($query) {
                $query->where('flow', Notice::FLOW_SFT)->orWhere('flow', Notice::FLOW_PRECHECK);
            })->get()->map(function ($item) {
                $item->update([
                    'is_answer' => NoticeDetail::IS_ANSWER,
                ]);
            });
            $noticeDetails = [
                // Send Notice Jimu
                [
                    'target_id' => null,
                    'type_acc' => $this->typeOfficeManager,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageAnkenTop,
                    'is_action' => false,
                    'content' => '出願：お申し込み受領',
                    'attribute' => __('labels.plan.notice_detail.attribute'),
                    'completion_date' => now(),
                ],
                // Send Notice User
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyDefault,
                    'type_page' => $this->typePageTop,
                    'is_action' => false,
                    'content' => '出願：お申し込み完了',
                    'attribute' => null,
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_0,
                ],
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyDefault,
                    'type_page' => $this->typePageTop,
                    'is_action' => false,
                    'content' => '出願：お申し込み完了・決済待ち',
                    'attribute' => null,
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_1,
                ],
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageTop,
                    'is_action' => false,
                    'content' => '出願：未入金です',
                    'attribute' => null,
                ],
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageAnkenTop,
                    'is_action' => false,
                    'content' => '出願：お申し込み完了',
                    'attribute' => null,
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_0,
                ],
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageAnkenTop,
                    'is_action' => false,
                    'content' => '出願：お申し込み完了・決済待ち',
                    'attribute' => null,
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_1,
                ],
            ];
        }

        $this->noticeService->sendNotice([
            'notices' => $notice,
            'notice_details' => $noticeDetails,
        ]);
    }

    /**
     * Send notice for payment with precheck simple
     *
     * @return void
     */
    public function noticePrecheckSimple()
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
            'flow' => Notice::FLOW_PRECHECK,
            'trademark_id' => $trademark->id,
            'user_id' => $this->currentUser->id,
        ];

        if ($this->paymentType == Payment::CREDIT_CARD) {
            $noticeDetails = [
                // Send Notice Tantou
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
                    'content' => 'プレチェックサービス：お申し込み受領',
                    'attribute' => __('labels.plan.notice_detail.attribute'),
                    'completion_date' => now(),
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
                    'attribute' => __('labels.plan.notice_detail.attribute_1'),
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
                    'attribute' => __('labels.plan.notice_detail.attribute_2'),
                ],
                // Send Notice User
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyDefault,
                    'type_page' => $this->typePageTop,
                    'is_action' => false,
                    'content' => 'プレチェックサービス：お申し込み完了',
                    'attribute' => null,
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_0,
                ],
                [
                    'target_id' => $this->currentUser->id,
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
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageAnkenTop,
                    'is_action' => false,
                    'content' => ' プレチェックサービス：お申し込み完了',
                    'attribute' => null,
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_0,
                ],
                [
                    'target_id' => $this->currentUser->id,
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
        } elseif ($this->paymentType == Payment::BANK_TRANSFER) {
            $noticeDetails = [
                // Send Notice Tantou
                [
                    'target_id' => null,
                    'type_acc' => $this->typeManager,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageAnkenTop,
                    'is_action' => false,
                    'content' => 'プレチェックサービス：お申し込み受領',
                    'attribute' => __('labels.plan.notice_detail.attribute'),
                    'completion_date' => now(),
                ],
                // Send Notice User
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyDefault,
                    'type_page' => $this->typePageTop,
                    'is_action' => false,
                    'content' => 'プレチェックサービス：お申し込み完了',
                    'attribute' => null,
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_0,
                ],
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyDefault,
                    'type_page' => $this->typePageTop,
                    'is_action' => false,
                    'content' => 'プレチェックサービス：お申し込み完了・決済待ち',
                    'attribute' => null,
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_1,
                ],
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageTop,
                    'is_action' => false,
                    'content' => 'プレチェックサービス：未入金です',
                    'attribute' => null,
                ],
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageAnkenTop,
                    'is_action' => false,
                    'content' => 'プレチェックサービス：お申し込み完了',
                    'attribute' => null,
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_0,
                ],
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageAnkenTop,
                    'is_action' => false,
                    'content' => 'プレチェックサービス：お申し込み完了・決済待ち',
                    'attribute' => null,
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_1,
                ],
            ];
        }

        $this->noticeService->sendNotice([
            'notices' => $notice,
            'notice_details' => $noticeDetails,
        ]);
    }

    /**
     * Send notice for payment with precheck select
     *
     * @return void
     */
    public function noticePrecheckSelect()
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
            'flow' => Notice::FLOW_PRECHECK,
            'trademark_id' => $trademark->id,
            'user_id' => $this->currentUser->id,
        ];

        if ($this->paymentType == Payment::CREDIT_CARD) {
            $noticeDetails = [
                // Send Notice Tantou
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
                    'content' => 'プレチェックサービス：お申し込み受領',
                    'attribute' => __('labels.plan.notice_detail.attribute'),
                    'completion_date' => now(),
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
                    'attribute' => __('labels.plan.notice_detail.attribute_1'),
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
                    'attribute' => __('labels.plan.notice_detail.attribute_2'),
                ],
                // Send Notice User
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyDefault,
                    'type_page' => $this->typePageTop,
                    'is_action' => false,
                    'content' => 'プレチェックサービス：お申し込み完了',
                    'attribute' => null,
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_0,
                ],
                [
                    'target_id' => $this->currentUser->id,
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
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageAnkenTop,
                    'is_action' => false,
                    'content' => ' プレチェックサービス：お申し込み完了',
                    'attribute' => null,
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_0,
                ],
                [
                    'target_id' => $this->currentUser->id,
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
        } elseif ($this->paymentType == Payment::BANK_TRANSFER) {
            $noticeDetails = [
                // Send Notice Tantou
                [
                    'target_id' => null,
                    'type_acc' => $this->typeManager,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageAnkenTop,
                    'is_action' => false,
                    'content' => 'プレチェックサービス：お申し込み受領',
                    'attribute' => __('labels.plan.notice_detail.attribute'),
                    'completion_date' => now(),
                ],
                // Send Notice User
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyDefault,
                    'type_page' => $this->typePageTop,
                    'is_action' => false,
                    'content' => 'プレチェックサービス：お申し込み完了',
                    'attribute' => null,
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_0,
                ],
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyDefault,
                    'type_page' => $this->typePageTop,
                    'is_action' => false,
                    'content' => 'プレチェックサービス：お申し込み完了・決済待ち',
                    'attribute' => null,
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_1,
                ],
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageTop,
                    'is_action' => false,
                    'content' => 'プレチェックサービス：未入金です',
                    'attribute' => null,
                ],
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageAnkenTop,
                    'is_action' => false,
                    'content' => 'プレチェックサービス：お申し込み完了',
                    'attribute' => null,
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_0,
                ],
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageAnkenTop,
                    'is_action' => false,
                    'content' => 'プレチェックサービス：お申し込み完了・決済待ち',
                    'attribute' => null,
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_1,
                ],
            ];
        }

        $this->noticeService->sendNotice([
            'notices' => $notice,
            'notice_details' => $noticeDetails,
        ]);
    }

    /**
     * Send notice for payment with change adress
     *
     * @return void
     */
    public function noticeChangeAddress()
    {
        $trademark = $this->trademark;
        $notice = [
            'flow' => Notice::FLOW_CHANGE_INFO,
            'trademark_id' => $trademark->id,
            'user_id' => $this->currentUser->id,
        ];

        if ($this->paymentType == Payment::CREDIT_CARD) {
            $noticeDetails = [
                // Send Notice Jimu
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
                    'content' => '更新手続き：お申し込み受領',
                    'attribute' => __('labels.plan.notice_detail.attribute'),
                    'completion_date' => now(),
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
                    'attribute' => __('labels.plan.notice_detail.attribute_1'),
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
                    'attribute' => __('labels.plan.notice_detail.attribute_2'),
                ],
                // Send Notice User
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyDefault,
                    'type_page' => $this->typePageTop,
                    'is_action' => false,
                    'content' => '更新手続き：お申し込み完了',
                    'attribute' => null,
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_0,
                ],
                [
                    'target_id' => $this->currentUser->id,
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
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageAnkenTop,
                    'is_action' => false,
                    'content' => '更新手続き：お申し込み完了',
                    'attribute' => null,
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_0,
                ],
                [
                    'target_id' => $this->currentUser->id,
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
        } elseif ($this->paymentType == Payment::BANK_TRANSFER) {
            $noticeDetails = [
                // Send Notice Jimu
                [
                    'target_id' => null,
                    'type_acc' => $this->typeOfficeManager,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageAnkenTop,
                    'is_action' => false,
                    'content' => '更新手続き：お申し込み受領',
                    'attribute' => __('labels.plan.notice_detail.attribute'),
                    'completion_date' => now(),
                ],
                // Send Notice User
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyDefault,
                    'type_page' => $this->typePageTop,
                    'is_action' => false,
                    'content' => '更新手続き：お申し込み完了',
                    'attribute' => null,
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_0,
                ],
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyDefault,
                    'type_page' => $this->typePageTop,
                    'is_action' => false,
                    'content' => '更新手続き：お申し込み完了・決済待ち',
                    'attribute' => null,
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_1,
                ],
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageTop,
                    'is_action' => false,
                    'content' => '更新手続き：未入金です',
                    'attribute' => null,
                ],
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageAnkenTop,
                    'is_action' => false,
                    'content' => '更新手続き：お申し込み完了',
                    'attribute' => null,
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_0,
                ],
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageAnkenTop,
                    'is_action' => false,
                    'content' => '更新手続き：お申し込み完了・決済待ち',
                    'attribute' => null,
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_1,
                ],
            ];
        }
        $this->noticeService->sendNotice([
            'notices' => $notice,
            'notice_details' => $noticeDetails,
        ]);
    }

    /**
     * Send notice for payment with change adress
     *
     * @return void
     */
    public function noticeChangeAddressKenrisha()
    {
        $trademark = $this->trademark;
        $notice = [
            'flow' => Notice::FLOW_CHANGE_INFO,
            'trademark_id' => $trademark->id,
            'user_id' => $this->currentUser->id,
        ];

        if ($this->paymentType == Payment::CREDIT_CARD) {
            $noticeDetails = [
                // Send Notice Jimu
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
                    'content' => '更新手続き：お申し込み受領',
                    'attribute' => __('labels.plan.notice_detail.attribute'),
                    'completion_date' => now(),
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
                    'attribute' => __('labels.plan.notice_detail.attribute_1'),
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
                // Send Notice User
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyDefault,
                    'type_page' => $this->typePageTop,
                    'is_action' => false,
                    'content' => '更新手続き：お申し込み完了',
                    'attribute' => null,
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_0,
                ],
                [
                    'target_id' => $this->currentUser->id,
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
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageAnkenTop,
                    'is_action' => false,
                    'content' => '更新手続き：お申し込み完了',
                    'attribute' => null,
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_0,
                ],
                [
                    'target_id' => $this->currentUser->id,
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
        } elseif ($this->paymentType == Payment::BANK_TRANSFER) {
            $noticeDetails = [
                // Send Notice Jimu
                [
                    'target_id' => null,
                    'type_acc' => $this->typeOfficeManager,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageAnkenTop,
                    'is_action' => false,
                    'content' => '更新手続き：お申し込み受領',
                    'attribute' => __('labels.plan.notice_detail.attribute'),
                    'completion_date' => now(),
                ],
                // Send Notice User
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyDefault,
                    'type_page' => $this->typePageTop,
                    'is_action' => false,
                    'content' => '更新手続き：お申し込み完了',
                    'attribute' => null,
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_0,
                ],
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyDefault,
                    'type_page' => $this->typePageTop,
                    'is_action' => false,
                    'content' => '更新手続き：お申し込み完了・決済待ち',
                    'attribute' => null,
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_1,
                ],
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageTop,
                    'is_action' => false,
                    'content' => '更新手続き：未入金です',
                    'attribute' => null,
                ],
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageAnkenTop,
                    'is_action' => false,
                    'content' => '更新手続き：お申し込み完了',
                    'attribute' => null,
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_0,
                ],
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageAnkenTop,
                    'is_action' => false,
                    'content' => '更新手続き：お申し込み完了・決済待ち',
                    'attribute' => null,
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_1,
                ],
            ];
        }
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
        $trademark = $this->trademark->load('machingResults');

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

        $matchingResult = $trademark->machingResults->last();
        $responseDeadline = $matchingResult->calculateResponseDeadline(-3);

        $notice = [
            'flow' => Notice::FLOW_RESPONSE_REASON,
            'trademark_id' => $trademark->id,
            'user_id' => $this->currentUser->id,
            'step' => Notice::STEP_1
        ];

        $noticeDetails = [];

        if ($this->paymentType == Payment::CREDIT_CARD) {
            $noticeDetails = [
                // Send Notice Tantou
                [
                    'type_acc' => $this->typeManager,
                    'target_page' => $this->targetPage,
                    'redirect_page' => $this->redirectPage,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageTop,
                    'is_action' => true,
                    'content' => __('labels.plan.notice_detail.content_notice_tantou_top'),
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
                    'content' => __('labels.plan.notice_detail.content_simple_1'),
                    'attribute' => __('labels.plan.notice_detail.attribute'),
                    'response_deadline' => $responseDeadline,
                    'completion_date' => now(),
                ],
                [
                    'type_acc' => $this->typeManager,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageAnkenTop,
                    'is_action' => false,
                    'content' => __('labels.plan.notice_detail.content_simple_2'),
                    'attribute' => __('labels.plan.notice_detail.attribute_1'),
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
                    'content' => __('labels.plan.notice_detail.content_notice_tantou_top'),
                    'attribute' => __('labels.plan.notice_detail.attribute_2'),
                    'response_deadline' => $responseDeadline,
                ],
                // Send Notice User
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyDefault,
                    'type_page' => $this->typePageTop,
                    'is_action' => false,
                    'content' => '拒絶理由通知対応：シンプルプラン申し込み完了・決済待ち',
                    'attribute' => null,
                    'response_deadline' => $responseDeadline,
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_1
                ],
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyDefault,
                    'type_page' => $this->typePageTop,
                    'is_action' => false,
                    'content' => __('labels.plan.notice_detail.content_simple_4'),
                    'attribute' => null,
                    'response_deadline' => $responseDeadline,
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_2
                ],
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageAnkenTop,
                    'is_action' => false,
                    'content' => '拒絶理由通知対応：シンプルプラン申し込み完了・決済待ち',
                    'attribute' => null,
                    'response_deadline' => $responseDeadline,
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_1
                ],
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageAnkenTop,
                    'is_action' => false,
                    'content' => __('labels.plan.notice_detail.content_simple_4'),
                    'attribute' => null,
                    'response_deadline' => $responseDeadline,
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_2
                ],
            ];
        } elseif ($this->paymentType == Payment::BANK_TRANSFER) {
            $noticeDetails = [
                // Send Notice Tantou
                [
                    'type_acc' => $this->typeManager,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageAnkenTop,
                    'is_action' => false,
                    'content' => __('labels.plan.notice_detail.content_simple_1'),
                    'attribute' => __('labels.plan.notice_detail.attribute'),
                    'response_deadline' => $responseDeadline,
                    'completion_date' => now(),
                ],
                // Send Notice User
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyDefault,
                    'type_page' => $this->typePageTop,
                    'is_action' => false,
                    'content' => __('labels.plan.notice_detail.content_simple_5'),
                    'attribute' => null,
                    'response_deadline' => $responseDeadline,
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_1
                ],
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageTop,
                    'is_action' => false,
                    'content' => '拒絶理由通知対応：未入金です',
                    'attribute' => null,
                    'response_deadline' => $responseDeadline,
                ],
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageAnkenTop,
                    'is_action' => false,
                    'content' => __('labels.plan.notice_detail.content_simple_5'),
                    'response_deadline' => $responseDeadline,
                    'attribute' => null,
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_1
                ],
            ];
        }

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

        $matchingResult = $trademark->machingResults->last();
        $responseDeadline = $matchingResult->calculateResponseDeadline(-3);

        $notice = [
            'flow' => Notice::FLOW_RESPONSE_REASON,
            'trademark_id' => $trademark->id,
            'user_id' => $this->currentUser->id,
            'step' => Notice::STEP_1
        ];

        $noticeDetails = [];

        if ($this->paymentType == Payment::CREDIT_CARD) {
            $noticeDetails = [
                // Send Notice Tantou
                [
                    'type_acc' => $this->typeManager,
                    'target_page' => $this->targetPage,
                    'redirect_page' => $this->redirectPage,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageTop,
                    'is_action' => true,
                    'content' => __('labels.plan.notice_detail.content_notice_tantou_top'),
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
                    'content' => '拒絶理由対応：セレクトプラン申し込み受領',
                    'attribute' => __('labels.plan.notice_detail.attribute'),
                    'response_deadline' => $responseDeadline,
                    'completion_date' => now(),
                ],
                [
                    'type_acc' => $this->typeManager,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageAnkenTop,
                    'is_action' => false,
                    'content' => '拒絶理由通知対応：セレクトプラン決済確認済',
                    'attribute' => __('labels.plan.notice_detail.attribute_1'),
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
                    'attribute' => __('labels.plan.notice_detail.attribute_2'),
                    'response_deadline' => $responseDeadline,
                ],
                // Send Notice User
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyDefault,
                    'type_page' => $this->typePageTop,
                    'is_action' => false,
                    'content' => '拒絶理由通知対応：セレクトプラン申し込み完了・決済待ち',
                    'attribute' => null,
                    'response_deadline' => $responseDeadline,
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_1
                ],
                [
                    'target_id' => $this->currentUser->id,
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
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageAnkenTop,
                    'is_action' => false,
                    'content' => '拒絶理由通知対応：セレクトプラン申し込み完了・決済待ち',
                    'attribute' => null,
                    'response_deadline' => $responseDeadline,
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_1
                ],
                [
                    'target_id' => $this->currentUser->id,
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
        } elseif ($this->paymentType == Payment::BANK_TRANSFER) {
            $noticeDetails = [
                // Send Notice Tantou
                [
                    'type_acc' => $this->typeManager,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageAnkenTop,
                    'is_action' => false,
                    'content' => '拒絶理由対応：セレクトプラン申し込み受領',
                    'attribute' => __('labels.plan.notice_detail.attribute'),
                    'response_deadline' => $responseDeadline,
                    'completion_date' => now(),
                ],
                // Send Notice User
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyDefault,
                    'type_page' => $this->typePageTop,
                    'is_action' => false,
                    'content' => __('labels.plan.notice_detail.content_notice_user_2'),
                    'attribute' => null,
                    'response_deadline' => $responseDeadline,
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_1
                ],
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageTop,
                    'is_action' => false,
                    'content' => __('labels.plan.notice_detail.content_select'),
                    'attribute' => null,
                    'response_deadline' => $responseDeadline,
                ],
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageAnkenTop,
                    'is_action' => false,
                    'content' => __('labels.plan.notice_detail.content_notice_user_2'),
                    'response_deadline' => $responseDeadline,
                    'attribute' => null,
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_1
                ],
            ];
        }

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
        $trademark->load('comparisonTrademarkResult');

        // Update Notice at a201b_02 (No 37: H I)
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

        $matchingResult = $trademark->machingResults->last();
        $responseDeadline = $matchingResult->calculateResponseDeadline(-9);

        $notice = [
            'flow' => Notice::FLOW_RESPONSE_REASON,
            'trademark_id' => $trademark->id,
            'user_id' => $this->currentUser->id,
            'step' => Notice::STEP_1
        ];

        $noticeDetails = [];

        if ($this->paymentType == Payment::CREDIT_CARD) {
            $noticeDetails = [
                // Send Notice Tantou
                [
                    'type_acc' => $this->typeManager,
                    'target_page' => $this->targetPage,
                    'redirect_page' => $this->redirectPage,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageTop,
                    'is_action' => true,
                    'content' => __('labels.plan.notice_detail.content_notice_tantou_top'),
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
                    'content' => '拒絶理由対応：セレクトプラン申し込み受領',
                    'attribute' => __('labels.plan.notice_detail.attribute'),
                    'response_deadline' => $responseDeadline,
                    'completion_date' => now(),
                ],
                [
                    'type_acc' => $this->typeManager,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageAnkenTop,
                    'is_action' => false,
                    'content' => '拒絶理由通知対応：セレクトプラン決済確認済',
                    'attribute' => __('labels.plan.notice_detail.attribute_1'),
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
                    'attribute' => __('labels.plan.notice_detail.attribute_2'),
                    'response_deadline' => $responseDeadline,
                ],
                // Send Notice User
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyDefault,
                    'type_page' => $this->typePageTop,
                    'is_action' => false,
                    'content' => '拒絶理由通知対応：セレクトプラン申し込み完了・決済待ち',
                    'attribute' => null,
                    'response_deadline' => $responseDeadline,
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_1
                ],
                [
                    'target_id' => $this->currentUser->id,
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
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageAnkenTop,
                    'is_action' => false,
                    'content' => '拒絶理由通知対応：セレクトプラン申し込み完了・決済待ち',
                    'attribute' => null,
                    'response_deadline' => $responseDeadline,
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_1
                ],
                [
                    'target_id' => $this->currentUser->id,
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
        } elseif ($this->paymentType == Payment::BANK_TRANSFER) {
            $noticeDetails = [
                // Send Notice Tantou
                [
                    'type_acc' => $this->typeManager,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageAnkenTop,
                    'is_action' => false,
                    'content' => '拒絶理由対応：セレクトプラン申し込み受領',
                    'attribute' => __('labels.plan.notice_detail.attribute'),
                    'response_deadline' => $responseDeadline,
                    'completion_date' => now(),
                ],
                // Send Notice User
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyDefault,
                    'type_page' => $this->typePageTop,
                    'is_action' => false,
                    'content' => '拒絶理由通知対応：セレクトプラン申し込み完了・決済待ち',
                    'attribute' => null,
                    'response_deadline' => $responseDeadline,
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_1
                ],
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageTop,
                    'is_action' => false,
                    'content' => '拒絶理由通知対応：未入金です',
                    'attribute' => null,
                    'response_deadline' => $responseDeadline,
                ],
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageAnkenTop,
                    'is_action' => false,
                    'content' => '拒絶理由通知対応：セレクトプラン申し込み完了・決済待ち',
                    'response_deadline' => $responseDeadline,
                    'attribute' => null,
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_1
                ],
            ];
        }

        $this->noticeService->sendNotice([
            'notices' => $notice,
            'notice_details' => $noticeDetails,
        ]);
    }

    /**
     * Notice U210 Over 02
     *
     * @return void
     */
    public function noticeU210Alert02()
    {
        $trademark = $this->trademark;

        // Update Notice at  (No 21: H I)
        $stepBeforeNotice = $this->noticeDetailService->findByCondition([
            'is_answer' => NoticeDetail::IS_NOT_ANSWER,
            'type_acc' => NoticeDetail::TYPE_USER,
        ])->with('notice')->whereHas('notice', function ($query) {
            $query->where('flow', Notice::FLOW_RENEWAL_BEFORE_DEADLINE)
                  ->orWhere('flow', Notice::FLOW_RESPONSE_REASON);
        })->get()
            ->where('notice.trademark_id', $trademark->id)
            ->where('notice.user_id', $trademark->user_id);
        $stepBeforeNotice->map(function ($item) {
            $item->update([
                'is_answer' => NoticeDetail::IS_ANSWER,
            ]);
        });
        $trademark->load('comparisonTrademarkResult');
        $comparisonTrademarkResult = $trademark->comparisonTrademarkResult;
        $notice = [
            'trademark_id' => $trademark->id,
            'user_id' => $this->currentUser->id,
            'flow' => Notice::FLOW_RENEWAL_BEFORE_DEADLINE,
        ];

        $noticeDetails = [];

        $responseDeadline = new Carbon($comparisonTrademarkResult->response_deadline);
        $responseDeadline->addDays(-4);
        if ($this->paymentType == Payment::CREDIT_CARD) {
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
                    'is_action' => true,
                    'content' => '事務担当　期限日前期間延長決済確認済み',
                    'attribute' => 'お客様から',
                    'response_deadline' => $responseDeadline,
                    'completion_date' => now()
                ],
                [
                    'type_acc' => $this->typeOfficeManager,
                    'target_page' => $this->targetPage,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageAnkenTop,
                    'is_action' => false,
                    'content' => '期限日前期間延長決済確認済',
                    'attribute' => 'お客様へ',
                    'response_deadline' => $responseDeadline,
                    'completion_date' => now()
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
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'type_notify' => $this->typeNotifyDefault,
                    'type_page' => $this->typePageTop,
                    'is_action' => false,
                    'content' => '期限日前期間延長：お申込み完了・決済待ち',
                    'completion_date' => now(),
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_1
                ],
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'type_notify' => $this->typeNotifyDefault,
                    'type_page' => $this->typePageTop,
                    'content' => '期限日前期間延長：決済完了・返信待ち',
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_2,
                ],
                // U-000anken_top
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageAnkenTop,
                    'content' => '期限日前期間延長：お申込み完了・決済待ち',
                    'completion_date' => now(),
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_1
                ],
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageAnkenTop,
                    'content' => '期限日前期間延長：決済完了・返信待ち',
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_2,
                ],
            ];
        } elseif ($this->paymentType == Payment::BANK_TRANSFER) {
            $noticeDetails = [
                // A-000anken_top
                [
                    'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                    'target_page' => $this->targetPage,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'is_action' => NoticeDetail::IS_ACTION_TRUE,
                    'content' => '事務担当　期限日前期間延長決済確認済み',
                    'attribute' => 'お客様から',
                    'response_deadline' => $responseDeadline,
                    'completion_date' => now(),
                ],
                // U-000top
                [
                    'target_id' => auth()->user()->id,
                    'type_acc' => NoticeDetail::TYPE_USER,
                    'target_page' => $this->targetPage,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_DEFAULT,
                    'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                    'content' => '期限日前期間延長：お申し込み完了・決済待ち',
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_1,
                ],
                [
                    'target_id' => auth()->user()->id,
                    'type_acc' => NoticeDetail::TYPE_USER,
                    'target_page' => $this->targetPage,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                    'content' => '期限日前期間延長：未入金です。',
                ],
                // U-000anken_top
                [
                    'target_id' => auth()->user()->id,
                    'type_acc' => NoticeDetail::TYPE_USER,
                    'target_page' => $this->targetPage,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'content' => '期限日前期間延長：お申し込み完了・決済待ち',
                    'completion_date' => now(),
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_1
                ],
            ];
        }

        $this->noticeService->sendNotice([
            'notices' => $notice,
            'notice_details' => $noticeDetails,
        ]);
    }

    /**
     * Notice U210 Alert 02
     *
     * @return void
     */
    public function noticeU210Over02()
    {
        $trademark = $this->trademark;

        // Update Notice
        $stepBeforeNotice = $this->noticeDetailService->findByCondition([
            'type_acc' => NoticeDetail::TYPE_USER,
            'is_answer' => NoticeDetail::IS_NOT_ANSWER,
        ])->with('notice')->whereHas('notice', function ($query) {
            $query->where('flow', Notice::FLOW_RENEWAL_BEFORE_DEADLINE)
                ->orWhere('flow', Notice::FLOW_RESPONSE_REASON);
        })->get()
            ->where('notice.trademark_id', $trademark->id)
            ->where('notice.user_id', $trademark->user_id);
        $stepBeforeNotice->map(function ($item) {
            $item->update([
                'is_answer' => NoticeDetail::IS_ANSWER,
            ]);
        });

        $trademark->load('comparisonTrademarkResult');
        $comparisonTrademarkResult = $trademark->comparisonTrademarkResult;
        $notice = [
            'trademark_id' => $trademark->id,
            'user_id' => $this->currentUser->id,
            'flow' => Notice::FLOW_RENEWAL_BEFORE_DEADLINE,
        ];

        $noticeDetails = [];

        $responseDeadline = $comparisonTrademarkResult->response_deadline;

        if ($this->paymentType == Payment::CREDIT_CARD) {
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
                    'is_action' => true,
                    'content' => '事務担当　期間外延長の申込受領',
                    'attribute' => 'お客様から',
                    'completion_date' => now(),
                ],
                [
                    'type_acc' => $this->typeOfficeManager,
                    'target_page' => $this->targetPage,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageAnkenTop,
                    'is_action' => false,
                    'content' => '事務担当　期間外延長決済確認済',
                    'attribute' => 'お客様へ',
                    'completion_date' => now(),
                ],
                [
                    'type_acc' => $this->typeOfficeManager,
                    'target_page' => $this->targetPage,
                    'redirect_page' => null,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageAnkenTop,
                    'is_action' => true,
                    'content' => '事務担当　期間外延長請求書作成',
                    'attribute' => '所内処理',
                    'response_deadline' => $responseDeadline,
                ],
                // U-000top
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'type_notify' => $this->typeNotifyDefault,
                    'type_page' => $this->typePageTop,
                    'is_action' => false,
                    'content' => '期間外延長：お申込み完了',
                    'completion_date' => now(),
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_1
                ],
                [
                    'target_id' => $this->currentUser->id,
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
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageAnkenTop,
                    'content' => '期間外延長：お申込み完了',
                    'completion_date' => now(),
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_1
                ],
                [
                    'target_id' => $this->currentUser->id,
                    'type_acc' => $this->typeUser,
                    'target_page' => $this->targetPage,
                    'type_notify' => $this->typeNotifyTodo,
                    'type_page' => $this->typePageAnkenTop,
                    'content' => '期間外延長：決済完了・返信待ち',
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_2,
                ],
            ];
        } elseif ($this->paymentType == Payment::BANK_TRANSFER) {
            $noticeDetails = [
                // A-000anken_top
                [
                    'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                    'target_page' => $this->targetPage,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'is_action' => NoticeDetail::IS_ACTION_TRUE,
                    'content' => '事務担当　期間外延長の申込受領',
                    'attribute' => 'お客様から',
                    'response_deadline' => $responseDeadline,
                    'completion_date' => now(),
                ],
                // U-000top
                [
                    'target_id' => auth()->user()->id,
                    'type_acc' => NoticeDetail::TYPE_USER,
                    'target_page' => $this->targetPage,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_DEFAULT,
                    'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                    'content' => '期限日前期間延長：お申し込み完了・決済待ち',
                    'completion_date' => now(),
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_1
                ],
                [
                    'target_id' => auth()->user()->id,
                    'type_acc' => NoticeDetail::TYPE_USER,
                    'target_page' => $this->targetPage,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                    'content' => '期限日前期間延長：未入金です。',
                ],
                // U-000anken_top
                [
                    'target_id' => auth()->user()->id,
                    'type_acc' => NoticeDetail::TYPE_USER,
                    'target_page' => $this->targetPage,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'content' => '期限日前期間延長：お申し込み完了・決済待ち',
                    'payment_id' => $this->payment->id,
                    'payment_status' => PAYMENT_STATUS_1,
                ],
            ];
        }

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
        $data = $this->data;

        $newRegisterTrademarkId = $payment->target_id;

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
            'user_id' => $this->currentUser->id,
            'flow' => Notice::FLOW_RENEWAL,
        ];

        $noticeDetails = [];

        $typeChange = $data['type_change'] ?? null;
        if ($this->paymentType == Payment::CREDIT_CARD) {
            if (empty($typeChange)) {
                $this->redirectPage = route('admin.update.change_address.skip', [
                    'id' => $newRegisterTrademarkId,
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
                        'content' => '後期納付手続き：申し込み受領',
                        'attribute' => 'お客様から',
                        'payment_id' => null,
                        'payment_status' => null,
                        'completion_date' => now(),
                    ],
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
                        'content' => '後期納付手続き：お申し込み完了',
                        'attribute' => null,
                        'payment_id' => $payment->id,
                        'payment_status' => PAYMENT_STATUS_0,
                    ],
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
                        'content' => '後期納付手続き：お申し込み完了',
                        'attribute' => null,
                        'payment_id' => $payment->id,
                        'payment_status' => PAYMENT_STATUS_0,
                    ],
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
                    'register_trademark_id' => $newRegisterTrademarkId,
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
                        'content' => '後期納付手続き：登録名義人住所／名変更申し込み受領',
                        'attribute' => 'お客様から',
                        'payment_id' => null,
                        'payment_status' => null,
                        'completion_date' => now(),
                    ],
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
                        'content' => '後期納付手続き：登録名義人住所／名変更お申込み完了',
                        'attribute' => null,
                        'payment_id' => $payment->id,
                        'payment_status' => PAYMENT_STATUS_0,
                    ],
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
                        'content' => '後期納付手続き：登録名義人住所／名変更お申込み完了',
                        'attribute' => null,
                        'payment_id' => $payment->id,
                        'payment_status' => PAYMENT_STATUS_0,
                    ],
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
            }
        } elseif ($this->paymentType == Payment::BANK_TRANSFER) {
            if (empty($typeChange)) {
                $noticeDetails = [
                    // A-000top
                    // A-000anken_top
                    [
                        'target_id' => null,
                        'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                        'target_page' => $this->targetPage,
                        'redirect_page' => null,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                        'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                        'content' => '後期納付手続き：申し込み受領',
                        'attribute' => 'お客様から',
                        'payment_id' => null,
                        'payment_status' => null,
                        'completion_date' => now(),
                    ],
                    // U-000top
                    [
                        'target_id' => $trademark->user_id,
                        'type_acc' => NoticeDetail::TYPE_USER,
                        'target_page' => $this->targetPage,
                        'redirect_page' => null,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_DEFAULT,
                        'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                        'content' => '後期納付手続き：お申し込み完了',
                        'attribute' => null,
                        'payment_id' => $payment->id,
                        'payment_status' => PAYMENT_STATUS_0,
                    ],
                    [
                        'target_id' => $trademark->user_id,
                        'type_acc' => NoticeDetail::TYPE_USER,
                        'target_page' => $this->targetPage,
                        'redirect_page' => null,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_DEFAULT,
                        'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                        'content' => '後期納付手続き：お申し込み完了・決済待ち',
                        'attribute' => null,
                        'payment_id' => $payment->id,
                        'payment_status' => PAYMENT_STATUS_1,
                    ],
                    [
                        'target_id' => $trademark->user_id,
                        'type_acc' => NoticeDetail::TYPE_USER,
                        'target_page' => $this->targetPage,
                        'redirect_page' => null,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                        'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                        'content' => '後期納付手続き：未入金です。',
                        'attribute' => null,
                        'payment_id' => null,
                        'payment_status' => null,
                    ],
                    // U-000anken_top
                    [
                        'target_id' => $trademark->user_id,
                        'type_acc' => NoticeDetail::TYPE_USER,
                        'target_page' => $this->targetPage,
                        'redirect_page' => null,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_DEFAULT,
                        'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                        'content' => '後期納付手続き：お申し込み完了',
                        'attribute' => null,
                        'payment_id' => $payment->id,
                        'payment_status' => PAYMENT_STATUS_0,
                    ],
                    [
                        'target_id' => $trademark->user_id,
                        'type_acc' => NoticeDetail::TYPE_USER,
                        'target_page' => $this->targetPage,
                        'redirect_page' => null,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_DEFAULT,
                        'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                        'content' => '後期納付手続き：お申し込み完了・決済待ち',
                        'attribute' => null,
                        'payment_id' => $payment->id,
                        'payment_status' => PAYMENT_STATUS_1,
                    ],
                ];
            } else {
                $noticeDetails = [
                    // A-000top
                    // A-000anken_top
                    [
                        'target_id' => null,
                        'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                        'target_page' => $this->targetPage,
                        'redirect_page' => null,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                        'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                        'content' => '後期納付手続き：登録名義人住所／名変更申し込み受領',
                        'attribute' => 'お客様から',
                        'payment_id' => null,
                        'payment_status' => null,
                        'completion_date' => now(),
                    ],
                    // U-000top
                    [
                        'target_id' => $trademark->user_id,
                        'type_acc' => NoticeDetail::TYPE_USER,
                        'target_page' => $this->targetPage,
                        'redirect_page' => null,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_DEFAULT,
                        'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                        'content' => '後期納付手続き：登録名義人住所／名変更お申込み完了',
                        'attribute' => null,
                        'payment_id' => $payment->id,
                        'payment_status' => PAYMENT_STATUS_0,
                    ],
                    [
                        'target_id' => $trademark->user_id,
                        'type_acc' => NoticeDetail::TYPE_USER,
                        'target_page' => $this->targetPage,
                        'redirect_page' => null,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_DEFAULT,
                        'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                        'content' => '後期納付手続き：登録名義人住所／名変更お申込み完了・決済待ち',
                        'attribute' => null,
                        'payment_id' => $payment->id,
                        'payment_status' => PAYMENT_STATUS_1,
                    ],
                    [
                        'target_id' => $trademark->user_id,
                        'type_acc' => NoticeDetail::TYPE_USER,
                        'target_page' => $this->targetPage,
                        'redirect_page' => null,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                        'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                        'content' => '後期納付手続き：未入金です。',
                        'attribute' => null,
                        'payment_id' => null,
                        'payment_status' => null,
                    ],
                    // U-000anken_top
                    [
                        'target_id' => $trademark->user_id,
                        'type_acc' => NoticeDetail::TYPE_USER,
                        'target_page' => $this->targetPage,
                        'redirect_page' => null,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_DEFAULT,
                        'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                        'content' => '後期納付手続き：登録名義人住所／名変更お申込み完了',
                        'attribute' => null,
                        'payment_id' => $payment->id,
                        'payment_status' => PAYMENT_STATUS_0,
                    ],
                    [
                        'target_id' => $trademark->user_id,
                        'type_acc' => NoticeDetail::TYPE_USER,
                        'target_page' => $this->targetPage,
                        'redirect_page' => null,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_DEFAULT,
                        'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                        'content' => '後期納付手続き：登録名義人住所／名変更お申込み完了・決済待ち',
                        'attribute' => null,
                        'payment_id' => $payment->id,
                        'payment_status' => PAYMENT_STATUS_1,
                    ],
                ];
            }
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

        $newRegisterTrademarkId = $payment->target_id;

        $registerTrademark = RegisterTrademark::where('id', $newRegisterTrademarkId)->first();
        $hasChangeData = false;
        if (!empty($registerTrademark->id_register_trademark_choice)) {
            $hasChangeData = true;
        }

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
            'user_id' => $this->currentUser->id,
            'flow' => Notice::FLOW_RENEWAL,
        ];

        $noticeDetails = [];

        if ($this->paymentType == Payment::CREDIT_CARD) {
            if ($hasChangeData) {
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
                        'content' => '更新手続き：登録名義人住所／名変更申し込み受領',
                        'attribute' => 'お客様から',
                        'payment_id' => null,
                        'payment_status' => null,
                        'completion_date' => now(),
                    ],
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
                        'content' => '更新手続き：登録名義人住所／名変更お申込み完了',
                        'attribute' => null,
                        'payment_id' => $payment->id,
                        'payment_status' => PAYMENT_STATUS_0,
                    ],
                    [
                        'target_id' => $trademark->user_id,
                        'type_acc' => NoticeDetail::TYPE_USER,
                        'target_page' => $this->targetPage,
                        'redirect_page' => null,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_DEFAULT,
                        'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                        'content' => '更新手続き：登録名義人住所／名変更お申込み完了・報告待ち',
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
                        'content' => '更新手続き：登録名義人住所／名変更お申込み完了',
                        'attribute' => null,
                        'payment_id' => $payment->id,
                        'payment_status' => PAYMENT_STATUS_0,
                    ],
                    [
                        'target_id' => $trademark->user_id,
                        'type_acc' => NoticeDetail::TYPE_USER,
                        'target_page' => $this->targetPage,
                        'redirect_page' => null,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                        'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                        'content' => '更新手続き：登録名義人住所／名変更お申込み完了・報告待ち',
                        'attribute' => null,
                        'payment_id' => $payment->id,
                        'payment_status' => PAYMENT_STATUS_2,
                    ],
                ];
            } else {
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
                        'content' => '更新手続き：申し込み受領',
                        'attribute' => 'お客様から',
                        'payment_id' => null,
                        'payment_status' => null,
                        'completion_date' => now(),
                    ],
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
                        'content' => '更新手続き：お申し込み完了',
                        'attribute' => null,
                        'payment_id' => $payment->id,
                        'payment_status' => PAYMENT_STATUS_0,
                    ],
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
                        'content' => '更新手続き：お申し込み完了',
                        'attribute' => null,
                        'payment_id' => $payment->id,
                        'payment_status' => PAYMENT_STATUS_0,
                    ],
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
            }
        } elseif ($this->paymentType == Payment::BANK_TRANSFER) {
            if ($hasChangeData) {
                $noticeDetails = [
                    // A-000top
                    // A-000anken_top
                    [
                        'target_id' => null,
                        'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                        'target_page' => $this->targetPage,
                        'redirect_page' => null,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                        'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                        'content' => '更新手続き：登録名義人住所／名変更申し込み受領',
                        'attribute' => 'お客様から',
                        'payment_id' => null,
                        'payment_status' => null,
                        'completion_date' => now(),
                    ],
                    // U-000top
                    [
                        'target_id' => $trademark->user_id,
                        'type_acc' => NoticeDetail::TYPE_USER,
                        'target_page' => $this->targetPage,
                        'redirect_page' => null,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_DEFAULT,
                        'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                        'content' => '更新手続き：登録名義人住所／名変更お申込み完了',
                        'attribute' => null,
                        'payment_id' => $payment->id,
                        'payment_status' => PAYMENT_STATUS_0,
                    ],
                    [
                        'target_id' => $trademark->user_id,
                        'type_acc' => NoticeDetail::TYPE_USER,
                        'target_page' => $this->targetPage,
                        'redirect_page' => null,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_DEFAULT,
                        'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                        'content' => '更新手続き：登録名義人住所／名変更お申込み完了・決済待ち',
                        'attribute' => null,
                        'payment_id' => $payment->id,
                        'payment_status' => PAYMENT_STATUS_1,
                    ],
                    [
                        'target_id' => $trademark->user_id,
                        'type_acc' => NoticeDetail::TYPE_USER,
                        'target_page' => $this->targetPage,
                        'redirect_page' => null,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                        'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                        'content' => '更新手続き：未入金です。',
                        'attribute' => null,
                        'payment_id' => null,
                        'payment_status' => null,
                    ],
                    // U-000anken_top
                    [
                        'target_id' => $trademark->user_id,
                        'type_acc' => NoticeDetail::TYPE_USER,
                        'target_page' => $this->targetPage,
                        'redirect_page' => null,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                        'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                        'content' => '更新手続き：登録名義人住所／名変更お申込み完了',
                        'attribute' => null,
                        'payment_id' => $payment->id,
                        'payment_status' => PAYMENT_STATUS_0,
                    ],
                    [
                        'target_id' => $trademark->user_id,
                        'type_acc' => NoticeDetail::TYPE_USER,
                        'target_page' => $this->targetPage,
                        'redirect_page' => null,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                        'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                        'content' => '更新手続き：登録名義人住所／名変更お申込み完了・決済待ち',
                        'attribute' => null,
                        'payment_id' => $payment->id,
                        'payment_status' => PAYMENT_STATUS_1,
                    ],
                ];
            } else {
                $noticeDetails = [
                    // A-000top
                    // A-000anken_top
                    [
                        'target_id' => null,
                        'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                        'target_page' => $this->targetPage,
                        'redirect_page' => null,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                        'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                        'content' => '更新手続き：申し込み受領',
                        'attribute' => 'お客様から',
                        'payment_id' => null,
                        'payment_status' => null,
                        'completion_date' => now(),
                    ],
                    // U-000top
                    [
                        'target_id' => $trademark->user_id,
                        'type_acc' => NoticeDetail::TYPE_USER,
                        'target_page' => $this->targetPage,
                        'redirect_page' => null,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_DEFAULT,
                        'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                        'content' => '更新手続き：お申し込み完了',
                        'attribute' => null,
                        'payment_id' => $payment->id,
                        'payment_status' => PAYMENT_STATUS_0,
                    ],
                    [
                        'target_id' => $trademark->user_id,
                        'type_acc' => NoticeDetail::TYPE_USER,
                        'target_page' => $this->targetPage,
                        'redirect_page' => null,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_DEFAULT,
                        'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                        'content' => '更新手続き：お申し込み完了・決済待ち',
                        'attribute' => null,
                        'payment_id' => $payment->id,
                        'payment_status' => PAYMENT_STATUS_1,
                    ],
                    [
                        'target_id' => $trademark->user_id,
                        'type_acc' => NoticeDetail::TYPE_USER,
                        'target_page' => $this->targetPage,
                        'redirect_page' => null,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                        'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                        'content' => '更新手続き：未入金です。',
                        'attribute' => null,
                        'payment_id' => null,
                        'payment_status' => null,
                    ],
                    // U-000anken_top
                    [
                        'target_id' => $trademark->user_id,
                        'type_acc' => NoticeDetail::TYPE_USER,
                        'target_page' => $this->targetPage,
                        'redirect_page' => null,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                        'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                        'content' => '更新手続き：お申し込み完了',
                        'attribute' => null,
                        'payment_id' => $payment->id,
                        'payment_status' => PAYMENT_STATUS_0,
                    ],
                    [
                        'target_id' => $trademark->user_id,
                        'type_acc' => NoticeDetail::TYPE_USER,
                        'target_page' => $this->targetPage,
                        'redirect_page' => null,
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                        'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                        'content' => '更新手続き：お申し込み完了・決済待ち',
                        'attribute' => null,
                        'payment_id' => $payment->id,
                        'payment_status' => PAYMENT_STATUS_1,
                    ],
                ];
            }
        }

        $this->noticeService->sendNotice([
            'notices' => $notice,
            'notice_details' => $noticeDetails,
        ]);
    }
}
