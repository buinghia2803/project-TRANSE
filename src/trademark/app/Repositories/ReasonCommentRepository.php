<?php

namespace App\Repositories;

use App\Models\ReasonComment;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class ReasonCommentRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   ReasonComment $reasonComment
     * @return  void
     */
    public function __construct(ReasonComment $reasonComment)
    {
        $this->model = $reasonComment;
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
            case 'admin_id':
            case 'type':
            case 'type_comment_step':
                return $query->where($column, $data);
            case 'raw':
                return $query->whereRaw($data);
            default:
                return $query;
        }
    }
}
