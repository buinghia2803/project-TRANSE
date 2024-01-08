<?php

namespace App\Services;

use App\Helpers\CommonHelper;
use App\Models\Admin;
use App\Models\AppTrademark;
use App\Models\Notice;
use App\Models\NoticeDetail;
use App\Models\PlanComment;
use App\Models\PrecheckComment;
use App\Models\ReasonComment;
use App\Models\SFTComment;
use App\Models\User;
use App\Repositories\NoticeDetailRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;

class NoticeDetailService extends BaseService
{
    /**
     * Initializing the instances and variables
     *
     * @param   NoticeDetailRepository $noticeDetailRepository
     */
    public function __construct(NoticeDetailRepository $noticeDetailRepository)
    {
        $this->repository = $noticeDetailRepository;
    }

    /**
     * Format Data Notice Detail
     *
     * @param Collection $datas
     * @param bool $hasComment
     * @return  Collection
     */
    public function formatData(Collection $datas, bool $hasComment = false): Collection
    {
        $relations = [
            'noticeDetailBtns',
            'noticeDetailBtns.trademarkDocuments',
            'notice',
            'notice.user',
            'notice.trademarkInfo',
            'notice.trademark',
            'notice.trademark.appTrademark.trademarkInfo',
            'notice.trademark.registerTrademark',
            'notice.trademark.comparisonTrademarkResult',
            'notice.trademark.comparisonTrademarkResult.machingResult',
        ];
        if ($hasComment == true) {
            $relations = array_merge($relations, [
                'notice.trademark.supportFirstTime',
                'notice.trademark.supportFirstTime.stfComment',
                'notice.trademark.prechecks',
                'notice.trademark.prechecks.precheckComments',
                'notice.trademark.freeHistories',
                'notice.trademark.comparisonTrademarkResult.planCorrespondences.reasonComments',
                'notice.trademark.planComments',
            ]);
        }
        $datas = $datas->load($relations);

        $noticeUsers = $datas->where('type_acc', NoticeDetail::TYPE_USER);
        $users = User::whereIn('id', $noticeUsers->pluck('target_id')->toArray())->get();

        $noticeAdmins = $datas->where('type_acc', '<>', NoticeDetail::TYPE_USER);
        $admins = Admin::whereIn('id', $noticeAdmins->pluck('target_id')->toArray())->get();

        $datas->map(function ($item) use ($users, $admins, $hasComment) {
            $notice = $item->notice;
            $noticeUser = $notice->user ?? null;
            $trademark = $notice->trademark ?? null;
            $comparisonTrademarkResult = $notice->trademark->comparisonTrademarkResult ?? null;
            $machingResult = $notice->trademark->comparisonTrademarkResult->machingResult ?? null;

            $item->notice_created_at = $notice->created_at ?? null;
            $item->trademark = $trademark;
            $item->trademark_id = (!empty($trademark)) ? $trademark->id : null;
            $item->flow = (!empty($notice)) ? $notice->flow : null;
            $item->maching_result_id = (!empty($machingResult)) ? $machingResult->id : null;
            $item->comparison_trademark_result_id = $comparisonTrademarkResult->id ?? 0;

            // Get username
            $item->username = (!empty($noticeUser)) ? $noticeUser->info_name : '';
            switch ($notice->flow) {
                case Notice::FLOW_APP_TRADEMARK:
                case Notice::FLOW_RESPONSE_REASON:
                    $trademarkInfo = $notice->trademarkInfo;
                    if (!empty($notice->trademarkInfo)) {
                        $item->username = $trademarkInfo->name;
                    } else {
                        $appTrademark = !empty($trademark) ? $trademark->appTrademark : null;
                        if (!empty($appTrademark)) {
                            $trademarkInfos = $appTrademark->trademarkInfo;
                            $lastTrademarkInfo = $trademarkInfos->last();
                            if (!empty($lastTrademarkInfo)) {
                                $item->username = $lastTrademarkInfo->name;
                            }
                        }
                    }
                    break;
                case Notice::FLOW_SFT:
                case Notice::FLOW_PRECHECK:
                case Notice::FLOW_FREE_HISTORY:
                case Notice::FLOW_QA:
                    $item->username = (!empty($noticeUser)) ? $noticeUser->info_name : '';
                    break;
                case Notice::FLOW_REGISTER_TRADEMARK:
                case Notice::FLOW_RENEWAL:
                case Notice::FLOW_RENEWAL_BEFORE_DEADLINE:
                case Notice::FLOW_CHANGE_INFO:
                    if (!empty($trademark)) {
                        $registerTrademark = $trademark->registerTrademark;

                        if (!empty($registerTrademark)) {
                            $item->username = $registerTrademark->trademark_info_name;
                        };
                    }
                    break;
            }

            // Get Actor Name
            $item->actor = '';
            if ($item->type_acc == NoticeDetail::TYPE_USER) {
                $actor = $users->where('id', $item->target_id)->first();
                $item->actor = $actor->info_name;
            } else {
                $actor = $admins->where('id', $item->target_id)->first();

                $actorName = '';
                switch ($item->type_acc) {
                    case NoticeDetail::TYPE_OFFICE_MANAGER:
                        $actorName = '事務担当';
                        break;
                    case NoticeDetail::TYPE_MANAGER:
                        $actorName = '担当者';
                        break;
                    case NoticeDetail::TYPE_SUPERVISOR:
                        $actorName = '責任者';
                        break;
                }

                $item->actor = $actorName. '：' . $actor->name;
            }

            // Get comparison response_deadline
            $item->comparison_response_deadline_raw = null;
            $item->comparison_response_deadline = null;
            if (!empty($trademark)) {
                $comparisonTrademarkResult = $trademark->comparisonTrademarkResult ?? null;
                if (!empty($comparisonTrademarkResult)) {
                    $item->comparison_response_deadline_raw = $comparisonTrademarkResult->response_deadline;
                    $item->comparison_response_deadline = CommonHelper::formatTime($item->comparison_response_deadline_raw ?? '', 'Y/m/d');
                }
            }

            // Get Detail response deadline
            $item->detail_response_deadline_raw = null;
            if (in_array($notice->flow, [
                Notice::FLOW_RESPONSE_REASON,
                Notice::FLOW_RENEWAL_BEFORE_DEADLINE,
            ])) {
                $item->detail_response_deadline_raw = $item->comparison_response_deadline_raw;
            } elseif (in_array($notice->flow, [
                Notice::FLOW_REGISTER_TRADEMARK,
                Notice::FLOW_RENEWAL,
                Notice::FLOW_CHANGE_INFO,
            ])) {
                if (!empty($trademark)) {
                    $registerTrademark = $trademark->registerTrademark;
                    if (!empty($registerTrademark)) {
                        if (!empty($registerTrademark->deadline_update)) {
                            $deadlineUpdate = $registerTrademark->deadline_update;
                            $deadlineUpdateBefore6Month = Carbon::parse($deadlineUpdate)->subMonth(6)->format('Y-m-d H:i:s');
                            $item->detail_response_deadline_raw = $deadlineUpdateBefore6Month;
                        }
                    };
                }
            } else {
                $item->detail_response_deadline_raw = $item->response_deadline;
            }
            $item->detail_response_deadline = CommonHelper::formatTime($item->detail_response_deadline_raw ?? '', 'Y/m/d');

            // Comment
            $comments = [];
            if ($hasComment == true) {
                $comments = $this->getNoticeComment($item);
            }
            $item->comments = $comments;

            $appTrademark = !empty($trademark) ? $trademark->appTrademark : null;
            if (!empty($appTrademark) && $appTrademark->is_cancel == AppTrademark::IS_CANCEL_TRUE && $notice->flow == Notice::FLOW_APP_TRADEMARK) {
                $noticeDetailBtns = $item->noticeDetailBtns ?? collect([]);

                $item->noticeDetailBtns = $noticeDetailBtns->map(function ($item) {
                    if (in_array($item->from_page, [U031, A031, U032])) {
                        $item->is_hidden_btn = true;
                    }

                    return $item;
                });
            }

            return $item;
        });

        return $datas;
    }

