<?php

namespace App\Services;

use App\Helpers\FileHelper;
use App\Jobs\SendGeneralMailJob;
use App\Models\Admin;
use App\Models\DocSubmission;
use App\Models\DocSubmissionCmt;
use App\Models\Notice;
use App\Models\NoticeDetail;
use App\Models\NoticeDetailBtn;
use App\Models\PlanCorrespondence;
use App\Repositories\ComparisonTrademarkResultRepository;
use App\Repositories\DocSubmissionAttachmentRepository;
use App\Repositories\DocSubmissionAttachPropertyRepository;
use App\Repositories\DocSubmissionCmtRepository;
use App\Repositories\MProductRepository;
use App\Repositories\NoticeDetailRepository;
use App\Services\BaseService;
use App\Repositories\DocSubmissionRepository;
use App\Services\Common\NoticeService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DocSubmissionService extends BaseService
{
    protected $mProductRepository;
    protected $docSubmissionCmtRepository;
    protected $docSubmissionAttachPropertyRepository;
    protected $docSubmissionAttachmentRepository;
    protected $noticeDetailRepository;
    protected $comparisonTrademarkResultRepository;
    protected $noticeService;

    /**
     * Initializing the instances and variables
     *
     * @param DocSubmissionRepository $docSubmissionRepository
     * @param MProductRepository $mProductRepository
     * @param DocSubmissionCmtRepository $docSubmissionCmtRepository
     * @param DocSubmissionAttachPropertyRepository $docSubmissionAttachPropertyRepository
     * @param DocSubmissionAttachmentRepository $docSubmissionAttachmentRepository
     * @param NoticeDetailRepository $noticeDetailRepository
     * @param ComparisonTrademarkResultRepository $comparisonTrademarkResultRepository
     * @param NoticeService $noticeService
     */
    public function __construct(
        DocSubmissionRepository $docSubmissionRepository,
        MProductRepository $mProductRepository,
        DocSubmissionCmtRepository $docSubmissionCmtRepository,
        DocSubmissionAttachPropertyRepository $docSubmissionAttachPropertyRepository,
        DocSubmissionAttachmentRepository $docSubmissionAttachmentRepository,
        NoticeDetailRepository $noticeDetailRepository,
        ComparisonTrademarkResultRepository $comparisonTrademarkResultRepository,
        NoticeService $noticeService
    )
    {
        $this->repository = $docSubmissionRepository;
        $this->mProductRepository = $mProductRepository;
        $this->docSubmissionCmtRepository = $docSubmissionCmtRepository;
        $this->docSubmissionAttachPropertyRepository = $docSubmissionAttachPropertyRepository;
        $this->docSubmissionAttachmentRepository = $docSubmissionAttachmentRepository;
        $this->noticeDetailRepository = $noticeDetailRepository;
        $this->comparisonTrademarkResultRepository = $comparisonTrademarkResultRepository;
        $this->noticeService = $noticeService;
    }

    /**
     * Get Doc Submission
     *
     * @param array $data
     * @param string $step
     *
     * @return mixed
     */
    public function getDocSubmission(array $data, $step = null): ?Model
    {
        $docSubmissions = $this->findByCondition($data)->orderBy('id', SORT_BY_DESC)->first();
        if ($docSubmissions) {
            $docSubmissions->load([
                'docSubmissionCmts' => function ($query) use ($step) {
                    if ($step) {
                        $query->where('type_comment_of_step', '!=', $step);
                    }
                    return $query->where('type', DocSubmissionCmt::TYPE_INTERNAL_COMMENT)
                        ->orderBy('created_at', SORT_BY_DESC)
                        ->orderBy('id', SORT_BY_DESC);
                },
            ]);
        }

        return $docSubmissions;
    }

    /**
     * Get data doc submission
     *
     * @param integer $docSubmissionId
     *
     * @return Model
     */
    public function getDataDocSubmission($docSubmissionId): ?Model
    {
        $docSubmissions = $this->repository->findByCondition([
            'id' => $docSubmissionId,
            // 'is_written_opinion' => DocSubmission::IS_WRITTEN_OPINION_TRUE,
        ])->select('id', 'is_written_opinion', 'description_written_opinion')
            ->first();

        if ($docSubmissions) {
            $docSubmissions->load([
                'docSubmissionAttachProperties.docSubmissionAttachments'
            ]);
        }

        return $docSubmissions;
    }

    /**
     * Get data common a025 shu02
     *
     * @param integer $trademarkPlanId
     * @return mixed
     */
    public function getDataProductCommonA205Shu02($trademarkPlanId)
    {
        return $this->mProductRepository->getDataProductCommonA205Shu02($trademarkPlanId);
    }

    /**
     * Store data a205
     *
     * @param array $inputs
     * @return array
     */
    public function storeDataA205(array $inputs): array
    {
        DB::beginTransaction();
        try {
            $descriptionWrittenOpinion = $inputs['data-submission']['description_written_opinion'] ?? null;

            $isWrittenOpinion = DocSubmission::IS_WRITTEN_OPINION_FALSE;
            if (!empty($inputs['data-submission']['is_written_opinion'])) {
                $isWrittenOpinion = DocSubmission::IS_WRITTEN_OPINION_TRUE;
            }

            $authCurrent = auth('admin')->user();

            //update or create: doc_submissions, doc_submission_attach_properties, doc_submission_attachments
            //1.doc_submissions
            $docSubmission = $this->repository->updateOrCreate([
                'id' => $inputs['doc_submission_id'],
            ], [
                'trademark_plan_id' => $inputs['trademark_plan_id'],
                'admin_id' => $authCurrent->id, //role tanto,
                'is_written_opinion' => $isWrittenOpinion,
                'description_written_opinion' => $descriptionWrittenOpinion,
            ]);

            //2.doc_submission_attach_properties
            $dataProperties = $inputs['data-properties'] ?? null;
            if ($docSubmission && $dataProperties) {
                $this->saveDataSubmissionAttachProperties($dataProperties, $docSubmission->id);
            }
            //update comment doc_submission_cmts
            $this->storeCommentDocsubmission($docSubmission->id, $inputs['content']);

            $dataA205 = $docSubmission->load(['docSubmissionAttachProperties.docSubmissionAttachments'])->toArray();
            $docSubmission->update([
                'data_a205' => json_encode($dataA205),
            ]);

            DB::commit();

            $inputs['from_page'] = A205;
            //set session redirect page
            $keySession = $this->createSessionRedirectA205Kakunin($inputs);

            $paramsRedirect = null;
            //if submit to a205kakunin
            if ($inputs['code'] == SAVE_SUBMIT) {
                $paramsRedirect = [
                    'id' => $inputs['comparison_trademark_result_id'],
                    'trademark_plan_id' => $inputs['trademark_plan_id'],
                    'doc_submission_id' => $docSubmission->id,
                    's' => $keySession
                ];
            }

            return [
                'status' => true,
                'redirect_page' => A205_KAKUNIN,
                'params_redirect' => $paramsRedirect,
            ];
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            throw new \Exception($e);

            return [
                'status' => false,
                'redirect_url' => null,
            ];
        }
    }

    /**
     * Store Comment Docsubmission
     *
     * @param integer $docSubmissionId
     * @param mixed $content
     * @return void
     */
    public function storeCommentDocsubmission(int $docSubmissionId, $content, $type = DocSubmissionCmt::TYPE_COMMENT_OF_STEP_1)
    {
        $comment = $this->docSubmissionCmtRepository->findByCondition([
            'doc_submission_id' => $docSubmissionId,
            'type' => DocSubmissionCmt::TYPE_INTERNAL_COMMENT,
            'type_comment_of_step' => $type,
        ])->first();

        if ($comment) {
            if ($content) {
                $comment = $this->docSubmissionCmtRepository->update($comment, [
                    'content' => $content,
                ]);
            } else {
                $comment = $comment->delete();
            }
        } else {
            if ($content) {
                $comment = $this->docSubmissionCmtRepository->create([
                    'doc_submission_id' => $docSubmissionId,
                    'admin_id' => auth('admin')->user()->id,
                    'type' => DocSubmissionCmt::TYPE_INTERNAL_COMMENT,
                    'type_comment_of_step' => $type,
                    'content' => $content
                ]);
            }
        }

        return $comment;
    }

    /**
     * Save data of a205shu.
     *
     * @param array $params
     * @return array
     */
    public function saveDataA205Shu(array $params): array
    {
        try {
            $admin = Auth::guard(ADMIN_ROLE)->user();
            DB::beginTransaction();
            $isWrittenOpinion = DocSubmission::IS_WRITTEN_OPINION_FALSE;
            if (!empty($params['data-submission']['is_written_opinion'])) {
                $isWrittenOpinion = DocSubmission::IS_WRITTEN_OPINION_TRUE;
            }

            $descriptionWrittenOpinion = isset($params['data-submission']) && isset($params['data-submission']['description_written_opinion'])
                ? $params['data-submission']['description_written_opinion']
                : '';
            //1.update doc_submissions
            $docSubmission = $this->repository->updateOrCreate([
                'id' => $params['doc_submission_id'],
            ], [
                'trademark_plan_id' => $params['trademark_plan_id'],
                'admin_id' => $admin->id,
                'is_written_opinion' => $isWrittenOpinion,
                'description_written_opinion' => $descriptionWrittenOpinion,
            ]);

            //2.update or create doc_submission_attach_properties
            $dataProperties = $params['data-properties'] ?? null;

            if ($docSubmission && $dataProperties) {
                $this->saveDataSubmissionAttachProperties($dataProperties, $docSubmission->id);
            }

            $params['from_page'] = A205_SHU;
            //set session redirect page
            $keySession = $this->createSessionRedirectA205Kakunin($params);

            $paramsRedirect = null;
            //if submit to a205kakunin
            if ($params['code'] == SAVE_SUBMIT) {
                $paramsRedirect = [
                    'id' => $params['comparison_trademark_result_id'],
                    'trademark_plan_id' => $params['trademark_plan_id'],
                    'doc_submission_id' => $docSubmission->id,
                    's' => $keySession
                ];
            }
            //update comment doc_submission_cmts
            $this->storeCommentDocsubmission($docSubmission->id, $params['content'], DocSubmissionCmt::TYPE_COMMENT_OF_STEP_4);
            DB::commit();

            return [
                'status' => true,
                'redirect_page' => A205_KAKUNIN,
                'params_redirect' => $paramsRedirect,
            ];
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            throw new \Exception($e);

            return [
                'status' => false,
                'redirect_url' => null,
            ];
        }
    }


    /**
     * Post a205 kakunin
     *
     * @param array $inputs
     * @return void
     * @throws \Exception
     */
    public function postA205Kakunin(array $inputs)
    {
        DB::beginTransaction();
        try {
            $comparisonTrademarkResult = $this->comparisonTrademarkResultRepository->find($inputs['comparison_trademark_result_id'])->load('trademark');
            $planCorrespondence = $comparisonTrademarkResult->planCorrespondence;
            $machingResult = $comparisonTrademarkResult->machingResult;

            $docSubmission = $this->repository->find($inputs['doc_submission_id']);

            $routeA205 = route('admin.refusal.documents.create', [
                'id' => $inputs['comparison_trademark_result_id'],
                'trademark_plan_id' => $inputs['trademark_plan_id'],
            ]);
            $routeA205s = route('admin.refusal.documents.supervisor', [
                'comparison_trademark_result_id' => $inputs['comparison_trademark_result_id'],
                'trademark_plan_id' => $inputs['trademark_plan_id'],
                'doc_submission_id' => $inputs['doc_submission_id']
            ]);

            //if from a205
            if (!empty($inputs['from_page'])) {
                if ($inputs['from_page'] == A205) {
                    //update doc_submissions: flag_role = 2
                    $this->repository->update($docSubmission, [
                        'flag_role' => DocSubmission::FLAG_ROLE_2,
                    ]);

                    //update comment notices
                    $this->noticeService->updateComment(
                        Notice::FLOW_RESPONSE_REASON,
                        $inputs['content'] ?? null,
                        $comparisonTrademarkResult->trademark_id
                    );

                    // Update Notice at (No 82: F G)
                    $this->noticeDetailRepository->findByCondition([
                        'completion_date' => null,
                    ])->with('notice')->get()
                        ->where('type_acc', '!=', NoticeDetail::TYPE_USER)
                        ->where('notice.trademark_id', $inputs['trademark_id'])
                        ->where('notice.user_id', $inputs['user_id'])
                        ->whereIn('notice.flow', [Notice::FLOW_RESPONSE_REASON, Notice::FLOW_RENEWAL_BEFORE_DEADLINE])
                        ->filter(function ($item) {
                            if ($item->notice->flow == Notice::FLOW_RESPONSE_REASON
                                && in_array($item->notice->step, [Notice::STEP_3, Notice::STEP_4, Notice::STEP_5])) {
                                return true;
                            } elseif ($item->notice->flow == Notice::FLOW_RENEWAL_BEFORE_DEADLINE && $item->notice->step == null) {
                                return true;
                            }
                            return false;
                        })
                        ->map(function ($item) {
                            $item->update([
                                'completion_date' => Carbon::now(),
                            ]);
                        });

                    // Set response deadline
                    $responseDeadlineA000Top = $machingResult->calculateResponseDeadline(-31);
                    $responseDeadlineA000AnkenTop = $machingResult->calculateResponseDeadline(-31);
                    if (!empty($machingResult) && $planCorrespondence->type == PlanCorrespondence::TYPE_2) {
                        $responseDeadlineA000Top = $machingResult->calculateResponseDeadline(-36);
                        $responseDeadlineA000AnkenTop = $machingResult->calculateResponseDeadline(-36);
                    }

                    //No.90 send notice to seki
                    $this->noticeService->sendNotice([
                        'notices' => [
                            'trademark_id' => $inputs['trademark_id'],
                            'trademark_info_id' => null,
                            'flow' => Notice::FLOW_RESPONSE_REASON,
                            'step' => Notice::STEP_5,
                            'user_id' => $inputs['user_id'],
                            'created_at' => Carbon::now()
                        ],
                        'notice_details' => [
                            //A-000top
                           [
                               'type_acc' => NoticeDetail::TYPE_SUPERVISOR,
                               'target_page' => $routeA205,
                               'redirect_page' => $routeA205s,
                               'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                               'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                               'is_action' => NoticeDetail::IS_ACTION_TRUE,
                               'content' => '拒絶理由通知対応：提出書類確認・承認',
                               'attribute' => '所内処理',
                               'response_deadline' => $responseDeadlineA000Top,
                           ],
                            // Send Notice Seki: A-000anken_top
                            [
                                'type_acc' => NoticeDetail::TYPE_SUPERVISOR,
                                'target_page' => $routeA205,
                                'redirect_page' => $routeA205s,
                                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                                'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                                'content' => '責任者　拒絶理由通知対応：提出書類確認・承認',
                                'attribute' => '所内処理',
                                'response_deadline' => $responseDeadlineA000AnkenTop,
                            ],
                        ],
                    ]);
                } elseif (in_array($inputs['from_page'], [A205_HIKI, A205_SHU])) {
                    //if from a205 hiki || a205 shu
                    //update doc_submissions: flag_role = 2 & is_confirm = 1
                    $this->repository->update($docSubmission, [
                        'flag_role' => DocSubmission::FLAG_ROLE_2,
                        'is_confirm' => DocSubmission::IS_CONFIRM,
                    ]);

                    //send mail
                    SendGeneralMailJob::dispatch('emails.a205.a205-kakunin-mail', [
                        'to' => auth('admin')->user()->email,
                        'subject' => __('labels.profile_edit.edit_email.change_email_title2'),
                        'docSubmissionCmts' => $docSubmission ? $docSubmission->docSubmissionCmts : collect([]),
                        'trademark' => $comparisonTrademarkResult->trademark
                    ]);

                    if ($inputs['from_page'] == A205_HIKI) {
                        $routeTargetPage = route('admin.refusal.documents.increase', [
                            'id' => $inputs['comparison_trademark_result_id'],
                            'trademark_plan_id' => $inputs['trademark_plan_id'],
                            'doc_submission_id' => $inputs['doc_submission_id'],
                        ]);
                    } else {
                        //a205_shu
                        $routeTargetPage = route('admin.refusal.documents.edit.supervisor', [
                            'id' => $inputs['comparison_trademark_result_id'],
                            'trademark_plan_id' => $inputs['trademark_plan_id'],
                            'doc_submission_id' => $inputs['doc_submission_id'],
                        ]);
                    }

                    $redirectPage = route('user.refusal_documents_confirm', [
                        'comparison_trademark_result_id' => $comparisonTrademarkResult->id,
                        'trademark_plan_id' => $inputs['trademark_plan_id'],
                        'doc_submission_id' => $inputs['doc_submission_id'],
                    ]);

                    //update comment notices
                    $this->noticeService->updateComment(
                        Notice::FLOW_RESPONSE_REASON,
                        $inputs['content'] ?? null,
                        $comparisonTrademarkResult->trademark_id
                    );

                    //send notice
                    //update G No.90
                    $this->noticeDetailRepository->findByCondition([
                        'completion_date' => null,
                    ])->with('notice')->get()
                        ->where('type_acc', '!=', NoticeDetail::TYPE_USER)
                        ->where('notice.trademark_id', $inputs['trademark_id'])
                        ->where('notice.user_id', $inputs['user_id'])
                        ->whereIn('notice.flow', [Notice::FLOW_RESPONSE_REASON, Notice::FLOW_RENEWAL_BEFORE_DEADLINE])
                        ->filter(function ($item) {
                            if ($item->notice->flow == Notice::FLOW_RESPONSE_REASON
                                && in_array($item->notice->step, [Notice::STEP_3, Notice::STEP_4, Notice::STEP_5])) {
                                return true;
                            } elseif ($item->notice->flow == Notice::FLOW_RENEWAL_BEFORE_DEADLINE && $item->notice->step == null) {
                                return true;
                            }
                            return false;
                        })
                        ->map(function ($item) {
                            $item->update([
                                'completion_date' => Carbon::now(),
                            ]);
                        });

                    // Set response deadline
                    $responseDeadlineA000AnkenTop = $machingResult->calculateResponseDeadline(-31);
                    $responseDeadlineUser = $machingResult->calculateResponseDeadline(-32);
                    if (!empty($machingResult) && $planCorrespondence->type == PlanCorrespondence::TYPE_2) {
                        $responseDeadlineA000AnkenTop = $machingResult->calculateResponseDeadline(-36);
                        $responseDeadlineUser = $machingResult->calculateResponseDeadline(-37);
                    }

                    //No.91 Send Notice Seki: A-000anken_top
                    $this->noticeService->sendNotice([
                        'notices' => [
                            'trademark_id' => $inputs['trademark_id'],
                            'trademark_info_id' => null,
                            'flow' => Notice::FLOW_RESPONSE_REASON,
                            'step' => Notice::STEP_5,
                            'user_id' => $inputs['user_id'],
                            'created_at' => Carbon::now()
                        ],
                        'notice_details' => [
                            // A-000top
                            [
                                'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                                'target_page' => $routeTargetPage,
                                'redirect_page' => route('admin.application-detail.index', $comparisonTrademarkResult->trademark_id),
                                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                                'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                                'is_action' => true,
                                'content' => '事務担当　拒絶理由通知対応：提出書類提出作業中',
                                'attribute' => '特許庁へ',
                                'response_deadline' => $responseDeadlineA000AnkenTop,
                            ],
                            // A-000anken_top
                            [
                                'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                                'target_page' => $routeTargetPage,
                                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                                'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                                'content' => '事務担当　拒絶理由通知対応：提出書類提出作業中',
                                'attribute' => '特許庁へ',
                                'response_deadline' => $responseDeadlineA000AnkenTop,
                                'completion_date' => null,
                                'buttons' => [
                                    [
                                        "btn_type"  => NoticeDetailBtn::BTN_CREATE_HTML,
                                        "url"  => $routeA205s . '&type=' . VIEW,
                                        "from_page" => $inputs['from_page'],
                                    ],
                                ]
                            ],
                            // u000top
                            [
                                'target_id' => $inputs['user_id'],
                                'type_acc' => NoticeDetail::TYPE_USER,
                                'target_page' => $routeTargetPage,
                                'redirect_page' => $redirectPage,
                                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                                'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                                'is_action' => true,
                                'content' => '拒絶理由通知対応：提出書類確認',
                                'attribute' => null,
                                'response_deadline' => $responseDeadlineUser,
                            ],
                            // u000anken_top
                            [
                                'target_id' => $inputs['user_id'],
                                'type_acc' => NoticeDetail::TYPE_USER,
                                'target_page' => $routeTargetPage,
                                'redirect_page' => $redirectPage,
                                'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                                'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                                'content' => '拒絶理由通知対応：提出書類確認',
                                'attribute' => null,
                                'response_deadline' => $responseDeadlineUser,
                            ],
                        ],
                    ]);
                }
            }
            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            throw new \Exception($e);

            return false;
        }
    }

    /**
     * Create Session Redirect A205 Kakunin
     *
     * @param array $inputs
     * @return String
     */
    public function createSessionRedirectA205Kakunin(array $inputs): String
    {
        $key = Str::random(11);
        $sessionData['from_page'] = $inputs['from_page'];
        $sessionData['content'] = $inputs['content'] ?? null;
        Session::put($key, $sessionData);

        return $key;
    }

    /**
     * Save data submission attach properties
     *
     * @param $dataProperties
     * @param $docSubmissionId
     * @return void
     */
    public function saveDataSubmissionAttachProperties($dataProperties, $docSubmissionId)
    {
        foreach ($dataProperties as $key => $item) {
            $docSubmissionAttachProperty = $this->docSubmissionAttachPropertyRepository->updateOrCreate([
                'id' => $item['doc_submission_attach_property_id'] ?? 0,
            ], [
                'doc_submission_id' => $docSubmissionId,
                'name' => $item['name']
            ]);

            //Convert data attach file
            $dataAttachOld = array_filter($item['data-attach'], function ($value) {
                return !empty($value['doc_submission_attachment_id']);
            });
            $dataAttachNew = array_values(array_filter($item['data-attach'], function ($value) {
                return empty($value['doc_submission_attachment_id']);
            }));

            foreach ($dataAttachNew as $k => $itemNew) {
                $dataAttachNew[$k]['file'] = $item['attach_file'][$k];
            }
            $dataResult = array_merge($dataAttachOld, $dataAttachNew);
            $dataAttachNewCreate = [];
            foreach ($dataResult as $i => $itemAttach) {
                //update data old
                if (!empty($itemAttach['doc_submission_attachment_id'])) {
                    $modelAttach = $this->docSubmissionAttachmentRepository->findByCondition([
                        'id' => $itemAttach['doc_submission_attachment_id'],
                        'doc_submission_attach_property_id' => $docSubmissionAttachProperty->id,
                    ])->first();
                    if ($modelAttach) {
                        $this->docSubmissionAttachmentRepository->update($modelAttach, ['file_no' => $itemAttach['file_no']]);
                    }
                } else {
                    //file < 3MB
                    $file = $itemAttach['file'];
                    if ($file->getSize() <= 1024 * 1024 * 3) {
                        //get folder file
                        $folderFile = FOLDER_UPLOAD_FILE_A205 . auth('admin')->user()->id;
                        $filePath = FileHelper::uploads($file, [], $folderFile);

                        if (isset($filePath[0]) && !empty($filePath[0])) {
                            $dataAttachNewCreate[$i]['doc_submission_attach_property_id'] = $docSubmissionAttachProperty->id;
                            $dataAttachNewCreate[$i]['attach_file'] = $filePath[0]['filepath'];
                            $dataAttachNewCreate[$i]['file_no'] = $itemAttach['file_no'];
                        }
                    }
                }
            }
            //store new data: doc_submission_attachments
            $this->docSubmissionAttachmentRepository->insert($dataAttachNewCreate);
        }

        return $docSubmissionId;
    }
}
