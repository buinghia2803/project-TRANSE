<?php

namespace App\Services;

use App\Helpers\FileHelper;
use App\Jobs\SendGeneralMailJob;
use App\Models\Admin;
use App\Models\History;
use App\Models\Notice;
use App\Models\NoticeDetail;
use App\Models\QuestionAnswer;
use App\Models\User;
use App\Repositories\HistoryRepository;
use App\Repositories\QARepository;
use App\Repositories\UserRepository;
use App\Services\Common\NoticeService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class QAService extends BaseService
{
    protected QARepository $qARepository;
    private $noticeDetailService;
    private $noticeService;
    private $historyRepository;
    private $userRepository;
    /**
     * Initializing the instances and variables
     *
     * @param   QARepository $qARepository
     */
    public function __construct(
        QARepository $qARepository,
        HistoryRepository $historyRepository,
        UserRepository $userRepository,
        NoticeService $noticeService,
        NoticeDetailService $noticeDetailService
    )
    {
        $this->repository = $qARepository;
        $this->historyRepository = $historyRepository;
        $this->userRepository = $userRepository;
        $this->noticeService = $noticeService;
        $this->noticeDetailService = $noticeDetailService;
    }

    /**
     * Get Detail Question Answer By Id
     *
     * @param $request
     * @return Collection
     */
    public function getQuestionAnswersById($id)
    {
        return $this->repository->getQuestionAnswersById($id);
    }

    /**
     * Get Detail Question Answer By Id
     *
     * @param $request
     * @return Collection
     */
    public function getListQuestionAnswers($id)
    {
        return $this->repository->getListQuestionAnswers($id);
    }

    /**
     * Create Question Answers
     *
     * @param $request
     * @return Collection
     */
    public function createQuestionAnswers($request)
    {
        DB::beginTransaction();
        try {
            $filepath = [];
            if ($request->question_attaching_file) {
                foreach ($request->question_attaching_file as $key => $value) {
                    if (count($request->question_attaching_file) > 0) {
                        $file = $value;
                        $image = FileHelper::uploads($file, [], '/uploads/question-answers');
                        $filepath[] = $image[0]['filepath'] ?? null;
                    };
                }
            }
            $stringFilePath = json_encode($filepath);
            // Check Send Mail and Create Notice To Admin
            $admin = Admin::where('role', Admin::ROLE_ADMIN_TANTO)->first();
            if (!$admin) {
                return null;
            }
            $condition = [
                'user_id' => Auth::user()->id,
                'admin_id' => $admin->id,
                'question_content' => $request->question_content,
                'question_type' => QuestionAnswer::QUESTION_FROM_CUSTOMERS,
                'is_confirm' => QuestionAnswer::IS_NOT_CONFIRM,
                'created_at' => Carbon::now(),
            ];

            if (isset($request->submitConfirm)) {
                // Create Record Question Answers From User
                $condition['question_date'] = Carbon::now();

                SendGeneralMailJob::dispatch('emails.qa-mail', [
                    'to' => auth()->user()->getListMail(),
                    'subject' => __('labels.qa.title_send_email_QA'),
                ]);
            } elseif (isset($request->submitSave)) {
                $condition['question_date'] = null;
            }
            if ($request->question_answer_draft_id) {
                $question = $this->repository->find($request->question_answer_draft_id);
                $condition['question_attaching_file'] = $request['question_attaching_file'] ? $stringFilePath : $question->question_attaching_file;
                $question->update($condition);

                DB::commit();
            } else {
                $condition['question_attaching_file'] = $stringFilePath;
                $question = $this->create($condition);

                DB::commit();
            }

            if (isset($request->submitConfirm)) {
                // Send Notice
                $this->noticeService->sendManager([
                    'notices' => [
                        'flow' => Notice::FLOW_QA,
                        'user_id' => Auth::guard('web')->id(),
                    ],
                    'notice_details' => [
                        [
                            'content' => 'Q&A：お客様からのご質問​・回答作成',
                            'target_page' => route('user.qa.02.qa'),
                            'redirect_page' => route('admin.question-answers.index', [
                                'user_id' => Auth::guard('web')->id(),
                                'qa_id' => $question->id,
                            ]),
                            'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                            'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                            'is_action' => true,
                        ],
                    ],
                ]);

                return CONFIRM_QA;
            } elseif (isset($request->submitSave)) {
                return DRAFT_QA;
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            $arrFilePath = json_decode($stringFilePath);

            foreach ($arrFilePath as $path) {
                FileHelper::unlink($path);
            }

            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Get Take 5 Question From Ams
     *
     * @param $authId
     * @return Collection
     */
    public function getQuestionFromAmsService($authId)
    {
        return $this->repository->getQuestionFromAmsRepository($authId);
    }

    /**
     * Get Draft
     *
     * @param $authId
     * @return Collection
     */
    public function getDraftQuestionUser($authId)
    {
        return $this->repository->getDraftQuestionUser($authId);
    }

    /**
     * Get Take 5 Question From Ams
     *
     * @param integer $authId
     * @return Collection
     */
    public function getQuestionFromAmsInputService(int $authId)
    {
        return $this->repository->getQuestionFromAmsInputRepository($authId);
    }

    /**
     * Create Answers The Question
     *
     * @param $request
     * @return Collection
     */
    public function createAnswerToUserService($request)
    {
        $filepath = [];
        if ($request->hasFile('answer_attaching_file')) {
            foreach ($request['answer_attaching_file'] as $key => $value) {
                if (count($request['answer_attaching_file']) > 0) {
                    $file = $value;
                    $image = FileHelper::uploads($file, [], '/uploads/question-answers');
                    $filepath[] = $image[0]['filepath'] ?? null;
                };
            }
        }
        $stringFilePath = json_encode($filepath);
        $condition = [
            'answer_content' => $request['answer_content'] ?? '',
            'answer_attaching_file' => $stringFilePath,
            'answer_date' => isset($request->submit) ? now() : null,
            'is_confirm' => QuestionAnswer::IS_CONFIRM,
        ];
        $recordsQuestionAnswers = $this->repository->find($request['question_answer_id']);
        $recordsQuestionAnswers->update($condition);
        $authId = Auth::user()->id ?? 1;
        if (isset($request->submit)) {
            SendGeneralMailJob::dispatch('emails.answer-the-question-mail-qa2', [
                'to' => Auth::user()->getListMail(),
                'subject' => __('labels.qa.title_send_email_ATQ2'),
            ]);
            // Update is_answer of notice_details
            $redirectPage = route('user.qa.02.kaito', ['id' => $recordsQuestionAnswers->id]);
            $redirectPage = str_replace(request()->root(), '', $redirectPage);

            $noticeDetailPeriou = $this->noticeDetailService->findByCondition([
                'type_acc' => NoticeDetail::TYPE_USER,
                'is_answer' => NoticeDetail::IS_NOT_ANSWER,
            ], ['notice'])->orderBy('id', 'DESC')->get()
                ->where('notice.flow', Notice::FLOW_QA)
                ->first();
            if ($noticeDetailPeriou) {
                $noticeDetailPeriou->update([
                    'is_answer' => NoticeDetail::IS_ANSWER,
                ]);
            }

            // Send Notice
            $this->noticeService->sendNotice([
                'notices' => [
                    'flow' => Notice::FLOW_QA,
                    'user_id' => $authId,
                ],
                'notice_details' => [
                    // Send Notice Tantou
                    [
                        'type_acc' => NoticeDetail::TYPE_MANAGER,
                        'target_page' => route('user.qa.02.kaito', ['id' => $recordsQuestionAnswers->id]),
                        'redirect_page' => route('admin.question.answers.show.kaito.list', [
                            'user_id' => $authId,
                            'qa_id' => $recordsQuestionAnswers->id,
                        ]),
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                        'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                        'is_action' => true,
                        'content' => 'Q&A：お客様からののご回答',
                        'is_answer' => NoticeDetail::IS_ANSWER
                    ],
                ],
            ]);
        }
    }

    /**
     * Get Question Answers User Send To Tantou
     *
     * @param int $userId
     * @param int $qaId
     * @return  Collection
     */
    public function getQuestionAnswersToTantou(int $userId, int $qaId)
    {
        return $this->repository->getQuestionAnswersToTantou($userId, $qaId);
    }

    /**
     * Get Question Answers User Send To Tantou
     *
     * @param int $userId
     * @param int $qaId
     * @return  Collection
     */
    public function getQuestionAnswersToSeki(int $userId, int $qaId)
    {
        return $this->repository->getQuestionAnswersToSeki($userId, $qaId);
    }

    /**
     * Get Question Answers User Send To Tantou Input
     *
     * @param int $userId
     * @param int $qaId
     * @return  Collection
     */
    public function getQuestionAnswersToTantouInput(int $userId, int $qaId)
    {
        return $this->repository->getQuestionAnswersToTantouInput($userId, $qaId);
    }

    /**
     * Get Question Answers Tantou Send To Seki Check
     *
     * @param int $userId
     * @param int $qaId
     * @return  Collection
     */
    public function getQuestionToSeki(int $userId, int $qaId)
    {
        return $this->repository->getQuestionToSeki($userId, $qaId);
    }

    /**
     * Get Question Answers Input User Send To Tantou
     *
     * @param int $userId
     *
     * @return  Collection
     */
    public function getQuestionAnswersInputToTantou(int $userId)
    {
        return $this->repository->getQuestionAnswersInputToTantou($userId);
    }

    /**
     * Get Detail User
     *
     * @param $id
     * @return Collection
     */
    public function getDetailUserService($id)
    {
        return $this->userRepository->findOrFail($id, ['nation', 'contactNation', 'prefecture', 'contactPrefecture', 'prefecture.nations']);
    }

    /**
     * Get Admin Send Question Answers To User
     *
     * @return  Collection
     */
    public function getQuestionAnswersToUser($userId)
    {
        return $this->repository->getQuestionAnswersToUser($userId);
    }

    /**
     * Get Admin Send Question Answers To User
     *
     * @return  Collection
     */
    public function getQuestionFromSeki($userId)
    {
        return $this->repository->getQuestionFromSeki($userId);
    }

    /**
     * Get Admin Send Question Answers To User
     *
     * @return  Collection
     */
    public function questionAnswersExist($userId)
    {
        return $this->repository->getQuestionFromSekiExist($userId);
    }

    /**
     * Get Admin Send Question Answers To User
     *
     * @return  Collection
     */
    public function getQuestionExistAnswer($userId)
    {
        return $this->repository->getQuestionExistAnswer($userId);
    }

    /**
     * Get Admin Send Question Answers To User
     *
     * @return  Collection
     */
    public function getListQuestionService($userId)
    {
        return $this->repository->getListQuestionRepository($userId);
    }

    /**
     * Get Admin Send Question Answers To User
     *
     * @return  Collection
     */
    public function getFirstQuestionService($userId)
    {
        return $this->repository->getFirstQuestionRepository($userId);
    }

    /**
     * Update Answers Admin , Create Record Notify and History
     *
     * @return void
     */
    public function updateAnswerAdmin($request, $userId)
    {
        try {
            DB::beginTransaction();
            $questionAnswers = $this->repository->getQuestionToSeki($userId, $request['qa_id'])->toArray();
            $countQuestionAnswers = count($questionAnswers);
            for ($i = 0; $i < $countQuestionAnswers; $i++) {
                $answersContentsEdit[] = $request['answer_content_edit_' . $questionAnswers[$i]['id']];
                $answersContentsDecision[] = $request['answer_content_decision_' . $questionAnswers[$i]['id']];
                $officeComments[] = $request['office_comments_' . $questionAnswers[$i]['id']];
                if ($request['submitEntry'] == DRAFT_QA) {
                    $condition[] = [
                        'answer_content_edit' => $answersContentsEdit[$i],
                        'answer_content_decision' => $answersContentsDecision[$i],
                        'answer_date' => now(),
                        'is_confirm' => QuestionAnswer::IS_NOT_CONFIRM,
                        'office_comments' => $officeComments[$i],
                    ];
                } elseif ($request['submitEntry'] == CONFIRM_QA) {
                    $condition[] = [
                        'answer_content' => $answersContentsDecision[$i],
                        'answer_content_edit' => null,
                        'answer_content_decision' => null,
                        'answer_date' => now(),
                        'is_confirm' => QuestionAnswer::IS_CONFIRM,
                        'office_comments' => $officeComments[$i],
                    ];
                }
                $recordsQuestionAnswers[] = $this->repository->find($questionAnswers[$i]['id']);
                $this->repository->update($recordsQuestionAnswers[$i], $condition[$i]);

                $conditionCreateHistory = [
                    'admin_id' => Auth::user()->id ?? 1,
                    'target_id' => $questionAnswers[$i]['id'],
                    'page' => History::PAGE_ANSWERS_ADMIN_ROLE_SEKI,
                    'action' => History::ACTION_ANSWERS_ADMIN_ROLE_SEKI,
                    'type' => History::TYPE_ANSWERS_ADMIN,
                ];
                $this->historyRepository->create($conditionCreateHistory, $userId);
            }

            if (isset($request['submitConfirm'])) {
                // Send Mail
                $user = User::find($userId);
                $adminSekiJimuEmail = Admin::whereIn('role', [Admin::ROLE_ADMIN_SEKI, Admin::ROLE_ADMIN_JIMU])->pluck('email')->toArray();
                if (!empty($user)) {
                    SendGeneralMailJob::dispatch('emails.a000qa02s-send-mail', [
                        'to' => $user->getListMail(),
                        'subject' => __('labels.qa.title_send_email_QA2'),
                        'bcc' => $adminSekiJimuEmail,
                    ]);
                }
            }
            DB::commit();
            if ($request['submitEntry'] == DRAFT_QA) {
                return DRAFT_QA;
            } elseif ($request['submitEntry'] == CONFIRM_QA) {
                return CONFIRM_QA;
            }
        } catch (Exception $e) {
            DB::rollback();
            Log::error($e);

            return false;
        }
    }

    /**
     * Create Question Answers From Admin
     *
     * @param $request
     * @param $id
     * @return void
     */
    public function createOrUpdateQuestion($request, $id)
    {
        try {
            DB::beginTransaction();
            $questionDraft = $this->repository->getQuestionAnswersToUser($id);
            $auth = Auth::user();
            $filepath = [];
            if (!$auth) {
                return null;
            }
            // Check File
            if ($request->question_attaching_file) {
                foreach ($request->question_attaching_file as $key => $value) {
                    if (count($request->question_attaching_file) > 0) {
                        $file = $value;
                        $image = FileHelper::uploads($file, [], '/uploads/question-answers');
                        $filepath[] = $image[0]['filepath'] ?? null;
                    };
                }
            }
            $stringFilePath = json_encode($filepath);

            $condition = [
                'user_id' => $id,
                'admin_id' => $auth->id,
                'question_content' => $request['question_content'] ?? '',
                'question_type' => QuestionAnswer::QUESTION_FROM_AMS,
                'response_deadline_user' => $request['response_deadline_user'] ?? null,
                'response_deadline_admin' => $request['response_deadline_admin'] ?? null,
                'office_comments' => $request['office_comments'] ?? null,
                'question_date' => now(),
                'is_confirm' => Admin::IS_NOT_CONFIRM,
            ];

            if ($request['submitEntry'] == DRAFT_QA) {
                $condition['flag_role'] = QuestionAnswer::FLAG_ROLE_TANTOU;
            } elseif ($request['submitEntry'] == CONFIRM_QA) {
                $condition['flag_role'] = QuestionAnswer::FLAG_ROLE_SEKI;
            }

            if ($questionDraft) {
                $question = $this->repository->find($questionDraft->id);
                $condition['question_attaching_file'] = $request['question_attaching_file'] ? $stringFilePath : $question->question_attaching_file;
                $question->update($condition);
            } else {
                $condition['question_attaching_file'] = $stringFilePath;
                $question = $this->repository->create($condition);
            }

            $conditionCreateHistory = [
                'admin_id' => $auth->id,
                'target_id' => $question->id,
                'page' => History::PAGE_CREATE_QUESTION,
                'action' => History::ACTION_ANSWERS_ADMIN,
                'type' => History::TYPE_ANSWERS_ADMIN,
            ];
            $this->historyRepository->create($conditionCreateHistory);

            if ($request['submitEntry'] == CONFIRM_QA) {
                // Send Notice
                $this->noticeService->sendSupervisor([
                    'notices' => [
                        'flow' => Notice::FLOW_QA,
                        'user_id' => $id,
                    ],
                    'notice_details' => [
                        [
                            'content' => 'Q&A：AMSからの質問・確認',
                            'target_page' => route('admin.question.answers.from.ams', $id),
                            'redirect_page' => route('admin.question.answers.from.ams.s', [
                                'id' => $id,
                                'qa_id' => $question->id,
                            ]),
                            'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                            'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                            'is_action' => true,
                            'response_deadline' => $condition['response_deadline_admin'] ?? null,
                        ],
                    ],
                ]);
            }

            DB::commit();
            if ($request['submitEntry'] == DRAFT_QA) {
                return DRAFT_QA;
            } elseif ($request['submitEntry'] == CONFIRM_QA) {
                return CONFIRM_QA;
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            $arrFilePath = json_decode($stringFilePath);
            foreach ($arrFilePath as $path) {
                FileHelper::unlink($path);
            }

            return false;
        }
    }

    /**
     * Modify Question From Admin Role Seki
     *
     * @param $request
     * @param $id
     * @return void
     */
    public function modifyQuestion($request, $id)
    {
        try {
            DB::beginTransaction();
            $arrFilePath = [];
            if ($request->hasFile('question_attaching_file')) {
                foreach ($request['question_attaching_file'] as $attachFile) {
                    $image = FileHelper::uploads($attachFile, [], '/uploads/question-answers');
                    $filepath = $image[0]['filepath'] ?? null;
                    $arrFilePath[] = $filepath;
                }
            };
            $stringFilePath = null;
            if (count($arrFilePath)) {
                $stringFilePath = json_encode($arrFilePath);
            }
            $dataQuestionAnswer = [
                'question_content_edit' => isset($request->submitConfirm) ? null : $request['question_content_edit'],
                'question_content_decision' => isset($request->submitConfirm) ? null : $request['question_content_decision'],
                'flag_role' => QuestionAnswer::FLAG_ROLE_SEKI,
                'is_confirm' => isset($request->submitConfirm) ? QuestionAnswer::IS_CONFIRM : QuestionAnswer::IS_NOT_CONFIRM,
                'question_date' => now(),
            ];

            if ($stringFilePath) {
                $dataQuestionAnswer['question_attaching_file'] = $stringFilePath;
            }
            if (isset($request->submitConfirm)) {
                $dataQuestionAnswer['question_content'] = $request['question_content_decision'];
            }
            $questionAnswers = $this->repository->find($request['qa_id']);
            $questionAnswers->update($dataQuestionAnswer);
            // Create history
            $conditionCreateHistory = [
                'admin_id' => Auth::user()->id ?? 1,
                'target_id' => $request['qa_id'],
                'page' => History::PAGE_ANSWERS_AMS_ADMIN_ROLE_SEKI,
                'action' => History::ACTION_ANSWERS_ADMIN,
                'type' => History::TYPE_ANSWERS_ADMIN,
            ];
            $this->historyRepository->create($conditionCreateHistory);
            // }
            if (isset($request->submitConfirm)) {
                // Send Mail
                $user = User::find($id);
                $adminSekiJimuEmail = Admin::whereIn('role', [Admin::ROLE_ADMIN_SEKI, Admin::ROLE_ADMIN_JIMU])->pluck('email')->toArray();
                if (!empty($user)) {
                    SendGeneralMailJob::dispatch('emails.create-question-mail-ams', [
                        'to' => $user->getListMail(),
                        'subject' => __('labels.qa.title_send_email_QA_AMS'),
                        'bcc' => $adminSekiJimuEmail,
                    ]);
                }
                $targetPage = route('admin.question.answers.from.ams', ['user_id' => $id]);
                $targetPage = str_replace(request()->root(), '', $targetPage);
                $noticeDetailPeriou = $this->noticeDetailService->findByCondition([
                    'type_acc' => NoticeDetail::TYPE_SUPERVISOR,
                    'target_page' => $targetPage,
                    'completion_date' => null,
                ], ['notice'])->orderBy('id', 'DESC')->get()
                    ->where('notice.flow', Notice::FLOW_QA);
                if ($noticeDetailPeriou) {
                    $noticeDetailPeriou->map(function ($query) {
                        $query->update([
                            'completion_date' => Carbon::now(),
                        ]);
                    });
                }
                // Send Notice
                $this->noticeService->sendUser($id, [
                    'notices' => [
                        'flow' => Notice::FLOW_QA,
                        'user_id' => $id,
                    ],
                    'notice_details' => [
                        [
                            'content' => 'Q&A：AMSからの質問・回答作成',
                            'target_page' => route('admin.question.answers.from.ams.s', [
                                'id' => $id,
                                'qa_id' => $questionAnswers->id,
                            ]),
                            'redirect_page' => route('user.qa.02.kaito', ['id' => $questionAnswers->id]),
                            'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                            'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                            'is_action' => true,
                            'response_deadline' => $questionAnswers->response_deadline_admin ?? null,
                        ],
                    ],
                ]);
            }

            DB::commit();
            if ($request['submitSave']) {
                return DRAFT_QA;
            } elseif ($request['submitConfirm']) {
                return CONFIRM_QA;
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            $arrFilePath = json_decode($stringFilePath);
            foreach ($arrFilePath as $path) {
                FileHelper::unlink($path);
            }

            return redirect()->back()->with([
                'error' => __('Error'),
            ]);
        }
    }

    /**
     * Update Answers To Seki
     *
     * @param $request
     * @param $id
     * @return void
     */
    public function updateAnswerToSeki($request, $id)
    {
        try {
            DB::beginTransaction();
            $answerInput = $this->repository->getQuestionAnswersToTantouInput($id, $request['qa_id'])->toArray();
            $countAnswerInput = count($answerInput);
            for ($i = 0; $i < $countAnswerInput; $i++) {
                $answersContents[] = $request['answer_content_' . $answerInput[$i]['id']];
                $officeComments[] = $request['office_comment_' . $answerInput[$i]['id']];

                if ($request['submitEntry'] == DRAFT_QA) {
                    $condition[] = [
                        'answer_content' => $answersContents[$i],
                        'answer_date' => now(),
                        'office_comments' => $officeComments[$i],
                        'is_confirm' => Admin::IS_NOT_CONFIRM,
                        'flag_role' => QuestionAnswer::FLAG_ROLE_TANTOU
                    ];
                } elseif ($request['submitEntry'] == CONFIRM_QA) {
                    $condition[] = [
                        'answer_content' => $answersContents[$i],
                        'answer_date' => now(),
                        'office_comments' => $officeComments[$i],
                        'is_confirm' => Admin::IS_NOT_CONFIRM,
                        'flag_role' => QuestionAnswer::FLAG_ROLE_SEKI
                    ];
                }
                $recordsQuestionAnswers[] = $this->repository->find($answerInput[$i]['id']);
                $this->repository->update($recordsQuestionAnswers[$i], $condition[$i]);
                $conditionCreateHistory = [
                    'admin_id' => Auth::user()->id,
                    'target_id' => $answerInput[$i]['id'],
                    'page' => History::PAGE_ANSWERS_ADMIN,
                    'action' => History::ACTION_ANSWERS_ADMIN,
                    'type' => History::TYPE_ANSWERS_ADMIN,
                ];
                $this->historyRepository->create($conditionCreateHistory, $id);
            }

            if ($request['submitEntry'] == CONFIRM_QA) {
                $redirectPage = route('admin.question-answers.index', [
                    'user_id' => $id,
                    'qa_id' => $request['qa_id'],
                ]);
                $redirectPage = str_replace(request()->root(), '', $redirectPage);

                $noticeDetailPeriou = $this->noticeDetailService->findByCondition([
                    'type_acc' => NoticeDetail::TYPE_MANAGER,
                    'redirect_page' => $redirectPage,
                    'completion_date' => null,
                ], ['notice'])->orderBy('id', 'DESC')->get()
                    ->where('notice.flow', Notice::FLOW_QA)
                    ->first();
                if ($noticeDetailPeriou) {
                    $noticeDetailPeriou->update([
                        'completion_date' => Carbon::now(),
                    ]);
                }
                // Send Notice
                $this->noticeService->sendSupervisor([
                    'notices' => [
                        'flow' => Notice::FLOW_QA,
                        'user_id' => $id,
                    ],
                    'notice_details' => [
                        [
                            'content' => 'Q&A：お客様からのご質問​・回答作成・確認',
                            'target_page' => route('admin.question-answers.index', ['user_id' => $id, 'qa_id' => $request['qa_id']]),
                            'redirect_page' => route('admin.question.answers.02s', ['user_id' => $id, 'qa_id' => $request['qa_id']]),
                            'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                            'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                            'is_action' => true,
                        ],
                    ],
                ]);
            }

            DB::commit();
            if ($request['submitEntry'] == DRAFT_QA) {
                return DRAFT_QA;
            } elseif ($request['submitEntry'] == CONFIRM_QA) {
                return CONFIRM_QA;
            }
        } catch (Exception $e) {
            Log::error($e);
            DB::rollback();
            return false;
        }
    }
}
