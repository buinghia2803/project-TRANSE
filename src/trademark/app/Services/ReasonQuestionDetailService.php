<?php

namespace App\Services;

use App\Helpers\FileHelper;
use App\Models\Admin;
use App\Models\Notice;
use App\Models\NoticeDetail;
use App\Models\ReasonQuestionDetail;
use App\Repositories\ReasonQuestionDetailRepository;
use App\Services\Common\NoticeService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReasonQuestionDetailService extends BaseService
{
    protected NoticeService         $noticeService;
    protected NoticeDetailService   $noticeDetailService;

    /**
     * Initializing the instances and variables
     *
     * @param ReasonQuestionDetailRepository $reasonQuestionDetailRepository
     * @param NoticeService $noticeService
     * @param NoticeDetailService $noticeDetailService
     */
    public function __construct(
        ReasonQuestionDetailRepository $reasonQuestionDetailRepository,
        NoticeService                  $noticeService,
        NoticeDetailService            $noticeDetailService
    )
    {
        $this->repository = $reasonQuestionDetailRepository;
        $this->noticeService = $noticeService;
        $this->noticeDetailService = $noticeDetailService;
    }

    /**
     * Get Reason Question Detail Data
     *
     * @param array $inputs
     * @return Collection
     */
    public function getReasonQuestionDetailData(array $inputs): Collection
    {
        return $this->repository->getReasonQuestionDetailData($inputs);
    }

    /**
     * Get Reason Question Detail Data (with reason_question_no)
     *
     * @param integer $comparisonTrademarkResultId
     * @param integer $isAnswer
     * @param string $sortBy
     * @return Collection
     */
    public function getReasonQuestionDetailDataV2(
        int $comparisonTrademarkResultId,
        int $isAnswer,
        string $sortBy,
        int $reasonQuestionNoId,
        int $isConfirm,
        string $condition
    ): Collection
    {
        return $this->repository->getReasonQuestionDetailDataV2($comparisonTrademarkResultId, $isAnswer, $sortBy, $reasonQuestionNoId, $isConfirm, $condition);
    }

    /**
     * Post Refusal PreQuestion Reply
     *
     * @param array $inputs
     * @return array
     * @throws \Exception
     */
    public function postRefusalPreQuestionReply(array $inputs): array
    {
        DB::beginTransaction();
        try {
            $respon = [
                'status' => false,
                'messages' => null,
                'redirect_page' => null,
            ];
            if (!empty($inputs['data'])) {
                if (in_array($inputs['from_page'], [U202_DRAFT, U202_DRAFT_TO_KAKUNIN])) {
                    foreach ($inputs['data'] as $item) {
                        $model = $this->find($item['id']);
                        $dataUpdate = [];
                        $dataUpdate['is_answer'] = ReasonQuestionDetail::IS_NOT_ANSWER;
                        $dataUpdate['answer'] = $item['answer'];

                        if (isset($item['attachment']) && !empty($item['attachment'])) {
                            if (count($item['attachment']) > 20) {
                                $respon['status'] = false;
                                $respon['redirect_page'] = __('messages.general.Import_A000_E001');
                                return $respon;
                            }

                            $filesItem = [];
                            if (!empty($item['attachment'])) {
                                // Add new file
                                foreach ($item['attachment'] as $file) {
                                    if (!empty($file) && $file->getSize() <= 1024 * 1024 * 3) {
                                        $filePath = FileHelper::uploads($file, [], ReasonQuestionDetail::getPathFolderU202(), false);
                                        if ($filePath) {
                                            $filesItem[] = $filePath[0]['filepath'];
                                        }
                                    }
                                }

                                // Delete old attachment
                                if (!empty($model->attachment)) {
                                    $attachments = json_decode($model->attachment, true);

                                    foreach ($attachments as $attachment) {
                                        FileHelper::unlink($attachment);
                                    }
                                }
                            } else {
                                //old file
                                if (!empty($model->attachment)) {
                                    $filesItem = json_decode($model->attachment, true);
                                }
                            }
                            $dataUpdate['attachment'] = json_encode($filesItem);
                        }

                        //update reason_question_details
                        if ($inputs['reason_question_detail_ids']->contains($item['id'])) {
                            $model->update($dataUpdate);
                        }
                    }
                }

                // Send Notice
                if ($inputs['from_page'] == U202KAKUNIN) {
                    foreach ($inputs['data'] as $item) {
                        $dataUpdate = [];
                        $dataUpdate['is_answer'] = ReasonQuestionDetail::IS_ANSWER;

                        if ($inputs['reason_question_detail_ids']->contains($item['id'])) {
                            $model = $this->find($item['id']);
                            $model->update($dataUpdate);
                        }
                    }

                    // Update Notice at u202 (No 21: H I)
                    $stepBeforeNotice = $this->noticeDetailService->findByCondition([
                        'type_acc' => NoticeDetail::TYPE_USER,
                        'is_answer' => NoticeDetail::IS_NOT_ANSWER,
                    ])->with('notice')->get()
                        ->where('notice.trademark_id', $inputs['trademark_id'])
                        ->where('notice.user_id', auth()->user()->id)
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

                    $targetPage = route('user.refusal.pre-question.reply', [
                        'id' => $inputs['comparison_trademark_result_id'],
                        'reason_question_no' => $inputs['reason_question_no'],
                    ]);
                    $targetPage = str_replace(request()->root(), '', $targetPage);

                    $redirectPage = route('admin.refusal.pre-question-re.supervisor.show', [
                        'id' => $inputs['comparison_trademark_result_id'],
                        'reason_question_no' => $inputs['reason_question_no'],
                    ]);

                    //if form page is: u202kakunin
                    $this->noticeService->sendNotice([
                        'notices' => [
                            'flow' => Notice::FLOW_RESPONSE_REASON,
                            'step' => Notice::STEP_2,
                            'trademark_id' => $inputs['trademark_id'],
                            'user_id' => auth()->user()->id,
                            'trademark_info_id' => null,
                            'created_at' => Carbon::now()
                        ],
                        'notice_details' => [
                            // Send Notice Seki: A-000top
                            [
                                'target_id' => null,
                                'type_acc' => NoticeDetail::TYPE_SUPERVISOR,
                                'target_page' => $targetPage,
                                'redirect_page' => $redirectPage,
                                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                                'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                                'is_action' => NoticeDetail::IS_ACTION_TRUE,
                                'content' => __('messages.u202.content_messages_to_admin'),
                                'created_at' => Carbon::now()
                            ],
                            // Send Notice Seki: A-000anken_top
                            [
                                'target_id' => null,
                                'type_acc' => NoticeDetail::TYPE_SUPERVISOR,
                                'target_page' => $targetPage,
                                'redirect_page' => $redirectPage,
                                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                                'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                                'content' => __('messages.u202.content_messages_to_admin'),
                                'attribute' => __('messages.u202.content_messages_attribute'),
                                'created_at' => Carbon::now()
                            ],
                            //Send Notice to user: U-000top : H
                            [
                                'target_id' => auth()->user()->id,
                                'type_acc' => NoticeDetail::TYPE_USER,
                                'target_page' => $targetPage,
                                'type_notify' => NoticeDetail::TYPE_NOTIFY_DEFAULT,
                                'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                                'content' => '拒絶理由通知対応：事前質問回答完了・返信待ち',
                                'created_at' => Carbon::now()
                            ],
                            //Send Notice to user: U-000anken_top: I
                            [
                                'target_id' => auth()->user()->id,
                                'type_acc' => NoticeDetail::TYPE_USER,
                                'target_page' => $targetPage,
                                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                                'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                                'content' => '拒絶理由通知対応：事前質問回答完了・返信待ち',
                                'created_at' => Carbon::now()
                            ],
                        ],
                    ]);
                }
            }

            DB::commit();
            $respon['status'] = true;
            if (isset($inputs['from_page'])) {
                //if save draft in screen u202kakunin
                if ($inputs['from_page'] == U202_DRAFT) {
                    $respon['redirect_page'] = U000ANKEN_TOP;
                } elseif ($inputs['from_page'] == U202KAKUNIN_DRAFT) {
                    $respon['redirect_page'] = U202;
                } elseif ($inputs['from_page'] == U202KAKUNIN || $inputs['from_page'] == U202_DRAFT_TO_KAKUNIN) {
                    $respon['redirect_page'] = U202KAKUNIN;
                }
            }

            return $respon;
        } catch (\Exception $e) {
            Log::error($e);
            $respon['status'] = false;
            DB::rollback();
        }

        return $respon;
    }

    /**
     * Rost Save ReReply Refusal PreQuestion
     *
     * @param array $inputs
     * @return array
     */
    public function postSaveReReplyRefusalPreQuestion(array $inputs): array
    {
        DB::beginTransaction();
        try {
            $respon = [
                'status' => false,
                'messages' => null,
                'redirect_page' => null,
            ];

            if (!empty($inputs['data'])) {
                if (in_array($inputs['from_page'], [U202N_DRAFT, U202N_DRAFT_REDIRECT_KAKUNIN])) {
                    foreach ($inputs['data'] as $item) {
                        $model = $this->find($item['id']);
                        $dataUpdate = [];
                        $dataUpdate['is_answer'] = ReasonQuestionDetail::IS_NOT_ANSWER;
                        $dataUpdate['answer'] = $item['answer'];

                        //update reason_question_details
                        if (isset($item['attachment']) && !empty($item['attachment'])) {
                            if (count($item['attachment']) > 20) {
                                $respon['status'] = false;
                                $respon['redirect_page'] = __('messages.general.Import_A000_E001');
                                return $respon;
                            }

                            $filesItem = [];
                            if (!empty($item['attachment'])) {
                                foreach ($item['attachment'] as $file) {
                                    //file < 3MB
                                    if (!empty($file) && $file->getSize() <= 1024 * 1024 * 3) {
                                        $filePath = FileHelper::uploads($file, [], ReasonQuestionDetail::getPathFolderU202(), false);
                                        if ($filePath) {
                                            $filesItem[] = $filePath[0]['filepath'];
                                        }
                                    }
                                }
                            } else {
                                //old file
                                if ($model->attachment) {
                                    $filesItem = json_decode($model->attachment, true);
                                }
                            }
                            $dataUpdate['attachment'] = json_encode($filesItem);
                        }

                        if ($inputs['reason_question_detail_ids']->contains($item['id'])) {
                            $model->update($dataUpdate);
                        }
                    }
                }

                //Send notices
                if (isset($inputs['from_page'])) {
                    if ($inputs['from_page'] == U202N_KAKUNIN) {
                        foreach ($inputs['data'] as $item) {
                            $dataUpdate = [];
                            $dataUpdate['is_answer'] = ReasonQuestionDetail::IS_ANSWER;

                            if ($inputs['reason_question_detail_ids']->contains($item['id'])) {
                                $model = $this->find($item['id']);
                                $model->update($dataUpdate);
                            }
                        }

                        // Update Notice at u202 (No 21: H I)
                        $targetPage = route('user.refusal.pre-question.re-reply', [
                            'id' => $inputs['comparison_trademark_result_id'],
                            'reason_question_no' => $inputs['reason_question_no'],
                        ]);

                        $targetPage = str_replace(request()->root(), '', $targetPage);
                        $redirectPage = route('admin.refusal.pre-question-re.supervisor.show', [
                            'id' => $inputs['comparison_trademark_result_id'],
                            'reason_question_no' => $inputs['reason_question_no'],
                        ]);

                        $stepBeforeNotice = $this->noticeDetailService->findByCondition([
                            'type_acc' => NoticeDetail::TYPE_USER,
                            'is_answer' => NoticeDetail::IS_NOT_ANSWER,
                        ])->with('notice')->get()
                            ->where('notice.trademark_id', $inputs['trademark_id'])
                            ->where('notice.user_id', auth()->user()->id)
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

                        // Send Notice
                        $this->noticeService->sendNotice([
                            'notices' => [
                                'flow' => Notice::FLOW_RESPONSE_REASON,
                                'step' => Notice::STEP_2,
                                'trademark_id' => $inputs['trademark_id'],
                                'user_id' => auth()->user()->id,
                                'trademark_info_id' => null,
                                'created_at' => Carbon::now()
                            ],
                            'notice_details' => [
                                // Send Notice Seki: A-000top
                                [
                                    'target_id' => null,
                                    'type_acc' => NoticeDetail::TYPE_SUPERVISOR,
                                    'target_page' => $targetPage,
                                    'redirect_page' => $redirectPage,
                                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                                    'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                                    'is_action' => NoticeDetail::IS_ACTION_TRUE,
                                    'content' => __('messages.u202.content_messages_to_admin'),
                                    'created_at' => Carbon::now()
                                ],
                                // Send Notice Seki: A-000anken_top
                                [
                                    'target_id' => null,
                                    'type_acc' => NoticeDetail::TYPE_SUPERVISOR,
                                    'target_page' => $targetPage,
                                    'redirect_page' => $redirectPage,
                                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                                    'content' => __('messages.u202.content_messages_to_admin'),
                                    'attribute' => __('messages.u202.content_messages_attribute'),
                                    'created_at' => Carbon::now()
                                ],
                                //Send Notice to user: U-000top
                                [
                                    'target_id' => auth()->user()->id,
                                    'type_acc' => NoticeDetail::TYPE_USER,
                                    'target_page' => $targetPage,
                                    'type_notify' => NoticeDetail::TYPE_NOTIFY_DEFAULT,
                                    'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                                    'content' => '拒絶理由通知対応：事前質問回答完了・返信待ち',
                                    'created_at' => Carbon::now()
                                ],
                                //Send Notice to user: U-000anken_top
                                [
                                    'target_id' => auth()->user()->id,
                                    'type_acc' => NoticeDetail::TYPE_USER,
                                    'target_page' => $targetPage,
                                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                                    'content' => '拒絶理由通知対応：事前質問回答完了・返信待ち',
                                    'created_at' => Carbon::now()
                                ],
                            ],
                        ]);
                    }
                }
            }

            //redirect data
            if ($inputs['from_page'] == U202N_DRAFT) {
                $respon['redirect_page'] = U000ANKEN_TOP;
            } elseif ($inputs['from_page'] == U202N_DRAFT_REDIRECT_KAKUNIN) {
                //save draft u202n and redirect u202n-kakunin
                $respon['redirect_page'] = U202N_KAKUNIN;
            } elseif ($inputs['from_page'] == U202N_KAKUNIN_DRAFT) {
                $respon['redirect_page'] = U202N;
            } elseif ($inputs['from_page'] == U202N_KAKUNIN) {
                //if form page is: u202n_kakunin
                $respon['redirect_page'] = U202N_KAKUNIN;
            }

            DB::commit();
            $respon['status'] = true;

            return $respon;
        } catch (\Exception $e) {
            Log::error($e);
            throw new \Exception($e->getMessage());
            $respon['status'] = false;

            DB::rollback();
        }
        return $respon;
    }
}
