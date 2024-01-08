<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendGeneralMailJob;
use App\Models\Admin;
use App\Models\Notice;
use App\Models\NoticeDetail;
use App\Models\QuestionAnswer;
use App\Models\Trademark;
use App\Models\User;
use App\Services\Common\NoticeService;
use App\Services\NoticeDetailService;
use App\Services\QAService;
use App\Services\UserService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QuestionAnswersController extends Controller
{
    /**
     * @var UserService $userService
     */
    protected $userService;

    /**
     * @var QAService $qAService
     */
    protected $qAService;
    private NoticeService $noticeService;

    /**
     * Constructor
     *
     * @param UserService  $userService  UserService.
     * @param QAService $qAService QAService.
     *
     * @return void
     */
    public function __construct(
        UserService $userService,
        QAService $qAService,
        NoticeService $noticeService,
        NoticeDetailService $noticeDetailService
    )
    {
        $this->userService = $userService;
        $this->qAService = $qAService;
        $this->noticeService = $noticeService;
        $this->noticeDetailService = $noticeDetailService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param integer $userId
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, int $userId)
    {
        $qaId = $request->qa_id;
        if (!isset($qaId)) {
            abort(404);
        }

        $questionAnswers = $this->qAService->getQuestionAnswersToTantou($userId, $qaId);
        $questionAnswersInput = $this->qAService->getQuestionAnswersToTantouInput($userId, $qaId);
        $userInfo = $this->userService->find($userId);
        if (!$userInfo) {
            abort(404);
        }

        return view('admin.modules.Q&A.a000qa02', compact('questionAnswers', 'questionAnswersInput', 'userInfo', 'qaId'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param integer                  $userId
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, int $userId)
    {
        $roleAdmin = Auth::user()->role;
        if ($roleAdmin != Admin::ROLE_ADMIN_TANTO) {
            abort(403);
        }
        $answerAdmin = $this->qAService->updateAnswerToSeki($request->all(), $userId);
        if ($answerAdmin == DRAFT_QA) {
            return redirect()->back()->with('message', __('messages.general.QA_save'))->withInput();
        } else {
            return redirect()->route('admin.home')->with('message', __('messages.general.tantou_send_to_seki'))->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param integer $userId
     * @return \Illuminate\Http\Response
     */
    public function show(int $userId)
    {
        $userInfo = $this->userService->find($userId, ['prefecture', 'nation', 'contactNation', 'contactPrefecture']);
        $link = route('admin.question-answers.index', ['user_id' => $userId]);
        $contactTypeAcc = [
            CONTACT_TYPE_ACC_GROUP,
            CONTACT_TYPE_ACC_INDIVIDUAL,
        ];

        $infoTypeAcc = [
            INFO_TYPE_ACC_GROUP,
            INFO_TYPE_ACC_INDIVIDUAL,
        ];
        if (!$userInfo) {
            abort(404);
        }

        return view('admin.modules.Q&A.a001kaiin', compact('userInfo', 'link', 'contactTypeAcc', 'infoTypeAcc'));
    }

    public function ajaxEditNameDetail(Request $request)
    {
        try {
            DB::beginTransaction();
            $dataUpdate = [];
            foreach ($request->all() as $key => $value) {
                if ($value == null) {
                    continue;
                }
                $dataUpdate[$key] = $value;
            }

            $userInfo = $this->userService->find($request->userId);
            $updated = $userInfo->update($dataUpdate);

            if ($updated) {
                DB::commit();

                return response()->json(['status' => true], 200);
            }

            return response()->json(['status' => false], 400);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);

            return response()->json(['status' => false], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param integer $userId
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function showQuestionAnswers02s(Request $request, int $userId)
    {
        $qaId = $request->qa_id;
        if (!isset($qaId)) {
            abort(404);
        }

        $questionAnswers = $this->qAService->getQuestionAnswersToSeki($userId, $qaId);
        $questionAnswersInput = $this->qAService->getQuestionToSeki($userId, $qaId);
        $userInfo = $this->userService->find($userId);
        if (!$userInfo) {
            abort(404);
        }

        return view('admin.modules.Q&A.a000qa02s', compact('questionAnswers', 'questionAnswersInput', 'userInfo', 'qaId'));
    }

    /**
     * Display the specified resource.
     *
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function showQuestionAnswersFromAms($userId)
    {
        $roleAdmin = Auth::user()->role;
        $arrQuestionFile = '';
        $questionAnswers = $this->qAService->getQuestionAnswersToUser($userId);
        if ($questionAnswers) {
            $arrQuestionFile = json_decode($questionAnswers->question_attaching_file);
        }
        $questionAnswersExist = $this->qAService->getQuestionExistAnswer($userId);
        $userInfo = $this->userService->find($userId);
        if (!$userInfo) {
            abort(404);
        }

        return view('admin.modules.Q&A.a000qa_from_ams', compact('questionAnswers', 'userInfo', 'questionAnswersExist', 'arrQuestionFile'));
    }

    /**
     * Display the specified resource.
     *
     * @param integer $userId - {user_id}?qa_id={question_answers.id}
     * @return \Illuminate\Http\Response
     */
    public function showKaitoList(Request $request, int $userId)
    {
        $params = $request->all();
        if (empty($params['qa_id'])) {
            abort(404);
        }
        $conditionFirst = [
            'user_id' => $userId,
            'qa_id' => $params['qa_id']
        ];
        $conditionList = [
            'user_id' => $userId,
            'is_confirm' => QuestionAnswer::IS_CONFIRM,
            'flag_role' => QuestionAnswer::FLAG_ROLE_SEKI,
        ];
        if (isset($request['qa_id'])) {
            $conditionFirst['id'] = $request['qa_id'];
            $conditionList['more_ids'] = $request['qa_id'];
        }

        $firstQuestion = $this->qAService->findByCondition($conditionFirst)->first();
        $listQuestion = $this->qAService->findByCondition($conditionList)
            ->orderBy('id', SORT_TYPE_DESC)
            ->paginate(PAGINATE_NUMBER);

        $userInfo = $this->userService->find($userId);
        $paginateNumber = PAGINATE_NUMBER;
        $qAId = $params['qa_id'];
        if (!$userInfo) {
            abort(404);
        }

        // Update Notice
        $redirectPage = route('admin.question.answers.show.kaito.list', [
            'user_id' => $userId,
            'qa_id' => $params['qa_id']
        ]);
        $redirectPage = str_replace(request()->root(), '', $redirectPage);

        $stepBeforeNotice = $this->noticeDetailService->findByCondition([
            'redirect_page' => $redirectPage,
            'completion_date' => null,
        ])->with('notice')->get()
            ->where('notice.user_id', $userId)
            ->where('notice.flow', Notice::FLOW_QA);
        $stepBeforeNotice->map(function ($item) {
            $item->update([
                'completion_date' => now(),
            ]);
        });

        $backPage = route('admin.question-answers.index', ['user_id' => $userId, 'qa_id' => $request['qa_id']]);

        return view('admin.modules.Q&A.a000qa03kaito_list', compact('firstQuestion', 'listQuestion', 'userInfo', 'paginateNumber', 'qAId', 'backPage'));
    }

    /**
     * Display the specified resource.
     *
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function showQuestionAnswersFromAmsS(Request $request, $userId)
    {
        $roleAdmin = Auth::user()->role;
        $questionAnswer = $this->qAService->findByCondition([
            'user_id' => $userId,
            'id' => $request['qa_id'],
        ])->first();

        $listQuestionAnswers = $this->qAService->findByCondition([
            'user_id' => $userId,
            'more_ids' => $request['qa_id'],
            'is_confirm' => QuestionAnswer::IS_CONFIRM,
            'flag_role' => QuestionAnswer::FLAG_ROLE_SEKI,
        ])->orderBy('id', SORT_TYPE_DESC)
            ->take(QuestionAnswer::QUESTION_ANSWERS_NEWEST)
            ->get();
        $userInfo = $this->userService->find($userId);

        if (!$userInfo) {
            abort(404);
        }

        return view('admin.modules.Q&A.a000qa_from_ams_s', compact('questionAnswer', 'userInfo', 'listQuestionAnswers'));
    }

    /**
     * Update Question Answers From Admin Seki
     *
     * @param \Illuminate\Http\Request $request
     * @param integer                  $id
     * @return \Illuminate\Http\Response
     */
    public function updateQuestionAnswers(Request $request, int $id)
    {
        $roleAdmin = Auth::user()->role;
        if ($roleAdmin != ROLE_SUPERVISOR) {
            abort(403);
        }
        $answerAdmin = $this->qAService->updateAnswerAdmin($request->all(), $id);
        if (!$answerAdmin) {
            return redirect()->back()->with('error', 'not found');
        }
        if ($request['submitEntry'] == CONFIRM_QA) {
            // Send Notice
            $targetPageOfNoticeDetailPeriou = route('admin.question-answers.index', ['user_id' => $id, 'qa_id' => $request['qa_id']]);
            $targetPageOfNoticeDetailPeriou = str_replace(request()->root(), '', $targetPageOfNoticeDetailPeriou);
            $noticeDetailPeriou = $this->noticeDetailService->findByCondition([
                'type_acc' => NoticeDetail::TYPE_SUPERVISOR,
                'target_page' => $targetPageOfNoticeDetailPeriou,
                'completion_date' => null,
            ], ['notice'])->get()
                ->where('notice.flow', Notice::FLOW_QA);
            if ($noticeDetailPeriou) {
                $noticeDetailPeriou->map(function ($query) {
                    $query->update([
                        'completion_date' => Carbon::now(),
                    ]);
                });
            }
            $this->noticeService->sendUser($id, [
                'notices' => [
                    'flow' => Notice::FLOW_QA,
                    'user_id' => $id,
                ],
                'notice_details' => [
                    [
                        'content' => 'Q&A：AMSからの回答',
                        'target_page' => route('admin.question.answers.02s', ['user_id' => $id, 'qa_id' => $request['qa_id']]),
                        'redirect_page' => route('user.qa.03.kaito.list', ['id' => $request['qa_id']]),
                        'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                        'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                        'is_action' => true,
                    ],
                ],
            ]);

            // Send Mail
            $adminSekiJimuEmail = Admin::whereIn('role', [Admin::ROLE_ADMIN_SEKI, Admin::ROLE_ADMIN_JIMU])->pluck('email')->toArray();
            SendGeneralMailJob::dispatch('emails.a000qa02s-send-mail', [
                'to' => User::find($id)->getListMail(),
                'subject' => __('labels.qa.title_send_email_QA2'),
                'bcc' => $adminSekiJimuEmail,
            ]);
        }
        if ($answerAdmin == DRAFT_QA) {
            return redirect()->back()->with('message', __('messages.general.QA_save'))->withInput();
        } else {
            return redirect()->route('admin.home')->with('message', __('messages.general.seki_submit_to_user'))->withInput();
        }
    }

    /**
     * Create Question From Admin
     *
     * @param  \Illuminate\Http\Request $request
     * @param  integer                  $id
     * @return \Illuminate\Http\Response
     */
    public function createQuestion(Request $request, int $id)
    {
        $createQuestion = $this->qAService->createOrUpdateQuestion($request, $id);

        if ($createQuestion == DRAFT_QA) {
            return redirect()->back()->with('message', __('messages.general.QA_save'))->withInput();
        } elseif ($createQuestion == CONFIRM_QA) {
            return redirect()->back()->with('message', __('messages.general.tantou_send_to_seki'))->withInput();
        } else {
            return redirect()->back()->with('error', __('messages.error'))->withInput();
        }
    }

    /**
     * Modify Question From Admin Role Seki
     *
     * @param  \Illuminate\Http\Request $request
     * @param  integer                  $id
     * @return \Illuminate\Http\Response
     */
    public function modifyQuestion(Request $request, int $id)
    {
        $createQuestion = $this->qAService->modifyQuestion($request, $id);

        if ($createQuestion == DRAFT_QA) {
            return redirect()->back()->with('message', __('messages.general.QA_save'))->withInput();
        } else {
            return redirect()->back()->with('message', __('messages.general.seki_submit_to_user'))->withInput();
        }
    }

    /**
     * Search by user create trademark
     *
     * @param  mixed $id
     * @return void
     */
    public function search($id)
    {
        \Session::put(SESSION_SEARCH_TOP, [
            'searchHasClose' => 'false',
            'searchType' => 'and',
            'searchData' => [
                [
                    'field' => 'user_id',
                    'value' => $id,
                    'condition' => 'equal',
                ],
            ],
        ]);

        return redirect()->route('admin.search.application-list', ['filter' => $id]);
    }
}
