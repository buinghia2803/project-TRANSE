<?php

namespace App\Repositories;

use App\Models\ReasonQuestionNo;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReasonQuestionNoRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   ReasonQuestionNo $reasonQuestionNo
     * @return  void
     */
    public function __construct(ReasonQuestionNo $reasonQuestionNo)
    {
        $this->model = $reasonQuestionNo;
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
            case 'is_confirm':
            case 'flag_role':
            case 'question_status':
                return $query->where($column, $data);
            case 'raw':
                return $query->whereRaw($data);
            default:
                return $query;
        }
    }
}
