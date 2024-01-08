<?php

namespace App\Repositories;

use App\Models\ReasonQuestionDetail;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReasonQuestionDetailRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   ReasonQuestionDetail $reasonQuestionDetail
     * @return  void
     */
    public function __construct(ReasonQuestionDetail $reasonQuestionDetail)
    {
        $this->model = $reasonQuestionDetail;
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
            case 'reason_question_id':
            case 'reason_question_no_id':
            case 'is_answer':
            case 'is_confirm':
                return $query->where($column, $data);
            case 'reason_question_ids':
                return $query->whereIn('reason_question_id', $data);
            case 'raw':
                return $query->whereRaw($data);
            default:
                return $query;
        }
    }

    /**
     * Get Reason Question Detail Data
     *
     * @param array $inputs
     * @return Collection
     */
    public function getReasonQuestionDetailData(array $inputs): Collection
    {
        $comparisonTrademarkResultId = $inputs['comparison_trademark_result_id'];
        $sortBy = $inputs['sort_by'];
        $reasonQuestionNo = $inputs['reason_question_no_id'];
        $isAnswer = $inputs['is_answer'] ?? null;
        $compare = $inputs['compare'] ?? '=';

        $query = $this->model
           ->join('reason_questions', 'reason_question_details.reason_question_id', '=', 'reason_questions.id')
           ->join('plan_correspondences', 'reason_questions.plan_correspondence_id', '=', 'plan_correspondences.id')
           ->join('comparison_trademark_results', 'plan_correspondences.comparison_trademark_result_id', '=', 'comparison_trademark_results.id')
           ->where('comparison_trademark_results.id', $comparisonTrademarkResultId);
        if ($isAnswer) {
            $query->where('reason_question_details.is_answer', $isAnswer);
        }
        if ($reasonQuestionNo) {
            $query->where('reason_question_details.reason_question_no_id', $compare, $reasonQuestionNo);
        }

        return $query->select('reason_question_details.*')
        ->orderBy('reason_question_details.id', $sortBy)
        ->get();
    }

    /**
     * Get Reason Question Detail Data (with reason_question_no)
     *
     * @param integer $comparisonTrademarkResultId
     * @param int $isAnswer
     * @param string $sortBy
     * @param int $reasonQuestionNoId
     * @return Collection
     */
    public function getReasonQuestionDetailDataV2(
        int $comparisonTrademarkResultId,
        int $isAnswer,
        string $sortBy,
        int $reasonQuestionNoId,
        $isConfirm,
        $condition
    ): Collection
    {
        return $this->model
            ->join('reason_question_no', 'reason_question_details.reason_question_no_id', '=', 'reason_question_no.id')
            ->join('reason_questions', 'reason_question_no.reason_question_id', '=', 'reason_questions.id')
            ->join('plan_correspondences', 'reason_questions.plan_correspondence_id', '=', 'plan_correspondences.id')
            ->join('comparison_trademark_results', 'plan_correspondences.comparison_trademark_result_id', '=', 'comparison_trademark_results.id')
            ->where('comparison_trademark_results.id', $comparisonTrademarkResultId)
            ->where('reason_question_details.is_answer', $isAnswer)
            ->where('reason_question_no.is_confirm', $isConfirm)
            ->where('reason_question_no.id', $condition, $reasonQuestionNoId)
            ->select('reason_question_details.*')
            ->orderBy('reason_question_details.id', $sortBy)
            ->get();
    }
}
