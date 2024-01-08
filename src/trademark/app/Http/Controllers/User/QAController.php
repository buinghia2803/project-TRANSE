<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\QuestionAnswers\CreateQuestionAnswersRequest;
use App\Models\Notice;
use App\Models\NoticeDetail;
use App\Models\QuestionAnswer;
use App\Services\Common\NoticeService;
use App\Services\NoticeDetailService;
use App\Services\QAService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class QAController extends Controller
{

    protected QAService $qAService;
    private NoticeService $noticeService;
    private NoticeDetailService $noticeDetailService;

    /**
     * Constructor
     *
     * @return  void
     */
    public function __construct(
        QAService $qAService,
        NoticeService $noticeService,
        NoticeDetailService $noticeDetailService
    )
    {
        $this->qAService = $qAService;
        $this->noticeService = $noticeService;
        $this->noticeDetailService = $noticeDetailService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showQA01()
    {
        $authId = Auth::user()->id;
        if (!$authId) {
            return redirect()->back()->with('error', 'not found');
        }
        return view('user.modules.Q&A.QA01-FAQ');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showQA02()
    {
        $convertQuestionAttachFile = [];
        $questionAnswers = $this->qAService->getQuestionAnswersById(auth()->user()->id);
        $questionDraft = $this->qAService->findByCondition([
            'user_id' => auth()->user()->id,
            'is_confirm' => QuestionAnswer::IS_NOT_CONFIRM,
            'question_date' => null,
        ])->orderBy('id', SORT_TYPE_DESC)
            ->first();
        if ($questionDraft && $questionDraft->question_attaching_file) {
            $convertQuestionAttachFile = json_decode($questionDraft->question_attaching_file);
        }

        return view('user.modules.Q&A.QA02-Q&A', compact('questionAnswers', 'questionDraft', 'convertQuestionAttachFile'));
    }


    /**
     * Show QA 03 Kaito
     *
     * @param  int $id - question_answer_id
     * @return void
     */
    public function showQA03Kaito($id = null)
    {
        $firstQA = null;
        $conditionList = [
            'user_id' => auth()->user()->id,
            'is_confirm' => QuestionAnswer::IS_CONFIRM,
        ];
        if (isset($id)) {
            $firstQA = $this->qAService->findByCondition([
                'user_id' => auth()->user()->id,
                'id' => $id,
            ])->first();
            if (!$firstQA) {
                abort(404);
            }
            $conditionList['more_ids'] = $id;
        }
        $listQA = $this->qAService->findByCondition($conditionList)->get();

        return view('user.modules.Q&A.u000qa03kaito_list', compact(
            'firstQA',
            'listQA'
        ));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showTop()
    {
        // To do

        return view('user.modules.u000top');
    }

    /**
     * Show QA 02 Kaito
     *
     * @param  int $id - question_answer_id
     * @return void
     */
    public function showQA02Kaito($id)
    {
        $authId = Auth::user()->id;
        $questions = $this->qAService->findByCondition([
            'user_id' => auth()->user()->id,
            'id' => $id,
            'is_confirm' => QuestionAnswer::IS_CONFIRM,
        ])->first();
        $questionsInput = $this->qAService->findByCondition([
            'user_id' => auth()->user()->id,
            'more_ids' => $id,
            'is_confirm' => QuestionAnswer::IS_CONFIRM,
        ])->orderBy('id', 'DESC')
            ->take(QuestionAnswer::QUESTION_ANSWERS_NEWEST)
            ->get();

        return view('user.modules.Q&A.u000qa02kaito', compact(
            'questions',
            'questionsInput',
            'authId',
            'id'
        ));
    }

    /**
     * Created new record Question Answers
     *
     * @param  \Illuminate\Http\CreateQuestionAnswersRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function createQA(CreateQuestionAnswersRequest $request)
    {
        try {
            $questionAnswers = $this->qAService->createQuestionAnswers($request);

            if (!$questionAnswers) {
                return redirect()->back()->with('error', 'Not found')->withInput();
            }
            if ($questionAnswers == DRAFT_QA) {
                return redirect()->back()->with('message', __('messages.general.QA_save'))->withInput();
            } elseif ($questionAnswers == CONFIRM_QA) {
                return redirect()->back()->with('message', __('messages.general.QA_U000_S001'))->withInput();
            }
        } catch (\Exception $e) {
            \Log::error($e);

            abort(500);
        }
    }

    /**
     * Created Answers To User
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function createAnswerToUser(Request $request)
    {
        $this->qAService->createAnswerToUserService($request);

        return redirect()->back()->with('message', __('messages.general.QA_save'))->withInput();
    }
}
