<?php

namespace App\Repositories;

use App\Models\Admin;
use App\Models\QuestionAnswer;
use App\Models\User;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class QuestionAnswerRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   QuestionAnswer $questionAnswer
     * @return  void
     */
    public function __construct(QuestionAnswer $questionAnswer)
    {
        $this->model = $questionAnswer;
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
            case 'question_type':
                return $query->where($column, $data);
            case 'more_ids':
                return $query->where('id', '<', $data);
            case 'raw':
                return $query->whereRaw($data);
            default:
                return $query;
        }
    }
}