    /**
     * Get Notice Comment
     *
     * @param NoticeDetail $item
     * @return  array
     */
    public function getNoticeComment(NoticeDetail $item): array
    {
        $comments = [];
        $notice = $item->notice;
        $trademark = $notice->trademark ?? null;

        if (empty($trademark)) {
            return $comments;
        }

        $comments[]['content'] = $item->comment ?? '';

        // switch ($notice->flow) {
        //     case Notice::FLOW_SFT:
        //         $supportFirstTime = $trademark->supportFirstTime;
        //         if (!empty($supportFirstTime)) {
        //             $stfComment = $supportFirstTime->stfComment;
        //             $stfComment = $stfComment->where('type', SFTComment::TYPE_COMMENT_INSIDER);
        //             foreach ($stfComment as $comment) {
        //                 $comments[]['content'] = $comment->content ?? '';
        //             }
        //         }
        //         break;
        //     case Notice::FLOW_PRECHECK:
        //         $prechecks = $trademark->prechecks ?? [];
        //         foreach ($prechecks as $precheck) {
        //             $precheckComments = $precheck->precheckComments->where('type', PrecheckComment::TYPE_COMMENT_INTERNAL);
        //             foreach ($precheckComments as $precheckComment) {
        //                 $comments[]['content'] = $precheckComment->content ?? '';
        //             }
        //         }
        //         break;
        //     case Notice::FLOW_FREE_HISTORY:
        //         // $freeHistories = $trademark->freeHistories ?? [];
        //         // foreach ($freeHistories as $freeHistory) {
        //         //     $comments[]['content'] = $freeHistory->comment ?? '';
        //         // }
        //         $comments[]['content'] = $item->comment ?? '';
        //         break;
        //     case Notice::FLOW_APP_TRADEMARK:
        //         $appTrademark = $trademark->appTrademark;
        //         if (!empty($appTrademark)) {
        //             $comments[]['content'] = $appTrademark->comment_office ?? '';
        //         }
        //         break;
        //     case Notice::FLOW_RESPONSE_REASON:
        //         $commentStep1 = [];
        //         $commentStep2 = [];
        //         $commentStep3 = [];
        //         $commentStep4 = [];
        //
        //         $comparisonTrademarkResult = $trademark->comparisonTrademarkResult ?? [];
        //         $planCorrespondences = $comparisonTrademarkResult->planCorrespondences ?? [];
        //         foreach ($planCorrespondences as $planCorrespondence) {
        //             $reasonComments = $planCorrespondence->reasonComments;
        //             foreach ($reasonComments as $reasonComment) {
        //                 if ($reasonComment->type_comment_step == ReasonComment::STEP_1) {
        //                     $commentStep1[]['content'] = $reasonComment->content ?? '';
        //                 }
        //                 if ($reasonComment->type_comment_step == ReasonComment::STEP_2) {
        //                     $commentStep2[]['content'] = $reasonComment->content ?? '';
        //                 }
        //             }
        //         }
        //
        //         $planComments = $trademark->planComments ?? [];
        //         foreach ($planComments as $planComment) {
        //             if ($planComment->type_comment_step == PlanComment::STEP_1) {
        //                 $commentStep3[]['content'] = $planComment->content ?? '';
        //             }
        //             if ($planComment->type_comment_step == PlanComment::STEP_2) {
        //                 $commentStep4[]['content'] = $planComment->content ?? '';
        //             }
        //         }
        //
        //         if ($notice->step == Notice::STEP_1) {
        //             $comments = $commentStep1;
        //         } elseif ($notice->step == Notice::STEP_2) {
        //             $comments = $commentStep2;
        //         } elseif ($notice->step == Notice::STEP_3) {
        //             $comments = $commentStep3;
        //         } elseif ($notice->step == Notice::STEP_4) {
        //             $comments = $commentStep4;
        //         }
        //         break;
        // }

        return $comments;
    }

