<?php

namespace App\Notices;

use App\Models\Notice;
use App\Models\NoticeDetail;
use App\Models\PlanCorrespondence;
use App\Services\Common\NoticeService;
use App\Services\NoticeDetailService;

class CommonNotice extends BaseNotice
{
    private NoticeDetailService $noticeDetailService;
    private NoticeService $noticeService;

    /**
     * Constructor
     *
     * @return  void
     */
    public function __construct(
        NoticeDetailService $noticeDetailService,
        NoticeService $noticeService
    )
    {
        parent::__construct();

        $this->noticeDetailService = $noticeDetailService;
        $this->noticeService = $noticeService;
    }

    /**
     * Notice a302hosei02 & a302hosei02skip
     *
     * @return void
     */
    public function noticeA302hosei02Group($machingResult, $params)
    {
        $trademark = $machingResult->trademark;
        $registerTrademark = $trademark->registerTrademark;

        $fromPage = $params['from_page'] ?? null;

        $targetPage = null;
        if ($fromPage == A302_HOSEI02_SKIP) {
            $targetPage = route('admin.registration.document.modification.skip', [
                'id' => $machingResult->id,
                'register_trademark_id' => $registerTrademark->id,
            ]);
        } elseif ($fromPage == A302_HOSEI02) {
            $targetPage = route('admin.registration.document.modification.product', [
                'id' => $machingResult->id,
                'register_trademark_id' => $registerTrademark->id,
            ]);
        }

        $redirectPage = route('admin.registration.document', [
            'id' => $machingResult->id,
            'register_trademark_id' => $registerTrademark->id,
        ]);

        // Update Old Notice
        $stepBeforeNotice = $this->noticeDetailService->findByCondition([
            'raw' => 'type_acc <> ' . NoticeDetail::TYPE_USER,
            'completion_date' => null,
        ])->with('notice')->get()
            ->where('notice.trademark_id', $trademark->id)
            ->where('notice.user_id', $trademark->user_id)
            ->whereIn('notice.flow', [Notice::FLOW_REGISTER_TRADEMARK]);
        $stepBeforeNotice->map(function ($item) {
            $item->update([
                'completion_date' => now(),
            ]);
        });

        $notice = [
            'trademark_id' => $trademark->id,
            'user_id' => $trademark->user_id,
            'flow' => Notice::FLOW_REGISTER_TRADEMARK,
        ];

        $noticeDetails = [
            // A-000top
            [
                'target_id' => null,
                'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                'target_page' => $targetPage,
                'redirect_page' => $redirectPage,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                'is_action' => true,
                'content' => '事務担当　登録査定：【商標登録料納付書 】作成',
                'attribute' => null,
                'payment_id' => null,
                'payment_status' => null,
            ],
            // A-000anken_top
            [
                'target_id' => null,
                'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                'target_page' => $targetPage,
                'redirect_page' => $redirectPage,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                'is_action' => false,
                'content' => '事務担当　登録査定：【商標登録料納付書 】作成',
                'attribute' => '特許庁へ',
                'payment_id' => null,
                'payment_status' => null,
            ],
        ];

        $this->noticeService->sendNotice([
            'notices' => $notice,
            'notice_details' => $noticeDetails,
        ]);
    }

