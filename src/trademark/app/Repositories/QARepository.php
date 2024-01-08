<?php

namespace App\Repositories;

use App\Models\QuestionAnswer;
use App\Models\Admin;
use App\Services\NotifyService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class QARepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   QuestionAnswers $questionAnswers
     * @return  void
     */
    public function __construct(QuestionAnswer $questionAnswers, NotifyService $notifyService)
    {
        $this->model = $questionAnswers;
        $this->notifyService = $notifyService;
    }

    /**
     * @param   Builder $query
     * @param   string  $column
     * @param   mixed   $data
     *
     * @return  Builder
     */
    public function mergeQuery(Builder $query, string $column, $data): Builder
    {
        switch ($column) {
            case 'id':
            case 'user_id':
            case 'admin_id':
            case 'question_content':
            case 'question_attaching_file':
            case 'question_type':
            case 'answer_content':
            case 'answer_attaching_file':
            case 'question_date':
            case 'answer_date':
            case 'response_deadline':
            case 'office_comments':
            case 'is_confirm':
                return $query->where($column, $data);
            case 'more_ids':
                return $query->where('id', '<', $data);
            default:
                return $query;
        }
    }

    /**
     * Get Detail Question Answer By Id
     *
     * @param $request
     * @return Collection
     */
    public function getQuestionAnswersById($id)
    {
        $questionAnswers = $this->model->where('user_id', $id)
            ->orderBy('id', 'DESC')
            ->take(QuestionAnswer::QUESTION_ANSWERS_NEWEST)
            ->get();

        if (!$questionAnswers) {
            return false;
        }

        return $questionAnswers;
    }

    /**
     * Get List Question Answer By Id
     *
     * @param $request
     * @return Collection
     */
    public function getListQuestionAnswers($id)
    {
        $questionAnswers = $this->model->where('user_id', $id)
            ->orderBy('id', 'DESC')
            ->get();

        if (!$questionAnswers) {
            return false;
        }

        return $questionAnswers;
    }

    public function getDraftQuestionUser($id)
    {
        $questionDraft = $this->model->where('user_id', $id)
            ->where('is_confirm', QuestionAnswer::IS_NOT_CONFIRM)
            ->where('question_date', null)
            ->first();

        return $questionDraft;
    }
    /**
     * Get List Question Answer By Id
     *
     * @param $request
     * @return Collection
     */
    public function getQuestionFromAmsRepository($authId)
    {
        $questions = $this->model->where('user_id', $authId)
            ->whereNotNull('question_answers.answer_date')
            ->orderBy('question_answers.id', 'DESC')
            ->take(QuestionAnswer::QUESTION_ANSWERS_NEWEST)
            ->get();

        return $questions;
    }

    /**
     * Get List Question Answer By Id Input
     *
     * @param integer $authId
     * @return Collection
     */
    public function getQuestionFromAmsInputRepository(int $authId)
    {

        $questionsInput = $this->model->where('user_id', $authId)
            ->where('question_type', QuestionAnswer::QUESTION_FROM_AMS)
            ->where('question_answers.is_confirm', QuestionAnswer::IS_CONFIRM)
            ->whereNull('question_answers.answer_date')
            ->orderBy('question_answers.id', 'DESC')
            ->take(QuestionAnswer::QUESTION_ANSWERS_NEWEST)
            ->get();

        return $questionsInput;
    }

    /**
     * Get Question Answers User Send To Tantou
     *
     * @param $id
     * @param int $qaId
     * @return Collection
     */
    public function getQuestionAnswersToTantou($id, int $qaId)
    {
        $questionAnswers = $this->model->where('user_id', $id)
            ->where('question_type', QuestionAnswer::QUESTION_FROM_CUSTOMERS)
            ->where('is_confirm', QuestionAnswer::IS_CONFIRM)
            ->orderBy('created_at', 'desc')
            ->take(QuestionAnswer::QUESTION_ANSWERS_NEWEST)
            ->where('id', '<=', $qaId)
            ->get();

        return $questionAnswers;
    }

    /**
     * Get Question Answers User Send To Tantou
     *
     * @param $id
     * @param int $qaId
     * @return Collection
     */
    public function getQuestionAnswersToSeki($id, int $qaId)
    {
        $questionAnswers = $this->model->where('user_id', $id)
            ->where('flag_role', QuestionAnswer::FLAG_ROLE_SEKI)
            ->where('is_confirm', QuestionAnswer::IS_CONFIRM)
            ->orderBy('created_at', 'desc')
            ->take(QuestionAnswer::QUESTION_ANSWERS_NEWEST)
            ->where('id', '<=', $qaId)
            ->get();

        return $questionAnswers;
    }

    /**
     * Get Question Answers User Send To Tantou Input
     *
     * @param int $id
     * @param int $qaId
     * @return Collection
     */
    public function getQuestionAnswersToTantouInput(int $id, $qaId = null)
    {
        $questionAnswers = $this->model->where('user_id', $id)
            ->where('question_type', QuestionAnswer::QUESTION_FROM_CUSTOMERS)
            ->where('is_confirm', QuestionAnswer::IS_NOT_CONFIRM)
            ->where('flag_role', QuestionAnswer::FLAG_ROLE_TANTOU)
            ->where('question_date', '!=', null)
            ->where('id', $qaId)
            ->get();

        return $questionAnswers;
    }

    /**
     * Get Question Answers Input User Send To Tantou
     *
     * @param int $userId
     *
     * @return mixed
     */
    public function getQuestionAnswersInputToTantou(int $userId)
    {
        $questionAnswersInput = QuestionAnswer::whereHas('admin', function ($query) {
            return $query->where('role', Admin::ROLE_ADMIN_TANTO);
        })
            ->where('admin_id', Auth::user()->id)
            ->where('user_id', $userId)
            ->where('question_type', QuestionAnswer::QUESTION_FROM_CUSTOMERS)
            ->orderBy('question_date', 'DESC')
            ->whereNull('answer_content')
            ->get();

        return $questionAnswersInput;
    }

    /**
     * Get Question Answers User Send To Tantou
     *
     * @param $user
     * @return Collection
     */
    public function getQuestionAnswersToUser($userId)
    {
        $questionAnswers = $this->model->where('user_id', $userId)
            ->where('question_type', QuestionAnswer::QUESTION_FROM_AMS)
            ->where('is_confirm', QuestionAnswer::IS_NOT_CONFIRM)
            ->where('flag_role', QuestionAnswer::FLAG_ROLE_TANTOU)
            ->first();

        return $questionAnswers;
    }

    /**
     * Get Question Answers User Send To Tantou
     *
     * @param $user
     * @return Collection
     */
    public function getQuestionFromSeki($userId)
    {
        $questionAnswers = $this->model->where('user_id', $userId)
            ->where('question_type', QuestionAnswer::QUESTION_FROM_AMS)
            ->where('is_confirm', QuestionAnswer::IS_NOT_CONFIRM)
            ->where('flag_role', QuestionAnswer::FLAG_ROLE_SEKI)
            ->get();

        return $questionAnswers;
    }

    /**
     * Get Question Answers From Seki Exist
     *
     * @param $userId
     * @return Collection
     */
    public function getQuestionFromSekiExist($userId)
    {
        $questionAnswers = $this->model->where('user_id', $userId)
            ->where('is_confirm', QuestionAnswer::IS_CONFIRM)
            ->where('flag_role', QuestionAnswer::FLAG_ROLE_SEKI)
            ->orderBy('created_at', 'desc')
            ->take(QuestionAnswer::QUESTION_ANSWERS_NEWEST)
            ->get();

        return $questionAnswers;
    }

    /**
     * Get Question Exist Answer
     *
     * @param $userId
     * @return Collection
     */
    public function getQuestionExistAnswer($userId)
    {
        $questionAnswers = $this->model->where('user_id', $userId)
            ->where('is_confirm', QuestionAnswer::IS_CONFIRM)
            ->where('flag_role', QuestionAnswer::FLAG_ROLE_SEKI)
            ->orderBy('created_at', 'desc')
            ->take(QuestionAnswer::QUESTION_ANSWERS_NEWEST)
            ->get();

        return $questionAnswers;
    }

    /**
     * Get First Record Question Answers
     *
     * @return  Collection
     */
    public function getFirstQuestionRepository($userId)
    {
        $questionFirst = $this->model->where('user_id', $userId)->orderBy('id', 'desc')->first();

        return $questionFirst;
    }

    /**
     * Get List Record Question Answers
     *
     * @return  Collection
     */
    public function getListQuestionRepository($userId)
    {
        $questionFirst = $this->getFirstQuestionRepository($userId);
        $questionExceptFirst = $this->model->where('id', '<>', $questionFirst->id ?? null)
            ->where('user_id', $userId)
            ->orderBy('question_answers.id', 'DESC')
            // ->take(QuestionAnswer::QUESTION_ANSWERS_NEWEST)
            ->paginate(PAGINATE_NUMBER);

        return $questionExceptFirst;
    }

    /**
     * Get Question Answer Tantou Send To Seki
     *
     * @param int $userId
     * @param int $qaId
     * @return  Collection
     */
    public function getQuestionToSeki(int $userId, int $qaId)
    {
        $questionAnswersInput = $this->model->where('user_id', $userId)
            ->where('question_type', QuestionAnswer::QUESTION_FROM_CUSTOMERS)
            ->where('is_confirm', QuestionAnswer::IS_NOT_CONFIRM)
            ->where('flag_role', QuestionAnswer::FLAG_ROLE_SEKI)
            ->where('id', $qaId)
            ->get();

        return $questionAnswersInput;
    }
}