    /**
     * Is Cancel Notice
     *
     * @param NoticeDetail $item
     * @return array
     */
    public function isCancel(NoticeDetail $item): array
    {
        $isCancel = false;
        $cancelType = null;
        $cancelID = 0;

        $notice = $item->notice;
        $trademark = $notice->trademark ?? null;

        switch ($notice->flow) {
            case Notice::FLOW_SFT:
                if (!empty($trademark)) {
                    $supportFirstTime = $trademark->supportFirstTime;
                    if (!empty($supportFirstTime) && $supportFirstTime->is_cancel == true) {
                        $isCancel = true;
                        $cancelType = Notice::FLOW_SFT;
                        $cancelID = $supportFirstTime->id;
                    }
                }
                break;
            case Notice::FLOW_PRECHECK:
                if (!empty($trademark)) {
                    $prechecks = $trademark->prechecks;
                    $precheck = $prechecks->last();
                    if (!empty($precheck) && $precheck->is_cancel == true) {
                        $isCancel = true;
                        $cancelType = Notice::FLOW_PRECHECK;
                        $cancelID = $precheck->id;
                    }
                }
                break;
            case Notice::FLOW_FREE_HISTORY:
                if (!empty($trademark)) {
                    $freeHistories = $trademark->freeHistories;
                    $freeHistory = $freeHistories->last();
                    if (!empty($freeHistory) && $freeHistory->is_cancel == true) {
                        $isCancel = true;
                        $cancelType = Notice::FLOW_FREE_HISTORY;
                        $cancelID = $freeHistory->id;
                    }
                }
                break;
            case Notice::FLOW_APP_TRADEMARK:
                $appTrademark = $trademark->appTrademark;
                if (!empty($appTrademark) && $appTrademark->is_cancel == true) {
                    $isCancel = true;
                    $cancelType = Notice::FLOW_APP_TRADEMARK;
                    $cancelID = $appTrademark->id;
                }
                break;
            case Notice::FLOW_RESPONSE_REASON:
                if (!empty($trademark)) {
                    $comparisonTrademarkResult = $trademark->comparisonTrademarkResult;

                    if (!empty($comparisonTrademarkResult)) {
                        $responseDeadline = $comparisonTrademarkResult->response_deadline;
                        $now = Carbon::now();

                        if ($comparisonTrademarkResult->is_cancel == true) {
                            $isCancel = true;
                            $cancelType = Notice::FLOW_RESPONSE_REASON;
                            $cancelID = $comparisonTrademarkResult->id;
                        } elseif (!empty($responseDeadline) && $responseDeadline < $now) {
                            $isCancel = true;
                        }
                    }
                }
                break;
        }

        return [
              'is_cancel' => $isCancel,
              'type' => $cancelType,
              'id' => $cancelID,
        ];
    }

    /**
     * Get notice detail with type acc and type notify and type page.
     *
     * @param array $params
     * @param array $relation
     * @return Builder
     */
    public function getNoticeDetails($params = [], $relation = []):  ?Builder
    {
        $conds = array_merge($params, [
            'type_acc' => NoticeDetail::TYPE_USER,
            'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
            'type_page' => NoticeDetail::TYPE_PAGE_TOP
        ]);

        return $this->repository->findByCondition($conds, $relation)
            ->orderBy('id', SORT_BY_DESC);
    }
}
