<?php

namespace App\Repositories;

use App\Models\PlanComment;
use Illuminate\Database\Eloquent\Builder;

class PlanCommentRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   PlanComment $planComment
     * @return  void
     */
    public function __construct(PlanComment $planComment)
    {
        $this->model = $planComment;
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
            case 'target_id':
            case 'type':
            case 'trademark_id':
            case 'trademark_plan_id':
            case 'type_comment_step':
                return $query->where($column, $data);
            case 'type_comment_steps':
                return $query->whereIn('type_comment_step', $data);
            default:
                return $query;
        }
    }
}