    /**
     * Notice a203s to a203sashi
     *
     * @return void
     */
    public function noticeA203StoA203Sashi($comparisonTrademarkResult, $trademarkPlan)
    {
        $comparisonTrademarkResult = $comparisonTrademarkResult->load([
            'trademark',
            'machingResult',
            'planCorrespondence',
        ]);

        $trademark = $comparisonTrademarkResult->trademark;
        $planCorrespondence = $comparisonTrademarkResult->planCorrespondence;
        $machingResult = $comparisonTrademarkResult->machingResult;

        // Update old notice
        $stepBeforeNotice = $this->noticeDetailService->findByCondition([
            'raw' => 'type_acc <> ' . NoticeDetail::TYPE_USER,
            'completion_date' => null,
        ])->with('notice')->get()
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
                'completion_date' => now(),
            ]);
        });

        // Create new notice
        $routerA203s = route('admin.refusal.response-plan.supervisor', [
            'id' => $comparisonTrademarkResult->id,
            'trademark_plan_id' => $trademarkPlan->id,
        ]);
        $routerA203sashi = route('admin.refusal.response-plan.supervisor-reject', [
            'id' => $comparisonTrademarkResult->id,
            'trademark_plan_id' => $trademarkPlan->id,
        ]);

        $responseDeadline = $machingResult->calculateResponseDeadline(-18);
        if (!empty($machingResult) && $planCorrespondence->type == PlanCorrespondence::TYPE_2) {
            $responseDeadline = $machingResult->calculateResponseDeadline(-24);
        }

        $this->noticeService->sendNotice([
            'notices' => [
                'trademark_id' => $trademark->id,
                'trademark_info_id' => null,
                'user_id' => $trademark->user_id,
                'flow' => Notice::FLOW_RESPONSE_REASON,
                'step' => Notice::STEP_3,
            ],
            'notice_details' => [
                // A-000top
                [
                    'target_id' => null,
                    'type_acc' => NoticeDetail::TYPE_SUPERVISOR,
                    'target_page' => $routerA203s,
                    'redirect_page' => $routerA203sashi,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                    'is_action' => true,
                    'content' => '責任者　拒絶理由通知対応：方針案差し戻し',
                    'attribute' => '所内処理',
                    'response_deadline' => $responseDeadline,
                    'completion_date' => null,
                ],
                // A-000anken_top
                [
                    'target_id' => null,
                    'type_acc' => NoticeDetail::TYPE_SUPERVISOR,
                    'target_page' => $routerA203s,
                    'redirect_page' => $routerA203sashi,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'content' => '責任者　拒絶理由通知対応：方針案差し戻し',
                    'attribute' => '所内処理',
                    'response_deadline' => $responseDeadline,
                    'completion_date' => null,
                ],
            ],
        ]);
    }

    /**
     * Notice a203s to a203shu
     *
     * @return void
     */
    public function noticeA203StoA203Shu($comparisonTrademarkResult, $trademarkPlan)
    {
        $comparisonTrademarkResult = $comparisonTrademarkResult->load([
            'trademark',
            'machingResult',
            'planCorrespondence',
        ]);

        $trademark = $comparisonTrademarkResult->trademark;
        $planCorrespondence = $comparisonTrademarkResult->planCorrespondence;
        $machingResult = $comparisonTrademarkResult->machingResult;

        // Update old notice
        $stepBeforeNotice = $this->noticeDetailService->findByCondition([
            'raw' => 'type_acc <> ' . NoticeDetail::TYPE_USER,
            'completion_date' => null,
        ])->with('notice')->get()
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
                'completion_date' => now(),
            ]);
        });

        // Create new notice
        $routerA203s = route('admin.refusal.response-plan.supervisor', [
            'id' => $comparisonTrademarkResult->id,
            'trademark_plan_id' => $trademarkPlan->id,
        ]);
        $routerA203shu = route('admin.refusal.response-plan.edit.supervisor', [
            'id' => $comparisonTrademarkResult->id,
            'trademark_plan_id' => $trademarkPlan->id,
        ]);

        $responseDeadline = $machingResult->calculateResponseDeadline(-18);
        if (!empty($machingResult) && $planCorrespondence->type == PlanCorrespondence::TYPE_2) {
            $responseDeadline = $machingResult->calculateResponseDeadline(-24);
        }

        $this->noticeService->sendNotice([
            'notices' => [
                'trademark_id' => $trademark->id,
                'trademark_info_id' => null,
                'user_id' => $trademark->user_id,
                'flow' => Notice::FLOW_RESPONSE_REASON,
                'step' => Notice::STEP_3,
            ],
            'notice_details' => [
                // A-000top
                [
                    'target_id' => null,
                    'type_acc' => NoticeDetail::TYPE_SUPERVISOR,
                    'target_page' => $routerA203s,
                    'redirect_page' => $routerA203shu,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                    'is_action' => true,
                    'content' => '責任者　拒絶理由通知対応：方針案修正',
                    'attribute' => '所内処理',
                    'response_deadline' => $responseDeadline,
                    'completion_date' => null,
                ],
                // A-000anken_top
                [
                    'target_id' => null,
                    'type_acc' => NoticeDetail::TYPE_SUPERVISOR,
                    'target_page' => $routerA203s,
                    'redirect_page' => $routerA203shu,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'content' => '責任者　拒絶理由通知対応：方針案修正',
                    'attribute' => '所内処理',
                    'response_deadline' => $responseDeadline,
                    'completion_date' => null,
                ],
            ],
        ]);
    }

    /**
     * Notice a204han|a204han_n to a203shu
     *
     * @return void
     */
    public function noticeA204hanToA203Shu($comparisonTrademarkResult, $trademarkPlan, $option = [])
    {
        $comparisonTrademarkResult = $comparisonTrademarkResult->load([
            'trademark',
            'machingResult',
            'planCorrespondence',
        ]);

        $trademark = $comparisonTrademarkResult->trademark;
        $planCorrespondence = $comparisonTrademarkResult->planCorrespondence;
        $machingResult = $comparisonTrademarkResult->machingResult;

        // Update old notice
        $stepBeforeNotice = $this->noticeDetailService->findByCondition([
            'raw' => 'type_acc <> ' . NoticeDetail::TYPE_USER,
            'completion_date' => null,
        ])->with('notice')->get()
            ->where('notice.trademark_id', $trademark->id)
            ->where('notice.user_id', $trademark->user_id)
            ->whereIn('notice.flow', [Notice::FLOW_RESPONSE_REASON, Notice::FLOW_RENEWAL_BEFORE_DEADLINE])
            ->filter(function ($item) {
                if ($item->notice->flow == Notice::FLOW_RESPONSE_REASON && in_array($item->notice->step, [Notice::STEP_4])) {
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

        // Create new notice
        $targetPage = $option['target_page'] ?? null;
        $routerA203shu = route('admin.refusal.response-plan.edit.supervisor', [
            'id' => $comparisonTrademarkResult->id,
            'trademark_plan_id' => $trademarkPlan->id,
        ]);

        $responseDeadline = $machingResult->calculateResponseDeadline(-31);
        if (!empty($machingResult) && $planCorrespondence->type == PlanCorrespondence::TYPE_2) {
            $responseDeadline = $machingResult->calculateResponseDeadline(-36);
        }

        $this->noticeService->sendNotice([
            'notices' => [
                'trademark_id' => $trademark->id,
                'trademark_info_id' => null,
                'user_id' => $trademark->user_id,
                'flow' => Notice::FLOW_RESPONSE_REASON,
                'step' => Notice::STEP_3,
            ],
            'notice_details' => [
                // A-000top
                [
                    'target_id' => null,
                    'type_acc' => NoticeDetail::TYPE_SUPERVISOR,
                    'target_page' => $targetPage,
                    'redirect_page' => $routerA203shu,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                    'is_action' => true,
                    'content' => '責任者　拒絶理由通知対応：方針案修正',
                    'attribute' => '所内処理',
                    'response_deadline' => $responseDeadline,
                    'completion_date' => null,
                ],
                // A-000anken_top
                [
                    'target_id' => null,
                    'type_acc' => NoticeDetail::TYPE_SUPERVISOR,
                    'target_page' => $targetPage,
                    'redirect_page' => $routerA203shu,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'content' => '責任者　拒絶理由通知対応：方針案修正',
                    'attribute' => '所内処理',
                    'response_deadline' => $responseDeadline,
                    'completion_date' => null,
                ],
            ],
        ]);
    }
}
