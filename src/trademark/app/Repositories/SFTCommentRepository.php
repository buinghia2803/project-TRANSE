<?php

namespace App\Repositories;

use App\Models\SFTComment;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class SFTCommentRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   SFTComment $sftComment
     * @return  void
     */
    public function __construct(SFTComment $sftComment)
    {
        $this->model = $sftComment;
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
            case 'type':
            case 'support_first_time_id':
                return $query->where($column, $data);
            case 'raw':
                return $query->whereRaw($data);
            default:
                return $query;
        }
    }
}
