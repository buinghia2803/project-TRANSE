<?php

namespace App\Repositories;

use App\Models\ReasonQuestion;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class ReasonQuestionRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   ReasonQuestion $reasonQuestion
     * @return  void
     */
    public function __construct(ReasonQuestion $reasonQuestion)
    {
        $this->model = $reasonQuestion;
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
            case 'plan_correspondence_id':
                return $query->where($column, $data);
            case 'raw':
                return $query->whereRaw($data);
            default:
                return $query;
        }
    }
}
